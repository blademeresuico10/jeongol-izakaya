<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class table extends Model
{
    protected $fillable = ['table_number', 'capacity'];

    use SoftDeletes;
    
    public function walkin()  
    {
        return $this->hasMany(walkin::class, 'table_id');
    }

    public function reservation()
    {
        return $this->hasMany(reservation::class, 'table_id');
    }

    public function order(){
        return $this->hasMany(orders::class, 'order_id');
    }
}