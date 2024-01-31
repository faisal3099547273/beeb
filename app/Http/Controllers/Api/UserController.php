<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Productcategory;
use App\Models\Csvmodel;
use App\Models\Winner;
use App\Models\Variety;
use App\Models\Galleryproduct;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
	public function submitProfile(Request $request){
		$validator = Validator::make($request->all(),[
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
	}

	public function submitPassword(Request $request){
		$password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        
        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changes successfully';
        } else {
            $notify[] = 'The password doesn\'t match!';
        }
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['error'=>$notify],
        ]);
	}

	public function withdrawMethods(){
		$withdrawMethod = WithdrawMethod::where('status',1)->get();
		$notify[] = 'Withdraw methods';
		return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
            'data'=>[
            	'methods'=>$withdrawMethod,
            	'image_path'=>imagePath()['withdraw']['method']['path']
            ],
        ]);
	}

	public function withdrawStore(Request $request){
		$validator = Validator::make($request->all(), [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->first();
        if (!$method) {
            $notify[] = 'Method not found.';
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }
        $user = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = 'Your requested amount is smaller than minimum amount.';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
	            'message'=>['error'=>$notify],
	        ]);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = 'Your requested amount is larger than maximum amount.';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
	            'message'=>['error'=>$notify],
	        ]);
        }

        if ($request->amount > $user->balance) {
            $notify[] = 'You do not have sufficient balance for withdraw.';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
	            'message'=>['error'=>$notify],
	        ]);
        }



        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();

        $notify[] = 'Withdraw request stored successfully';
        return response()->json([
            'code'=>202,
            'status'=>'created',
            'message'=>['success'=>$notify],
            'data'=>$withdraw
        ]);
	}

	public function withdrawConfirm(Request $request){

        $withdraw = Withdrawal::with('method','user')->where('trx', $request->transaction)->where('status', 0)->orderBy('id','desc')->first();

        if (!$withdraw) {
            $notify[] = 'Withdraw request not found';
            return response()->json([
                'code'=>404,
                'status'=>'error',
	            'message'=>['error'=>$notify],
	        ]);
        }



        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $rules['transaction'] = 'required';
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        
        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user,$request->authenticator_code);
            if (!$response) {
                $notify[] = 'Wrong verification code';
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
	                'message'=>['error'=>$notify],
	            ]);
            }   
        }


        if ($withdraw->amount > $user->balance) {
            $notify[] = 'Your request amount is larger then your current balance.';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$notify],
            ]);
        }

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = 'Could not upload your ' . $request[$inKey];
                                    return response()->json([
						                'message'=>['error'=>$notify],
						            ]);
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();



        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        $general = GeneralSetting::first();
        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify[] = 'Withdraw request sent successfully';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
	}

    public function withdrawLog(){
        $withdrawals = Withdrawal::where('user_id', auth()->user()->id)->where('status', '!=', 0)->with('method')->orderBy('id','desc')->paginate(getPaginate());
            return response()->json([
            'code'=>200,
            'status'=>'ok',
            'data'=>[
                'withdrawals'=>$withdrawals,
                'verification_file_path'=>imagePath()['verify']['withdraw']['path'],
            ]
        ]);
    }

    public function depositHistory(){
        $deposits = Deposit::where('user_id', auth()->user()->id)->where('status', '!=', 0)->with('gateway')->orderBy('id','desc')->paginate(getPaginate());
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'data'=>[
                'deposit'=>$deposits,
                'verification_file_path'=>imagePath()['verify']['deposit']['path'],
            ]
        ]);
    }

    public function transactions(){
        $user = auth()->user();
        $transactions = $user->transactions()->paginate(getPaginate());
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'data'=>[
                'transactions'=>$transactions,
            ]
        ]);
    }
    public function ApiProductstore(Request $request, $imgValidation = null)
{
    $validator = Validator::make($request->all(), [
        'name'                  => 'required',
        'quantity'                  => 'required',
        'category_id'              => 'required|exists:categories,id',
        'price'                 => 'required|numeric|gte:0',
        'long_description'      => 'required',
        'specification'         => 'nullable|array',
        'started_at'            => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
        'image'              => 'required|image|mimes:jpeg,jpg,png',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    $product = new Product();
    $product->name = $request->input('name');
    $product->category_id = $request->input('category_id');
    $product->price = $request->input('price');
    $product->expired_at = $request->input('expired_at');
    // $product->merchant_id = auth()->guard('merchant')->id();
    $product->status    = 1;
    $product->short_description = $request->input('short_description');
    $product->long_description = $request->input('long_description');
    $product->specification = $request->input('specification');
    $product->started_at = $request->input('started_at');
    $product->quantity = $request->input('quantity');
    if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $imagePath = $image->getClientOriginalName();
                $imagePaths = $image->move('assets/products/', $imagePath);

                // Create a new Galleryproduct record for each uploaded image
                $galleryProduct = new Galleryproduct;
                $galleryProduct->product_id = $product->id;
                $galleryProduct->image = $imagePaths;
                $galleryProduct->save();
            }
        }
    $product->save();

    return response()->json(['message' => 'Product created successfully'], 200);
}

public function varietyCreate(Request $request)
    {
       $categories = ProductCategory::select('id', 'name')->get();

        return response()->json(['categories' => $categories], 200);
    }
   public function getProductBidsApi($id)
    {
        $product = Product::where('merchant_id', auth()->guard('merchant')->id())
            ->with('winner')
            ->findOrFail($id);

        $bids = Bid::where('product_id', $id)
            ->with('user', 'product', 'winner')
            ->withCount('winner')
            ->orderBy('winner_count', 'DESC')
            ->latest()
            ->paginate(getPaginate());

        $winner = $product->winner;
        dd($product, $bids, $winner);

        return response()->json([
            'product' => $product,
            'bids' => $bids,
            'winner' => $winner,
        ], 200);
    }

    public function postBidWinnerApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bid_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $bid = Bid::with('user', 'product')
            ->whereHas('product', function ($product) {
                $product->where('merchant_id', auth()->guard('merchant')->id());
            })
            ->findOrFail($request->bid_id);

        $product = $bid->product;

        $winnerExists = Winner::where('product_id', $product->id)->exists();

        if ($winnerExists) {
            return response()->json(['error' => 'Winner for this product id already selected'], 422);
        }

        if ($product->expired_at > now()) {
            return response()->json(['error' => 'This product is not expired till now'], 422);
        }

        $winner = new Winner();
        $winner->user_id = $bid->user_id;
        $winner->product_id = $bid->product_id;
        $winner->bid_id = $bid->id;
        $winner->save();

        return response()->json(['success' => 'Winner published successfully'], 200);
    }
public function getBidsApi()
{
    $bids = Bid::with('user', 'product')
        ->whereHas('product', function ($product) {
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })
        ->latest()
        ->paginate(getPaginate());

    return response()->json(['bids' => $bids], 200);
}
   public function Vehiclestore(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name'                  => 'required',
        'quantity'              => 'required',
        'category_id'           => 'required|exists:categories,id',
        'price'                 => 'required|numeric|gte:0',
        'long_description'      => 'required',
        'specification'         => 'nullable|array',
        'started_at'            => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
        'image'                 => 'required|image|mimes:jpeg,jpg,png',
        'sale_started_at'       => 'nullable|date',
        'sale_expired_at'       => 'nullable|date|after_or_equal:sale_started_at',
        'short_description'     => 'required',
        'Year'                  => 'required',
        'Make'                  => 'required',
        'Model'                 => 'required',
        'Plateform'             => 'required',
        'Class'                 => 'required',
        'Notes'                 => 'required',
        'latitude'              => 'required',
        'longitude'             => 'required',
        'images.*'              => 'image|mimes:jpeg,jpg,png',
    ]);
    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }
 if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
    $product = new Product();
        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->merchant_id = auth()->guard('merchant')->id();
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->sale_start_date = $request->sale_started_at ?? "";
        $product->sale_end_date = $request->sale_expired_at ?? "";
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->specification = $request->specification ?? null;
        $product->Year = $request->Year;
        $product->Make = $request->Make;
        $product->Model = $request->Model;
        $product->Plateform = $request->Plateform;
        $product->Class = $request->Class;
        $product->Notes = $request->Notes;
        $product->latitude = $request->latitude;
        $product->longitude = $request->longitude;
       
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->getClientOriginalName();
                $imagePaths = $image->move('assets/product/', $imagePath);
                $uploadedImages[] = $imagePaths;
                $gallery = new Gallery();
                $gallery->product_id = $product->id;
                $gallery->image =  $imagePaths;
                $gallery->save();
            }
        }
 $product->save();
    return response()->json(['message' => 'Vehicle created successfully'], 200);
    }
    protected function validation($request, $imgValidation){
        $request->validate([
            'name'                  => 'required',
            'category'              => 'required|exists:categories,id',
            'price'                 => 'required|numeric|gte:0',
            'expired_at'            => 'required',
            'short_description'     => 'required',
            'long_description'      => 'required',
            'specification'         => 'nullable|array',
            'started_at'            => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
            // 'image'                 => [$imgValidation,'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);
    }
    
    public function getMerchantvehicle(Request $request)
{
    $merchantId = auth()->guard('merchant')->id();

    $products = Product::where('merchant_id', $merchantId)
                        ->where('status', 0)
                        ->get();

    return response()->json(['products' => $products, 'merchantId' => $merchantId], 200);
}
public function getMerchantProducts(Request $request)
{
    // return $request->all();
    $merchantId = auth()->id();
   // $merchantId = auth()->guard('merchant')->id();

    $products = Product::where('merchant_id', $merchantId)
                        ->where('status', 0)->with('merchant')
                        ->get();

    return response()->json(['products' => $products], 200);
}

  public function UpdateVehiclestore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
        'name'                  => 'required',
        'quantity'              => 'required',
        'category_id'           => 'required|exists:categories,id',
        'price'                 => 'required|numeric|gte:0',
        'long_description'      => 'required',
        'specification'         => 'nullable|array',
        'started_at'            => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
        'image'                 => 'required|image|mimes:jpeg,jpg,png',
        'sale_started_at'       => 'nullable|date',
        'sale_expired_at'       => 'nullable|date|after_or_equal:sale_started_at',
        'short_description'     => 'required',
        'Year'                  => 'required',
        'Make'                  => 'required',
        'Model'                 => 'required',
        'Plateform'             => 'required',
        'Class'                 => 'required',
        'Notes'                 => 'required',
        'latitude'              => 'required',
        'longitude'             => 'required',
        'images.*'              => 'image|mimes:jpeg,jpg,png',
    ]);
    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }
    $product = Product::findOrFail($id);
 if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
    
        $product->name = $request->name;
        $product->category_id = $request->input('category_id') ?? $product->category_id;
        // $product->merchant_id = auth()->guard('merchant')->id();
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->sale_start_date = $request->sale_started_at ?? "";
        $product->sale_end_date = $request->sale_expired_at ?? "";
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->specification = $request->specification ?? null;
        $product->Year = $request->Year;
        $product->Make = $request->Make;
        $product->Model = $request->Model;
        $product->Plateform = $request->Plateform;
        $product->Class = $request->Class;
        $product->Notes = $request->Notes;
        $product->latitude = $request->latitude;
        $product->longitude = $request->longitude;
       
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->getClientOriginalName();
                $imagePaths = $image->move('assets/product/', $imagePath);
                $uploadedImages[] = $imagePaths;
                $gallery = new Gallery();
                $gallery->product_id = $product->id;
                $gallery->image =  $imagePaths;
                $gallery->save();
            }
        }
 $product->save();
    return response()->json(['message' => 'Vehicle Update successfully'], 200);
    }
 public function authorization()
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
}