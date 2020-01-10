<?php

namespace App\Http\Controllers;

use App\User;
use App\Image;

use App\BlogPost;
use App\Services\Counter;

use Illuminate\Http\Request;
use App\Events\BlogPostPosted;
use App\Http\Requests\StorePost;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    private $counter;
    public function __construct(Counter $counter){
        $this->middleware('auth')->except(['index', 'show']);
        $this->counter = $counter;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(BlogPost::all());

        return view(
            'posts.index',
            [
                'posts' => BlogPost::latestWithRelations()->get(),
            ]
        );
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

        $validatedData['user_id'] = $request->user()->id;

        $blogPost = BlogPost::create($validatedData);

        // $blogPost = new BlogPost();
        // $blogPost->title = $request->input('title');
        // $blogPost->content = $request->input('content');

        // $blogPost->save();

        
        // $hasFile = $request->hasFile('thumbnail');
        // dump($hasFile);
        // if ($hasFile) {
        //     $file = $request->file('thumbnail');
        //     dump($file);
        //     dump($file->getClientMimeType());
        //     dump($file->getClientOriginalExtension());
        //     // dump($file->store('thumbails'));  // This line is a shorter version of the commented line below, they do the same thing
        //     // dump(Storage::disk('public')->putFile('thumbails', $file)); 
        //     // dump($file->storeAs('thumbails', $blogPost->id . '.'. $file->guessExtension())); // this line helps you store files with specified name
        //     // dump(Storage::disk('local')->putFileAs('thumbails', $file, $blogPost->id . '.' . $file->guessExtension())); // does the same as the line above, you can specify the disk space here

        //     dump($file->store('thumbails'));
        //     dump(Storage::disk('public')->putFile('thumbails', $file));
        //     $name1 = $file->storeAs('thumbails', $blogPost->id . '.'. $file->guessExtension());
        //     $name2 = Storage::disk('local')->putFileAs('thumbails', $file, $blogPost->id . '.' . $file->guessExtension());
        //     dump(Storage::url($name1));
        //     dump(Storage::disk('local')->url($name2));
        // }
        // die;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');
            $blogPost->image()->save(
                Image::make(['path' => $path])
            );
        }

        event(new BlogPostPosted($blogPost));

        $request->session()->flash('status', 'Blog post was created!');
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
        $blogPost = Cache::tags(['blog-post'])->remember("blog-post-{$id}", 60, function() use($id) {
            return BlogPost::with('comments', 'tags', 'user', 'comments.user')
                ->findOrFail($id);
        });

        $counter = resolve(Counter::class);
        
        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $this->counter->increment("blog-post-{$id}", ['blog-post']),
        ]);
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
        

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');
            if ($post->image) {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            } else {
                $post->image()->save(
                    Image::make(['path' => $path])
                );
            }
        }


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
