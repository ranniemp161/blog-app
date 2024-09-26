<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //show the post form:
    public function showPostForm()
    {
        return view('create-post');
    }

    //creating actual post:
    public function storeNewPost(Request $request)
    {
        $userData = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $userData['title'] = strip_tags($userData['title']);
        $userData['body'] = strip_tags($userData['body']);

        $userData['user_id'] = auth()->id();

        $newPost = Post::create($userData);

        return redirect("/post/{$newPost->id}")->with('success', 'New post created');
    }

    // Show the single post:
    public function viewSinglePost(Post $post)
    {
        $post['body'] = Str::markdown($post->body);

        return view('single-post', ['post' => $post]);
    }
}
