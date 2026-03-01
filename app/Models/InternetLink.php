<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternetLink extends Model
{
    protected $fillable = ['internet_group_id', 'url', 'link_title', 'order'];

    public function group() {
        return $this->belongsTo(InternetGroup::class);
    }

}
