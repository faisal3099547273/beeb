<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\Winner;
use App\Models\Csvmodel;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use App\Models\Productcategory;
use App\Models\Galleryproduct;
class ProductController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;
    protected $search;

    protected function filterProduct($type){
        $category_ids = Productcategory::pluck('id')->toArray();
        $products = Product::whereNotIn('productcate_id', $category_ids);
        $this->pageTitle    = ucfirst($type). 'Products';
        $this->emptyMessage = 'No '.$type. ' products found';

        if($type != 'all'){
            $products = $products->$type();
        }

        if(request()->search){
            $search  = request()->search;

            $products    = $products->where(function($qq) use ($search){
                $qq->where('name', 'like', '%'.$search.'%')->orWhere(function($product) use($search){
                    $product->whereHas('merchant', function ($merchant) use ($search) {
                        $merchant->where('username', 'like',"%$search%");
                    })->orWhereHas('admin', function ($admin) use ($search) {
                        $admin->where('username', 'like',"%$search%");
                    });
                });
            });

            $this->pageTitle    = "Search Result for '$search'";
            $this->search = $search;
        }

        return $products->with('merchant', 'admin')->orderBy('admin_id', 'DESC')->latest()->paginate(getPaginate());
    }
    protected function filterProducts($type)
    {
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

        return $query->with('merchant', 'admin')->orderBy('admin_id', 'DESC')->latest()->paginate(getPaginate());
    }


    public function index()
    {

        $segments       = request()->segments();
        $products       = $this->filterProduct(end($segments));
        $pageTitle      = $this->pageTitle;
        $emptyMessage   = $this->emptyMessage;
        $search         = $this->search;

        return view('admin.vehicle.index', compact('pageTitle', 'emptyMessage', 'products', 'search'));
    }
    public function list(){
        $segments       = request()->segments();
        $products       = $this->filterProducts(end($segments));
        $pageTitle      = $this->pageTitle;
        $emptyMessage   = $this->emptyMessage;
        $search         = $this->search;
        return view('admin.product.index', compact('pageTitle', 'emptyMessage', 'products', 'search'));
    }
    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $product = Product::findOrFail($request->id);
        $product->status = 1;
        $product->save();

        $notify[] = ['success', 'Vehicle Approved Successfully'];
        return back()->withNotify($notify);
    }

    public function create()
    {
        $pageTitle = 'Create Vehicle';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();



        return view('admin.vehicle.create', compact('pageTitle', 'categories','distinctYears','distinctClass','distinctModels','distinctMake'));
    }
    public function createProduct(Request $request){
        $pageTitle = "Create Product";
        $categories = Productcategory::where('status', 1)->get();
        return view('admin.product.create', compact('categories'));
    }
    public function edit($id)
    {
        $pageTitle = 'Update Vehicle';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();
        $images = Gallery::get();
        $existingImageIds = Galleryproduct::where('product_id', $id)->pluck('id')->toArray();
        $product = Product::findOrFail($id);

        return view('admin.vehicle.edit', compact('pageTitle', 'existingImageIds','images', 'categories', 'product','distinctYears','distinctClass','distinctModels','distinctMake'));
    }
    public function editproduct($id){
        $pageTitle = 'Update Vehicle';
        $categories = Productcategory::where('status', 1)->get();
        $images = Galleryproduct::get();
        $existingImageIds = Galleryproduct::where('product_id', $id)->pluck('id')->toArray();


        $product = Product::findOrFail($id);
       return view('admin.product.edit', compact('categories', 'product', 'product', 'images', 'existingImageIds'));
    }

    public function store(Request $request)
    {
        // return $request->image;

        $this->validation($request, 'required');
        $product            = new Product();
        $product->admin_id  = auth()->guard('admin')->id();
        $product->status    = 1;
// return "SS";
        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Vehicle added successfully'];
        return back()->withNotify($notify);
    }
    public function productStore(Request $request){
        $this->validations($request, 'required');
        $product            = new Product();
        $product->admin_id  = auth()->guard('admin')->id();
        $product->status    = 1;
        $this->saveProducts($request, $product);
        $notify[] = ['success', 'Product added successfully'];
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
    public function updateProduct(Request $request, $id){
       $this->validations($request, 'nullable');
       $product = Product::findOrFail($id);
       $this->saveProducts($request, $product);
       $notify[] = ['success', 'Vehicle updated successfully'];
       return back()->withNotify($notify);
    }

    public function saveProduct($request, $product)
    {
        //  return $request->image;
        if ($request->hasFile('image')) {
            // try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            // } catch (\Exception $exp) {
            //     $notify[] = ['error', 'Image could not be uploaded.'];
            //     return back()->withNotify($notify);
            // }
        }


        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
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
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $imagePath = $image->getClientOriginalName();
                $imagePaths = $image->move('assets/products/', $imagePath);

                // Create a new Galleryproduct record for each uploaded image
                $galleryProduct = new Gallery;
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
                $gallery = new Gallery();
                $gallery->product_id = $product->id;
                $gallery->image =  $imagePaths;
                $gallery->save();
            }
        }

    }

    public function saveProducts($request, $product)
    {
        //  dd($request->all());
        //  return $request->image;
        if ($request->hasFile('image')) {
            // try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            // } catch (\Exception $exp) {
            //     $notify[] = ['error', 'Image could not be uploaded.'];
            //     return back()->withNotify($notify);
            // }
        }
        // unset($request->has('existing_image_ids'));
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->productcate_id = $request->category;
        $product->price = $request->price;
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
            'quantity'                 => 'required|numeric',
            'short_description'     => 'required',
            'image'                 => [$imgValidation,'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);
    }

    public function productBids($id)
    {
        $product = Product::with('winner')->findOrFail($id);
        $pageTitle = $product->name.' Bids';
        $emptyMessage = $product->name.' has no bid yet';
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(getPaginate());
        return view('admin.vehicle.product_bids', compact('pageTitle', 'emptyMessage', 'bids'));
    }

    public function bidWinner(Request $request)
    {
        $request->validate([
            'bid_id' => 'required'
        ]);

        $bid = Bid::with('user', 'product')->findOrFail($request->bid_id);
        $product = $bid->product;
        $winner = Winner::where('product_id', $product->id)->exists();

        if($winner){
            $notify[] = ['error', 'Winner for this product is already selected'];
            return back()->withNotify($notify);
        }

        if($product->expired_at > now()){
            $notify[] = ['error', 'This product is not expired till now'];
            return back()->withNotify($notify);
        }

        $user = $bid->user;
        $general = GeneralSetting::first();

        $winner = new Winner();
        $winner->user_id = $user->id;
        $winner->product_id = $product->id;
        $winner->bid_id = $bid->id;
        $winner->save();

        notify($user, 'BID_WINNER', [
            'product' => $product->name,
            'product_price' => showAmount($product->price),
            'currency' => $general->cur_text,
            'amount' => showAmount($bid->amount),
        ]);

        $notify[] = ['success', 'Winner selected successfully'];
        return back()->withNotify($notify);
    }

    public function productWinner(){
        $pageTitle = 'All Winners';
        $emptyMessage = 'No winner found';
        $winners = Winner::with('product', 'user')->latest()->paginate(getPaginate());

        return view('admin.vehicle.winners', compact('pageTitle', 'emptyMessage', 'winners'));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::with('product')->whereHas('product')->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify[] = ['success', 'Product mark as delivered'];
        return back()->withNotify($notify);

    }
}
