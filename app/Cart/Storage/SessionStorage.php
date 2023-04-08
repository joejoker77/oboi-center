<?php

namespace App\Cart\Storage;


use App\Cart\CartItem;
use App\Entities\Shop\Product;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class SessionStorage implements StorageInterface
{
    private $key;
    private $session;
    private $timeout;

    public function __construct($key, $timeout)
    {
        $this->key     = $key;
        $this->timeout = $timeout;
        $this->session = new Session();
    }

    public function load(): array
    {
        if ($cookie = \request()->cookie($this->key)) {
            if (!$this->isJson($cookie)) {
                $arrCookie = explode('|', Crypt::decryptString($cookie));
                $cookie = $arrCookie[1];
            }
            return array_filter(array_map(function (array $row) {
                if (isset($row['p'], $row['q']) && $product = Product::find($row['p'])) {
                    /** @var Product $product */
                    return new CartItem($product, $row['q'] ?? null, $row['t']);
                }
                return false;
            }, json_decode($cookie, true)));
        }

        return $this->session::pull($this->key, []);
    }

    public function save(array $items): void
    {
        $this->session::push($this->key, $items);
        $res = [];
        foreach ($items as $item) {
            $quantity = $item->getQuantity();
            $product  = $item->getProductId();
            $type     = $item->getOrderType();
            $res[]    = ['p' => $product, 'q' => $quantity, 't' => $type];
        }
        Cookie::queue(Cookie::make($this->key, json_encode($res), time() + $this->timeout));
    }

    private function isJson($string):bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
