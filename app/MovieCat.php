<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieCat extends Model
{
  protected $fillable = [
      'movie_id','cat_id'
  ];
}
