<?php

namespace App\Cart;

use App\Cart\Cost\Cost;
use App\Cart\Storage\CookieStorage;
use App\Cart\Storage\StorageInterface;
use App\Cart\Cost\Calculator\CalculatorInterface;

class Cart
{
    private StorageInterface $storage;
    private CalculatorInterface $calculator;

    /** @var CartItem[] */
    private array|null $items = null;

    public function __construct(StorageInterface $storage, CalculatorInterface $calculator)
    {
        $this->storage    = $storage;
        $this->calculator = $calculator;
    }

    public function getItems(): array
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        return $this->items;
    }

    public function getAllItems(): array
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        return $this->items;
    }

    public function getAmount(): int
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        return count($this->items);
    }

    public function add(CartItem $item):void
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        foreach ($this->items as $i => $current) {
            if ($current->getId() == $item->getId()) {
                $this->items[$i] = $current->plus($item->getQuantity());
                $this->saveItems();
                return;
            }
        }
        $this->items[] = $item;
        $this->saveItems();
    }

    public function set($id, $quantity):void
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        if ($quantity == 0) {
            $this->remove($id);
            return;
        }

        foreach ($this->items as $i => $current) {
            if ($current->getId() == $id) {
                $this->items[$i] = $current->changeQuantity($quantity);
                $this->saveItems();
                return;
            }
        }
        throw new \DomainException('Товар не найден');
    }

    public function remove($id):void
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        foreach ($this->items as $i => $current) {
            if ($current->getId() == $id) {
                unset($this->items[$i]);
                $this->saveItems();
                return;
            }
        }
        throw new \DomainException('Товар не найден');
    }

    public function clear():void
    {
        $this->items = [];
        $this->saveItems();
    }

    public function getCost():Cost
    {
        if ($this->items == null) {
            $this->loadItems();
        }

        return $this->calculator->getCost($this->items);
    }

    public function getWeight():int
    {
        if ($this->items == null) {
            $this->loadItems();
        }
        return array_sum(array_map(function (CartItem $item) {
            return $item->getWeight();
        }, $this->items));
    }

    private function loadItems():void
    {
        $this->items = $this->storage->load();
    }

    private function saveItems():void
    {
        $this->storage->save($this->items);
    }
}
