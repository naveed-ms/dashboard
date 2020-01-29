<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Session;
use App\library\encodeClass;
use App\Http\Requests;
use App\Movie;
use App\video;
use App\artist;
use App\videoArtist;
use DB;
use Auth;
use Activity;

class videoController extends Controller
{
    private $bitcodinJob;
    
    public function __construct(){
      $this->bitcodinJob = new encodeClass();
    }
    
    public function index()
    {

       $prefix = DB::getTablePrefix();  //ashok changes

       $data['artists'] = artist::orderBy("name")->get();

       //ashok changes
       
       $data["label"] = DB::select("SELECT * FROM {$prefix}label"); 

       //ashok changes

       return view("video.index" ,$data);
    }

    public function saveVideo(Request $req,$id = null){
      $param = $req->all();
      $isNew = false;
      if ($param['id'] > 0){
        $video = video::find($param['id']);
        $isNew = false;

      }else{
        $video = new video;
        $isNew = true;
      }
      
      $video->uid = Auth::Id();
      $video->name = $param["name"];
      $video->post_date = $param["post_date"];
      $video->type = $param["type"]; // temp
      $video->feature = $param["featured"];

      $video->geo = $param["featured_geo"];  // ashok changes

      $video->save();
      Activity::log( ($isNew ? "Create" : "Update") . ' Video ' . $video->name . " in " . $video->type);

      if (!empty($param["artists"])){
        // videoArtist
        videoArtist::where("video_id",$video->id)->delete();
        foreach($param["artists"] as $art){
          $videoArt = new videoArtist;
          $videoArt->art_id = $art;
          $videoArt->video_id = $video->id;
          $videoArt->save();
        }
      }
      return response()->json(["message"=>"save", "video_id"=>$video->id]);
    }

    public function getEncoderStatus($video_id, $job_id){
      $job_id = $job_id;
      $log = json_decode($this->bitcodinJob->logReader($job_id),true);
      $job_status = (!empty($log['job']['status']) ? strtolower($log['job']['status']) : "");
      $transfer_status =  (!empty($log['transfer'][0]['status']) ? strtolower($log['transfer'][0]['status']) : "");
      if (true){
        if ($job_status == "finished" && $transfer_status == "finished"){
          $video = video::find($video_id);
          $url = str_replace("http://","https://",$log['job']['outputPath']);
          //$url = str_replace("bsongs.storage.googleapis.com","bsbestsongs.global.ssl.fastly.net",$url);
          $video->mpd_url = $url . "/" . $job_id . ".mpd";
          $video->uid = Auth::Id();
          $video->save();
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
        $movie_id = (!empty($param['video_id']) ? $param['video_id'] : $param['movie_id'] );
        $type = $param["type"];
        // cover_url start
        if ($req->hasFile("cover_url")) {
            $movie = video::find($movie_id);
            $cover_file = $req->file("cover_url");
            Storage::cloud()->put($type . "_cover/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            $cdn = config("app.cdn_url") . "/" . $type . "_cover/" .  $cover_file->getClientOriginalName();
            $movie->cover_url = $cdn;
            $movie->uid = Auth::id();
            $movie->save();
            
            return "Uploaded";
        }
        // cover_url end
            // mpd_url start
          if ($req->hasFile("mpd_url")) {
            $movie = video::find($movie_id);
            $pattArr = [ "-"=>"", " " => "-", "(" => "", ")" => "", "[" => "", "]" => "", "{" => "",  "}" => "" ];
            $slug = "";
            foreach ($pattArr as $key => $value) {
              $slug = str_replace($key,$value,$slug);
            }
            $mpd_file = $req->file("mpd_url");
            Storage::cloud()->put("Best-Songs-data" . "/" .  ($type == "bollywood_gupshup" ? "GupShup" : $type) .  "/" . $mpd_file->getClientOriginalName(), File::get($mpd_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            $file_url =  "https://storage.googleapis.com/bsongs/Best-Songs-data" . "/" .  ($type == "bollywood_gupshup" ? "GupShup" : $type) .  "/" . $mpd_file->getClientOriginalName();
            // $this->bitcodinJob->param = [
            //   "movie" => $movie->slug,
            //   "cat" => $type,
            //   "subcat" => "video_data",
            //   "file" => $mpd_file->getClientOriginalName()
            // ];
            $this->bitcodinJob->UID = $movie_id;
            $this->bitcodinJob->eType = "Video";
            $job_id = $this->bitcodinJob->job($file_url, ($type == "bollywood_gupshup" ? "gupshup" : $type) . "/" . $mpd_file->getClientOriginalName());
            Activity::log('Update Video Cover ' . $movie->name . " in " . $type);
            return response()->json(["message"=>"Uploaded","movie_id"=>$movie_id, "job_id"=>$job_id]) ;
          }
          // mpd_url end
            // mp4_url start
          if ($req->hasFile("mp4_url")) {
            $movie = video::find($movie_id);
            $mp4_file = $req->file("mp4_url");
            $pattArr = [ "-"=>"", " " => "-", "(" => "", ")" => "", "[" => "", "]" => "", "{" => "",  "}" => "" ];
            $slug = "";
            foreach ($pattArr as $key => $value) {
              $slug = str_replace($key,$value,$movie->name);
            }
            Storage::cloud()->put("video-data" . "/" .  ($type == "bollywood_gupshup" ? "gupshup" : $type)  . "/mp4s" .  "/"  . $mp4_file->getClientOriginalName(), File::get($mp4_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            $movie->mp4_url = config("app.cdn_url") . "/video-data" . "/" .  ($type == "bollywood_gupshup" ? "gupshup" : $type) .  "/mp4s" . "/" . urlencode($mp4_file->getClientOriginalName());
            $movie->uid = Auth::id();
            $movie->save();
            Activity::log('Update Video Mp4 360 ' . $movie->name . " in " . $movie->type);
            return "Uploaded";
          }
          // mp4_url end
      }

}
