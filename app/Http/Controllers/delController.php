<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\event;
use App\track;
use App\albumtracks;
use App\Movie;
use App\trailler;
use App\video;
use DB;


class delController extends Controller
{
    //Delete Track
    public function delTracks(Request $req){


        $param = $req->all();
        if (!empty($param["movie"]) && !empty($param["track"])){
            albumtracks::where(["album_id"=>$param["movie"], "track_id"=>$param["track"]])->delete();
            $allTracks = track::where(["movie_id"=>$param["movie"],"id"=>$param["track"]])->get();
            foreach($allTracks as $track){
                DB::table("playlisttracks")->where("track_id", $track->id)->delete();
                DB::table("users_liked_tracks")->where("track_id", $track->id)->delete();
            }
            track::where("id", $param["track"])->delete();
        }elseif(!empty($param["movie"])){
            albumtracks::where("album_id",$param["movie"])->delete();
            $allTracks = track::where("movie_id",$param["movie"])->get();
            foreach($allTracks as $track){
                DB::table("playlisttracks")->where("track_id", $track->id)->delete();
                DB::table("users_liked_tracks")->where("track_id", $track->id)->delete();
            }
            track::where("movie_id", $param["movie"])->delete();
        }


    }

    // Delete MOvie
    public function delMovie(Request $req, $id){


        $m = Movie::find($id);
        if(count($m)){ 
            if($m->post_id)
            {
                $evt = new event;
                $evt->object_id = $m->post_id;
                $evt->object_type = 'album';
                $evt->flag = 'D';
                $evt->save();
            }
            else
            {
                return response()->json(["message"=>"POST ID 0 from Wordpress"]);
            }

        }

        albumtracks::where("album_id",$id)->delete();

        $allTracks = track::where("movie_id",$id)->get();
        foreach($allTracks as $track){
            DB::table("playlisttracks")->where("track_id", $track->id)->delete();
            DB::table("users_liked_tracks")->where("track_id", $track->id)->delete();
            DB::table("feature_video_tracks")->where("track_id", $track->id)->delete();
            DB::table("albumtracks")->where("track_id", $track->id)->delete();
            //albumtracks
        }

        track::where("movie_id",$id)->delete();
        Movie::where("id", $id)->delete();

        return response()->json(["message"=>"deleted"]);

    }
    
    // Delete Trailer
    public function delTrailer(Request $req){
    
        $param = $req->all();

        if (!empty($param["movie"]) && !empty($param["trailer"])){
            

            $trailer = trailler::find($param["trailer"]);
            if($trailer)
            {
                $evt = new event;
                $evt->object_id = $trailer->post_id;
                $evt->object_type = 'trailer';
                $evt->flag = 'D';
                $evt->save();

                exit();

                trailler::where("id", $param["trailer"])->delete();
            }
            else{
                echo "Record NOt Found";
            }

            
        }
        
    
    }

    //Delete Video
    public function delVideo(Request $req){
    
        $param = $req->all();

        if (!empty($param["video"])){
            

            $movie = video::find($param["video"]);

            if($movie)
            {
                $evt = new event;
                $evt->object_id = $movie->post_id;
                $evt->object_type = 'video';
                $evt->flag = 'D';
                $evt->save();
                exit();
                video::where("id", $param["video"])->delete();
            }
            else{
                echo "Record NOt Found";
            }

            
        }
        
    
    }
}
