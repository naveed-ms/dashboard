<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use App\trailler;
use App\Movie;
use Event;
use App\Events\userActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class trailerController extends Controller
{

    private $prefix = "";
    private $cover_url = "";
    private $mpd_url = "";
    private $mp4_url = "";
    public function __construct(){
        $this->prefix = DB::getTablePrefix();
    }

    public function index(){
      $movies = Movie::orderBy("name")->get();
      return view("trailer.index",[
        "movies"=>$movies
      ]);
      // Events::fire(new userActivity());
    }

    public function save(Request $req){
      $param = $req->all();
      // dd($param);
      // $trailler = trailler::findOrFail($param["id"]);
      $trailler = new trailler;
      $movieName  = Movie::find($param["movie_id"])->name;
      $trailler->movie_id = $param["movie_id"];
      $trailler->name = $movieName . " Trailer";
      $trailler->cover_url = config("app.cdn_url") . "/" . Session::get('cover_url');
      if ($trailler->save() == true){
        echo "Saved";
      }
    }
    public function uploader(Request $req,$movie_id = null) {
        if ($req->hasFile("cover_url")) {
            $cover_file = $req->file("cover_url");
            Session::put("cover_url", "Best-Songs-data/trailer/" . $cover_file->getClientOriginalName());
            Storage::cloud()->put("Best-Songs-data/trailer/" . $cover_file->getClientOriginalName(), File::get($cover_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        }
    }
}
