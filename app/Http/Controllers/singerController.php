<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\singerRequest;
use App\Http\Controllers\Controller;
use App\Singer;
class singerController extends Controller
{
  public function index(){

    return view("singer.index");
  }

  public function save(singerRequest $req){
    $param = $req->all();
    if (!empty($param['id'])){
      $singers = Singer::find($param['id']);
    }else{
      $singers = new Singer;
    }
    $singers->name = $param["name"];
    //$singers->gender = $param["gender"];
    if ($singers->save()){
      echo "Saved";
    }
  }
}
