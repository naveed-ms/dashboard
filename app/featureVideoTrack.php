<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class featureVideoTrack extends Model
{
    
    public function track(){
        return $this->belongsTo("App\\track");
    }
}
