<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class menu extends Model
{
     use SoftDeletes;
    protected $table = 'menu';
    protected $fillable = ['menu_item', 'price'];

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
