<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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

    private function sharedData($user)
    {
        $currentlyFollwing = 0;

        if (auth()->check()) {
            $currentlyFollwing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        View::share('sharedData', [
            'username' => $user->username,
            'avatar' => $user->avatar,
            'postCount' => $user->posts()->count(),
            'currentlyFollowed' => $currentlyFollwing

        ]);
    }

    //show list of posts by the user:
    public function profile(User $user)
    {

        $this->sharedData($user);
        return View(
            'profile-post',
            [

                'posts' => $user->posts()->latest()->get(),
            ]
        );
    }
    //show list of the user's followers
    public function profileFollowers(User $user)
    {
        $this->sharedData($user);

        return View(
            'profile-following',
            [

                'posts' => $user->posts()->latest()->get()

            ]
        );
    }
    //show list of the user's following
    public function profileFollowing(User $user)
    {
        $this->sharedData($user);
        return View(
            'profile-followers',
            [

                'posts' => $user->posts()->latest()->get()

            ]
        );
    }

    //avatar-form method:

    public function showAvatar()
    {
        return view('avatar-form');
    }

    //store the avatar:

    public function storeAvatar(Request $request)
    {
        // Validate the uploaded file to ensure it's an image and within the size limit
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Generate a unique filename for the avatar
        $filename = sprintf('%d-%s.jpg', $user->id, uniqid());

        // Initialize the ImageManager instance and process the uploaded avatar
        $imageManager = new ImageManager(new Driver());
        $avatarImage = $imageManager->read($request->file('avatar'));

        // Resize the image to 120x120 pixels and convert it to JPEG format
        $resizedAvatar = $avatarImage->cover(120, 120)->toJpeg();

        // Store the processed image in the specified directory
        Storage::put("public/avatars/{$filename}", $resizedAvatar);

        // Update the user's avatar filename in the database
        $oldAvatar = $user->avatar;
        $user->avatar = $filename;
        $user->save(); // @phpstan-ignore-line;

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }



        return back()->with('success', 'Avatar uploaded successfully');
    }
}
