<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Session;
use App\library\encodeClass;
use App\Http\Requests;
use App\Movie;
use App\MovieCat;
use App\trailler;
use App\artist;
use App\artistMovie;
use App\track;
use DB;
use Auth;
use Activity;

class movieController extends Controller
{
    private $bitcodinJob;
    public function __construct(){
      $this->bitcodinJob = new encodeClass();
    }
    private function getCat($id){
      $sub_cat = DB::select("select
        cat.name
      from tbl_movies as movie
      inner join tbl_movie_cats as mcat on movie.id=mcat.movie_id
      inner join tbl_categories as cat on cat.id=mcat.cat_id
      where LENGTH(cat.name) <= 3 and movie.id={$id} AND NOT cat.id IN (33,15,32,59,54,56,31,75)
      group by cat.name");
      $cat = DB::select("select
        case cat.name when 'Artist-Albums' then 'Artist' else cat.name end as name
      from tbl_movies as movie
      inner join tbl_movie_cats as mcat on movie.id=mcat.movie_id
      inner join tbl_categories as cat on cat.id=mcat.cat_id
      where LENGTH(cat.name) > 3 and movie.id={$id} AND NOT cat.id IN (33,15,32,59,54,56,31,75)
      group by cat.name");
      if ($cat[0]->name == "Bollywood-Mashup" || $cat[0]->name == "MASHUPS"){
        return $cat[0]->name;
      }else{
        return $cat[0]->name . "/" . $sub_cat[0]->name;
      }
    }
    public function index(){
      $prefix = DB::getTablePrefix();
      $data['cat'] = DB::select("SELECT id, name FROM {$prefix}categories WHERE LENGTH(name) > 3 and not id in (33,15,32,59,54,56,31,75) ORDER BY name");
      $data['subcat'] = DB::select("SELECT id, name FROM {$prefix}categories WHERE LENGTH(name) <= 3 ORDER BY name");
      $data['artists'] = artist::orderBy("name")->get();
      $data["label"] = DB::select("SELECT * FROM {$prefix}label");
      return view("movie.index",$data);
    }

  public function save(Request $req,$id = null){
    $param = $req->all();
    $isNew = false;
    if ($param['id'] > 0){
      $movies = Movie::find($param['id']);
      $isNew = false;
    }else{
      $movies = new Movie;
      $isNew = true;
    }
    $movies->name = $param["name"];
    $pattArr = [
      "-"=>"",
      " " => "-",
      "(" => "",
      ")" => "",
      "[" => "",
      "]" => "",
      "{" => "",
      "}" => ""
    ];
    foreach ($pattArr as $key => $value) {
      $param["name"] = str_replace($key,$value,$param["name"]);
    }
    // $movies->slug = $param["name"];
    $movies->post_date = date_format( date_create($param['post_date']) ,'Y-m-d');
    $movies->updated_at = date('Y-m-d');
    $movies->label = $param["label"];
    $movies->geo = $param["geo"];
     $movies->share_url = "https://bestsongs.pk/songs/" .  Str::slug($param["name"], '-');
    // $movies->featured = (is_array($param['carousel']) ? (in_array(33,$param['carousel']) ? 1 : 0) : ((int)$param['carousel'] == 33 ? 1 : 0));
    $movies->uid = Auth::id();
    if ($movies->save()){
      Activity::log( ($isNew ? "Create" : "Update") . ' Movie ' . $movies->name);
     if ($isNew == false){
       $tracks = track::where("movie_id", $movies->id)->get();
        foreach($tracks as $track){
         $track->updated_at = date('Y-m-d');
         $track->geo = $param["geo"];
         $track->save();
 //        echo $val->id;
        }
     }

      if (!empty($param["cat"])){
        MovieCat::where("movie_id",$movies->id)->delete();

        $cat = new MovieCat;
        $cat->cat_id = $param["cat"];
        $cat->movie_id = $movies->id;
        $cat->save();
        if ((int)$param["cat"] == 53 || (int)$param["cat"] == 54){
          // nothing todo anything
        }else{
          
          $subcat = new MovieCat;
          $subcat->cat_id = $param["subcat"];
          $subcat->movie_id = $movies->id;
          $subcat->save();  
        }
      }

      // MovieCat::whereIn('cat_id', array(33, 15, 32, 54, 56, 31,59))->where("movie_id",$movies->id)->delete();
      $isTrailerCarousel = 0;
      if (!empty($param['carousel'])){
        foreach($param['carousel'] as $vals){
            if ((int)$vals == 15){
             $isTrailerCarousel = 1;
            }else{
              $carousel = new MovieCat;
              $carousel->cat_id = $vals;
              $carousel->movie_id = $movies->id;
              $carousel->save();
            }
        }  
      }
      // trailler::where("movie_id","=",$movies->id)->update(["feature" => $isTrailerCarousel]);
      // if (!empty($param["artists"])){
        artistMovie::where("movie_id",$movies->id)->delete();
        foreach($param["artists"] as $id){
          $artistMovie = new artistMovie;
          $artistMovie->movie_id = $movies->id;
          $artistMovie->post_id = $id;
          $artistMovie->save();
        }
      // }

      if (!empty($param['trailer_title'])){
        $trailer_isNew = false;
        // init tariler table start
        if (!empty($param['trailer_id'])){
          $trailer = trailler::find($param['trailer_id']);
          $trailer_isNew = false;
        } else {
          $trailer = new trailler;
          $trailer_isNew = true;
        }
        $trailer->name = $param['trailer_title'];
        $pattArr = [
          "-"=>"",
          " " => "-",
          "(" => "",
          ")" => "",
          "[" => "",
          "]" => "",
          "{" => "",
          "}" => ""
        ];
        foreach ($pattArr as $key => $value) {
          $param['trailer_title'] = str_replace($key,$value,$param['trailer_title']);
        }
        $trailer->slug = $param['trailer_title'];
        $trailer->cover_url = $movies->cover_url; 
        $trailer->movie_id = $movies->id;
        $trailer->post_date = $movies->post_date;
        $trailer->feature = $isTrailerCarousel;
        $trailer->trailer_geo = $param['trailer_geo'];
        $trailer->share_url = "https://bestsongs.pk/videos/" .  Str::slug($param["trailer_title"], '-');
        $trailer->uid = Auth::id();
        $trailer->save();
        Activity::log( ($trailer_isNew ? "Create" : "Update") . ' Movie trailer ' . $trailer->name);
        return response()->json(["message"=>"save", "movie_id"=>$movies->id, "trailer_id"=>$trailer->id]);
      }
      return response()->json(["message"=>"save", "movie_id"=>$movies->id]);
    }
  }

  public function delete($id = 0){

  }

  public function getEncoderStatus($trailer_id, $job_id){
    $job_id = $job_id;
    $log = json_decode($this->bitcodinJob->logReader($job_id),true);
    $job_status = (!empty($log['job']['status']) ? strtolower($log['job']['status']) : "");
    $transfer_status =  (!empty($log['transfer'][0]['status']) ? strtolower($log['transfer'][0]['status']) : "");
    if (true){
      if ($job_status == "finished" && $transfer_status == "finished"){
        $trailler = trailler::find($trailer_id);
        $url = str_replace("http://","https://",$log['job']['outputPath']);
        //$url = str_replace("bsongs.storage.googleapis.com","bsbestsongs.global.ssl.fastly.net",$url);
        $trailler->mpd_url = str_replace("http://","https://",$url) . "/" . $job_id . ".mpd";
        $trailler->save();
        
        echo "Encoded successfully";
      } else if($job_status == "error" || $transfer_status == "error"){
        echo "An error while encoding ....";
      } else {
        echo "In Process ....";
      }
    }
  }

  public function uploader(Request $req) {
      $param = $req->all();
      // dd($req->all());
      $movie_id = $param['movie_id'];
      $movie = Movie::find($movie_id);

      // cover_url start
      if ($req->hasFile("cover_url")) {
          $cover_file = $req->file("cover_url");
          Storage::cloud()->put("movie_cover/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
          $cdn = config("app.cdn_url") . "/movie_cover/" .  $cover_file->getClientOriginalName();
          $movie->cover_url = $cdn;
          $movie->uid = Auth::id();
          $movie->save();
          $trailer = trailler::find($movie->id);
          $trailer->updated_at = date("Y-m-d");
          $trailer->cover_url = $cdn;
          $trailer->save();
          Activity::log( 'Update Movie Cover ' . $movie->name);
          return "Uploaded";
      }
      // cover_url end
      // trailer start
      if (!empty($param['trailer_id'])){
        $trailer = trailler::find($param['trailer_id']);
        // cover_url start
        if ($req->hasFile("trailer_cover_url")) {
            $cover_file = $req->file("trailer_cover_url");
            Storage::cloud()->put("trailer_cover/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            $cdn = config("app.cdn_url") . "/trailer_cover/" .  $cover_file->getClientOriginalName();
            $trailer->cover_url = $cdn;
            $trailer->uid = Auth::id();
            $trailer->save();
            Activity::log( 'Update Trailer Cover ' . $trailer->name);
            return "Uploaded";
        }
        // cover_url end
          // mpd_url start
        if (false && $req->hasFile("mpd_url")) {
          $mcat = $this->getCat($movie_id);
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
          $mpd_file = $req->file("mpd_url");
          Storage::cloud()->put("Best-Songs-data" . "/" .  $mcat . "/" . $slug .  "/" . $mpd_file->getClientOriginalName(), File::get($mpd_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
          $file_url =  "https://storage.googleapis.com/bsongs/Best-Songs-data" . "/" .  $mcat . "/" . $slug .  "/" . $mpd_file->getClientOriginalName();
          $mcat1 = explode("/",$mcat);
          $this->bitcodinJob->param = [
            "movie" => $movie->slug,
            "cat" => $mcat1[0],
            "subcat" => $mcat1[1],
            "file" => $mpd_file->getClientOriginalName()
          ];
          $this->bitcodinJob->UID = $trailer->id;
          $this->bitcodinJob->eType = "Trailer";
          $job_id = $this->bitcodinJob->job($file_url, ((strtolower(explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]) . "/" . $slug .  "/" . $mpd_file->getClientOriginalName()));
          Activity::log( 'Movie trailer encoded ' . $trailer->name);
          return response()->json(["message"=>"Uploaded","movie_id"=>$movie_id, "trailer_id"=>$trailer->id, "job_id"=>$job_id]) ;
        }
        // mpd_url end
          // mp4_url start
        if (false && $req->hasFile("mp4_url")) {
          $mp4_file = $req->file("mp4_url");
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
          Storage::cloud()->put("video-data" . "/" .  (strtolower(explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]) . "/" . $slug . "/mp4s/trailer" .  "/"  . $mp4_file->getClientOriginalName(), File::get($mp4_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
          $trailer->mp4_url = config("app.cdn_url") . "/video-data" . "/" .  (strtolower(explode("/",$mcat)[0]) . "/" . explode("/",$mcat)[1]) . "/" . $slug .  "/mp4s/trailer" . "/" . urlencode($mp4_file->getClientOriginalName());
          $trailer->uid = Auth::id();
          $trailer->save();
          Activity::log( 'Update Trailer Mp4 360 ' . $trailer->name);
          return "Uploaded";
        }
        // mp4_url end
      }
      // trailer end
    }
}
