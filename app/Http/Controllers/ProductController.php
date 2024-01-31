<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Csvmodel;
use App\Models\Review;
use App\Rules\FileTypeValidate;
use App\Models\Variety;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Gallery;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Galleryproduct;
use App\Models\Productcategory;
use App\Models\Order;
use App\Models\CustomerDetails;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function products()
    {
        $pageTitle      = request()->search_key?'Search Vehicles':'All Vehicle';
        $emptyMessage   = 'No vehicle found';
        $categories     = Category::with('products')->where('status', 1)->get();

        $products       = Product::live();
        $products       = $products->where('name', 'like', '%'.request()->search_key.'%')->with('category');
        $allProducts    = clone $products->get();
        if(request()->category_id){
            $products       = $products->where('category_id', request()->category_id);
        }
        $products = $products->paginate(getPaginate(18));

        $distinctYears = Csvmodel::distinct()->select('Year')->get();

        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();
        return view($this->activeTemplate.'product.list', compact('pageTitle', 'distinctYears','distinctModels', 'distinctMake' ,'emptyMessage', 'products', 'allProducts', 'categories'));
    }

    public function filter(Request $request, $selectedValue = null, $name_searching = null)
    {
        // dd($request->all());
        $pageTitle      = 'Search Vehicles';
        $emptyMessage   = 'No product found';
        // Start with a query for live products
        $products = Product::live();
  // Filter products by name if a search key is provided
        if ($request->has('search_key') && $request->input('search_key') != "" ) {
            $searchKey = $request->input('search_key');
            $products->where('name', 'like', '%' . $searchKey . '%');
        }
        // dd($selectedValue);
        // Check if $selectedValue is not empty or null
        if (isset($selectedValue) && $selectedValue != "" || isset($name_searching)) {

            // Filter products by Year, Model, or Make if a value is selected
            $products->where('name', 'like', '%' . $name_searching . '%')
            ->where(function ($query) use ($selectedValue) {
                $query->where('Year', $selectedValue)
                    ->orWhere('Make', $selectedValue)
                    ->orWhere('Model', $selectedValue);
            });

            return response()->json([
                'products' => $products->paginate(getPaginate(18)),
                'code' => 'success',
            ]);
        }
        $individual = $request['sorting'];
        // $product_type = $products->with('merchant');
        // dd( $product_type->get());
//         $products =  $product_type->whereHas('merchant', function ($query) use ($individual) {
//             $query->where('type', '=' , $individual);
//         });
// dd( $products->get());
        if ($individual == 'individual') {
        //    dd('rong');
            $products = $products->with('merchant')->whereHas('merchant', function ($query) use ($individual) {
                $query->where('type', '=' , $individual);
            })->get();
            return response()->json([
             'code'=>200,
             'products' => $products,
            ]);
            // dd($products->get());
        } elseif ($individual == 'dealer') {
            // dd('rong');
             $products = $products->with('merchant')->whereHas('merchant', function ($query) use ($individual) {
                $query->where('type', '=', $individual);
            })->get();
            return response()->json([
                'code'=>200,
                'products' => $products,
               ]);
            // dd($products->get());
        }
        if($request->sorting){
            $products->orderBy($request->sorting, 'ASC');
        }
        if($request->categories){
            $products->whereIn('category_id', $request->categories);
        }
        if($request->minPrice){
            $products->where('price', '>=', $request->minPrice);
        }
        if($request->maxPrice){
            $products->where('price', '<=', $request->maxPrice);
        }

        $products = $products->paginate(getPaginate(18));

        return view($this->activeTemplate.'product.filtered', compact('pageTitle', 'emptyMessage', 'products'));
    }
    public function store(Request $request)
    {
        
        $this->validation($request, 'required');
        $product            = new Product();

        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Vehicle added successfully'];
        return back()->withNotify($notify);
    }
    public function saveProduct($request, $product)
    {
        //dd('test');
        // dd($request->started_at);
        if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->merchant_id = auth()->guard('merchant')->id();
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->sale_start_date = $request->sale_started_at ?? null;
        $product->sale_end_date = $request->sale_expired_at ?? null;
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

        $product->save();
       // dd($product);
       return response()->json(['message' => 'Vehicle added successfully']);
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
            'image'                 => [$imgValidation,'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);
    }
    public function Apistore(Request $request)
{
    $this->validation($request, 'required');
    $product = new Product();

    try {
        $this->saveProduct($request, $product);
    } catch (\Exception $exp) {
        return response()->json(['error' => $exp->getMessage()], 400);
    }

    return response()->json(['message' => 'Vehicle added successfully']);
}
    public function productDetails($id)
    {
        $pageTitle = 'Live Auction';
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();
        $latestBid = Bid::where('product_id', $id)
        ->orderBy('created_at', 'desc')
        ->first();
       // dd($latestBid);
        $highestBid = Bid::where('product_id', $id)
        ->orderByDesc('amount')
        ->first();

    if ($highestBid) {
        $userName = $highestBid->user->firstname;
    } else {
        $userName = "No Bids Yet"; // Handle the case where there are no bids.
    }
        $product = Product::with('reviews', 'merchant', 'bids.user', 'reviews.user')->where('status', '!=', 0)->findOrFail($id);
        //dd($highestBid);
        $product_comments = Comment::with(['product', 'user'])->where('product_id', $id)

       ->get();

        $relatedProducts = Product::live()->where('category_id', $product->category_id)->where('id', '!=', $id)->limit(10)->get();

        $imageData      = imagePath()['product'];
        $seoContents    = getSeoContents($product, $imageData, 'image');
        $images = Gallery :: with(['product'])->where('product_id', $id)->get();
        return view($this->activeTemplate.'product.details', compact('pageTitle','highestBid', 'userName','product','distinctYears','distinctClass','distinctModels','distinctMake','relatedProducts', 'seoContents', 'latestBid', 'product_comments', 'images'));
    }


    public function loadMore(Request $request)
    {
        $reviews = Review::where('product_id', $request->pid)->with('user')->latest()->paginate(5);

        return view($this->activeTemplate . 'partials.product_review', compact('reviews'));
    }

    public function bid(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'product_id' => 'required|integer|gt:0'
        ]);

        $product = Product::live()->with('merchant', 'admin')->findOrFail($request->product_id);

        $user = auth()->user();

        if($product->price > $request->amount){
            $notify[] = ['error', 'Bid amount must be greater than product price'];
            return back()->withNotify($notify);
        }

        if($request->amount > $user->balance){
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }

        $bid = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->exists();

        if($bid){
            $notify[] = ['error', 'You already bidden on this product'];
            return back()->withNotify($notify);
        }

        $bid = new Bid();
        $bid->product_id = $product->id;
        $bid->user_id = $user->id;
        $bid->amount = $request->amount;
        $bid->save();

        $product->total_bid += 1;
        $product->save();
        $user->balance -= $request->amount;
        $user->save();

        $general = GeneralSetting::first();

        $trx = getTrx();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $request->amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Subtracted for a new bid';
        $transaction->trx = $trx;
        $transaction->save();

        if($product->admin){
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = auth()->user()->id;
            $adminNotification->title = 'A user has been bidden on your product';
            $adminNotification->click_url = urlPath('admin.product.bids',$product->id);
            $adminNotification->save();

            $notify[] = ['success', 'Bidden successfully'];
            return back()->withNotify($notify);
        }

        $product->merchant->balance += $request->amount;
        $product->merchant->save();

        $transaction = new Transaction();
        $transaction->merchant_id = $product->merchant_id;
        $transaction->amount = $request->amount;
        $transaction->post_balance = $product->merchant->balance;
        $transaction->trx_type = '+';
        $transaction->details = showAmount($request->amount) . ' ' . $general->cur_text . ' Added for Bid';
        $transaction->trx =  $trx;
        $transaction->save();

        notify($product->merchant, 'BID_COMPLETE', [
            'trx' => $trx,
            'amount' => showAmount($request->amount),
            'currency' => $general->cur_text,
            'product' => $product->name,
            'product_price' => showAmount($product->price),
            'post_balance' => showAmount($product->merchant->balance),
        ], 'merchant');

        $notify[] = ['success', 'Bidden successfully'];
        return back()->withNotify($notify);

    }
    public function deleteImages($id)
    {
        // Delete the image with the given $id from the database
        $image = Galleryproduct::find($id);
        if ($image) {
            $image->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    public function saveProductReview(Request $request)
    {

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'product_id' => 'required|integer'
        ]);

        Bid::where('user_id', auth()->id())->where('product_id', $request->product_id)->firstOrFail();


        $review = Review::where('user_id', auth()->id())->where('product_id', $request->product_id)->first();
        $product = Product::find($request->product_id);

        if(!$review){
            $review = new Review();
            $product->total_rating += $request->rating;
            $product->review_count += 1;
            $notify[] = ['success', 'Review given successfully'];
        }else{
            $product->total_rating = $product->total_rating - $review->rating + $request->rating;
            $notify[] = ['success', 'Review updated successfully'];
        }

        $product->avg_rating = $product->total_rating / $product->review_count;
        $product->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth()->id();
        $review->product_id = $request->product_id;
        $review->save();

        return back()->withNotify($notify);

    }

    public function saveMerchantReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'merchant_id' => 'required|integer'
        ]);

        $merchant = Merchant::with('bids')->whereHas('bids', function($bid){
            $bid->where('user_id', auth()->id());
        })
        ->findOrFail($request->merchant_id);

        $review = Review::where('user_id', auth()->id())->where('merchant_id', $request->merchant_id)->first();

        if(!$review){
            $review = new Review();
            $merchant->total_rating += $request->rating;
            $merchant->review_count += 1;
            $notify[] = ['success', 'Review given successfully'];
        }else{
            $merchant->total_rating = $merchant->total_rating - $review->rating + $request->rating;
            $notify[] = ['success', 'Review updated successfully'];
        }

        $merchant->avg_rating = $merchant->total_rating / $merchant->review_count;
        $merchant->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth()->id();
        $review->merchant_id = $request->merchant_id;
        $review->save();

        return back()->withNotify($notify);

    }
    public function productDetail($id, $slug){
        $product = Product::with(['productCate', 'variation','merchant'])->where('id',$id)->first();
         $images = Galleryproduct::where('product_id', $id)->get();

        $pageTitle = 'Product Details';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();
        return view($this->activeTemplate.'product-details',compact('pageTitle','sections', 'product', 'images'));
    }
    public function productPrice($id){
        $variation = Variety::find($id);
        return response()->json([
            'success'=>200,
            "variation"=> $variation,
        ]);
    }
    public function cardAdd(Request $request){
       $data = $request->all();

            return response()->json([
                 'code' => 200,
                 'data' => $data,
            ]);
    //    return view($this->activeTemplate.'addcard',compact('data'));
    }
    public function cardShow(Request $request){
        $data = json_decode($request->input('data'));
         $ipAddress = $request->ip();
         $order = new Order();
         if (isset($data->size)) {
            $variation_id = $data->size;
            $variation = Variety::select('name')->where('id', $variation_id)->first();
            $type = $variation->name;
            $order->type = $type;

        }
         $order->ip_address = $ipAddress;
         $order->product_id = $data->productId;
        $order->quantity =  $data->quantity;
        $order->total_price = $data->price;

        // $productId = $data->productId;
        // $quantityToSubtract = $data->quantity;

        // $product = Product::find($productId);

        // if ($product) {
        //     // Check if the product exists

        //     // Calculate the new quantity
        //     $newQuantity = $product->quantity - $quantityToSubtract;

        //     if ($newQuantity >= 0) {

        //         $product->quantity = $newQuantity;

        //         $product->save();

        //     } else {

        //     }
        // } else {

        // }
        if( $order->save()){
            
           // dd($order);
            return redirect()->route('oders.list');

        }


    }
    public function cardDetails(Request $request){
        $ipAddress = $request->ip();
        $orders = Order::with('product.productCate', 'product.variation')->where('status',0)->where('ip_address', $ipAddress)->get();
        $orderIds = $orders->pluck('product_id')->toArray();
        // dd($orderIds);

        $sizes = "";
        if (!$orders->isEmpty()) {
            $product_id = $orders->first()->product_id;
            $variations = Variety::select('name')->whereIn('product_id', $orderIds)->get();

            $sizes = [];
            foreach ($variations as $variation) {
                $sizes[] = $variation->name;
            }

            // dd($sizes);
        } else {
              $sizes = "";
        }

        $pageTitle = 'Your cart';

        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();

        return view($this->activeTemplate.'addcard', compact('orders','sections','pageTitle','sizes'));
    }
    public function removeOrders(Request $request, $id=null){
        $order = Order::find($id);
        if($order->delete()){
            return response()->json([
                  'status'=>200,
                  'message'=>"Order removed successfully",
            ]);
        }
    }
    public function quantityChange(Request $request, $id=null) {
        $quantity = $request->input('quantity');
        // dd($quantity);
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $oldprice = $order->total_price;
        $oldquantity = $order->quantity;

        if ($quantity >= 1 && $oldquantity > 0) {
            // Calculate the price of one item and then multiply by the new quantity
            $eachItemPrice = $oldprice / $oldquantity;
            $newPrice = $eachItemPrice * $quantity;
        } else
        {
            // Use the total price as is when quantity is 0 or 1, or if old quantity is 0
            $newPrice = $oldprice;
            $quantity =  $oldquantity;
        }

        $order->total_price = $newPrice;
        $order->quantity = $quantity;
        $order->save();

        return response()->json(['message' => 'Quantity updated successfully', 'new_total_price' => $newPrice]);
    }
    public function cardCount(Request $request)
    {
        $count = Order::where('ip_address',$request->ip())->where('status',0)->count();

        return response()->json([
            'count' => $count,
            'code' => 'success',
        ]);
    }

    public function payment(Request $request){
        $pageTitle = 'Payment';
        $ipAddress = $request->ip();
        $address_details=CustomerDetails::where('ip_address',$ipAddress)->get();
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();
         $orders = Order::with(['product.productCate'])->where('status', 0)->where('ip_address', $ipAddress)->orderBy('order_id', 'desc')->get();
         $totalPrice = 0;
         foreach($orders as $order){
            $totalPrice += $order->total_price;
         }
        //  dd($totalPrice);
         return view($this->activeTemplate.'payment', compact('sections','pageTitle', 'totalPrice', 'orders','address_details'));
    }
    public function payementTransiction(Request $request)
    {
       // dd($request);
        $address_count=CustomerDetails::where('ip_address',$request->ip())->get()->count();
        

        if($address_count < 2)
        {
            $customer_details= new CustomerDetails();
       
        $request->validate([
    'name' => 'required',
    'email' => 'required',
    // 'email' => 'required|exists:categories,id',
    'phone_no' => 'required',
    'address' => 'required',
    'country' => 'required',
    'city' => 'required',
    'postal_code' => 'required',
], [
    'name.required' => 'Required',
    'email.required' => 'Required',
    'phone_no.required' => 'Required',
    'address.required' => 'Required',
    'country.required' => 'Required',
    'city.required' => 'Required',
    'postal_code.required' => 'Required',
]);
    $customer_details->name=$request->name ?? '';
    $customer_details->ip_address=$request->ip() ?? '';
    $customer_details->email=$request->email ?? '';
    $customer_details->phone_no=$request->phone_no ?? '';
    $customer_details->address=$request->address ?? '';
    $customer_details->country=$request->country ?? '';
    $customer_details->city=$request->city ?? '';
    $customer_details->postal_code=$request->postal_code ?? '';
    $customer_details->status= 1;
    $customer_details->save();
    
}
try
{
    $order_details=Order::where('ip_address',$request->ip())->where('status',0)->get();
    foreach($order_details as $order)
    {
        $order->status=1;
        $order->save();
    }
if($request->pay_method === 'cash_on_delivery')
// if($request->pay_method === 'cash_on_delivery' || $request->pay_method === null)
{
    if(isset($request->delivery_address)){
        
    $delivery_details=CustomerDetails::find($request->delivery_address);
    }
   else
    {
      $delivery_details=CustomerDetails::where('ip_address',$request->ip())->first();  
    }
    $delivery_details->status=3;
    $delivery_details->save();
    return redirect('paypal/success')->with('delivery_details', $delivery_details);

   
}
}
catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
        
        try
{
if($request->pay_method === 'paypal' )
{
        $provider = new PayPalClient;

        session(['dataToPass' => $request->all()]);
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $order = $provider->createOrder([
            "intent" =>"CAPTURE",
            "application_context" => [
                "return_url"=> route('payment_success'),
                "cancel_url"=> route('payment_cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->total_price,
                        // "value" => $request->input('total_price'),
                        // "quantity" => $request->input('quantity'),
                    ],
                ],
            ],
        ]);
        // dd(  $order);
    // dd($order);
        if (isset($order['id']) && !empty($order['id'])) {
            $approveLink = null;

            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approveLink = $link['href'];
                    break;
                }
            }

            if ($approveLink !== null) {
                // Redirect the user to the PayPal approval URL
                return redirect()->away($approveLink);
            } else {
                // Handle the case where there is no 'approve' link (possibly an error)
                return "Approval link not found.";
            }
        } else {
            // Handle the case where no order ID is present (possibly an error)
            return "Order ID not found.";
        }
}
}
catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

    }
    public function payementSuccess(Request $request){
         $delivery_details = "Zahid";
        //  $delivery_details = session('delivery_details');
       // dd($delivery_details);
         session()->flash('success', 'Payment is successfully completed');
        //dd($delivery_details->name,' Congrates! your order Recieved Successfully');
        session()->flash('success', $delivery_details.' Congrates! your order Recieved Successfully');
        // session()->flash('success', $delivery_details->name.' Congrates! your order Recieved Successfully');
            return redirect()->route('merchants');
        return view($this->activeTemplate.thank_you_page, compact('delivery_details'));
        $data = session('dataToPass');


        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $order = $provider->capturePaymentOrder($request->token);
        if (isset($order['status']) && strtolower($order['status']) == 'completed') {
            $currentIp = $request->ip(); // Get the current IP address
            DB::table('orders')->where('ip_address', $currentIp)->delete();
            session()->flash('success', 'Payment is successfully completed');
            return redirect()->route('merchants');
        } else {
            return "Error: Payment was not completed successfully.";
        }

    }
}
