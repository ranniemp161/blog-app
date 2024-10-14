<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user)
    {

        //you cannot follow yourself:
        if ($user->id == auth()->user()->id) {
            return back()->with('failure', 'you cannot follow yourself.');
        }

        //you cannot follow the person you are already following:
        $existingFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if ($existingFollowing) {
            return back()->with('failure', 'you are already following this person');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'you are now following this person');
    }

    public function unfollow(User $user)
    {
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();

        return back()->with('success', 'unfollow successfully');
    }
}
