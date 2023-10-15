<?php

namespace App\Cart;

use App\Entities\Shop\Product;

class CartItem
{
    private $product;

    private $quantity;

    private $type_order;

    public function __construct(Product $product, $quantity, $type_order)
    {
        dd($product->isCanBuy());

        if (!$product->isCanBuy() && $type_order == 'checkout') {
            throw new \DomainException('Не может быть куплен');
        }

        $this->product    = $product;
        $this->quantity   = $quantity;
        $this->type_order = $type_order;
    }

    public function getId():string
    {
        return md5(serialize([$this->product->id]));
    }

    public function getProductId():int
    {
        return $this->product->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity():int
    {
        return $this->quantity;
    }

    public function getOrderType():string
    {
        return $this->type_order;
    }

    public function getPrice():int
    {
        return $this->product->price;
    }

    public function getWeight():int
    {
        return $this->product->weight * $this->quantity;
    }

    public function getCost():int
    {
        return $this->getPrice() * $this->quantity;
    }

    public function plus($quantity): static
    {
        return new static($this->product, $this->quantity + $quantity, $this->type_order);
    }

    public function changeQuantity($quantity): static
    {
        return new static($this->product, $quantity, $this->type_order);
    }
}
