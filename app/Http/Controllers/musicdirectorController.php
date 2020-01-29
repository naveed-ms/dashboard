<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\musicdirectorRequest;
use App\Http\Controllers\Controller;
use App\music_director;

class musicdirectorController extends Controller
{
  public function index(){

    return view("musicdirector.index");
  }

  public function save(musicdirectorRequest $req){
    $param = $req->all();
    if (!empty($param['id'])){
      $md = music_director::find($param =['id']);
    }else {
      $md = new music_director ; # code...
    }
      //"name"=>$param["name"];
    $md->name = $param["name"];
    if ($md->save()){
      echo "Saved";
    }
  }
}
