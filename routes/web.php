<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Jobs;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [Jobs::class, 'index'])->name('jobs');
Route::get('/jobs/jobDetail/{id}', [Jobs::class, 'detail'])->name('jobDetail');
Route::post('/applyJob', [Jobs::class, 'applyJob'])->name('applyJob');
Route::post('/saveJob', [Jobs::class, 'saveJobs'])->name('saveJobs');


Route::group(['account'], function(){
	//Guest Routes
	Route::group(['middleware' => 'guest'], function(){
		Route::get('/register', [AccountController::class, 'registration'])->name('account.registration');
		Route::post('/process-register', [AccountController::class, 'processRegistration'])->name('account.processRegistration');
		Route::get('/login', [AccountController::class, 'login'])->name('account.login');
		Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
	});

	//Authenticate Routes

	Route::group(['middleware' => 'auth'], function(){
		Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
		Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
		Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
		Route::post('/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');

		Route::get('/job/create-job', [Jobs::class, 'createJob'])->name('job.createJob');
		Route::post('/save-job', [Jobs::class, 'saveJob'])->name('job.saveJob');
		Route::get('/job/myjobs', [Jobs::class, 'myJob'])->name('job.myjobs');
		Route::get('/job/myjobs/edit/{jobId}', [Jobs::class, 'editJob'])->name('job.editJob');
		Route::post('/update-job/{jobId}', [Jobs::class, 'updateJob'])->name('job.updateJob');
		Route::post('/delete-job', [Jobs::class, 'deleteJob'])->name('job.deleteJob');

		Route::get('/job/my-job-application', [Jobs::class, 'myJobApplication'])->name('job.myJobApplication');
		Route::post('/remove-job-application', [Jobs::class, 'removeJob'])->name('job.removeJob');

	});

});