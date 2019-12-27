<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home(){
        // dd(Auth::check());
        // dd(Auth::id());
        // dd(Auth::user());
        return view('home');
    }

    public function contact(){
        return view('contact');
    }

    public function blogPost($id, $welcome = 1) {
            $pages = [
                1 => [
                    'title' => 'Hello Page 1'
                ],
                2 => [
                    'title' => 'Hello Page 2'
                ],
            ];
            $welcomes = [1 => '<b>Hello</b> ', 2=>'Welcome to '];
        
            return view('blog-post', [
                'data' => $pages[$id], 
                'welcome' => $welcomes[$welcome]
                ]);
    }
}
