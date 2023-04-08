@extends('layouts.admin')
@php $active_menu = null @endphp
@section('content')
    <div class="row pt-3">
        <div class="col-md-5">
            <button type="button" class="btn btn-success btn-sm mb-3" id="jsCreateMenu">Создать меню</button>
            @if(!$menus->isEmpty())
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Системное имя</th>
                            <th>Отображение заголовка</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($menus as $menu)
                        @if(request('menuActive') and $menu->id == (int)request('menu'))
                            @php $active_menu = $menu @endphp
                        @endif
                        <tr>
                            <td>{{ $menu->title }}</td>
                            <td>{{ $menu->handler }}</td>
                            <td>@if($menu->show_title) Да @else Нет @endif</td>
                            <td>
                                <a href="#" class="list-inline-item edit-menu"
                                   data-menu-id="{{ $menu->id }}" data-bs-toggle="tooltip"
                                   data-bs-placement="bottom"
                                   data-bs-title="Редактировать"
                                >
                                    <span data-feather="edit"></span>
                                </a>|
                                <a href="#" class="list-inline-item add-items"
                                   data-menu-id="{{ $menu->id }}" data-bs-toggle="tooltip"
                                   data-bs-placement="bottom"
                                   data-bs-title="Добавить пункты меню"
                                >
                                    <span data-feather="list"></span>
                                </a>|

                                <form method="POST" class="list-inline-item js-confirm"
                                      action="{{ route('admin.navigations.destroy', $menu) }}"
                                      data-bs-toggle="tooltip" data-bs-placement="bottom"
                                      data-bs-title="Удалить меню"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn p-0 align-baseline js-confirm text-danger" type="submit"><span data-feather="trash-2"></span></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="col-md-7">
            <menu-manage>
                <div class="menu-items-container">
                    @if(request('menuActive'))
                        @include('admin.navigations.partials.form-menu-items', ['menu' => $active_menu])
                    @endif
                </div>
            </menu-manage>
            <script>
                let inputId = ''
                function fmSetLink ($url) {
                    document.getElementById(inputId).value = $url;
                }
            </script>
        </div>
    </div>
@endsection
