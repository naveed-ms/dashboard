<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\artistRequest;

use App\artist;
use App\Singer;
use App\track;

use App\artist_track;
use App\singer_track;
use App\music_director;
use App\genere;

class trackController extends Controller
{
    private $prefix = "";
    public function __construct(){
      $this->prefix = DB::getTablePrefix();
    }


    public function index(){
      $artists = artist::all();
      $singers = Singer::all();
      $generes = genere::where ("type","Music")->get();
      $music_directors = music_director::all();
      return view('unmapartist.index',[
        "artists"=>$artists,
        "singers"=>$singers,
        "music_directors"=>$music_directors,
        "generes"=>$generes
      ]);
    }


    public function save(Request $req){
      $param = $req->all();
      $err = "save";
      $track = track::find($param['id']);
      $track->director_id = $param['music_director'];
      $track->genere = $param['genere'];
      $track->save();
      artist_track::where('track_id',$param['id'])->delete();
      singer_track::where('track_id',$param['id'])->delete();

      for ($i=0; $i < count($param["artist_id"]); $i++){
        $artist_track = new artist_track;
        $artist_track->track_id =$param["id"];
        $artist_track->artist_id =$param["artist_id"][$i];
        if ($artist_track->save() == true){
          $err = "Saved";
        }else{
          $err = "";
          $param["artist_id"] = array();
        }
      }

      for ($i=0; $i < count($param["singer_id"]); $i++){
        $singer_track = new singer_track;
        $singer_track->track_id =$param["id"];
        $singer_track->singer_id =$param["singer_id"][$i];
        if ($singer_track->save() == true){
          $err = "Saved";
        }else{
          $err = "";
          $param["singer_id"] = array();
        }
      }

      return $err;
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
        $artists = artist::all();
      }
      return response()->json($artists);
    }
// For Singer
    public function getSingerData($id = null){
      $singer = [];
      if (!empty($id)){
        $singer = singer::find($id);
      }else{
        $singer = singer::all();
      }
      return response()->json($singer);
    }

    public function artist(){
      return view("artist.index");
    }

    public function saveArtist(artistRequest $req){
      $param = $req->all();
      $artist = artist::firstOrNew([
        'id'=>$param["id"]
      ]);
      $artist->name=$param["name"];
      $artist->gender=$param["gender"];
      if ($artist->save()){
        echo "Saved";
      }
    }



    public function editTrack(){
      $artists = artist::all();
      $singers = Singer::all();
      $generes = genere::where ("type","Music")->get();
      $music_directors = music_director::all();
      return view('unmapartist.index',[
        "artists"=>$artists,
        "singers"=>$singers,
        "music_directors"=>$music_directors,
        "generes"=>$generes
      ]);
    }

    public function saveTrack(){

    }




}
