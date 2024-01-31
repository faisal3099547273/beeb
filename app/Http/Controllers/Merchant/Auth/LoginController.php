<?php
namespace App\Http\Controllers\Merchant\Auth;

use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'merchant';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('merchant.guest')->except('logout');
        $this->username = $this->findUsername();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Merchant Login";
        return view('merchant.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('merchant');
    }
    

    protected function validateLogin(Request $request)
    {
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $validate = Validator::make($request->all(),$validation_rule);
        return $validate;

    }


    public function login(Request $request)
    {
        $this->validateLogin($request);


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    


    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect('/');
    }

    public function resetPassword()
    {
        $pageTitle = 'Account Recovery';
        return view('merchant.reset', compact('pageTitle'));
    }

    public function authenticated(Request $request, $merchant){
        if ($merchant->status == 0) {
            $this->guard('merchant')->logout();
            $notify[] = ['error','Your account has been deactivated.'];
            return redirect()->route('merchant.login')->withNotify($notify);
        }

        $merchant = auth()->guard('merchant')->user();
        $merchant->tv = $merchant->ts == 1 ? 0 : 1;
        $merchant->save();
        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();
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
        $userLogin->merchant_id = $merchant->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        return redirect()->route('merchant.dashboard');
    }
   public function Sellerlogin(Request $request)
{
    $validator = $this->validateLogin($request);

    if ($validator->fails()) {
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['error' => $validator->errors()->all()],
        ]);
    }

    $credentials = request([$this->username, 'password']);
    if (!Auth::guard('merchant')->attempt($credentials)) {
        $response[] = 'Unauthorized merchant';
        return response()->json([
            'code' => 401,
            'status' => 'unauthorized',
            'message' => ['error' => $response],
        ]);
    }

    $merchant = $request->user('merchant');
    $tokenResult = $merchant->createToken('auth_token', ['merchant_id' => $merchant->id])->plainTextToken;
    $this->authenticated($request, $merchant);
    $response[] = 'Login Successful';

    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $response],
        'data' => [
            'merchant' => auth()->guard('merchant')->user(),
            'merchant_id' => auth()->guard('merchant')->id(),
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ],
    ]);
}
public function createToken($name, array $abilities = ['*'])
{
    return $this->tokens()->create([
        'name' => $name,
        'token' => hash('sha256', $plainTextToken = Str::random(40)),
        'abilities' => $abilities,
        'merchant_id' => $this->id, // Include merchant_id in the token
    ]);
}
protected function sendFailedLoginResponse(Request $request, $message = null)
{
    $response = [
        'error' => trans('auth.failed'),
    ];

    if ($message !== null) {
        $response['message'] = $message;
    }

    return new JsonResponse($response, 401);
}

protected function sendLockoutResponse(Request $request)
{
    $seconds = $this->limiter()->availableIn(
        $this->throttleKey($request)
    );

    return new JsonResponse([
        'error' => trans('auth.throttle', ['seconds' => $seconds]),
    ], 429);
}

protected function sendLoginResponse(Request $request)
{
    $this->clearLoginAttempts($request);

    return new JsonResponse(['message' => 'Login successful']);
}
}
