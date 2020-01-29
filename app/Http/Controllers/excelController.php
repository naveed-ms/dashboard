<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\language;
use App\Http\Requests;
use App\music_director;

class excelController extends Controller
{
  public function importExcel()
  {
    $file = "storage/app/public/music-director.xlsx";
    //$file = "storage/app/public/acteresses-list.xlsx";
    Excel::load($file, function($data) {

     foreach ($data->toArray() as $value) {
       for ($i=0; $i < count($value); $i++) {
         print_r($value[$i]["music_directors"]);
         $m_directors= new music_director;
         $m_directors->name = $value[$i]["music_directors"];
         //$m_directors->genre= $value[$i]["genre"];
        //  $artist->DOB = date_format(date_create($value[$i]["dob"]),'Y-m-d');
         $m_directors->save();
        print_r($data->toArray());
       }
     }

      //  for ($i=0; $i < count($data->toArray()[0]); $i++)
      //  {
      //    $posts = Post::all();
      //    $json = json_encode($data->toArray()[0]);
      //    return View::make('posts.index', compact('posts', 'json'));
       //
      //    print_r($data->toArray()[0][$i]["name_music_directors"]);
      //    print_r("------");
      //    print_r($data->toArray()[0][$i]["genre"]);
      //    print_r("<br />");
      // }




    });
  }
}
