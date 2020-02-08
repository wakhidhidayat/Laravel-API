<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function review() {
        return $this->hasMany("App\Review");
    }

    protected $fillable = [
        'name','detail','discount','price','stock'
    ];
}
