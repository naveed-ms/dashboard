<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Http\Response;

use App\library\wordpressClass;
use App\Category;
use App\Movie;
use App\track;
use App\MovieCat;
use App\wpmigrate;

class wpDataController extends Controller
{
    private $wpClient;
    private $wpmigrate;
    public function __construct(){
      $this->wpClient = new wordpressClass();
      $this->wpmigrate = new wpmigrate;
    }
    public function index(){
      $lastMigrateId = wpmigrate::orderBy('post_id', 'desc')->first();
      $posts = json_decode($this->cURL("https://bestsongs.pk/api/rpc/mig.php?ID={$lastMigrateId->post_id}&number=1&offset=10"),true);
      $lastId = 0;
      foreach($posts as $post){
        $this->savePost($post);
        $lastId = $post["ID"];
      }
      $this->wpmigrate->post_id = $lastId;
      $this->wpmigrate->update_date = date('Y-m-d');
      $this->wpmigrate->save();
    }

    private function savePost(array $param){
      $movie_id = $this->saveMovie($param);
      $this->setCategory($param,$movie_id);
      $this->saveTracks($param,$movie_id);
    }

    private function saveMovie(array $param){
      $movies = Movie::firstOrNew(['name'=>$param['title']]);
      $movies->name = $param['title'];
      $movies->cover_url = $param["cover_url"];
      $movies->post_date = date("Y-m-d",strtotime($param['date']));
      $movies->modified_date = date("Y-m-d",strtotime($param['modified_date']));
      $movies->save();
      return $movies->id;
    }

    private function setCategory(array $param,$movie_id){
      if (!empty($param["terms"])){
        foreach($param["terms"] as $term){
            $cats = Category::firstOrNew(['name'=>$term['title']]);
            $cats->name = $term['title'];
            $cats->save();
            $MovieCat = MovieCat::firstOrNew(['movie_id'=>$movie_id,'cat_id'=>$cats->id]);
            $MovieCat->movie_id = $movie_id;
            $MovieCat->cat_id = $cats->id;
            $MovieCat->save();
        }
      }
    }

    private function saveTracks(array $param,$movie_id){
      if (!empty($param["songs"])){
        foreach($param["songs"] as $track){
          $tracks = track::firstOrNew(['name'=>$track['title']]);
          $tracks->name = $track['title'];
          $tracks->movie_id = $movie_id;
          $tracks->audio_url =  $track['audio_url'];
          $tracks->mpd_url =  $track['mpd_url'];
          $tracks->mp4_url =  $track['mp4_url'];
          $tracks->save();
        }
      }
    }


    private function cURL($url, $post = false, $header = false){
      $ch = curl_init($url);
      if ($post !== false) {
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      }
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HEADER, $header);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($ch);
      return $result;
    }

}
