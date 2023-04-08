<?php

namespace App\Cart\Storage;

use App\Cart\CartItem;
use Illuminate\Support\Facades\Auth;

class HybridStorage implements StorageInterface
{
    private StorageInterface $storage;
    private string $cookieKey;
    private int $cookieTimeout;

    public function __construct($cookieKey, $cookieTimeout)
    {
        $this->cookieKey     = $cookieKey;
        $this->cookieTimeout = $cookieTimeout;
    }

    public function load():array
    {
        $this->getStorage();
        return $this->storage->load();
    }

    public function loadAll():array
    {
        $this->getStorage();
        return $this->storage->loadAll();
    }

    public function save(array $items): void
    {
        $this->storage->save($items);
    }

    private function getStorage(): void
    {
        $sessionStorage = new SessionStorage($this->cookieKey, $this->cookieTimeout);

        if (!auth()->check()) {
            $this->storage = $sessionStorage;
        } else {
            $dbStorage = new DbStorage(Auth::user()->id);
            if ($cookieItems = $sessionStorage->load()) {
                $dbItems = $dbStorage->load();
                $items   = array_merge($dbItems, array_udiff($cookieItems, $dbItems, function (CartItem $first, CartItem $second) {
                    if ($first->getId() == $second->getId()) {
                        return 0;
                    } else {
                        return -1;
                    }
                }));
                $dbStorage->save($items);
                $sessionStorage->save([]);
            }
            $this->storage = $dbStorage;
        }
    }
}
