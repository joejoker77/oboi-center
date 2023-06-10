<?php /** @var App\Entities\User\User $user */ ?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="canonical" href="{{ url()->current() }}">
    {!! Meta::toHtml() !!}
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="{{ mix('css/style.css', 'build') }}" rel="stylesheet">
</head>
<body>
    <div class="page-wrapper">
        <header @if(Route::current()->getName() === 'home') class="main" @endif>
            <div class="container-fluid">
                <div class="row d-none d-lg-flex">
                    <div class="col">Ваш город: Москва</div>
                    <div class="col">
                        <nav class="navbar navbar-expand py-0 top-menu">
                            <x-menu handler="topMenu" menuClass="navbar-nav ms-auto" />
                        </nav>
                    </div>
                </div>
                <div class="row pt-3 pb-2 menu-search">
                    <div class="col mobile-shadow">
                        <div class="d-flex gap-lg-3">
                            <button type="button" class="btn btn-catalog" data-bs-toggle="dropdown" data-bs-target="#mainMenu" aria-expanded="false" data-bs-auto-close="false">
                                <svg class="me-lg-3 open-button" width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="14" cy="16" r="2" fill="black"/>
                                    <circle cx="8" cy="16" r="2" fill="black"/>
                                    <circle cx="2" cy="16" r="2" fill="black"/>
                                    <circle cx="14" cy="9" r="2" fill="black"/>
                                    <circle cx="8" cy="9" r="2" fill="black"/>
                                    <circle cx="2" cy="9" r="2" fill="black"/>
                                    <circle cx="14" cy="2" r="2" fill="black"/>
                                    <circle cx="8" cy="2" r="2" fill="black"/>
                                    <circle cx="2" cy="2" r="2" fill="black"/>
                                </svg>
                                <span class="material-symbols-outlined close">close</span>
                                <span class="d-none d-lg-inline">Каталог</span>
                            </button>

                            <div class="d-block d-lg-none">
                                <button class="btn btn-contact" data-bs-toggle="dropdown" data-bs-target="#contactInfo" aria-expanded="false" data-bs-auto-close="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.7417 2.69486C14.6141 2.56481 15.5074 2.60742 16.3666 2.82286L16.3666 2.82286C17.5025 3.1077 18.5783 3.69457 19.4653 4.58153L18.9758 5.07107L19.4653 4.58153C20.3522 5.46849 20.9392 6.54431 21.224 7.68028C21.4394 8.5395 21.482 9.43267 21.352 10.3051C21.2956 10.6833 20.9433 10.9442 20.5652 10.8878C20.187 10.8314 19.9261 10.4791 19.9825 10.101C20.0858 9.40808 20.0519 8.69872 19.8809 8.01704L19.8809 8.01702C19.6554 7.11742 19.1911 6.26549 18.4862 5.5606C17.7813 4.85573 16.9294 4.39147 16.0298 4.1659C15.3481 3.99497 14.6387 3.96105 13.9458 4.06434C13.5677 4.12071 13.2154 3.85984 13.159 3.48167C13.1027 3.10349 13.3635 2.75123 13.7417 2.69486Z" fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9136 5.99832C14.9597 5.84238 16.0666 6.16713 16.8738 6.9742L16.8738 6.97422C17.6809 7.78134 18.0056 8.88838 17.8496 9.93445C17.7933 10.3126 17.441 10.5735 17.0628 10.5171C16.6847 10.4607 16.4238 10.1085 16.4802 9.7303C16.5742 9.09952 16.3784 8.43702 15.8947 7.95331C15.8947 7.9533 15.8947 7.9533 15.8947 7.95329M15.8947 7.95329C15.4109 7.46955 14.7485 7.27378 14.1177 7.36781C13.7395 7.42418 13.3873 7.16331 13.3309 6.78514C13.2745 6.40697 13.5354 6.0547 13.9136 5.99832" fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.67805 5.44351C4.91839 5.44351 4.44453 6.12423 4.66154 6.7674C5.43966 9.07349 6.88968 12.5426 9.05215 14.7051L9.05216 14.7051C11.2146 16.8676 14.6837 18.3176 16.9898 19.0957L16.9899 19.0957C17.633 19.3127 18.3137 18.8389 18.3137 18.0792V15.7376C18.3137 15.6538 18.2683 15.5766 18.1951 15.5359L18.1951 15.5359L16.1554 14.4019C16.0888 14.3648 16.0082 14.3631 15.9401 14.3972L15.94 14.3972L13.7649 15.4848C13.6288 15.5528 13.4742 15.5741 13.3248 15.5454L13.4553 14.8655C13.3248 15.5454 13.3246 15.5454 13.3244 15.5454L13.3239 15.5453L13.3229 15.5451L13.3203 15.5446L13.3133 15.5432L13.2924 15.5388C13.2755 15.5352 13.2525 15.5301 13.224 15.5234C13.1671 15.5099 13.0879 15.4898 12.9903 15.4617C12.7953 15.4054 12.5252 15.3164 12.2113 15.1826C11.5862 14.9162 10.7702 14.465 10.0312 13.726L10.0312 13.726C9.2923 12.9871 8.83983 12.1699 8.5722 11.5439C8.43779 11.2295 8.34811 10.9589 8.29134 10.7636C8.26291 10.6659 8.24261 10.5865 8.22899 10.5295C8.22219 10.501 8.21704 10.478 8.21338 10.4611L8.20899 10.4402L8.20758 10.4333L8.20707 10.4307L8.20686 10.4296L8.20677 10.4291C8.20672 10.4289 8.20668 10.4287 8.88625 10.2965L8.20668 10.4287C8.17753 10.2788 8.19874 10.1235 8.26703 9.98692L9.35479 7.81141L9.35481 7.81137C9.38885 7.7433 9.38713 7.66278 9.35015 7.59617L8.22108 5.56233C8.18034 5.48896 8.10306 5.44351 8.0193 5.44351H5.67805ZM9.62057 10.3759C9.62068 10.3763 9.62079 10.3767 9.6209 10.3771C9.66446 10.5269 9.73604 10.744 9.84535 10.9996C10.0652 11.5138 10.4299 12.1666 11.0103 12.7469C11.0103 12.7469 11.0103 12.7469 11.0103 12.7469M11.0103 12.7469C11.5906 13.3272 12.2418 13.6905 12.7541 13.9088C13.009 14.0174 13.2251 14.0883 13.3742 14.1314C13.3745 14.1314 13.3748 14.1315 13.3751 14.1316L15.3208 13.1588C15.3208 13.1588 15.3208 13.1587 15.3208 13.1587C15.7979 12.9202 16.362 12.9325 16.8282 13.1917L16.5153 13.7546L16.8282 13.1917L18.8678 14.3257C19.3805 14.6107 19.6983 15.1511 19.6983 15.7376V18.0792C19.6983 19.7044 18.1638 20.9531 16.5472 20.4076C14.2121 19.6197 10.4769 18.088 8.07308 15.6841C5.66919 13.2803 4.13749 9.54511 3.3496 7.21007L3.96075 7.00385L3.3496 7.21007C2.80412 5.59345 4.05281 4.0589 5.67805 4.0589H8.0193C8.60615 4.0589 9.14678 4.37721 9.43161 4.89018L9.43164 4.89023L10.5607 6.92414L10.5608 6.92415C10.8194 7.39014 10.8317 7.95382 10.5932 8.43063C10.5932 8.43065 10.5932 8.43066 10.5932 8.43068L9.62057 10.3759" fill="white"/>
                                    </svg>
                                </button>
                                <div id="contactInfo" class="dropdown-menu">
                                    <div class="phone">
                                        <span>Контактный телефон:</span>
                                        <a href="tel:+74957205965">495 720 59 65</a>
                                        <a class="ms-auto" href="tel:+79037205965">903 720 59 65</a>
                                    </div>
                                    <div class="time-work">
                                        <span>Часы работы:</span>
                                        <span>с 10:00 до 20:00</span>
                                    </div>
                                </div>
                            </div>

                            <x-menu handler="mainMenu" menuClass="main-menu dropdown-menu w-100" template="components.mega-menu" menuId="mainMenu"/>
                            <search-form class="w-100 d-block">
                                <form action="{{ route('shop.search') }}" class="search-form" method="get" id="searchForm">
                                    @csrf
                                    <input class="form-control" type="text" name="query" id="search" aria-label="search" placeholder="Поиск" value="{{ request()->get('query') }}">
                                    <svg class="search-icon" width="25" height="29" viewBox="0 0 25 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="11.9822" cy="12.5681" r="9" transform="rotate(-105 11.9822 12.5681)" stroke="#a0a0a0"/>
                                        <line x1="24.3223" y1="23.5249" x2="18.9057" y2="18.7646" stroke="#a0a0a0"/>
                                    </svg>
                                </form>
                            </search-form>
                        </div>
                    </div>
                    <div class="col text-center header-logo">
                        @if(Route::current()->getName() !== 'home')
                            <a href="{{ route('home') }}" class="logo-link">
                                @endif
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="left-text me-1">Обои</div>
                                    <svg class="logo-icon" viewBox="0 0 46 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path class="colorized" d="M44.2839 16.1036C47.834 28.0139 41.009 40.5328 29.0396 44.0655C17.0703 47.5981 4.4893 40.8067 0.939138 28.8964C-2.61102 16.9861 4.21406 4.46716 16.1834 0.934507C28.1527 -2.59814 40.7337 4.19328 44.2839 16.1036Z" fill="#A8ABB2"/>
                                        <path d="M42.5773 13.6744C45.24 22.6071 40.1212 31.9964 31.1442 34.6458C22.1672 37.2953 12.7314 32.2018 10.0688 23.269C7.40617 14.3363 12.525 4.94709 21.502 2.2976C30.4789 -0.351884 39.9147 4.74168 42.5773 13.6744Z" fill="white"/>
                                        <path class="colorized" d="M39.8832 17.9709C39.4155 24.4245 33.7788 29.279 27.2932 28.8136C20.8076 28.3483 15.9291 22.7393 16.3967 16.2857C16.8644 9.83205 22.5011 4.97759 28.9867 5.44295C35.4724 5.9083 40.3509 11.5172 39.8832 17.9709Z" fill="#A8ABB2"/>
                                        <path d="M35.956 20.0384C35.5819 25.2013 31.0725 29.0848 25.884 28.7126C20.6955 28.3403 16.7927 23.8531 17.1668 18.6902C17.5409 13.5273 22.0503 9.64373 27.2388 10.016C32.4273 10.3883 36.3301 14.8755 35.956 20.0384Z" fill="white"/>
                                        <path d="M35.956 20.0384C35.5819 25.2013 31.0725 29.0848 25.884 28.7126C20.6955 28.3403 16.7927 23.8531 17.1668 18.6902C17.5409 13.5273 22.0503 9.64373 27.2388 10.016C32.4273 10.3883 36.3301 14.8755 35.956 20.0384Z" fill="white"/>
                                        <path d="M35.956 20.0384C35.5819 25.2013 31.0725 29.0848 25.884 28.7126C20.6955 28.3403 16.7927 23.8531 17.1668 18.6902C17.5409 13.5273 22.0503 9.64373 27.2388 10.016C32.4273 10.3883 36.3301 14.8755 35.956 20.0384Z" fill="white"/>
                                        <path d="M35.956 20.0384C35.5819 25.2013 31.0725 29.0848 25.884 28.7126C20.6955 28.3403 16.7927 23.8531 17.1668 18.6902C17.5409 13.5273 22.0503 9.64373 27.2388 10.016C32.4273 10.3883 36.3301 14.8755 35.956 20.0384Z" fill="white"/>
                                        <path class="colorized" d="M30.5043 21.201C30.3248 23.678 28.1613 25.5413 25.672 25.3627C23.1827 25.1841 21.3102 23.0312 21.4897 20.5542C21.6692 18.0772 23.8327 16.2139 26.322 16.3925C28.8113 16.5711 30.6838 18.724 30.5043 21.201Z" fill="#A8ABB2"/>
                                    </svg>
                                    <div class="right-text ms-1">Центр</div>
                                </div>
                                @if(Route::current()->getName() !== 'home')
                            </a>
                        @endif
                    </div>
                    <div class="col">
                        <div class="d-flex w-100">
                            <div class="contact-info d-none d-lg-flex flex-lg-column">
                                <div class="phone">
                                    <span class="material-symbols-outlined">phone_in_talk</span>
                                    <a href="tel:+74957205965">+7 (495) 720-59-65</a>
                                </div>
                                <div class="time-work">с 10:00 до 19:00</div>
                            </div>
                            <div class="btn-toolbar control-icons ms-auto">
                                @auth
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-target="#clientMenu" aria-expanded="false">
                                            <svg width="23" height="29" viewBox="0 0 25 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12.5003" cy="8.11289" r="6.79032" stroke="#1A5294" stroke-width="2"/>
                                                <path d="M24 30V26.129C24 21.7107 20.4183 18.129 16 18.129H9C4.58172 18.129 1 21.7107 1 26.129V30" stroke="#1A5294" stroke-width="2"/>
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu" id="clientMenu">
                                            <li>
                                                <a href="{{ route('cabinet.profile.index') }}" class="dropdown-item">Личный кабинет</a>
                                            </li>
                                            <li>
                                                <form action="{{ route('logout') }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item btn btn-link">Выйти</button>
                                                </form>
                                            </li>
                                            @if(auth()->user()->userProfile->isAdmin())
                                                <li>
                                                    <a href="{{ route('admin.home') }}" target="_blank" class="dropdown-item">Панель администратора</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <a href="#" class="btn btn-link d-none d-lg-flex align-items-baseline">
                                        <svg width="31" height="29" viewBox="0 0 33 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16.5205 27.9987C16.4113 27.9987 16.302 27.9464 16.2474 27.894L16.1927 27.8416C13.1881 25.9562 -1.56219 15.8482 1.38786 6.73524C2.42584 3.59285 4.88422 1.65504 8.05279 1.44555C11.3853 1.23606 14.7723 2.96437 16.5205 5.6354C18.2687 2.96437 21.6558 1.23606 24.9336 1.49793C28.1568 1.70742 30.5606 3.64522 31.5986 6.78761C34.6032 15.8482 19.853 25.9562 16.8483 27.894L16.7937 27.9464C16.6844 27.9464 16.6298 27.9987 16.5205 27.9987ZM8.65373 2.38827C8.48984 2.38827 8.32594 2.38827 8.16205 2.38827C5.37589 2.59776 3.2453 4.2737 2.37121 7.04948C-0.305685 15.3244 13.1881 24.7516 16.5205 26.9513C19.853 24.7516 33.4014 15.3244 30.6698 7.04948C29.7958 4.2737 27.6652 2.59776 24.879 2.44064C21.6558 2.23115 18.3233 4.06421 16.9576 6.83999C16.9029 6.99711 16.6844 7.10185 16.5205 7.10185C16.302 7.10185 16.1381 6.99711 16.0835 6.83999C14.7723 4.22133 11.713 2.38827 8.65373 2.38827Z" fill="none" stroke="#1A5294"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-link">
                                        <svg width="23" height="29" viewBox="0 0 25 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12.5003" cy="8.11289" r="6.79032" stroke="#1A5294" stroke-width="2"/>
                                            <path d="M24 30V26.129C24 21.7107 20.4183 18.129 16 18.129H9C4.58172 18.129 1 21.7107 1 26.129V30" stroke="#1A5294" stroke-width="2"/>
                                        </svg>
                                    </a>
                                @endauth
                                <a href="#" id="cartLink" class="btn btn-link">
                                    <x-count-cart />
                                    <svg width="32" height="29" viewBox="0 0 33 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.92578 6.194H31.5974L28.1347 18.3134H14.7168" stroke="#1A5294" stroke-width="2"/>
                                        <circle cx="11.6865" cy="26.1044" r="2.89552" stroke="#1A5294" stroke-width="2"/>
                                        <circle cx="25.5371" cy="26.1044" r="2.89552" stroke="#1A5294" stroke-width="2"/>
                                        <path d="M0 1H5.62687L11.2537 23.0746" stroke="#1A5294" stroke-width="2"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <section id="content">
            <div class="container">
                <div class="breadcrumbs">
                    @section('breadcrumbs', Diglactic\Breadcrumbs\Breadcrumbs::render())
                    @yield('breadcrumbs')
                </div>
                @include('layouts.partials.flash')
            </div>
            @yield('content')
        </section>
    </div>
    <footer>
        <div class="container">
            <div class="row">
                <div class="footer-menu">
                    <x-menu handler="footerMenu" menuClass="nav" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 d-flex flex-column justify-content-between">
                    <div class="copyright">
                        <p>© {{ Carbon\Carbon::now()->year }} «Обои Центр» Права защищены.<br>Копирование информации запрещено.</p>
                    </div>
                    <div class="privacy-policy">
                        <x-menu handler="footerLinks" />
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="contacts">
                        <a href="tel:+74957205965" class="phone" title="Позвонить нам">+7 (495) 720-59-65</a>
                        <a href="mailto:info@oboi-center.pro" class="email" title="Написать на почту">info@oboi-center.pro</a>
                    </div>

                    <div class="social-media">
                        <a href="https://vk.com/public211264040" target="_blank" title="Наша группа в VK">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 225.000000 225.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,225.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M184 2221 c-53 -13 -103 -51 -132 -99 l-27 -47 0 -950 0 -950 28 -47 c18 -31 44 -57 75 -75 l47 -28 950 0 950 0 47 28 c31 18 57 44 75 75 l28 47 0 950 0 950 -28 47 c-18 31 -44 57 -75 75 l-47 28 -930 2 c-511 0 -944 -2 -961 -6z m1045 -705 c20 -18 21 -29 21 -186 0 -147 2 -171 18 -190 16 -19 22 -21 40 -11 33 18 111 134 172 256 l55 110 164 0 c140 0 165 -2 174 -16 16 -26 -39 -129 -147 -273 -53 -70 -96 -135 -96 -145 0 -24 7 -32 113 -141 139 -143 165 -205 95 -229 -40 -14 -258 -14 -294 -1 -15 6 -69 52 -120 102 -78 76 -97 90 -117 84 -29 -7 -47 -45 -54 -114 -8 -76 -27 -87 -146 -79 -198 12 -336 98 -479 299 -104 145 -262 449 -256 491 3 21 7 22 142 25 92 2 143 -1 152 -9 7 -6 35 -57 63 -112 59 -124 128 -227 164 -247 24 -13 28 -13 44 3 14 14 18 39 22 129 4 140 -7 183 -55 205 -38 18 -41 25 -20 42 29 24 73 30 197 28 109 -2 130 -5 148 -21z"/>
                                </g>
                            </svg>
                        </a>
                        <a href="viber://chat?number=%2B79671381956" title="Написать в Viber">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 287.000000 286.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,286.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M329 2836 c-156 -55 -278 -190 -313 -346 -23 -99 -24 -2035 -1 -2119 34 -131 120 -245 229 -305 125 -70 60 -66 1192 -66 991 0 1024 1 1086 20 125 38 239 134 293 245 56 115 55 78 55 1157 0 690 -3 1013 -11 1050 -31 147 -129 272 -267 341 l-85 42 -1056 2 -1056 3 -66 -24z m1234 -491 c187 -33 350 -116 478 -244 152 -152 234 -347 254 -604 7 -76 5 -90 -10 -107 -21 -23 -67 -26 -83 -7 -6 7 -16 63 -22 123 -17 187 -64 321 -152 438 -128 170 -317 272 -547 296 -97 9 -131 25 -131 62 0 10 9 27 20 38 25 25 74 26 193 5z m-730 -29 c66 -28 242 -242 303 -371 55 -112 44 -151 -64 -240 -68 -56 -82 -80 -82 -133 1 -53 50 -171 103 -244 120 -168 347 -328 467 -328 56 0 65 5 121 76 64 80 95 98 165 92 64 -5 170 -69 331 -197 140 -112 159 -156 108 -258 -33 -66 -130 -171 -197 -214 -46 -30 -61 -34 -118 -34 -55 0 -80 7 -164 44 -372 166 -687 403 -926 696 -151 185 -308 453 -383 655 -45 122 -51 157 -37 211 14 51 88 130 187 197 96 65 127 73 186 48z m792 -202 c241 -61 400 -237 441 -490 15 -87 15 -104 3 -130 -11 -21 -22 -30 -46 -32 -40 -4 -63 27 -63 84 0 178 -104 344 -262 419 -43 20 -107 40 -156 49 -93 16 -112 27 -112 67 0 60 50 69 195 33z m36 -230 c95 -30 166 -120 188 -238 13 -71 0 -109 -39 -114 -40 -5 -59 18 -70 81 -17 108 -63 155 -169 176 -81 16 -105 57 -60 102 23 23 62 21 150 -7z"/>
                                </g>
                            </svg>
                        </a>
                    </div>

                    <div class="social-links">
                        <a href="https://t.me/moscow_oboi">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 404.000000 403.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,403.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M1870 4003 c-334 -24 -676 -141 -955 -327 -349 -232 -632 -590 -770 -973 -46 -126 -91 -304 -107 -418 -16 -117 -16 -435 0 -550 30 -205 105 -443 199 -628 265 -521 734 -894 1303 -1037 612 -154 1282 1 1760 406 336 285 553 632 660 1054 205 812 -131 1675 -835 2146 -49 33 -103 67 -120 76 -212 109 -318 151 -500 197 -186 48 -435 69 -635 54z m495 -228 c365 -75 661 -233 916 -485 53 -52 114 -117 136 -145 185 -231 304 -487 369 -790 26 -119 28 -148 28 -345 0 -197 -2 -226 -28 -345 -64 -300 -190 -570 -369 -790 -52 -64 -198 -210 -262 -262 -220 -179 -490 -305 -790 -369 -119 -26 -148 -28 -345 -28 -197 0 -226 2 -345 28 -303 65 -559 184 -790 369 -27 22 -93 83 -145 136 -252 254 -407 547 -486 916 -26 120 -28 147 -28 345 0 197 3 226 28 345 80 376 234 663 496 925 90 90 258 230 276 230 3 0 20 11 38 25 50 38 241 130 351 169 110 39 288 82 399 96 39 5 150 7 246 6 143 -3 199 -8 305 -31z"/>
                                    <path d="M2780 2980 c-47 -17 -94 -34 -105 -40 -11 -5 -110 -43 -220 -85 -110 -42 -228 -87 -262 -101 -106 -41 -184 -71 -283 -109 -52 -20 -122 -47 -155 -60 -33 -13 -91 -36 -130 -50 -38 -15 -108 -42 -155 -60 -47 -19 -134 -52 -195 -75 -60 -23 -148 -56 -195 -75 -47 -18 -123 -48 -170 -66 -100 -38 -152 -71 -204 -129 -50 -56 -66 -97 -66 -170 0 -71 15 -110 63 -166 43 -50 91 -74 227 -115 58 -17 125 -38 150 -46 170 -54 270 -82 300 -85 l35 -3 5 -260 c5 -275 4 -265 53 -327 8 -10 40 -30 72 -44 49 -23 66 -26 115 -21 103 11 156 39 255 132 50 47 95 85 100 85 6 0 42 -24 80 -53 323 -242 368 -267 476 -267 158 0 267 99 304 275 9 44 30 143 46 220 17 77 43 201 59 275 29 141 68 326 119 565 17 77 39 185 51 240 11 55 32 150 46 210 31 131 28 192 -12 272 -35 69 -86 115 -158 142 -78 29 -143 26 -246 -9z m179 -185 c59 -30 63 -91 20 -295 -17 -80 -43 -206 -59 -280 -31 -151 -74 -353 -120 -570 -17 -80 -44 -208 -60 -285 -70 -335 -76 -353 -129 -375 -57 -23 -78 -10 -527 327 -40 29 -76 53 -82 53 -6 0 -67 -55 -137 -122 -70 -68 -137 -128 -149 -135 -11 -7 -38 -12 -58 -13 l-38 0 0 224 0 224 122 108 c67 60 150 134 183 164 65 61 294 265 384 344 31 27 74 65 96 85 22 20 82 74 133 120 88 79 110 111 76 111 -19 0 -66 -27 -247 -142 -76 -48 -140 -88 -143 -88 -2 0 -41 -24 -87 -53 -45 -30 -129 -83 -187 -119 -267 -165 -330 -204 -392 -245 -37 -24 -87 -54 -112 -68 l-44 -24 -143 45 c-336 105 -375 118 -396 131 -25 17 -36 39 -28 63 7 23 75 67 139 90 28 11 92 34 141 54 50 19 131 50 180 68 149 57 358 138 395 153 19 8 64 25 100 39 36 13 118 45 182 70 65 25 120 46 124 46 3 0 20 6 37 14 18 8 122 49 232 91 110 42 227 87 260 100 72 28 234 91 255 98 27 10 50 7 79 -8z"/>
                                </g>
                            </svg>
                            Написать в Telegram
                        </a>
                        <a href="https://wa.me/79671381956">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M2370 5114 c-19 -2 -78 -9 -130 -15 -788 -90 -1517 -582 -1919 -1296 -351 -624 -416 -1405 -175 -2095 20 -57 67 -168 105 -247 l69 -144 -160 -573 c-88 -316 -160 -585 -160 -599 0 -69 76 -145 145 -145 23 0 185 44 941 256 l230 64 145 -69 c647 -310 1379 -333 2059 -64 626 247 1172 795 1418 1424 340 872 195 1834 -385 2554 -409 507 -974 829 -1633 930 -100 15 -472 28 -550 19z m395 -304 c583 -54 1146 -347 1517 -790 225 -268 393 -596 471 -921 49 -201 61 -309 61 -539 0 -230 -12 -338 -61 -539 -78 -325 -246 -653 -471 -921 -296 -354 -732 -624 -1183 -733 -201 -49 -309 -61 -539 -61 -388 0 -658 63 -1017 240 -100 49 -184 84 -202 84 -17 0 -240 -59 -497 -130 -257 -72 -469 -129 -471 -126 -2 2 55 213 127 469 71 257 130 480 130 498 0 19 -32 95 -83 197 -155 311 -229 593 -243 932 -45 1013 604 1936 1575 2244 282 89 592 123 886 96z"/>
                                    <path d="M1800 3939 c-30 -5 -89 -25 -130 -45 -64 -30 -92 -53 -195 -157 -133 -135 -187 -218 -236 -360 -38 -110 -49 -188 -49 -338 0 -240 54 -448 182 -701 213 -422 584 -785 1009 -988 223 -106 398 -150 624 -157 187 -6 301 12 432 69 117 51 187 101 309 222 91 91 118 125 147 186 70 144 70 286 0 430 -30 63 -57 96 -162 201 -101 101 -139 132 -191 156 -205 96 -402 61 -579 -103 -83 -77 -115 -93 -153 -74 -32 17 -506 489 -524 522 -24 46 -17 65 60 149 41 45 89 108 105 139 79 147 80 305 3 460 -35 70 -229 272 -307 319 -107 65 -231 90 -345 70z m169 -313 c14 -8 72 -61 128 -119 92 -94 102 -108 108 -152 12 -75 -7 -117 -92 -208 -116 -123 -159 -241 -135 -369 22 -118 42 -144 355 -455 314 -312 331 -325 451 -345 128 -21 243 21 358 131 80 76 126 101 183 101 60 0 96 -23 201 -131 81 -82 104 -113 114 -147 23 -86 2 -128 -127 -253 -108 -105 -178 -146 -294 -174 -83 -19 -264 -19 -368 0 -403 77 -830 379 -1106 785 -68 100 -164 293 -199 403 -79 245 -75 509 11 670 39 73 217 254 268 272 44 16 101 13 144 -9z"/>
                                </g>
                            </svg>
                            Написать в WhatsApp
                        </a>
                    </div>

                </div>
                <div class="col-lg-4">
                    <p class="subscribe">Подписаться на рассылку</p>
                    <form action="#" method="POST">
                        @csrf
                        <div class="input-group">
                            <div class="form-floating">
                                <input class="form-control" id="subscribe" type="email" name="subscriber" placeholder="E-mail">
                                <label class="form-label" for="subscribe">E-mail</label>
                            </div>
                            <button type="submit" class="btn btn-dark">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                    <path d="M19.7071 8.70711C20.0976 8.31658 20.0976 7.68342 19.7071 7.29289L13.3431 0.928933C12.9526 0.538409 12.3195 0.538409 11.9289 0.928933C11.5384 1.31946 11.5384 1.95262 11.9289 2.34315L17.5858 8L11.9289 13.6569C11.5384 14.0474 11.5384 14.6805 11.9289 15.0711C12.3195 15.4616 12.9526 15.4616 13.3431 15.0711L19.7071 8.70711ZM-8.74228e-08 9L19 9L19 7L8.74228e-08 7L-8.74228e-08 9Z" fill="white"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <x-side-cart />

    <div class="modal fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalTitle" style="display: none;" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
    <!-- Scripts -->
    <script src="{{ mix('js/app.js', 'build') }}"></script>
    @yield('scripts')
</body>
</html>
