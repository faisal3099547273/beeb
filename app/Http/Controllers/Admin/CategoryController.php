<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Productcategory;
class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Categories';
        $emptyMessage = 'No category found';
        $categories = Category::latest()->with('products')->paginate(getPaginate());

        return view('admin.category.index', compact('pageTitle', 'emptyMessage', 'categories'));
    }
    public function porductCategory(){
        $pageTitle = 'All Categories';
        $emptyMessage = 'No category found';
        $categories = Productcategory::latest()->with('catProducts')->paginate(getPaginate());
        return view('admin.productCategory.index', compact('pageTitle', 'emptyMessage', 'categories'));
    }
    public function saveCategory(Request $request, $id=0)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required'
        ]);

        $category = new Category();
        $notification =  'Category created successfully';

        if($id){
            $category = Category::findOrFail($id);
            $category->status = $request->status ? 1 : 0;
            $notification = 'Category updated successfully';
        }

        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);

    }
    public function saveProductCategory(Request $request, $id = null){
        $request->validate([
            "name" => "required",
            "icon" => "required",
        ]);
        $category = new Productcategory();
        $notification = "Category created successfully";
        if($id){
            $category = Productcategory::findOrFail($id);
            $category->status = $request->status ? 1 : 0;
            $notification = "Category updated successfully";
        }
        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->save();
        $notify[] = ['sucess', $notification];
        return back()->with($notify);
    }
}
