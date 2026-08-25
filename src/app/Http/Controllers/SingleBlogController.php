<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SingleBlogController extends Controller
{
    public function singleBlog()
    {
        return view('site.blog.single-blog');
    }
}
