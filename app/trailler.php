<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trailler extends Model
{
  protected $fillable = [
      'id','name',
  ];
  protected $appends = ['value'];

  public function getValueAttribute(){
    return $this->attributes['id'];
  }

}
