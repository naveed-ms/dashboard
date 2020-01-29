<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class track extends Model
{
  protected $fillable = [
      'name',
  ];
  protected $appends = ['value'];

  public function getValueAttribute(){
    return $this->attributes['id'];
  }

}
