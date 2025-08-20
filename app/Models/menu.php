<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    protected $table = 'menu';
    protected $fillable = ['menu_item', 'price'];

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
