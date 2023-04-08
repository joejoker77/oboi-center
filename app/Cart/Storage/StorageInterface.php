<?php

namespace App\Cart\Storage;

use App\Cart\CartItem;
use Illuminate\Http\Request;

interface StorageInterface
{
    /** @return CartItem[] */
    public function load(): array;

    /** @param CartItem[] $items */
    public function save(array $items): void;
}
