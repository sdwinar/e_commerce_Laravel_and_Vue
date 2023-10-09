<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserController extends Controller
{
    use HasFactory;
    public function index()
    {
        return User::latest()->get();
    }

    public function store()
    {
        // request()->validate([
        //     'name' => 'required',
        //     'email' => 'required|unique:users,email',
        //     'password' => 'required|min:8',
        // ]);

        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);
    }
}
