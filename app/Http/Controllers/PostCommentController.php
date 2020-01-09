<?php
namespace App\Http\Controllers;
use App\BlogPost;
// use App\Mail\CommentPosted;
use App\Http\Requests\StoreComment;
use App\Mail\CommentPostedMarkdown;
// use Illuminate\Support\Facades\Mail;
use App\Jobs\NotifyUsersPostWasCommented;
use App\Jobs\ThrottledMail;

class PostCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }
    public function store(BlogPost $post, StoreComment $request)
    {
        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id
        ]);
        // Mail::to($post->user)->send(
        //     new CommentPostedMarkdown($comment)
        // );
        
        // The queue method also works the same as 
        // Mail::to($post->user)->queue(
        //     new CommentPostedMarkdown($comment)
        // );

        // to run the job at a later time
        // $when = now()->addMinutes(1);

        // Mail::to($post->user)->later(
        //     $when,
        //     new CommentPostedMarkdown($comment)
        // );

        ThrottledMail::dispatch(new CommentPostedMarkdown($comment), $post->user)
            ->onQueue('low');
            
        NotifyUsersPostWasCommented::dispatch($comment)
            ->onQueue('high');

        return redirect()->back()
            ->withStatus('Comment was created!');
    }
}