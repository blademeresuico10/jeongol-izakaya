<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    protected $fillable = ['name', 'is_active'];
    public $timestamps = false;

    public function menus()
    {
        return $this->hasMany(menu::class, 'category_id');
    }
}
