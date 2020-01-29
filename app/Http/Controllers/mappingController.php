<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Movie;
use App\track;
use App\artist;
use App\artistMovie;
use App\MovieCat;
use App\albumMap;
use App\albumtracks;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Auth;
use Activity;

class mappingController extends Controller
{
    public function __contruct(){

    }

    public function index(){
        $movies = Movie::orderBy("name")->get();
        $artists = artist::orderBy("name")->get();
        return view("Mapping/index",[
            "movies" => $movies,
            "artists" => $artists
        ]);
    }

    public function uploader(Request $req){
        $param = $req->all();
        $id = $param["id"];
        if ($req->hasFile("cover_url")){
            $cover_file = $req->file("cover_url");
            Storage::cloud()->put("movie_cover/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            // $album = albumMap::find($id);
            // $album->cover_url = config("app.cdn_url") . "/movie_cover/" .  $cover_file->getClientOriginalName();
            $movie = Movie::find($id);
            $movie->cover_url = config("app.cdn_url") . "/movie_cover/" .  $cover_file->getClientOriginalName();
            $movie->save();
            Activity::log( 'Update playlist Cover ' . $movie->name);
            return "Uploaded";
        }
    }


    public function save(Request $req, $id = null){
        $param = $req->all();
        $isNew = true;
        if (!empty($id)){
            $album = albumMap::find($id);
            $param["movie_id"] = $album->movie_id;
            $isNew = false;
        }else{
            $album = new albumMap;
        }
        $album->name = $param["name"];
        $album->ref = implode(",", $param["ref"]);
        $album->artists = implode(",", $param["artists"]);
        $movieId = $this->addAlbum($param);
        $album->movie_id = $movieId;
        if ($album->save()){
            Activity::log(  ($isNew ? "Create" : "Update") . ' playlist ' . $album->name);
            return response()->json(["message"=>"save", "id"=>$movieId]);
        }else{
            return response("An error occourd", 500);
        }
    }


    private function addAlbum($param){
        $home = $param["home"];
        if (!empty($param["movie_id"])){ $movies = Movie::find($param["movie_id"]); }else{ $movies = new Movie; }
        $movies->name = $param["name"];
        $pattArr = ["-"=>""," " => "-","(" => "",")" => "","[" => "","]" => "","{" => "","}" => ""];
        foreach ($pattArr as $key => $value) { $param["name"] = str_replace($key,$value,$param["name"]); }
        // $movies->slug = $param["name"];
        $movies->post_date = date('Y-m-d');
        $movies->updated_at = date('Y-m-d');
        $movies->feature  = (int)$home > 0 ? 1 : 0;  
        $movies->total_tracks = count($param["ref"]);

         //ashok changes
        $movies->geo = $param["geo"];
        $movies->share_url = "https://bestsongs.pk/songs/" .  Str::slug($param["name"], '-');

        $movies->uid = Auth::id();
        if ($movies->save()){
            MovieCat::where("movie_id",$movies->id)->delete();

            // category
            // $cat = new MovieCat;
            // $cat->cat_id = "";
            // $cat->movie_id = $movies->id;
            // $cat->save();

            // alphabets
            // $cat = new MovieCat;
            // $cat->cat_id = "";
            // $cat->movie_id = $movies->id;
            // $cat->save();

            // homecategory
            if ((int)$home > 0){
                $cat = new MovieCat;
                $cat->cat_id = ((int)$home == 2 ? 32 : ((int)$home == 3 ? 67 : 57));
                $cat->movie_id = $movies->id;
                $cat->save();
            }

            // if ((int)$home == 2){
            //     $cat = new MovieCat;
            //     $cat->cat_id = 32;
            //     $cat->movie_id = $movies->id;
            //     $cat->save();
            // }

            if (!empty($param["artists"])){
                artistMovie::where("movie_id",$movies->id)->delete();
                foreach($param["artists"] as $aVal){
                    $this->addArtist($aVal,$movies->id);
                }   
            }


            albumtracks::where("album_id",$movies->id)->delete();
            foreach($param["ref"] as $val){
                $this->addSongs($val,$movies->id,$param["geo"]);    //ashok changes
            }
        }

        return $movies->id;
    }

    private function addSongs($id,$mid,$geo){  //ashok changes
        $new = new albumtracks;
        $new->album_id = $mid;
        $new->track_id = $id;

        //ashok changes
        $track = track::find($id);
        $track->geo = $geo;
         //ashok changes

        $new->save();
        return null;
    }

    private function addArtist($id, $mid){
        $a = new artistMovie;
        $a->movie_id = $mid;
        $a->post_id = $id;
        $a->save();
    }
}
