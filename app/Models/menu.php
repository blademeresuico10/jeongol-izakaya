<?php
// app/Models/Menu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = ['menu_item', 'regular_price', 'image', 'category_id', 'has_customer_discount', 'status'];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(orders::class);
    }


    public function hasActiveOrders()
    {
        return $this->orders()
            ->whereHas('reservations', function ($query) {
                $query->whereIn('status', ['pending', 'confirmed', 'in_progress']);
            })
            ->exists();
    }
    public function menuDiscount()
    {
        return $this->hasOne(MenuDiscount::class, 'menu_id', 'id');
    }

    public function ingredients()
    {
        return $this->hasMany(MenuIngredient::class, 'menu_id');
    }

    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class, 'menu_id');
    }
}
