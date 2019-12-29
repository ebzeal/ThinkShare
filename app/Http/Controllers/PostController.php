<?php

namespace App\Http\Controllers;

use App\BlogPost;

use Illuminate\Http\Request;
use App\Http\Requests\StorePost;

use Illuminate\Support\Facades\Gate;


class PostController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(BlogPost::all());
        return  view(
            'posts.index', 
            ['posts' => BlogPost::withCount('comments')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePost $request)
    {

        $validatedData = $request->validated();

        $blogPost = BlogPost::create($validatedData);

        // $blogPost = new BlogPost();
        // $blogPost->title = $request->input('title');
        // $blogPost->content = $request->input('content');

        // $blogPost->save();

        $request->session()->flash('status', 'Blog post was created');

        return redirect()->route('posts.show', ['post' => $blogPost->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('posts.show', ['post' => BlogPost::with('comments')->findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        $this->authorize($post);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You cannot edit another author's article");
        // };

        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $this->authorize($post);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You cannot edit another author's article");
        // };

        $validatedData = $request->validated();

        $post->fill($validatedData);
        $post->save();
        $request->session()->flash('status', 'Blog post was updated');

        return redirect()->route('posts.show', ['post' => $post->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You cannot delete another author's article");
        // };

        $this->authorize($post);


        $post->delete();

        // BlogPost::destroy($id);
        
        $request->session()->flash('status', 'Blog post was deleted');

        return redirect()->route('posts.index');
    }
}
