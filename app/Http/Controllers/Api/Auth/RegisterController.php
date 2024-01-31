<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = '';
        }
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));
        $validate = Validator::make($data, [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:users',
            'mobile' => 'required|string|max:50|unique:users',
            'password' => ['required','confirmed',$password_validation],
            'username' => 'required|alpha_num|unique:users|min:6',
            'captcha' => 'sometimes',
            'mobile_code' => 'sometimes|in:'.$mobileCodes,
            'country_code' => 'sometimes|in:'.$countryCodes,
            'country' => 'sometimes|in:'.$countries,
            'agree' => $agree
        ]);
        return $validate;
    }


    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        
        $exist = User::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            $response[] = 'The mobile number already exists';
            return response()->json([
                'code'=>409,
                'status'=>'conflict',
                'message'=>['error'=>$response],
            ]);
        }
        

        $user = $this->create($request->all());

        $response['access_token'] =  $user->createToken('auth_token')->plainTextToken;
        $response['user'] = $user;
        $response['token_type'] = 'Bearer';
        $notify[] = 'Registration successfull';
        return response()->json([
            'code'=>202,
            'status'=>'created',
            'message'=>['success'=>$notify],
            'data'=>$response
        ]);

    }

// My register Controller



public function sellerregister(Request $request)
    {
       
        $credentials = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required|string|regex:/^[0-9]+$/|digits_between:10,15',
            'username' => 'required|string|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]+$/|unique:users',
            'password' => 'required|string|confirmed',
            
        ]);

        $credentials['role'] = $request->input('role', 'individual');

        $existingUser = User::where('username', $credentials['username'])
        ->orWhere('email', $credentials['email'])
        ->orWhere('phonenumber', $credentials['phonenumber'])
        ->first();

    if ($existingUser) {
        $existingFields = [];
        if ($existingUser->username === $credentials['username']) {
            $existingFields[] = 'username';
        }
        if ($existingUser->email === $credentials['email']) {
            $existingFields[] = 'email';
        }
        if ($existingUser->phone === $credentials['phonenumber']) {
            $existingFields[] = 'phonenumber';
        }

        return response()->json(['message' => 'Email, username, or phonenumber already exists']);
    }

}


// the end-----------------------------------------------------------
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $general = GeneralSetting::first();


        $referBy = @$data['reference'];
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

        //User Create
        $user = new User();
        $user->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $user->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        $user->username = trim($data['username']);
        $user->ref_by = $referUser ? $referUser->id : 0;
        $user->country_code = $data['country_code'];
        $user->mobile = $data['mobile_code'].$data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail',$user->id);
        $adminNotification->save();


        //Login Log Create
        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;
        
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();


        return $user;
    }
    
    
    
    
    
    
    
    
    
    protected function registerValidator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));
        $validate = Validator::make($data, [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:merchants',
            'mobile' => 'required|string|max:50|unique:merchants',
            'password' => ['required','confirmed',$password_validation],
            'username' => 'required|alpha_num|unique:merchants|min:6',
            'captcha' => 'sometimes|required',
            'type'=> 'required|string|max:50',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
            'agree' => $agree
        ]);

        return response()->json($validate->messages(), 422); // Return validation messages if validation fails
    }

    public function registerMerchant(Request $request)
    {
        $this->registerValidator($request->all());
        $exist = Merchant::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        if (isset($request->captcha)) {
            if (!captchaVerify($request->captcha, $request->captcha_secret)) {
                $notify[] = ['error', "Invalid captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }


        event(new Registered($merchant = $this->create($request->all())));

        Auth::guard('merchant')->login($merchant);

        return response()->json(['message' => 'Registration successful']);
    }

public function createMerchant(Request $request)
    {
         $general = GeneralSetting::first();

        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = $general->agree ? 'required' : 'nullable';

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));

        $validator = Validator::make($request->all(), [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:merchants',
            'mobile' => 'required|string|max:50|unique:merchants',
            'password' => ['required', 'confirmed', $passwordValidation],
            'username' => 'required|alpha_num|unique:merchants|min:6',
            'captcha' => 'sometimes|required',
            'type' => 'required|string|max:50',
            'mobile_code' => 'required|in:' . $mobileCodes,
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'agree' => $agree,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Continue with creating the Merchant if validation passes
        $merchant = new Merchant();
        $merchant->firstname = $request->input('firstname');
        $merchant->lastname = $request->input('lastname');
        $merchant->email = strtolower(trim($request->input('email')));
        $merchant->password = Hash::make($request->input('password'));
        $merchant->username = trim($request->input('username'));
        $merchant->type = $request->input('type');
        $merchant->country_code = $request->input('country_code');
        $merchant->mobile = $request->input('mobile_code') . $request->input('mobile');
        $merchant->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => $request->input('country') ?? null,
            'city' => '',
        ];
        $merchant->status = 1;
        $merchant->ev = $general->ev ? 0 : 1;
        $merchant->sv = $general->sv ? 0 : 1;
        $merchant->ts = 0;
        $merchant->tv = 1;
        $merchant->save();

        // Additional actions if needed (notifications, logging, etc.)

        return response()->json(['message' => 'Merchant created successfully']);
    }




    public function checkMerchant(Request $request)
    {
        $exist['data'] = null;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Merchant::where('email',$request->email)->first();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = Merchant::where('mobile',$request->mobile)->first();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = Merchant::where('username',$request->username)->first();
            $exist['type'] = 'username';
        }

        return response()->json($exist);
    }

    public function registrationSuccess()
    {
        return response()->json(['message' => 'Registration successful']);
    }

}
