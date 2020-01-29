<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Movie;
use App\MovieCat;
use DB;
use App\Category;

class CarouselController extends Controller
{
    public function __construct(){

    }

    public function index(){
      $movies = Movie::orderBy('post_date', 'desc')->take(500)->get();
      $categories = Category::whereIn("id",[15,32,33])->get();
      return view('Carousel.index',[
        "album" => $movies,
        "categories" =>$categories
      ]);
    }

    public function update(Request $req){
      $param = $req->all();
      MovieCat::whereIn("cat_id",[15,32,33])->delete();
      foreach($param['data'] as $val){
        $MovieCat = new MovieCat;
        $MovieCat->movie_id = $val['a_id'];
        $MovieCat->cat_id = $val['c_id'];
        if ($MovieCat->save()){
          $movie = Movie::find($val['a_id']);
          $movie->m_order = $val['m_order'];
          if ($movie->save()){
            echo json_encode(["message"=>"Updated"]);
          }
        }
      }
    }
}
