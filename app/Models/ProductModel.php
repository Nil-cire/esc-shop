<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id'; // 主鍵

    protected $fillable = [
        'uuid',
        'store_uid',
        'name',
        'description',
        'spec',
        'note',
        'price',
        'special_price',
        'special_price_start',
        'special_price_end',
        'stock',
        'image_url',
        'link',
        'is_enable'
    ];

    protected $casts = [
        // 'rating' => 'decimal:2'
        'price' => 'float',
        'special_price' => 'float',
        'is_enable' => 'boolean'
    ];

    public function products()
    {
        return $this->all();
    }

    public function get_by_store_id($store_uid)
    {
        return $this->where('store_uid', $store_uid)->get();
    }

    public function add_product(
        $store_uid,
        $name,
        $description = null,
        $spec = null,
        $note = null,
        $price,
        $special_price = null,
        $special_price_start = null,
        $special_price_end = null,
        $stock = null,
        $image_url = null,
        $link = null
    ) {

        $uuid = \Str::orderedUuid();

        $product = [
            'store_uid' => $store_uid,
            'uuid' => $uuid,
            'name' => $name,
            'description' => $description,
            'spec' => $spec,
            'note' => $note,
            'price' => $price,
            'special_price' => $special_price,
            'special_price_start' => $special_price_start,
            'special_price_end' => $special_price_end,
            'stock' => $stock,
            'image_url' => $image_url,
            'link' => $link,
        ];

        // $insertProduct = $this->insert($product);
        $insertProduct = ProductModel::create($product);
        return $insertProduct;

    }

    public function delete_product($store_id, $product_uid)
    {
        $this->where('store_uid', $store_id)->where('uuid', $product_uid)->delete();
    }

    public function update_product(
        $store_uid,
        $product_uid,
        $name = null,
        $description = null,
        $spec = null,
        $note = null,
        $price = null,
        $special_price = null,
        $special_price_start = null,
        $special_price_end = null,
        $stock = null,
        $image_url = null,
        $link = null,
    ) {
        $product = [
            'name' => $name,
            'description' => $description,
            'spec' => $spec,
            'note' => $note,
            'price' => $price,
            'special_price' => $special_price,
            'special_price_start' => $special_price_start,
            'special_price_end' => $special_price_end,
            'stock' => $stock,
            'image_url' => $image_url,
            'link' => $link,
        ];

        // $updateProduct = tap(\DB::table($this->getTable())->where('store_uid', $store_uid)->where('uuid', $product_uid))
        //     ->update(array_filter($product, function($value){return $value !== null && trim($value) !== '';}))
        //     ->first();

        $updateProduct = tap(ProductModel::where(['store_uid' => $store_uid, 'uuid' => $product_uid])->first())
            ->update(array_filter($product, function ($value) {
                return $value !== null && trim($value) !== ''; }));
        // $updateProduct = $this->where('store_uid', $store_uid)->where('uuid', $product_uid)->update(array_filter($product));
        return $updateProduct;
    }

    public function enable_product($store_uid, $product_uid, $is_enable)
    {
        $product = [
            'is_enable' => $is_enable
        ];

        $updateProduct = tap(ProductModel::where(['store_uid' => $store_uid, 'uuid' => $product_uid])->first())
            ->update(array_filter($product, function ($value) {
                return $value !== null; }));
        // $updateProduct = $this->where('store_uid', $store_uid)->where('uuid', $product_uid)->update(array_filter($product));

        return $updateProduct;
    }
}
