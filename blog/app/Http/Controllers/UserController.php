<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //user validation:
        $userData = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'password' => ['required', 'confirmed', 'min:8'],
            'email' => ['required', 'email', Rule::unique('users', 'email')]
        ]);

        //hash the password:
        $userData['password'] = bcrypt($userData['password']);

        //storing it to the database:
        User::create($userData);

        return 'you are registered!!!!!!!!';
    }
}
