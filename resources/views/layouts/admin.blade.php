<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    @yield('meta')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="{{ mix('css/admin.css', 'build') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">

</head>
<body>
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="{{ route('home') }}" target="_blank">Обои центр</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <input class="form-control form-control-dark w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="#">Sign out</a>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">
                <ul class="nav flex-column" id="nav_accordion">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('admin')) active @endif" aria-current="page" href="{{ route('admin.home') }}">
                            <span data-feather="home" class="align-text-bottom"></span>
                            Статистика
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link @if(request()->is('admin/shop*')) active @endif">
                            <span class="align-text-bottom" data-feather="shopping-bag"></span>
                            Магазин
                        </a>
                        <ul class="submenu collapse @if(request()->is('admin/shop*')) show @endif">
                            <li class="nav-item">
                                <a class="nav-link @if(request()->is('admin/shop/categories*')) active @endif" href="{{ route('admin.shop.categories.index') }}">
                                    <span data-feather="list" class="align-text-bottom"></span>
                                    Категории
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->is('admin/shop/attributes*')) active @endif" href="{{ route('admin.shop.attributes.index') }}">
                                    <span data-feather="sliders" class="align-text-bottom"></span>
                                    Аттрибуты
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->is('admin/shop/brands*')) active @endif" href="{{ route('admin.shop.brands.index') }}">
                                    <span data-feather="globe" class="align-text-bottom"></span>
                                    Бренды
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->is('admin/shop/tags*')) active @endif" href="{{ route('admin.shop.tags.index') }}">
                                    <span data-feather="hash" class="align-text-bottom"></span>
                                    Теги
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->is('admin/shop/products*')) active @endif" href="{{ route('admin.shop.products.index') }}">
                                    <span data-feather="shopping-bag" class="align-text-bottom"></span>
                                    Продукты
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.shop.delivery-methods.index') }}" class="nav-link @if(request()->is('admin/shop/delivery-methods*')) active @endif">
                                    <span class="align-text-bottom" data-feather="truck"></span>
                                    Способы доставки
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.file-manager') }}" class="nav-link @if(request()->is('admin/file-manager')) active @endif">
                            <span class="align-text-bottom" data-feather="folder"></span>
                            Файловый менеджер
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.navigations.index') }}" class="nav-link @if(request()->is('admin/navigations')) active @endif">
                            <span class="align-text-bottom" data-feather="list"></span>
                            Навигация по сайту
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <span class="align-text-bottom" data-feather="book-open"></span>
                            Блог
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Saved reports</span>
                    <a class="link-secondary" href="#" aria-label="Add a new report">
                        <span data-feather="plus-circle" class="align-text-bottom"></span>
                    </a>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span data-feather="file-text" class="align-text-bottom"></span>
                            Current month
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span data-feather="file-text" class="align-text-bottom"></span>
                            Last quarter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span data-feather="file-text" class="align-text-bottom"></span>
                            Social engagement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span data-feather="file-text" class="align-text-bottom"></span>
                            Year-end sale
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @include('layouts.partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<div id="mainOverlay">
    <div class="d-flex justify-content-center align-items-center">
        <div class="spinner-border text-white" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
<div class="modal fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalTitle" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="h1 modal-title fs-5" id="mainModalTitle"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script>
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
<script src="{{ asset('vendor/ckeditor4/ckeditor.js') }}"></script>
<script src="{{ mix('js/admin.js', 'build') }}"></script>
@yield('scripts')
</body>
</html>
