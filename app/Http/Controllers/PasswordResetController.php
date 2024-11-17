<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFunctionsHelper;
use App\Models\User;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Nette\Utils\Random;

class PasswordResetController extends Controller
{
    public function generateResetLink(Request $request)
    {

        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Email address of the user
        $email = $request->input('email');

        // Generate a new token
        $token = Str::random(60);

        // Insert the token into the password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Generate the reset password link
        $link = URL::to('/') . '/password/reset/' . $token;
        dd($link);
        // Return the link as a response
        return response()->json(['reset_link' => $link], 200);
    }

    public function usersEmailList()
    {
        $users = User::get();
        echo '<table>';
        echo '<tr>';
        echo '<td>iboType</td>';
        echo '<td>first_name</td>';
        echo '<td>last_name</td>';
        echo '<td>email</td>';
        echo '<td>mobile_number</td>';
        echo '<td>card_id</td>';
        echo '</tr>';
        foreach ($users as $user) {


            echo '<tr>';
            echo '<td>' . $user->iboType . '.</td>';
            echo '<td>' . $user->first_name . '.</td>';
            echo '<td>' . $user->last_name . '.</td>';
            echo '<td>' . $user->email . '.</td>';
            echo '<td>' . $user->mobile_number . '.</td>';
            echo '<td>' . $user->card_id . '.</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function requestPasswordReset(Request $request)
    {
        $email = $request->email;
        $userInfo = $this->userInfo('email', $email);

        $flag = 5;
        if ($userInfo) {
            $user = User::where('email', $userInfo->email)->first();

            if (blank($user)) {
                $user = new User();
                $user->s_id = $userInfo->id;
                $user->card_id = $userInfo->card_id;
                $user->iboType = $userInfo->iboType;
                $user->first_name = $userInfo->firstname;
                $user->last_name = $userInfo->lastname;
                $user->email = $userInfo->email;
                $user->mobile_number = $userInfo->mobile;
                $user->password = Hash::make($userInfo->card_id);

                if ($userInfo->iboType == 1) {
                    $role_id = 2;
                } else if ($userInfo->iboType == 2) {
                    $role_id = 3;
                } else if ($userInfo->iboType == 3) {
                    $role_id = 4;
                } else {
                    $role_id = 5;
                }

                $user->role_id = $role_id;
                $user->status = $userInfo->user_index->status;
                $user->save();

                \DB::table('model_has_roles')->insert([
                    'role_id' => $role_id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user['id']
                ]);

                //$this->saveAddress($user->id, $userInfo);


                $key = config('app.key');

                if (Str::startsWith($key, 'base64:')) {
                    $key = base64_decode(substr($key, 7));
                }

                $token = hash_hmac('sha256', Str::random(40), $key);
                $dbToken = Hash::make($token);

                \DB::table('password_resets')->insert([
                    'email' => $userInfo->email,
                    'token' => $dbToken,
                    'created_at' => date('Y-m-d h:i:s')
                ]);
            }

            if ($user->iboType == 1) {
                $flag = 1;
            }
            if ($user->iboType == 2) {
                $flag = 2;
            }
        }


        // Example logic to determine the response
        if ($flag == 1) {
            return response()->json(['status' => 1]); // Requires file upload
        } elseif ($flag == 2) {
            $randomPassword = $this->generateRandomPassword();
            $user->password = Hash::make($randomPassword);
            $user->save();
            return response()->json(['status' => 2, 'password' => $randomPassword]); // Provide password directly
        } else {
            return response()->json(['status' => 5]); // No account match
        }
    }

    public function uploadFile(Request $request)
    {
        // Validate the incoming request to ensure the 'email' and 'document' fields are present
        $request->validate([
            'email' => 'required|email',
            'document' => 'required|file'
        ]);

        // Find the user by email
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return response()->json(['status' => 5, 'message' => 'Account non trovato, contattaci per creare un account']);
        }

        // Handle the document upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'document-' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/signup/documents'), $fileName);

            // Save the document path to the UserDocuments model
            $path = '/images/signup/documents/';
            $userDocuments = UserDocuments::where('user_id', $user->id)->first();
            if (blank($userDocuments)) {
                $userDocuments = new UserDocuments;
                $userDocuments->user_id = $user->id;
            }

            $userDocuments->document = $path . $fileName;
            $userDocuments->save();

            // Generate a random password and update the user
            $randomPassword = $this->generateRandomPassword();
            $user->password = Hash::make($randomPassword);
            $user->save();

            return response()->json(['status' => 1, 'message' => 'File caricato con successo', 'password' => $randomPassword]);
        }

        return response()->json(['status' => 0, 'message' => 'File non caricato correttamente, riprova']);
    }




    protected function userInfo($column, $value)
    {
        $response = ApiFunctionsHelper::getRequestResult('post', config('constants')['info']['userInfoForLogin'], [$column => $value]);
        $data = json_decode($response);

        if (!isset($data->error)) {
            return $data;
        }

        return false;
    }


    function generateRandomPassword($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }
}
