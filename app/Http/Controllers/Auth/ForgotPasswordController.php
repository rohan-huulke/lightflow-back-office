<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function form($id)
    {
        $user = User::where('id', $id)->first();

        if(blank($user)) {
            return redirect()->route('login')->with('error', 'User does not exist.');
        }

        return view('auth.emailPassword', ['email' => $user->email]);
    }

    public function email(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if(blank($user)) {
            return redirect()->route('login')->with('error', 'User does not exist.');
        }

        $hasRequested = \DB::table('password_resets')->where('email', $user->email)->first();

        if(blank($hasRequested)) {
            return redirect()->route('login')->with('error', 'No password reset request.');
        }

        $user->hash = $hasRequested->token;

        MailHelper::sendMail($user, 'reset-password');

        return redirect()->route('login')->with('error', 'Reset password email has been sent.');
    }

    public function reset(Request $request)
    {
        $hasRequested = \DB::table('password_resets')->where('email', $request->input('email'))->where('token', $request->input('hash'))->first();

        if(blank($hasRequested)) {
            return redirect()->route('login')->with('error', 'Invalid request for password reset.');
        }

        $user = User::where('email', $request->input('email'))->first();

        if(blank($user)) {
            return redirect()->route('login')->with('error', 'User does not exist.');
        }

        return view('auth.resetPassword', ['email' => $user->email]);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'min:6|required_with:cpassword|same:cpassword',
            'cpassword' => 'min:6'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if(blank($user)) {
            return redirect()->route('login')->with('error', 'User does not exist.');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        \DB::table('password_resets')->where('email', $user->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been updated successfully.');
    }

    public function saveMessage(Request $request)
    {
        \DB::table('user_messages')->insert([
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s')
        ]);

        return redirect()->route('login')->with('success', 'Your message has been sent successfully.');
    }
}
