<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller{
    public function postCreatePost(Request $request){

        $this->validate($request, [
            'body' => 'required|max:1000'
        ]);

        $post = new Post();
        $post->body = $request['body'];

        $message = 'There was an error!';
        if($request->user()->posts()->save($post)){
            $message = 'Post was successfully created!';
        }
        return redirect()->route('dashboard')->with(['message' => $message]);
    }

    public function getDashboard(){
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('dashboard', ['posts' => $posts]);
    }

    public function getDeletePost($post_id){

        $post = Post::where('id', $post_id)->first();
//        $post = Post::find($post_id)->first();

        if(Auth::user() != $post->user){
            return redirect()->back();
        }
        $post->delete();
        return redirect()->route('dashboard')->with(['message' => 'Successfully deleted!']);

    }

    public function postEditPost(Request $request){

        $this->validate($request, [
            'body' => 'required'
        ]);

        $post = Post::find($request['postId']);

        if(Auth::user() != $post->user){
            return redirect()->back();
        }
        $post->body = $request['body'];
        $post->update();

        return response()->json(['new_body' => $post->body]);
    }

    public function postLikePost(Request $request){
        $post_id = $request['post_id'];
        $is_like = $request['isLike'] === 'true';
        $update = false;
        $post = Post::find($post_id);
        if(!$post){
            return response()->json(['message' => "There are no posts with such id!", 'post' => $post]);
        }
        $user = Auth::user();
        $like = $user->likes()->where('post_id', $post_id)->first();
        if($like){
            $already_like = $like->like;
            $update = true;
            if($already_like == $is_like){
                $like->delete();
                return response()->json(['message' => "Your like was undone!"]);
            }
        }else{
            $like = new Like();
        }

        $like->like = $is_like;
        $like->user_id = $user->id;
        $like->post_id = $post_id;

        if($update){
            $like->update();
        }else{
            $like->save();
        }
        return response()->json(['message' => "Your vote successfully!"]);
    }
}