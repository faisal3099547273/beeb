<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Productcategory;
use App\Models\Csvmodel;
use App\Models\Winner;
use App\Models\Variety;
use App\Models\Galleryproduct;
use App\Models\Gallery;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

use function PHPSTORM_META\type;

class ProductController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;
    protected $search;
    protected function filterProducts($type){
        $category_ids = Productcategory::pluck('id')->toArray();
        $products = Product::whereNotIn('productcate_id', $category_ids);
        $this->pageTitle    = ucfirst($type). ' Vehicle';
        $this->emptyMessage = 'No '.$type. ' products found';

        if($type != 'all'){
            $products = $products->$type();
        }

        if(request()->search){
            $search  = request()->search;
            $products    = $products->orWhere('name', 'like', '%'.$search.'%')
                                    ->orWhereHas('merchant', function ($merchant) use ($search) {
                                            $merchant->where('username', 'like',"%$search%");
                                        })->orWhereHas('admin', function ($admin) use ($search) {
                                            $admin->where('username', 'like',"%$search%");
                                        });

            $this->pageTitle    = "Search Result for '$search'";
        }

        return $products->with('category')->where('merchant_id', auth()->guard('merchant')->id())->latest()->paginate(getPaginate());
    }
    protected function filterProduct($type){
        $category_ids = Productcategory::pluck('id')->toArray();
        $query = Product::whereIn('productcate_id', $category_ids);

        $this->pageTitle = ucfirst($type) . ' Products';
        $this->emptyMessage = 'No ' . $type . ' products found';

        if ($type != 'all') {
            // Assuming that $type represents a relationship on the Product model.
            // Make sure the $type is a valid relationship defined on the Product model.
            $query->with($type);
        }

        if (request()->search) {
            $search = request()->search;

            $query->where(function ($qq) use ($search) {
                $qq->where('name', 'like', '%' . $search . '%')->orWhere(function ($product) use ($search) {
                    $product->whereHas('merchant', function ($merchant) use ($search) {
                        $merchant->where('username', 'like', "%$search%");
                    })->orWhereHas('admin', function ($admin) use ($search) {
                        $admin->where('username', 'like', "%$search%");
                    });
                });
            });

            $this->pageTitle = "Search Result for '$search'";
            $this->search = $search;
        }

         return  $query->with('productCate')->where('merchant_id', auth()->guard('merchant')->id())->orderBy('productcate_id', 'DESC')->latest()->paginate(getPaginate());
    }
    public function index()
    {
        $segments       = request()->segments();
        $products       = $this->filterProducts(end($segments));
        $pageTitle      = $this->pageTitle;
        $emptyMessage   = $this->emptyMessage;
        return view('merchant.vehicle.index', compact('pageTitle', 'emptyMessage', 'products'));
    }
    public function ApiIndex(Request $request)
{
    $segments       = request()->segments();

    $products = $this->ApifilterProducts(end($segments));

    $response = [
        'pageTitle' => $this->pageTitle,
        'emptyMessage' => $this->emptyMessage,
        'products' => $products,
    ];

    return response()->json($response, 200);
}
protected function ApifilterProducts($type)
{
    $categoryIds = Productcategory::pluck('id')->toArray();
    $merchantId = auth()->guard('merchant')->id();

    $products = Product::whereNotIn('productcate_id', $categoryIds)
        ->where('merchant_id', $merchantId);

    $this->pageTitle = ucfirst($type) . ' Vehicle';
    $this->emptyMessage = 'No ' . $type . ' products found';

    if ($type != 'all') {
        // Add your additional conditions or specific filtering logic based on $type
        // For example, if $type is 'featured', you might have a method in your Product model like 'scopeFeatured'
        // $products = $products->$type(); 

        // If you have specific conditions, add them here
        // $products = $products->where(...);
    }

    if (request()->search) {
        $search = request()->search;
        $products = $products->orWhere('name', 'like', '%' . $search . '%')
            ->orWhereHas('merchant', function ($merchant) use ($search) {
                $merchant->where('username', 'like', "%$search%");
            })->orWhereHas('admin', function ($admin) use ($search) {
                $admin->where('username', 'like', "%$search%");
            });

        $this->pageTitle = "Search Result for '$search'";
    }

    return $products->with('category')->latest()->paginate(getPaginate());
}
     public function list(){
        $segments       = request()->segments();
        $products       = $this->filterProduct(end($segments));
        $pageTitle      = $this->pageTitle;
        $emptyMessage   = $this->emptyMessage;
        return view('merchant.product.index', compact('pageTitle', 'emptyMessage', 'products'));
     }
    public function create(){

        $pageTitle = 'Create Vehicle';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();

        return view('merchant.vehicle.create', compact('pageTitle', 'categories','distinctYears','distinctClass','distinctModels','distinctMake'));
    }
    public function createproduct(){
        $pageTitle = 'Create Vehicle';
        $categories = Productcategory::where('status', 1)->get();
        return view('merchant.product.create', compact('pageTitle', 'categories'));

    }
    public function productStore(Request $request){
        $this->validations($request, 'required');
        $product            = new Product();
        $product->merchant_id = auth()->guard('merchant')->id();
        $product->status    = 1;
        $this->saveProducts($request, $product);
        $notify[] = ['success', 'Vehicle added successfully'];
        return back()->withNotify($notify);
    }
   
    public function saveProducts($request, $product)
    {
        if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        // unset($request->has('existing_image_ids'));
        $product->name = $request->name;
        $product->productcate_id = $request->category;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->short_description = $request->short_description;
        $product->save();
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
        // if ($request->has('existing_image_ids')) {
        //     $existingImageIds = json_decode($request->input('existing_image_ids'));

        //     if (is_array($existingImageIds)) {
        //         foreach ($existingImageIds as $imageId) {
        //             $image = Galleryproduct::find($imageId);

        //             if ($image) {
        //                 // Get the file name from the existing image record
        //                 $imagePath = $image->image;

        //                 // Extract just the file name without the path
        //                 $fileName = pathinfo($imagePath, PATHINFO_BASENAME);

        //                 // Update the image record with the new file name
        //                 $image->update(['image' => 'assets/products/' . $fileName]);
        //             }
        //        }
        //     }
        //     else{
        //         $image = Galleryproduct::find($existingImageIds);

        //         if ($image) {
        //             // Get the file name from the existing image record
        //             $imagePath = $image->image;

        //             // Extract just the file name without the path
        //             $fileName = pathinfo($imagePath, PATHINFO_BASENAME);

        //             // Update the image record with the new file name
        //             $image->update(['image' => 'assets/products/' . $fileName]);
        //         }
        //     }
        // }

        elseif ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {
                $imagePath = $image->getClientOriginalName();
                $imagePaths = $image->move('assets/products/', $imagePath);
                $uploadedImages[] = $imagePaths;
                $gallery = new Galleryproduct();
                $gallery->product_id = $product->id;
                $gallery->image =  $imagePaths;
                $gallery->save();
            }
        }


    }
    public function edit($id){
        $pageTitle = 'Update Vehicle';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();
        $product = Product::where('merchant_id', auth()->guard('merchant')->id())->where('id', $id)->firstOrFail();

        return view('merchant.vehicle.edit', compact('pageTitle', 'categories', 'product','distinctYears','distinctClass','distinctModels','distinctMake'));
    }


    public function store(Request $request)
    {
       
        $this->validation($request, 'required');
        $product            = new Product();

        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Vehicle added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, 'nullable');
        $product = Product::findOrFail($id);
        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Vehicle updated successfully'];
        return back()->withNotify($notify);
    }

    public function saveProduct($request, $product)
    {
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

        $product->save();
        $product->save();
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
    }
    public function saveApiProduct($request, $product)
    {
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

        $product->save();
        $product->save();
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
    protected function validations($request, $imgValidation){
        $request->validate([
            'name'                  => 'required',
            'category'              => 'required|exists:categories,id',
            'price'                 => 'required|numeric|gte:0',
            'quantity'              =>  'required|numeric',
            'short_description'     => 'required',

            'image'                 => [$imgValidation,'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);
    }
    public function productBids($id){
        $product = Product::where('merchant_id', auth()->guard('merchant')->id())->with('winner')->findOrFail($id);
        $pageTitle = $product->name.' Bids';
        $emptyMessage = $product->name.' has no bid yet';
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(getPaginate());
        $winner = $product->winner;

        return view('merchant.vehicle.product_bids', compact('pageTitle', 'emptyMessage', 'bids', 'winner'));
    }

    public function bidWinner(Request $request){
        $request->validate([
            'bid_id' => 'required'
        ]);


        $bid = Bid::with('user', 'product')
        ->whereHas('product', function($product){
           $product->where('merchant_id', auth()->guard('merchant')->id());
        })->findOrFail($request->bid_id);

        $product = $bid->product;

        $winner = Winner::where('product_id', $product->id)->exists();

        if($winner){
            $notify[] = ['error', 'Winner for this product id already selected'];
            return back()->withNotify($notify);
        }

        if($product->expired_at > now()){
            $notify[] = ['error', 'This product is not expired till now'];
            return back()->withNotify($notify);
        }

        $winner = new Winner();
        $winner->user_id = $bid->user_id;
        $winner->product_id = $bid->product_id;
        $winner->bid_id = $bid->id;
        $winner->save();

        $notify[] = ['success', 'Winner published successfully'];
        return back()->withNotify($notify);

    }

    public function bids()
    {
        $pageTitle    = 'All Bids';
        $emptyMessage = 'No bids found';
        $bids = Bid::with('user', 'product')->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })->latest()->paginate(getPaginate());

        return view('merchant.vehicle.bids', compact('pageTitle', 'emptyMessage', 'bids'));
    }

    public function productWinner(){
        $pageTitle = 'All Winners';
        $emptyMessage = 'No winner found';
        $winners = Winner::with('product', 'user')
        ->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })
        ->latest()->paginate(getPaginate());

        return view('merchant.vehicle.winners', compact('pageTitle', 'emptyMessage', 'winners'));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::with('product')->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify[] = ['success', 'Product mark as delivered'];
        return back()->withNotify($notify);

    }
    public function varitylist( $id= null){

        $segments       = request()->segments();

        $pageTitle =  'Variation';
       $emptyMessage = 'No ' . $segments[1] . ' found';
        $varieties = Variety::with('product')
        ->whereHas('product', function ($query) {
            $query->where('merchant_id', auth()->guard('merchant')->id());
        })
        ->where('product_id', $id);
        if(request()->search){
            $search = request()->search;
            $varieties = $varieties->orWhere('name', 'like', '%' . $search . '%');
            $this->pageTitle = "Search Result for '$search'";
        }



        $varieties =  $varieties->latest('varieties.created_at')
        ->paginate(getPaginate());


          return view('merchant.varity.index',compact('pageTitle','emptyMessage','varieties'));
    }
    public function varityEdit($id){
        $pageTitle = 'Edit Variation';
        $categories = Productcategory::all()->pluck('id')->toArray();
        $products = Product::where('merchant_id', auth()->guard('merchant')->id())->whereIn('productcate_id', $categories)->get();

        $id = intval($id);
        $variation = Variety::where('id', $id)->first();
        return view('merchant.varity.edit', compact('variation', 'products', 'pageTitle'));
    }
    public function varietyCreate(){
        $pageTitle = 'Create Variation';
       $categories = Productcategory::all()->pluck('id')->toArray();
       $products = Product::where('merchant_id', auth()->guard('merchant')->id())->whereIn('productcate_id', $categories)->get();

       return view('merchant.varity.create', compact('products','pageTitle'));
    }
    protected function varietyValidation($request, $imgValidation){
        $request->validate([
            'name'                  => 'required',
            'product'              => 'required|exists:products,id',
            'price'                 => 'required|numeric|gte:0',
        ]);
    }
    public function varietyStore(Request $request){
        $this->varietyValidation($request, 'required');
        $variety = new Variety();
        $notify[] = ['success', 'Variety added successfully'];
        $variety->name = $request->name;
        $variety->price = $request->price;
        $variety->product_id = $request->product;
        $variety->save();
        return back()->withNotify($notify);
    }
    public function variationUpdate(Request $request, $id){

        $variation = Variety::find($id);
        $this->varietyValidation($request, 'required');
        $variation->name = $request->name;
        $variation->price = $request->price;

        $variation->product_id = $request->product;
        $variation->save();
        $notify[] = ['success', 'Variation updated successfully'];
        return back()->withNotify($notify);
    }
    public function ApiProductstore(Request $request)
    {
         $validator = Validator::make($request->all(), [
        'name' => 'required',
        'category' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    $product = new Product();
    $product->name = $request->input('name');
    $product->category = $request->input('category');
    $product->save();

    return response()->json(['message' => 'Product created successfully'], 201);
    }
}
