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
        'student_price',
        'govt_employee_price',
        'image',
        'category',
        'has_customer_discount'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'student_price' => 'decimal:2',
        'govt_employee_price' => 'decimal:2',
        'has_customer_discount' => 'boolean'
    ];
}