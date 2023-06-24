@php /** @var App\Entities\User\User[] $users */ @endphp
@extends('layouts.admin')

@section('content')
    <h1 class="py-4">Пользователи</h1>
    <div class="d-flex flex-column">
        <div class="ms-auto">
            <form class="p-0 mb-3" method="POST" id="formActions" action="{{ route('admin.users.multi-action') }}">
                @csrf
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="remove" class="btn btn-lg btn-danger js-confirm" data-confirm="multi" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Удалить выбранных">
                        <span data-feather="trash-2"></span>
                    </button>
                </div>
            </form>
        </div>
        <table class="table table-striped table-bordered" id="userTable">
            <thead>
            <tr>
                <th style="text-align: center">
                    <input type="checkbox" class="form-check-input" name="select-all" style="cursor: pointer">
                </th>
                <th style="max-width: 100px">
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'id' ? '-id' : 'id']) }}">
                        ID @if(request('sort') && request('sort') == 'id') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-id') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'name' ? '-name' : 'name']) }}">
                        Имя @if(request('sort') && request('sort') == 'name') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-name') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'last_name' ? '-last_name' : 'last_name']) }}">
                        Фамилия @if(request('sort') && request('sort') == 'last_name') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-last_name') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'phone' ? '-phone' : 'phone']) }}">
                        Телефон @if(request('sort') && request('sort') == 'phone') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-phone') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'email' ? '-email' : 'email']) }}">
                        E-mail @if(request('sort') && request('sort') == 'email') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-email') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'role' ? '-role' : 'role']) }}">
                        Роль @if(request('sort') && request('sort') == 'role') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-role') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            <tr>
                <form action="?" name="search-users" method="GET" id="searchUsers"></form>
                <td>&nbsp;</td>
                <th style="max-width: 100px"><input form="searchUsers" type="text" name="id" class="form-control" aria-label="Искать по ID" value="{{ request('id') }}"></th>
                <th style="max-width: 200px"><input form="searchUsers" type="text" name="name" class="form-control" aria-label="Искать по имени" value="{{ request('name') }}"></th>
                <th style="max-width: 200px"><input form="searchUsers" type="text" name="last_name" class="form-control" aria-label="Искать фамилии" value="{{ request('last_name') }}"></th>
                <th style="max-width: 120px"><input form="searchUsers" type="text" name="phone" class="form-control" aria-label="Искать по номер телефона" value="{{ request('phone') }}"></th>
                <th><input form="searchUsers" type="text" name="email" class="form-control" aria-label="Искать по E-mail" value="{{ request('email') }}"></th>
                <th>
                    <select name="role" id="selectRole" class="js-choices" form="searchUsers">
                        <option value="">-= Выбрать роль =-</option>
                        @foreach(App\Entities\User\UserProfile::roleList() as $key => $role)
                            <option value="{{ $key }}"
                                @selected($key == request('role'))
                            >
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th>
                    <select name="status" id="selectStatus" class="js-choices" form="searchUsers">
                        <option value="">-= Выбрать статус =-</option>
                        @foreach(App\Entities\User\User::statusesList() as $key => $status)
                            <option value="{{ $key }}"
                                @selected($key == request('status'))
                            >
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td style="text-align: center">
                        <input form="formActions" type="checkbox" value="{{ $user->id }}" class="form-check-input" name="selected[]" style="cursor: pointer">
                    </td>
                    <td>{{ $user->id }}</td>
                    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td>
                    <td>{{ $user->userProfile->last_name }}</td>
                    <td>{{ $user->userProfile->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        <span class="badge {{ $user->userProfile::getCurrentRole($user->userProfile->role)['class'] }}">
                            {{ $user->userProfile::getCurrentRole($user->userProfile->role)['name'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $user::getBadgeStatus($user->status)['class'] }}">
                            {{ $user::getBadgeStatus($user->status)['name'] }}
                        </span>
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
