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
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class AccountController extends Controller
{
    public function registration()
    {
        return view("front.account.registration");
    }

    public function processRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:5|same:confirm_password",
            "confirm_password" => "required",
        ]);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash("success", "You have Register Successfully");

            return response()->json([
                "status" => true,
                "errors" => [],
            ]);
        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function login()
    {
        // Logic for login
        return view("front.account.login");
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->passes()) {
            if (
                Auth::attempt([
                    "email" => $request->email,
                    "password" => $request->password,
                ])
            ) {
                return redirect()->route("account.profile");
            } else {
                return redirect()
                    ->route("account.login")
                    ->with("error", "Either Email/password is incorrect");
            }
        } else {
            return redirect()
                ->route("account.login")
                ->withErrors($validator)
                ->withInput($request->only("email"));
        }
    }

    public function profile()
    {
        // Logic for login
        $id = Auth::user()->id;
        $user = User::find($id);

        return view("front.account.profile", ["user" => $user]);
    }

    public function updateProfile(Request $request)
    {
        $id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email," . $id,
            "mobile" => "required",
            "designation" => "required",
        ]);

        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash("success", "You have updated successfully");

            return response()->json([
                "status" => true,
                "errors" => [],
            ]);
        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }
    }

    public function updateProfilePic(Request $request)
    {
        $id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            "image" => "required|image",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors(),
            ]);
        }

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = $id . "_" . time() . "." . $ext;

        // Move the original image
        $image->move(public_path("/profile_pic/"), $imageName);

        // Create Thumb
        $sourcePath = public_path("/profile_pic/" . $imageName);
        Image::make($sourcePath)
            ->fit(150, 150)
            ->save(public_path("/profile_pic/thumb/" . $imageName));

        // Delete old images
        File::delete(public_path("/profile_pic/thumb/" . Auth::user()->image));
        File::delete(public_path("/profile_pic/" . Auth::user()->image));

        // Update user's image
        User::where("id", $id)->update(["image" => $imageName]);

        session()->flash("success", "Profile picture updated successfully");

        return response()->json([
            "status" => true,
            "errors" => [],
        ]);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route("account.login");
    }
}
