<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Http\Requests;
use App\Http\Requests\genereRequest;
use App\Http\Controllers\Controller;
use App\genere;


class genereController extends Controller
{
  public function index(){

    return view("genere.index");
  }

  public function save(genereRequest $req){
    $param = $req->all();
    if (!empty($param['id'])) {
      $generes = genere::find($param['id']);
    }else {
      $generes = new genere;
    }
    $generes->name = $param["name"];
    $generes->type = $param["type"];
    if ($generes->save()){
      echo "Saved";
    }
  }

}
