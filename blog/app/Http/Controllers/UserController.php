<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Function registration:

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
        $user = User::create($userData);
        auth()->login($user);
        return redirect('/')->with('Account Created.');
    }


    // Function for login:
    public function login(Request $request)
    {
        $userData = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $userData['loginusername'], 'password' => $userData['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You successfully log-in.');
        } else {
            return redirect('/')->with('failure', 'invalid credentials');
        }
    }

    // Function for log-out:
    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You successfully log-out.');
    }


    // Function for the user who log-in successfully:
    public function homefeed()
    {
        if (auth()->check()) {
            return view('homepageFeed');
        } else {
            return view('homePage');
        }
    }

    //show list of posts by the user:
    public function profile(User $user)
    {

        return View(
            'profile-post',
            [
                'username' => $user->username,
                'posts' => $user->posts()->latest()->get(),
                'postCount' => $user->posts()->count()
            ]
        );
    }
}
