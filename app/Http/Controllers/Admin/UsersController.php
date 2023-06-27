<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Entities\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{

    public function index(Request $request):View
    {
        $query = User::with('userProfile');
        $query = $this->queryParams($request, $query);
        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort(404);
    }

    public function show(User $user):View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user):View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user):RedirectResponse
    {
        if ($request->get('status') && $request->get('status') !== $user->status) {
            $user->update($request->only('status'));
        }
        if ($request->get('role') && $request->get('role') !== $user->userProfile->role) {
            $user->userProfile->update($request->only('role'));
        }
        return redirect()->route('admin.users.index')->with('success', 'Данные пользователя отредактированы.');
    }

    public function destroy(User $user):RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь удален');
    }

    public function multiDelete(Request $request)
    {
        if (!empty($request->get('selected'))) {

            if (in_array(Auth::user()->id, $request->get('selected'))) {
                return back()->with('warning', 'Внимание, вы удаляете свою собственную учетную запись.');
            }

            foreach ($request->get('selected') as $userId) {
                $user = User::find($userId);
                $user->delete();
            }

            return back()->with('success', 'Все выбранные пользователи были удалены');
        } else {
            return back()->with('warning', 'Внимание! Не выбран ни один пользователь');
        }
    }

    private function queryParams(Request $request, $query)
    {
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('user'))) {
            $users = User::where('name', $value)->pluck('id')->toArray();
            if (!empty($users)) {
                $query->whereIn('user_id', $users);
            }
        }
        if (!empty($value = $request->get('last_name'))) {
            $query->where('last_name', $value);
        }
        if (!empty($value = $request->get('phone'))) {
            $query->whereHas('userProfile', function ($q) use ($value) {
                $q->where('phone', $value);
            });
        }
        if (!empty($value = $request->get('email'))) {
            $query->where('email', $value);
        }
        if (!empty($value = $request->get('role'))) {
            $query->whereHas('userProfile', function ($q) use ($value) {
                $q->where('role', $value);
            });
        }
        if (!empty($value = $request->get('status'))) {
            $query->where('status', $value);
        }
        if(!empty($value = $request->get('sort'))) {

            if ($value == 'role') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.role', 'ASC')->select('users.*');
            } else if ($value == '-role') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.role', 'DESC')->select('users.*');
            } else if ($value == 'phone') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.phone', 'ASC')->select('users.*');
            } else if ($value == '-phone') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.phone', 'DESC')->select('users.*');
            } else if ($value == 'last_name') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.last_name', 'ASC')->select('users.*');
            } else if($value == '-last_name') {
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')->orderBy('user_profiles.last_name', 'DESC')->select('users.*');
            } else if ($value[0] == '-') {
                $value = str_replace('-', '', $value);
                $query->orderBy($value, 'DESC');
            } else {
                $query->orderBy($value);
            }
        } else {
            $query->orderBy('id');
        }
        return $query;
    }
}
