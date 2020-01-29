<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use App\library\encodeClass;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\mediaRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Activity;
use App\Category;
use App\Movie;
use App\Singer;
use App\Artist;
use App\music_director;
use App\genere;

class uploadController extends Controller {

    public function __construct() {
        
    }

    public function upload(Request $req) {
        $movies = Movie::all();
        $music_directors = music_director::all();
        $generes = genere::where("type", "Music")->get();
        $artists = Artist::all();
        //Activity::log('Some activity that you wish to log');
        return view("media.upload", [
            "movies" => $movies,
            "music_directors" => $music_directors,
            "generes" => $generes,
            "artists" => $artists
        ]);
    }

    public function uploadFiles(Request $req) {
        if ($req->hasFile("cover_url")) {
            $cover_file = $req->file("cover_url");
            Storage::cloud()->put("Best-Songs-data/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
            
        }
        if ($req->hasFile("mp3_file")) {
            $mp3_file = $req->file("mp3_file");
            $extension = $mp3_file->getClientOriginalExtension();
            Storage::cloud()->put("Best-Songs-data/" . $mp3_file->getClientOriginalName(), File::get($mp3_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        }

        if ($req->hasFile("mp4_720_file")) {
            $mp4_720_file = $req->file("mp4_720_file");
        }

        if ($req->hasFile("mp4_360_file")) {
            $mp4_360_file = $req->file("mp4_360_file");
        }
    }

    public function addNew(mediaRequest $req) {
        $param = $req->all();
        $this->uploadFiles($req);
        $title = $param["title"];
        $movie = $param["movie"];
        $music_director = $param["music_director"];
        $genere = $param["genere"];
        if (is_array($param["singer_list"])) {
            
        }

        if (is_array($param["artist_list"])) {
            
        }
    }

}
