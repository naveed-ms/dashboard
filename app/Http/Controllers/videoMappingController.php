<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\featureVideo;
use App\featureVideoTrack;
use App\Movie;
use App\artist;
use App\track;
use DB;
use Auth;
use Activity;

class videoMappingController extends Controller
{
    public function index(){
        $data = [
            "movies" => Movie::all()->sortBy('name'),
            "artists" => artist::all() 
        ];
        return view("videoMap.index",$data);
    }


    public function uploader(Request $req){
        $param = $req->all();
        $id = $param["id"];
        if ($req->hasFile("cover_url")){
            $cover_file = $req->file("cover_url");
            Storage::cloud()->put("topVideo_cover/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            $movie = featureVideo::find($id);
            $movie->cover_url = config("app.cdn_url") . "/topVideo_cover/" .  $cover_file->getClientOriginalName();
            $movie->save();
            Activity::log( 'Update Top Video Cover ' . $movie->name);
            return "Uploaded";
        }
    }

    public function save(Request $req, $id=null){
        $param = $req->all();
        $isNew = true;
        if (!empty($id)){
            $videoMap = featureVideo::find($id);
            $isNew = false;
        }else{
            $videoMap = new featureVideo;
        }
        $videoMap->name = $param['name'];
        $videoMap->featured = ((int)$param["featured"] == 1 ? 1 : ((int)$param["featured"] == 2 ? 2 : 0));
        $videoMap->updated_at = date("Y-m-d h:i:s");
        $videoMap->geo = $param['geo'];  //ashok changes
         $videoMap->share_url = "https://bestsongs.pk/videos/" .  Str::slug($param["name"], '-');
        $save = $videoMap->save();
        featureVideoTrack::where("feature_video_id", $videoMap->id)->delete();
        foreach($param['ref'] as $key => $val){
            $videoMapTracks = new featureVideoTrack;
            $videoMapTracks->feature_video_id = $videoMap->id;
            $videoMapTracks->track_id = $val;
            $track = track::find($val);
            $track->geo = $param['geo'];
            $videoMapTracks->save();
        }
        if ($save){
            Activity::log( ($isNew ? "Create" : "Update") . ' Top Video ' . $videoMap->name);
            return response()->json(["message"=>"save", "id"=>$videoMap->id]);
        }else{
            return response("An error occurd", 500);
        }
    }



}
