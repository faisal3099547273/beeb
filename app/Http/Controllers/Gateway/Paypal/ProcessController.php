<?php

namespace App\Http\Controllers\Gateway\Paypal;

use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $basic =  GeneralSetting::first();
        $paypalAcc = json_decode($deposit);
        //dd($paypalAcc);
        $gatewayParameters = json_decode($paypalAcc->gateway->gateway_parameters);
        $paypalEmail = $gatewayParameters->paypal_email->value;

       // $paypalAcc = json_decode($deposit->gateway()->gateway_parameters());
        
        $val['cmd'] = '_xclick';
        $val['business'] = trim($paypalEmail);
        $val['cbt'] = $basic->sitename;
        $val['currency_code'] = $deposit->method_currency;
        $val['quantity'] = 1;
        $val['item_name'] = "Payment To {$basic->sitename} Account";
        $val['custom'] = $deposit->trx;
        $val['amount'] = round($deposit->amount,2);
        // $val['amount'] = round($deposit->final_amo,2);
        $val['return'] = route(gatewayRedirectUrl(true));
        $val['cancel_return'] = route(gatewayRedirectUrl());
        $val['notify_url'] = route('ipn.'.$deposit->gateway->alias);
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
         $send['url'] = 'https://www.sandbox.paypal.com/'; // use for sandbod text
        //$send['url'] = 'https://www.paypal.com/cgi-bin/webscr';
        //dd($send['url']);
        return json_encode($send);
    }
    
    public function ipn()
    {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
            $details[$key] = $value;
        }
        
        // $paypalURL = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr?"; // use for sandbox text
        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $callUrl = $paypalURL . $req;
        $verify = curlContent($callUrl);
        
        if ($verify == "VERIFIED") {
            $deposit = Deposit::where('trx', $_POST['custom'])->orderBy('id', 'DESC')->first();
            $deposit->detail = $details;
            $deposit->save();

            if ($_POST['mc_gross'] == $deposit->final_amo && $deposit->status == '0') {
                PaymentController::userDataUpdate($deposit->trx);
            }
        }
    }
}
