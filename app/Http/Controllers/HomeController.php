<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Job;
use Hash;
class HomeController extends Controller
{
    public function index() {
        
        $categories = Category::where('status', 1)->orderBy('name', 'asc')->take(8)->get();
        $featuredJobs = Job::where('status', 1)->orderBy('isFeatured', 'asc')->with('JobType')->take(8)->get();
        $latestJobs = Job::where('status', 1)->orderBy('created_at', 'desc')->with('JobType')->take(8)->get();
        
        return view('front.home',[
            'categories' => $categories,
            'featuredJobs' => $featuredJobs,
            'latestJobs' => $latestJobs
        ]);
    }

     public function contact() {

        return view('front.contact');
    }
}
