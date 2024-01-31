<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }
    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }


    public function authorization()
    {
        $user = auth()->user();
        if (!$user->status) {

            auth()->user()->tokens()->delete();
            $notify[] = 'Your account has been deactivated';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
            ]);

        }elseif (!$user->ev) {
            if (!$this->checkValidCode($user, $user->ver_code)) {
                $user->ver_code = verificationCode(6);
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                sendEmail($user, 'EVER_CODE', [
                    'code' => $user->ver_code
                ]);
            }
            $notify[] = 'Email verification';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
                'data'=>[
                    'verification_url'=>route('api.user.verify.email'),
                    'verification_method'=>'POST',
                    'resend_url'=>route('api.user.send.verify.code').'?type=email',
                    'resend_method'=>'GET',
                    'verification_type'=>'email'
                ]
            ]);
        }elseif (!$user->sv) {
            if (!$this->checkValidCode($user, $user->ver_code)) {
                $user->ver_code = verificationCode(6);
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                sendSms($user, 'SVER_CODE', [
                    'code' => $user->ver_code
                ]);
            }
            $notify[] = 'SMS verification';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
                'data'=>[
                    'verification_url'=>route('api.user.verify.sms'),
                    'verification_method'=>'POST',
                    'resend_url'=>route('api.user.send.verify.code').'?type=phone',
                    'resend_method'=>'GET',
                    'verification_type'=>'sms'
                ]
            ]);
        }elseif (!$user->tv) {
            $notify[] = 'Google Authenticator';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
                'data'=>[
                    'verification_url'=>route('api.user.go2fa.verify'),
                    'verification_method'=>'POST',
                    'verification_type'=>'2fa'
                ]
            ]);
        }

    }
     public function Sellerauthorization()
    {
       $merchant = auth()->guard('merchant')->user();

if (!$merchant->status) {

    auth()->guard('merchant')->user()->tokens()->delete();
    $notify[] = 'Your merchant account has been deactivated';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
    ]);

} elseif (!$merchant->ev) {
    if (!$this->checkValidCode($merchant, $merchant->ver_code)) {
        $merchant->ver_code = verificationCode(6);
        $merchant->ver_code_send_at = Carbon::now();
        $merchant->save();
        sendEmail($merchant, 'EVER_CODE', [
            'code' => $merchant->ver_code
        ]);
    }
    $notify[] = 'Merchant email verification';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
        'data' => [
            'verification_url' => route('api.merchant.verify.email'),
            'verification_method' => 'POST',
            'resend_url' => route('api.merchant.send.verify.code') . '?type=email',
            'resend_method' => 'GET',
            'verification_type' => 'email'
        ]
    ]);

} elseif (!$merchant->sv) {
    if (!$this->checkValidCode($merchant, $merchant->ver_code)) {
        $merchant->ver_code = verificationCode(6);
        $merchant->ver_code_send_at = Carbon::now();
        $merchant->save();
        sendSms($merchant, 'SVER_CODE', [
            'code' => $merchant->ver_code
        ]);
    }
    $notify[] = 'Merchant SMS verification';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
        'data' => [
            'verification_url' => route('api.merchant.verify.sms'),
            'verification_method' => 'POST',
            'resend_url' => route('api.merchant.send.verify.code') . '?type=phone',
            'resend_method' => 'GET',
            'verification_type' => 'sms'
        ]
    ]);

} elseif (!$merchant->tv) {
    $notify[] = 'Merchant Google Authenticator';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
        'data' => [
            'verification_url' => route('api.merchant.go2fa.verify'),
            'verification_method' => 'POST',
            'verification_type' => '2fa'
        ]
    ]);
}

    }

    public function sendVerifyCode(Request $request)
    {
        $user = Auth::user();


        if ($this->checkValidCode($user, $user->ver_code, 2)) {
            $target_time = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            $notify[] = 'Please Try after ' . $delay . ' Seconds';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify]
            ]);
        }
        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code = $user->ver_code;
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }



        if ($request->type === 'email') {
            sendEmail($user, 'EVER_CODE',[
                'code' => $user->ver_code
            ]);

            $notify[] = 'Email verification code sent successfully';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify]
            ]);
        } elseif ($request->type === 'phone') {
            sendSms($user, 'SVER_CODE', [
                'code' => $user->ver_code
            ]);
            $notify[] = 'SMS verification code sent successfully';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify]
            ]);
        } else {
            $notify[] = 'Sending Failed';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify]
            ]);
        }
    }

    public function emailVerification(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email_verified_code'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $email_verified_code = $request->email_verified_code;
        $user = Auth::user();

        if ($this->checkValidCode($user, $email_verified_code)) {
            $user->ev = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $notify[] = 'Email verified successfully';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
            ]);
        }
        $notify[] = 'Verification code didn\'t match!';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
    }

    public function smsVerification(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'sms_verified_code'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $sms_verified_code =  $request->sms_verified_code;

        $user = Auth::user();
        if ($this->checkValidCode($user, $sms_verified_code)) {
            $user->sv = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $notify[] = 'SMS verified successfully';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
            ]);
        }
        $notify[] = 'Verification code didn\'t match!';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
    }
    public function g2faVerification(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),[
            'code'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $code = $request->code;
        $response = verifyG2fa($user,$code);
        if ($response) {
            $notify[] = 'Verification successful';
        }else{
            $notify[] = 'Wrong verification code';
        }
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['error'=>$notify],
        ]);
    }
    public function sendSellerVerifyCode(Request $request)
{
    $merchant = Auth::guard('merchant')->user();

    if ($this->checkValidCode($merchant, $merchant->ver_code, 2)) {
        $target_time = $merchant->ver_code_send_at->addMinutes(2)->timestamp;
        $delay = $target_time - time();
        $notify[] = 'Please Try after ' . $delay . ' Seconds';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify]
        ]);
    }

    if (!$this->checkValidCode($merchant, $merchant->ver_code)) {
        $merchant->ver_code = verificationCode(6);
        $merchant->ver_code_send_at = Carbon::now();
        $merchant->save();
    } else {
        $merchant->ver_code = $merchant->ver_code;
        $merchant->ver_code_send_at = Carbon::now();
        $merchant->save();
    }

    if ($request->type === 'email') {
        sendEmail($merchant, 'MERCHANT_EVER_CODE', [
            'code' => $merchant->ver_code
        ]);

        $notify[] = 'Merchant email verification code sent successfully';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify]
        ]);
    } elseif ($request->type === 'phone') {
        sendSms($merchant, 'MERCHANT_SVER_CODE', [
            'code' => $merchant->ver_code
        ]);
        $notify[] = 'Merchant SMS verification code sent successfully';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify]
        ]);
    } else {
        $notify[] = 'Sending Failed';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify]
        ]);
    }
}

public function sellerEmailVerification(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email_verified_code' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['error' => $validator->errors()->all()],
        ]);
    }

    $email_verified_code = $request->email_verified_code;
    $merchant = Auth::guard('merchant')->user();

    if ($this->checkValidCode($merchant, $email_verified_code)) {
        $merchant->ev = 1;
        $merchant->ver_code = null;
        $merchant->ver_code_send_at = null;
        $merchant->save();
        $notify[] = 'Merchant email verified successfully';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify],
        ]);
    }

    $notify[] = 'Verification code didn\'t match!';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
    ]);
}

public function sellerSmsVerification(Request $request)
{
    $validator = Validator::make($request->all(), [
        'sms_verified_code' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['error' => $validator->errors()->all()],
        ]);
    }

    $sms_verified_code =  $request->sms_verified_code;
    $merchant = Auth::guard('merchant')->user();

    if ($this->checkValidCode($merchant, $sms_verified_code)) {
        $merchant->sv = 1;
        $merchant->ver_code = null;
        $merchant->ver_code_send_at = null;
        $merchant->save();
        $notify[] = 'Merchant SMS verified successfully';
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['success' => $notify],
        ]);
    }

    $notify[] = 'Verification code didn\'t match!';
    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['success' => $notify],
    ]);
}

public function sellerG2faVerification(Request $request)
{
    $merchant = auth()->guard('merchant')->user();
    $validator = Validator::make($request->all(), [
        'code' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'code' => 200,
            'status' => 'ok',
            'message' => ['error' => $validator->errors()->all()],
        ]);
    }

    $code = $request->code;
    $response = verifyG2fa($merchant, $code);

    if ($response) {
        $notify[] = 'Merchant verification successful';
    } else {
        $notify[] = 'Wrong verification code';
    }

    return response()->json([
        'code' => 200,
        'status' => 'ok',
        'message' => ['error' => $notify],
    ]);
}

}
