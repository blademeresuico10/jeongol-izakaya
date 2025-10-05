<?php
// app/Models/Menu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';
    
    protected $fillable = [
        'menu_item',
        'regular_price',
        'image',
        'category',
        'has_customer_discount',
        'status' 
    ];

    
    public function orders()
    {
        return $this->hasMany(orders::class);
    }

   
    public function hasActiveOrders()
    {
        return $this->orders()
            ->whereHas('reservations', function($query) {
                $query->whereIn('status', ['pending', 'confirmed', 'in_progress']); 
            })
            ->exists();
    }
    public function menuDiscount()
    {
        return $this->hasOne(MenuDiscount::class, 'menu_id', 'id');
    }

    
}