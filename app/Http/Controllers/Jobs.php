<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\JobNotificationEmail;
use App\Models\User;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\SaveJob;
use Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class Jobs extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $jobTypes = JobType::where('status', 1)->get();

        $jobs = Job::where('status', 1);

        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' .$request->keyword. '%')->orWhere('keywords', 'like', '%' .$request->keyword. '%');
            });
        }

        if (!empty($request->location)) {
            $jobs = $jobs->where('location', 'like', '%' .$request->location. '%');
        }

        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', $request->category);
        }

        $jobTypeArray = [];

        if (!empty($request->job_Type)) {
            $jobTypeArray = explode(',', $request->job_Type);
            $jobs = $jobs->whereIn('job_type_id', $jobTypeArray);
        }

        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience', $request->experience);
        }

        $jobs = $jobs->with(['JobType', 'category']);

        if ($request->sort == 0) {
            $jobs =$jobs->orderBy('created_at', 'ASC');
        }else{
             $jobs =$jobs->orderBy('created_at', 'DESC');
        }
      
        $jobs = $jobs->paginate(5);

        return view('front.jobs', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray,
        ]);
    }

    public function createJob()
    {
        $categories = Category::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        $jobtypes = JobType::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        return view('front.job.create', [
            'categories' => $categories,
            'jobtypes' => $jobtypes,
        ]);
    }

    public function saveJob(Request $request)
    {
        $rules = [
            'title' => 'required',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'experience' => 'required',
            'company_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $job = new Job();
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualification = $request->qualifications;
            $job->keywords = $request->title;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;
            $job->save();
            session()->flash('success', 'Jobe Added Successfully');

            return response()->json([
                'status' => true,
                'errors' => [],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function myJob()
    {
        $jobs = Job::where('user_id', Auth::user()->id)
            ->with('jobType')
            ->orderBy('created_at', 'DESC')
            ->paginate(5);
        return view('front.job.myjobs', [
            'jobs' => $jobs,
        ]);
    }

    public function editjob(Request $request, $id)
    {
        $categories = Category::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        $jobtypes = JobType::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();

        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $id,
        ])->first();
        if ($job == null) {
            abort(404);
        }

        return view('front.job.edit', [
            'categories' => $categories,
            'jobtypes' => $jobtypes,
            'job' => $job,
        ]);
    }

    public function updateJob(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'experience' => 'required',
            'company_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $job = Job::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualification = $request->qualifications;
            $job->keywords = $request->title;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;
            $job->save();
            session()->flash('success', 'Jobe Updated Successfully');

            return response()->json([
                'status' => true,
                'errors' => [],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function detail($id)
    {
    $job = Job::where(['id' => $id, 'status' => 1])->with(['JobType', 'category'])->first();

    if ($job == null) {
        abort(404); // Fixed typo from abrot(404) to abort(404)
    }

    return view('front.jobDetail', ['job' => $job]);
    }

public function deleteJob(Request $request)
{
    try {
        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId,
        ])->firstOrFail();

        $job->delete();

        session()->flash('success', 'Job successfully deleted.');

        return response()->json([
            'status' => true,
            'message' => 'Job successfully deleted.',
        ]);
    } catch (ModelNotFoundException $e) {
        session()->flash('error', 'Job not found or already deleted.');

        return response()->json([
            'status' => false,
            'message' => 'Job not found or already deleted.',
        ]);
    }
}
    public function saveJobs(Request $request)
{
    $id = $request->id;
    $job = Job::find($id);

    if ($job == null) {
        session()->flash('error', 'Job not found'); // Fix: Use flash instead of session()
        return response()->json([
            'status' => false,
        ]);
    } 
    
    $count = JobApplication::where([ // Fix: Use JobApplication instead of saveJob
        'user_id' => Auth::user()->id,
        'job_id' => $id
    ])->count();

    if ($count > 0) {
        session()->flash('error', 'You already applied on job');
         return response()->json([
            'status' => false,
        ]);
    }

    $saveJob = new SaveJob;
    $saveJob->job_id = $id;
    $saveJob->user_id = Auth::user()->id;
    $saveJob->save();

     session()->flash('success', 'You have successfully');
         return response()->json([
            'status' => true,
        ]);
 }   

}
