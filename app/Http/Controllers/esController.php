<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Elasticsearch;

class esController extends Controller
{
    public function _constructor(){

    }

    public function index(){

    }

    public function save (Request $req){
      $param = $req->all();
      $param['es_id']  = "AVdGwDNklAEyqeRqtT4f";
      if (!empty($param['es_id'])){
        // find object
         $es_param = ['id' => $param['es_id'], 'index' => 'blogging', 'type' => 'trackTypeTest'];
         $data = Elasticsearch::get($es_param);
        //  $es_param = ['id' => $param['es_id'],'index' => 'blogging', 'type' => 'trackTypeTest', 'body' => [
        //    "title" => "Hello123"
        //    ]];
        //  $data = Elasticsearch::index($es_param);
         dd($data);
      }else{
        // create new object
        //  $es_param = ['index' => 'blogging', 'type' => 'trackTypeTest', 'body' => [
        //    "title" => "Hello"
        //    ]];
        //  $data = Elasticsearch::index($es_param);
        //  dd($data['_id']);
      }
      // update object

    }

}
