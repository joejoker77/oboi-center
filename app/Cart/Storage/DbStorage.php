<?php

namespace App\Cart\Storage;

use App\Cart\CartItem;
use App\Entities\Shop\Product;
use Illuminate\Support\Facades\DB;

class DbStorage implements StorageInterface
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function load(): array
    {
        $rows = DB::select('select * from shop_cart_items where user_id = '.$this->userId.' order by product_id');
        return array_map(function (\stdClass $row) {
            /** @var Product $product */
            if ($product = Product::find($row->product_id)) {
                return new CartItem($product, $row->quantity, $row->type);
            }
        }, $rows);
    }

    public function loadAll():array
    {
        $rows = DB::select('select * from shop_cart_items order by product_id');
        return array_map(function (\stdClass $row) {
            /** @var Product $product */
            if ($product = Product::find($row->product_id)) {
                return new CartItem($product, $row->quantity, $row->type);
            }
        }, $rows);
    }

    public function save(array $items): void
    {
        DB::delete('delete from shop_cart_items where user_id = ?', [$this->userId]);
        $data = [];
        foreach ($items as $item) {
            $data[] = ['user_id' => $this->userId, 'product_id'=>$item->getProductId(), 'quantity' => $item->getQuantity(), 'type' => $item->getOrderType()];
        }
        DB::table('shop_cart_items')->insert($data);
    }
}
