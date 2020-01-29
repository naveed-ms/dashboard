<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class featureVideo extends Model
{
    

    public function track_id(){
        return $this->hasMany("App\\featureVideoTrack");
    }
}
