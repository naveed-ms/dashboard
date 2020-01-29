<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\artist;
use App\Singer;
use App\track;
use App\trailler;
use App\Movie;
use App\video;
use App\artistMovie;
use App\artist_track;
use App\singer_track;
use App\music_director;
use App\genere;
use App\videoArtist;
use DB;
use App\featureVideo;
use App\featureVideoTrack;
use App\albumMap;
use App\event;
use App\evtJob;


class getDataController extends Controller
{
  private $prefix = null;
  public function __construct(){
    $this->prefix = DB::getTablePrefix();
  }

  public function getMovies($id = null){
    $movies = [];
    if (!empty($id)){
      $movies = Movie::orderBy('name')->find($id);
      $movies['artists'] = DB::select("SELECT artist.id, artist.name FROM {$this->prefix}artist_movies As mart INNER JOIN {$this->prefix}artists As artist On mart.post_id=artist.id WHERE mart.movie_id=:id",["id"=>$id]);
      $movies['cat'] = DB::select("SELECT 
      cat.id, cat.name 
      FROM {$this->prefix}categories as cat 
      INNER JOIN {$this->prefix}movie_cats as mcat on mcat.cat_id=cat.id 
      WHERE LENGTH(cat.name) > 3 AND mcat.movie_id = :id 
      ORDER BY case when cat.id in (33,15,32,59,54,56,31) then 2 else 1 end, name",
      ["id" => $id]);
      $movies['subcat'] = DB::select("SELECT cat.id, cat.name FROM {$this->prefix}categories as cat INNER JOIN {$this->prefix}movie_cats as mcat on mcat.cat_id=cat.id WHERE LENGTH(cat.name) <= 3 AND mcat.movie_id = :id ORDER BY name",["id" => $id]);
    }else{
      $movies = Movie::orderBy('name')->get();
    }
    return response()->json($movies);
  }
  
  public function getAlbum($id = null){
    $movies = [];
    if (!empty($id)){
      $movies = Movie::orderBy('name')->find($id);
      $movies['artists'] = DB::select("SELECT artist.id, artist.name FROM {$this->prefix}artist_movies As mart INNER JOIN {$this->prefix}artists As artist On mart.post_id=artist.id WHERE mart.movie_id=:id",["id"=>$id]);
      $movies['cat'] = DB::select("SELECT cat.id, cat.name FROM {$this->prefix}categories as cat INNER JOIN {$this->prefix}movie_cats as mcat on mcat.cat_id=cat.id WHERE LENGTH(cat.name) > 3 AND mcat.movie_id = :id ORDER BY name",["id" => $id]);
      $movies['subcat'] = DB::select("SELECT cat.id, cat.name FROM {$this->prefix}categories as cat INNER JOIN {$this->prefix}movie_cats as mcat on mcat.cat_id=cat.id WHERE LENGTH(cat.name) <= 3 AND mcat.movie_id = :id ORDER BY name",["id" => $id]);
    }else{
      $movies = DB::select("SELECT movie.*, movie.id as value  FROM tbl_movie_cats  as mcat INNER JOIN tbl_movies as movie ON mcat.movie_id=movie.id INNER JOIN tbl_categories as cat ON cat.id = mcat.cat_id WHERE cat.id = :cat_id",[
		"cat_id" => 7
	  ]);
    }
    return response()->json($movies);
  }
  
  public function getVideos($id = null){
    $video = [];
    if (!empty($id)){
      $video = video::orderBy('name')->find($id);
      $video['artists'] = videoArtist::where("video_id",$id)->get();
      }
        else{
      $video = video::orderBy('updated_at','desc')->get();
    }
    return response()->json($video);
  }

  public function getTrackByMovie($movie_id){
    $tracks = [];
    $tracks = track::where("movie_id",$movie_id)->get();
    return response()->json($tracks);
  }

  public function getTrackData($id = null){
    $query = "";
    $param = [];
    $tracks = [];
    $data = [];
    $artist_query = "
    SELECT
    artist.id, artist.name
    FROM {$this->prefix}artist_tracks As artist_track
    INNER JOIN {$this->prefix}artists As artist On artist.id = artist_track.artist_id
    WHERE artist_track.track_id=:id
    ";
    $singer_query = "
    SELECT
    singer.id, singer.name
    FROM {$this->prefix}singer_tracks As singer_track
    INNER JOIN {$this->prefix}singers As singer On singer.id = singer_track.singer_id
    WHERE singer_track.track_id=:id
    ";
    if (!empty($id)){
      $param["id"] = $id;
      $artists = DB::select($artist_query,$param);
      $singers = DB::select($singer_query,$param);
      $tracks = track::find($id)->toArray();
      $data["track"] = $tracks;
      $data["artists"] = $artists;
      $data["singers"] = $singers;
    }else{
      $query = "
      SELECT
      track.id,movie.name As album, track.name As track, count(artist.name) As artist,track.genere
      FROM {$this->prefix}tracks As track
      INNER JOIN {$this->prefix}movies As movie On track.movie_id=movie.id
      Left Outer Join {$this->prefix}artist_tracks As artist_track On track.id=artist_track.track_id
      Left Outer Join {$this->prefix}artists As artist On artist.id=artist_track.artist_id
      Group By movie.name,track.name
      Order By movie.name, track.name, id DESC
      LIMIT 0,500
      ";
      $data = DB::select($query);
    }
    return response()->json($data);
  }


  public function getArtistData($id = null){
    $artists = [];
    if (!empty($id)){
      $artists = artist::find($id);
    }else{
      $artists = artist::orderBy("name")->get();
    }
    return response()->json($artists);
  }
// For Singer
  public function getSingerData($id = null){
    $singer = [];
    if (!empty($id)){
      $singer = singer::find($id);
    }else{
      $singer = singer::orderBy("name")->get();
    }
    return response()->json($singer);
  }

  public function getMusicDirectorData($id = null){
    $md = [];
    if (!empty($id)){
      $md = music_director::find($id);
    }else{
      $md = music_director::all();
    }
    return response()->json($md);
  }



  public function getTraillerData($movie_id=null, $id = null){
    $prefix = DB::getTablePrefix();
    $param = [];
    $query = "";
    if (!empty($id)){
      $traillers = trailler::find($id);
    }else if (!empty($movie_id)){
      $traillers = trailler::where("movie_id",$movie_id)->get();
    }else{
      $query = "select trailler.id, trailler.name as title, movie.name as album,trailler.trailer_geo as trailer_geo from {$prefix}traillers as trailler left outer join {$prefix}movies as movie on trailler.movie_id=movie.id";
      $traillers = DB::select($query,$param);
    }
    return response()->json($traillers);
  }

  public function getGenereData($id = null){
    $generes = [];
    if (!empty($id)){
      $generes = genere::find($id);
    }else{
      $generes = genere::all();
    }
    return response()->json($generes);
  }

  public function getCarouselData(){
    $data = DB::select("SELECT
                        movie.id as a_id, movie.name as title, cat.id as c_id, cat.name as carousel, movie.m_order
                        FROM tbl_movies as movie
                        INNER JOIN tbl_movie_cats as mcat ON movie.id=mcat.movie_id
                        INNER JOIN tbl_categories as cat ON mcat.cat_id=cat.id
                        WHERE cat.id in (15,32,33) ORDER BY cat.name");
    return response()->json($data);

  }

  public function getCategoryData(){
    $getCat = DB::select("SELECT id, name, id AS value FROM {$this->prefix}categories WHERE LENGTH(name) > 3 ORDER BY name");
    return response()->json($getCat);
  }

  public function getSubCategoryData(){
    $getCat = DB::select("SELECT id, name, id AS value FROM {$this->prefix}categories WHERE LENGTH(name) <= 3 ORDER BY name");
    return response()->json($getCat);
  }

  public function getPlaylist(Request $req, $id = null){
    $param = $req->all();
    if (!empty($param["tracks"])){
      $data = DB::select("SELECT id, name FROM {$this->prefix}tracks WHERE id IN ({$param["tracks"]})");
    }else if(!empty($param["artists"])){
      $data = DB::select("SELECT id, name FROM {$this->prefix}artists WHERE id IN ({$param["artists"]})");
    }else{
      if (!empty($id)){
        $data = albumMap::find($id);
        $m = Movie::find($data->movie_id);
        $cat = DB::select("SELECT cat.id, cat.name FROM {$this->prefix}movie_cats as mcat inner join {$this->prefix}categories as cat on mcat.cat_id=cat.id where mcat.movie_id=:id",['id'=>$data->movie_id]);

        $data["geo"] = $m->geo; //ashok changes
        
        $data['cat'] = ($cat ? $cat[0] : null);
        $data["tracks"] = DB::select("SELECT t.id, t.name FROM {$this->prefix}tracks as t INNER JOIN {$this->prefix}albumtracks as at ON at.track_id=t.id WHERE at.album_id={$data->movie_id}");
        $data["arists"] = DB::select("SELECT a.id, a.name FROM {$this->prefix}artists as a INNER JOIN {$this->prefix}artist_movies as am ON am.post_id=a.id WHERE am.movie_id = {$data->movie_id}");
      }else{
        $data = albumMap::orderBy("updated_at", "DESC")->get();
      }
    }
    
    return response()->json($data);
  }

  public function getVirtualMaps(Request $req, $id = null){
    $param = $req->all();
    if (!empty($param["tracks"])){
      $data = DB::select("SELECT id, name FROM {$this->prefix}tracks WHERE id IN ({$param["tracks"]})");
    }else if(!empty($param["artists"])){
      $data = DB::select("SELECT id, name FROM {$this->prefix}artists WHERE id IN ({$param["artists"]})");
    }else{
      if (!empty($id)){
        $data = featureVideo::find($id);
        $data["tracks"] = featureVideo::find($id)->track_id;
      }else{
        $data = featureVideo::orderBy("updated_at","DESE")->get();
      }
    }
    
    return response()->json($data);
  }

  public function create_event(Request $req)
  {
    $param = $req->all();
    
    if($param["object_type"] == "album"){
      $m = track::where(["movie_id" => $param["object_id"]])->get();
      $albumTrack_sql = "SELECT track.id, track.name, ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url FROM tbl_albumtracks as albumtrack inner join tbl_tracks as track on albumtrack.track_id = track.id WHERE albumtrack.album_id=:id";
      if(count($m) > 0){
        $evt = new event;
        $evt->object_id = $param["object_id"];
        $evt->object_type = $param["object_type"];
        $evt->save();

        //event for evtJob
        $evtjob = new evtJob;
        $evtjob->object_id = $param["object_id"];
        $evtjob->object_type = $param["object_type"];
        $evtjob->save(); 

      }else{
        $m = db::select($albumTrack_sql,["id" => $param["object_id"]]);
        if(count($m) > 0){
          $evt = new event;
          $evt->object_id = $param["object_id"];
          $evt->object_type = $param["object_type"];
          $evt->save();

          //event for evtJob
          $evtjob = new evtJob;
          $evtjob->object_id = $param["object_id"];
          $evtjob->object_type = $param["object_type"];
          $evtjob->save(); 

        }
      }
    }else{
        
        $evt = new event;
        $evt->object_id = $param["object_id"];
        $evt->object_type = $param["object_type"];
        $evt->save(); 

        //event for evtJob
        $evtjob = new evtJob;
        $evtjob->object_id = $param["object_id"];
        $evtjob->object_type = $param["object_type"];
        $evtjob->save();     
    }
  }

}
