<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\artistRequest;
use App\artist;
use App\Singer;
use App\track;
use App\Movie;
use App\artist_track;
use App\singer_track;
use App\music_director;
use App\genere;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Session;
use App\library\encodeClass;
use Auth;
use Activity;
use App\albumtracks;

class trackController extends Controller {

    private $prefix = "";

    private $cover_url = "";
    private $mp3_url = "";
    private $mpd_url = "";
    private $mp4_url = "";
    private $bitcodinJob;
    public function __construct() {
        $this->prefix = DB::getTablePrefix();
        $this->bitcodinJob = new encodeClass();
    }

    private function getCat($id){
      $sub_cat = DB::select("select
        cat.name
      from tbl_movies as movie
      inner join tbl_movie_cats as mcat on movie.id=mcat.movie_id
      inner join tbl_categories as cat on cat.id=mcat.cat_id
      where LENGTH(cat.name) <= 3 and movie.id={$id}
      group by cat.name");
      $cat = DB::select("select
        case cat.name when 'Artist-Albums' then 'Artist' else cat.name end as name
      from tbl_movies as movie
      inner join tbl_movie_cats as mcat on movie.id=mcat.movie_id
      inner join tbl_categories as cat on cat.id=mcat.cat_id
      where LENGTH(cat.name) > 3 and movie.id={$id} and NOT cat.id IN(33,15,32,59,54,56,31)
      group by cat.name");
      if ($cat[0]->name == "Punjabi-Songs"){
        $cat[0]->name = "Punjabi";
      }elseif ($cat[0]->name == "Sindhi-Songs"){
        $cat[0]->name = "Sindhi";
      }
      
      if ($cat[0]->name == "Bollywood-Mashup" || $cat[0]->name == "MASHUPS"){
        return $cat[0]->name;
      }else{
        return $cat[0]->name . "/" . $sub_cat[0]->name;
      }
      
    }

    private function getArtist($id){
      // tbl_artist_movies
      $d =  DB::select("select 
      a.id, a.name, mcat.cat_id, replace(art.name,' ','-') as artist 
      from tbl_movies as a 
      inner join tbl_artist_movies as b on a.id=b.movie_id 
      inner join tbl_artists as art on b.post_id=art.id 
      inner join tbl_movie_cats as mcat on a.id=mcat.movie_id 
      where mcat.cat_id in(7,60,58) and a.id={$id}");
      if (!empty($d)){
        return $d[0]->artist;
      }else{
        return null;
      }
    }

    private function isRegional($id){
        $d = DB::select("select 
        a.id
        from tbl_movies as a 
        inner join tbl_artist_movies as b on a.id=b.movie_id 
        inner join tbl_movie_cats as mcat on a.id=mcat.movie_id 
        where mcat.cat_id in (58,64) and a.id={$id}");
        if (!empty($d)){
          return "Regional";  
        }else {
          return null;
        }
    }

    public function index($movie_id = null) {
        if (!empty($movie_id)) {
            $data = track::where("movie_id", $movie_id)->paginate(25);
        } else {
            $data = track::paginate(25);
        }
        $movies = Movie::orderBy('name')->get();
        return view('track.index', [
            "data" => $data,
            "movies" => $movies,
            "movie_id" => $movie_id
        ]);
    }

    public function edit($id) {
        $movie_id = track::find($id);
        $artists = artist::orderBy("name")->get();
        $singers = Singer::orderBy("name")->get();
        $movies = Movie::all();
        $generes = genere::where("type", "Music")->get();
        $music_directors = music_director::all();
        return view('track.edit', [
            "artists" => $artists,
            "singers" => $singers,
            "movies" => $movies,
            "music_directors" => $music_directors,
            "generes" => $generes,
            "movie_id" => $movie_id->movie_id
        ]);
    }

    public function newTrack($id = null) {
        $artists = artist::orderBy("name")->get();
        $singers = Singer::orderBy("name")->get();
        $movies = Movie::all();
        $generes = genere::where("type", "Music")->get();
        $movie_id = (!empty($id) ? $id : 0);
        $music_directors = music_director::all();
        return view('track.edit', [
            "artists" => $artists,
            "singers" => $singers,
            "movies" => $movies,
            "music_directors" => $music_directors,
            "generes" => $generes,
            "movie_id" => $movie_id
        ]);
    }

    public function getEncoderStatus($track_id, $job_id){
      $job_id = $job_id;
      $log = json_decode($this->bitcodinJob->logReader($job_id),true);

      $job_status = (!empty($log['job']['status']) ? strtolower($log['job']['status']) : "");
      $transfer_status =  (!empty($log['transfer'][0]['status']) ? strtolower($log['transfer'][0]['status']) : "");
      if (true){
        if ($job_status == "finished" && $transfer_status == "finished"){
          $track = track::find($track_id);
          $url = str_replace("http://","https://",$log['job']['outputPath']);
          //$url = str_replace("bsongs.storage.googleapis.com","bsbestsongs.global.ssl.fastly.net",$url);
          $track->mpd_url = $url . "/" . $job_id . ".mpd";
          $track->save();
          
          echo "Encoded successfully";
        } else if($job_status == "error" && $transfer_status == "error"){
          echo "An error while encoding ....";
        } else {
          echo "In Process ....";
        }
      }
    }


    public function uploader(Request $req) {
      // cover_url start
      $param = $req->all();
	//dd($req->all());
      $track_id = $param["track_id"];
      $movie_id = $param["movie_id"];
      // dd($param);
      if ($req->hasFile("cover_url")) {
          $cover_file = $req->file("cover_url");
          Storage::cloud()->put("track_cover/" . str_replace(" ","-",$cover_file->getClientOriginalName()), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
          $track = track::find($track_id);
          $cdn = config("app.cdn_url") . "/track_cover" . "/" .  str_replace(" ","-",$cover_file->getClientOriginalName());
          $track->cover_url = $cdn;
          $track->uid = Auth::id();
          $track->save();
          Activity::log('Update Track Cover ' . $track->name);
          return "Uploaded";
      }
      // cover_url end

      // audio_url start
      if ($req->hasFile("audio_url")) {
        $movie = Movie::find($movie_id);
        $slug = $movie->slug;
        if (empty($slug)){
          $slug = $movie->name;
           $pattArr = [
            "-"=>"",
            " " => "-",
            "(" => "",
            ")" => "",
            "[" => "",
            "]" => "-",
            "{" => "-",
            "}" => "-",
            "&"=>"-","."=>"-","--"=>"-"
          ];
          foreach ($pattArr as $key => $value) {
            $slug = str_replace($key,$value,$slug);
          }
        }
        $mp3_file = $req->file("audio_url");
        $art = $this->getArtist($movie_id);
        $regional = $this->isRegional($movie_id);
        Storage::cloud()->put("Best-Songs-data" . "/" . (!empty($regional) ? $regional . "/" : "") .  $this->getCat($movie_id) . "/" . (!empty($art) ? $art . "/" : "") . $slug .  "/" . str_replace(" ","-",$mp3_file->getClientOriginalName()), File::get($mp3_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        $track = track::find($track_id);
        $track->audio_url = config("app.cdn_url") . "/Best-Songs-data" . "/" . (!empty($regional) ? $regional . "/" : "") .  $this->getCat($movie_id) . "/" . (!empty($art) ? $art . "/" : "") . $slug .  "/" . str_replace(" ","-",$mp3_file->getClientOriginalName());
        $track->uid = Auth::id();
        $track->save();
        Activity::log('Update Track Mp3 ' . $track->name);
        return "Uploaded";
      }
      // audio_url end

      // mpd_url start
      if (false && $req->hasFile("mpd_url")) {
        $mcat = $this->getCat($movie_id);
        $movie = Movie::find($movie_id);
        $art = $this->getArtist($movie_id);
        $regional = $this->isRegional($movie_id);
        // $regional = (!empty($regional) ? "regional" : "");
        $slug = $movie->slug;
        if (empty($slug)){
          $slug = $movie->name;
           $pattArr = [
            "-"=>"",
            " " => "-",
            "(" => "",
            ")" => "",
            "[" => "",
            "]" => "-",
            "{" => "-",
            "}" => "-","&"=>"-",".","-","--"=>"-"
          ];
          foreach ($pattArr as $key => $value) {
            $slug = str_replace($key,$value,$slug);
          }
        }
        $mpd_file = $req->file("mpd_url");
        Storage::cloud()->put("Best-Songs-data" . "/" . (!empty($regional) ? "Regional" . "/" : "") .   $mcat . "/" . (!empty($art) ? $art . "/" : "") . $slug .  "/" . $mpd_file->getClientOriginalName(), File::get($mpd_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        $file_url =  "https://storage.googleapis.com/bsongs/Best-Songs-data" . "/" . (!empty($regional) ? "Regional" . "/" : "") .  $mcat . "/" . (!empty($art) ? $art . "/" : "")  . $slug .  "/" . $mpd_file->getClientOriginalName();
        $mcat1 = explode("/",$mcat);
        if (is_array($mcat)){
          $mcat =  (!empty($regional) ? ((explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]) : (strtolower(explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]));
          $this->bitcodinJob->param = [
            "movie" => $movie->slug,
            "cat" => $mcat1[0],
            "subcat" => $mcat1[1],
            "file" => $mpd_file->getClientOriginalName()
          ];
        }
        
        $this->bitcodinJob->UID = $track_id;
        $this->bitcodinJob->eType = "Track";
        $job_id = $this->bitcodinJob->job($file_url, (!empty($regional) ? "regional/" : "") . ($mcat . "/" . (!empty($art) ? $art . "/" : "") . $slug .  "/" . $mpd_file->getClientOriginalName()));
        Activity::log('Track Encoded ' . $track_id);
        return response()->json(["message"=>"Uploaded","track_id"=>$track_id,"job_id"=>$job_id]);
      }
      // mpd_url end

      // mp4_url start
      if (false && $req->hasFile("mp4_url")) {
        $movie = Movie::find($movie_id);
        $slug = $movie->slug;
        if (empty($slug)){
          $slug = $movie->name;
           $pattArr = [
            "-"=>"",
            " " => "-",
            "(" => "",
            ")" => "",
            "[" => "",
            "]" => "-",
            "{" => "-",
            "}" => "-"
          ];
          foreach ($pattArr as $key => $value) {
            $slug = str_replace($key,$value,$slug);
          }
        }
        $mcat = $this->getCat($movie_id);
        $art = $this->getArtist($movie_id);
        $regional = $this->isRegional($movie_id);
        $regional = (!empty($regional) ? "regional" : "");
        if (is_array($mcat)){
          $mcat =  (!empty($regional) ? ((explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]) : (strtolower(explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]));
        }
        $mp4_file = $req->file("mp4_url");
        Storage::cloud()->put("video-data" . "/" . (!empty($regional) ? $regional . "/" : "") .  $mcat . "/" . (!empty($art) ? $art . "/" : "") . $slug . "/mp4s" .  "/"  . $mp4_file->getClientOriginalName(), File::get($mp4_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        $track = track::find($track_id);
        $track->mp4_url = config("app.cdn_url") . "/video-data" . "/" .  (!empty($regional) ? $regional . "/" : "") .  $mcat . "/" . (!empty($art) ? $art . "/" : "") . $slug .  "/mp4s" . "/" . urlencode($mp4_file->getClientOriginalName());
        $track->uid = Auth::id();
        $track->save();
        Activity::log('Update Track Mp4 360 ' . $track->name);
        return "Uploaded";
      }
      // mp4_url end


  }

    public function save(Request $req) {
        $param = $req->all();
        $err = "save";
        $isNew = false;
        // $this->uploader($req);
        if ($param['id'] > 0){
          $track = track::find($param['id']);
          $isNew = false;
        }else{
          $track = new track;
          $isNew = true;
        }
        $track->name = $param["name"];
        $pattArr = ["-"=>""," " => "-","(" => "",")" => "","[" => "","]" => "-","{" => "-","}" => "-"];
        foreach ($pattArr as $key => $value) {
          $param["name"] = str_replace($key,$value,$param["name"]);
        }
        $track->slug = $param["name"];
        $track->director_id = $param['music_director'];
        $track->movie_id = $param['movie'];
        $track->genere = $param['genere'];
        $track->updated_at = date('Y-m-d');

        //ashok changes 
        $track->geo =  $param['geo'];
        $track->video_share_url = "https://bestsongs.pk/videos/" .  Str::slug($param["name"], '-');

        
        // $movie = Movie::find($param['movie']);
        // $track->audio_url = config("app.cdn_url") . "/Best-Songs-data/" . (bollywood / hollywood) . "/" .  $this->getCat($param['movie']) . "/" . str_replace(" ","-",$movie->name) .  "/" . Session::get("mp3_url");
        // $track->cover_url = config("app.cdn_url") . "/" . Session::get('cover_url');
        $track->uid = Auth::id();
        $track->save();
        Activity::log( ($isNew ? "Create" : "Update") . ' Track ' . $track->name);
        $movie = Movie::find($param['movie']);
        $movie->total_tracks = track::where("movie_id",$param['movie'])->count();
        $movie->updated_at = date("Y-m-d h:i:s");
        $movie->save();
        albumtracks::where(["album_id"=>$movie->id,"track_id"=>$track->id])->delete();
        $albumtracks = new albumtracks;
        $albumtracks->album_id = $movie->id;
        $albumtracks->track_id = $track->id;
        $albumtracks->save();
        artist_track::where('track_id', $param['id'])->delete();
        for ($i = 0; $i < count($param["artist_id"]); $i++) {
            $artist_track = new artist_track;
            $artist_track->track_id = $param["id"];
            $artist_track->artist_id = $param["artist_id"][$i];
            if ($artist_track->save() == true) {
                $err = "save";
            } else {
                $err = "";
                $param["artist_id"] = array();
            }
        }
        singer_track::where('track_id',$param['id'])->delete();
        for ($i=0; $i < count($param["singer_id"]); $i++){
          $singer_track = new singer_track;
          $singer_track->track_id =$param["id"];
          $singer_track->singer_id =$param["singer_id"][$i];
          if ($singer_track->save() == true){
            $err = "save";
          }else{
            $err = "";
            $param["singer_id"] = array();
          }
        }
        return response()->json(["message"=>$err, "track_id"=>$track->id]);
    }

    public function artist() {
        return view("artist.index");
    }

    public function saveArtist(artistRequest $req) {
        $param = $req->all();
        if (!empty($param["id"])){
          $artist = artist::find($param["id"]);
        } else {
          $artist = new artist;
        }
        $artist->name = $param["name"];
        if (!empty($param['gender'])){
            $artist->gender = $param["gender"];
        }
        if ($req->hasFile("cover_url")) {
          $cover_file = $req->file("cover_url");
          Storage::cloud()->put("artists_cover/" . str_replace(" ","-",$cover_file->getClientOriginalName()), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
          $cdn = config("app.cdn_url") . "/artists_cover" . "/" .  str_replace(" ","-",$cover_file->getClientOriginalName());
          $artist->cover_url = $cdn;
        }
        if ($artist->save()) {
            echo "Saved";
        }
    }

    public function editTrack() {
        $artists = artist::all();
        $singers = Singer::all();
        $generes = genere::where("type", "Music")->get();
        $music_directors = music_director::all();
        return view('unmapartist.index', [
            "artists" => $artists,
            "singers" => $singers,
            "music_directors" => $music_directors,
            "generes" => $generes
        ]);
    }

    public function saveTrack(Request $req) {
        $param = $req->all();
        $err = "save";
        $track = new track;
        $track->name = $param["name"];
        $pattArr = [
          "-"=>"",
          " " => "-",
          "(" => "",
          ")" => "",
          "[" => "",
          "]" => "-",
          "{" => "-",
          "}" => "-"
        ];
        foreach ($pattArr as $key => $value) {
          $param["name"] = str_replace($key,$value,$param["name"]);
        }
        $track->slug = $param["name"];
        $track->director_id = $param['music_director'];
        $track->genere = $param['genere'];
        $track->movie_id = $param['movie'];

        //ashok changes 
        $track->geo =  $param['geo'];
        $track->video_share_url = "https://bestsongs.pk/videos/" .  Str::slug($param["name"], '-');

        //$movie = Movie::find($param['movie']);
        // $track->audio_url = config("app.cdn_url") . "/Best-Songs-data/" . (bollywood / hollywood) . "/" .  $this->getCat($param['movie']) . "/" . str_replace(" ","-",$movie->name) .  "/" . Session::get("mp3_url");
        // $track->cover_url = config("app.cdn_url") . "/" . Session::get("cover_url");
        $track->uid = Auth::id();
        $track->save();
        Activity::log('Create Track ' . $track->name);
        $movie = Movie::find($param['movie']);
        $movie->total_tracks = track::where("movie_id",$param['movie'])->count();
        $movie->save();
        albumtracks::where(["album_id"=>$movie->id,"track_id"=>$track->id])->delete();
        $albumtracks = new albumtracks;
        $albumtracks->album_id = $movie->id;
        $albumtracks->track_id = $track->id;
        $albumtracks->save();
        Session::put("track_id",$track->id);
        artist_track::where('track_id', $track->id)->delete();

        for ($i = 0; $i < count($param["artist_id"]); $i++) {
            $artist_track = new artist_track;
            $artist_track->track_id = $track->id;
            $artist_track->artist_id = $param["artist_id"][$i];
            if ($artist_track->save() == true) {
                $err = "save";
            } else {
                $err = "";
                $param["artist_id"] = array();
            }
        }

        // singer_track::where('track_id',$param['id'])->delete();
        // for ($i=0; $i < count($param["singer_id"]); $i++){
        //   $singer_track = new singer_track;
        //   $singer_track->track_id =$param["id"];
        //   $singer_track->singer_id =$param["singer_id"][$i];
        //   if ($singer_track->save() == true){
        //     $err = "save";
        //   }else{
        //     $err = "";
        //     $param["singer_id"] = array();
        //   }
        // }

        return response()->json(["message"=>$err, "track_id"=>$track->id]);
    }


    public function bulk(Request $req, $movie_id = null){
      $data = [];
      $data["movies"] = Movie::orderBy("name")->get();
      if (!empty($movie_id)){
        $data["movie_id"] = $movie_id;
      }else{
        $data["movie_id"] = 0;
      }

      return view("track.bulk",$data);
    }

    public function bulkSave(Request $req){
      $param = $req->all();
      $chk = track::where(["movie_id"=>$param["movie"], "name"=>$param["name"]])->first();
      if (!empty($chk)){
        return response("Name already exist !", 500);
      }
      $track = new track;
      $track->name = $param["name"];
      $pattArr = [
        " " => "-",
        "(" => "",
        ")" => "",
        "[" => "",
        "]" => "-",
        "{" => "-",
        "}" => "-"
      ];
      foreach ($pattArr as $key => $value) {
        $param["name"] = str_replace($key,$value,$param["name"]);
      }
      $movie = Movie::find($param["movie"]);
      $track->movie_id = $param["movie"];
      $track->slug = $param["name"];
      $track->uid = Auth::id();
      $track->save();
      $movie->updated_at = date('Y-m-d');
      $movie->total_tracks = track::where("movie_id",$param['movie'])->count();
      $movie->save();
      albumtracks::where(["album_id"=>$movie->id,"track_id"=>$track->id])->delete();
      $albumtracks = new albumtracks;
      $albumtracks->album_id = $movie->id;
      $albumtracks->track_id = $track->id;
      $albumtracks->save();
      return response()->json(["track_id" => $track->id]);
    }


}
