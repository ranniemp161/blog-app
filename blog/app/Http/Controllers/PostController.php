<?php

namespace App\Http\Controllers;

use App\Models\Post;
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



    //DELETE the post method:
    public function delete(Post $post)
    {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'deleted successfully');
    }

    // function to show the EDIT form:
    public function showEditForm(Post $post)
    {
        return view('edit-post', ['post' => $post]);
    }

    //UPDATE the post:
    public function updatePost(Post $post, Request $request)
    {
        $userData = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $userData['title'] = strip_tags($userData['title']);
        $userData['body'] = strip_tags($userData['body']);

        $post->update($userData);

        return back()->with('success', 'post updated');
    }

    //For Searching Qeuries:

    public function search($term)
    {
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }
}
