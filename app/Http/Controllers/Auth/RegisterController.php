<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiFunctionsHelper;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        $response = ApiFunctionsHelper::getRequestResult('post', config('constants')['info']['userInfo'], ['email' => $data['email']]);

        $data = json_decode($response);

        $user =  User::create([
            's_id' => $data->id,
            'card_id' => $data->card_id,
            'iboType' => $data->iboType,
            'first_name' => $data->firstname,
            'last_name' => $data->lastname,
            'email' => $data->email,
            'mobile_number' => $data->mobile,
            'role_id' => 2,
            'status' => 1,
            'password' => Hash::make($data->card_id),
        ]);

        \DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => $user['id']
        ]);

        return $user;
    }
}
