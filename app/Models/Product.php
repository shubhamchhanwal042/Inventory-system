<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'category',
        'base_price',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {

            $product->slug = Str::slug($product->name);

            $product->sku = 'SKU-'.strtoupper(Str::random(6));
        });
    }

    // Eloquent Scope
    public function scopeActive($query)
    {
        return $query->where('status','active');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}