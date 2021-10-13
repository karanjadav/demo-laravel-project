<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $user = Auth::user();
            $data = Post::with('user')->latest()->get();
            if(!$user->hasRole('admin')) {
                $data = $user->posts;
            }
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        $url = route('post.destroy', $row->id);
                        $btn = "<a href='javascript:void(0)' class='edit-post btn btn-success btn-sm' data-url='" . route('post.update', $row->id)."'
                        data-title='$row->title' data-content='$row->content' data-toggle='modal' data-target='#editModal'>Edit</a>
                        <a href='$url' class='edit btn btn-danger btn-sm'>Delete</a>";

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
       return view('posts');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request) {
    $input = $request->except('image');
    try{
        $post = Auth::user()->posts()->create($input);
        $this->uploadImage($post, $request);
        toastr()->success('Post created succesfully');

        return redirect()->back();
    } catch (Exception $e) {
        Log::error($e);
        toastr()->error($e->getMessage());
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadImage($post, $request) {
        if($request->has('image')) {
            if($post->image) {
                if(Storage::exists($post->image)) {
                    Storage::delete($post->image);
                }
            }
            $extension = $request->image->extension();
            $imageName = $request->title .'-'. time() .'.'. $extension;
            if($url = $request->image->storeAs('/public', $imageName)) {
                $post->image = $url;
                $post->save();
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {
    $input = $request->except('image');
    try{
        $post->update($input);
        $this->uploadImage($post, $request);
        toastr()->success('Post updated succesfully');

        return redirect()->back();
    } catch (Exception $e) {
        Log::error($e);
        toastr()->error($e->getMessage());
    }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        if($post->image) {
            if(Storage::exists($post->image)) {
                Storage::delete($post->image);
            }
        }
        $post->delete();
        toastr()->success('Post deleted succesfully');

        return redirect()->back();
    }
}
