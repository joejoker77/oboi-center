<?php

use App\Entities\Shop\Order;
use App\Http\Router\PostPath;
use App\Http\Router\ProductPath;
use Diglactic\Breadcrumbs\Generator;
use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('home', function (Generator $generator) {
    $generator->push('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
    <polyline points="9 22 9 12 15 12 15 22"></polyline>
</svg>', route('home'));
});

Breadcrumbs::for('login', function (Generator $generator) {
    $generator->parent('home');
    $generator->push('Логин/Регистрация', route('login'));
});

Breadcrumbs::for('password.request', function (Generator $generator) {
    $generator->parent('login');
    $generator->push('Сброс пароля', route('password.request'));
});

Breadcrumbs::for('cabinet.profile.index', function (Generator $generator) {
    $generator->parent('home');
    $generator->push('Личный кабинет', route('cabinet.profile.index'));
});

Breadcrumbs::for('cabinet.profile.add-delivery-address', function (Generator $generator) {
    $generator->parent('cabinet.profile.index');
    $generator->push('Добавить адрес доставки', route('cabinet.profile.add-delivery-address'));
});

Breadcrumbs::for('cart.index', function (Generator $generator) {
    $generator->parent('home');
    $generator->push('Корзина', route('cart.index'));
});
Breadcrumbs::for('cart.checkout', function (Generator $generator) {
    $generator->parent('cart.index');
    $generator->push('Оформление заказа', route('cart.checkout'));
});

Breadcrumbs::for('catalog.inner_category', function (Generator $generator, ProductPath $path) {
    if ($path->category && $parent = $path->category->parent) {
        $generator->parent('catalog.inner_category', $path->withCategory($parent));
    } else {
        $generator->parent('home');
    }
    if ($path->category) {
        $generator->push($path->category->name, route('catalog.index', $path));
    }
});

Breadcrumbs::for('catalog.index', function (Generator $generator, ProductPath $path = null) {
    $path = $path ?: product_path(null, null);
    $generator->parent('catalog.inner_category', $path->withProduct(null));
    if ($path->product) {
        $generator->push($path->product->name, route('catalog.index'));
    }
});

Breadcrumbs::for('blog.inner_category', function (Generator $generator, PostPath $path) {
    if ($path->category && $parent = $path->category->parent) {
        $generator->parent('blog.inner_category', $path->withCategory($parent));
    } else {
        $generator->parent('home');
    }
    if ($path->category) {
        $generator->push($path->category->name, route('blog.index', $path));
    }
});

Breadcrumbs::for('blog.index', function (Generator $generator, PostPath $path = null) {
    $path = $path?:post_path(null,null);
    $generator->parent('blog.inner_category', $path->withPost(null));
    if ($path->post) {
        $generator->push($path->post->title, route('blog.index'));
    }
});

Breadcrumbs::for('shop.filter', function (Generator $generator) {
    $generator->parent('home');
    $generator->push('Результат поиска', route('shop.filter'));
});

Breadcrumbs::for('shop.search', function (Generator $generator) {
    $generator->parent('home');
    $generator->push('Результат поиска', route('shop.search'));
});
