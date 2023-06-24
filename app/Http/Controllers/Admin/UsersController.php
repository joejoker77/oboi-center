<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{

    public function index(Request $request):View
    {
        $query = User::with('userProfile');
        $query = $this->queryParams($request, $query);
        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }




    private function queryParams(Request $request, $query)
    {
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('name'))) {
            $query->where('name', $value);
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
