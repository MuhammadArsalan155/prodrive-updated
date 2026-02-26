<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function openProfile(){
        $user = Auth::user();
        return view('profile.add',compact('user'));
    }
    public function updateProfile(Request $request){
        $user = Auth::user();
        if($request->password != null){
            $status = User::where('id',$user->id)->update([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);
            if($status){
                toastr()->success('Profile updated successfully!', 'Completed!');
                return redirect()->back();
            }else{
                toastr()->error('Updation Failed, Please try again later', 'Unknown!');
                return redirect()->back();
            }
        }else{
            $status = User::where('id',$user->id)->update([
                "name" => $request->name,
                "email" => $request->email
            ]);
            if($status){
                toastr()->success('Profile updated successfully!', 'Completed!');
                return redirect()->back();
            }else{
                toastr()->error('Updation Failed, Please try again later', 'Unknown!');
                return redirect()->back();
            }
        }
    }

}
