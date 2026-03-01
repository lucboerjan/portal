<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternetGroup extends Model
{
    protected $fillable = ['name', 'order'];

        public function links() {
        return $this->hasMany(InternetLink::class)->orderBy('order');
    }

}
