<?php

namespace App\Http\Controllers\Admin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Csvmodel;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;
    protected $search;
    protected function filterVehicles($type)
    {
        $query = Csvmodel::select('Year', 'Model', 'Make', 'Platform', 'Notes', 'admin_id',  'Class');

        if ($type != 'all') {
            $query = $query->where('column_name', $type); // Replace 'column_name'
               //with the actual column you want to filter by.
        }

        if (request()->search) {
            $search = request()->search;

            $query = $query->where(function ($qq) use ($search) {
                $qq->where('Year', 'like', '%' . $search . '%');
            });

            $this->pageTitle = "Search Result for '$search'";
            $this->search = $search;
        }
        $query = $query->groupby('Year');
        return $query->orderBy('id', 'DESC')->latest()->paginate(getPaginate());
    }


    public function uploadfile(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $file = $request->file('excel_file');

        // Load the Excel file using PhpSpreadsheet
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Assuming the first row contains column headers
        $headers = $data[0];

        // Loop through the remaining rows and insert data into the database
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];

            $rowData = [];
            foreach ($headers as $key => $header) {
                $rowData[$header] = $row[$key];
            }

            // Create a new record in the database table
            Csvmodel::create($rowData);
        }

        return "done";
    }
public function csvForm(){
    $pageTitle = 'Create Product';
    // $categories = Category::where('status', 1)->get();
    return view('admin.product.upload-excel');
}
public function index(){

   $segments       = request()->segments();
   $csv       = $this->filterVehicles(end($segments));
   $pageTitle = 'Create Model';
   $emptyMessage   = $this->emptyMessage;
   $search         = $this->search;
    return view('admin.model.index',compact('csv','pageTitle'));
}
public function create()
    {
        $pageTitle = 'Create Model';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();

        return view('admin.model.create', compact('pageTitle', 'categories','distinctYears','distinctClass','distinctModels','distinctMake'));
    }
    public function edit($id)
    {
        $pageTitle = 'Update Model';
        $categories = Category::where('status', 1)->get();
        $distinctYears = Csvmodel::distinct()->select('Year')->get();
        $distinctClass = Csvmodel::distinct()->select('Class')->get();
        $distinctModels = Csvmodel::distinct()->select('Model')->get();
        $distinctMake = Csvmodel::distinct()->select('Make')->get();
        $product = Csvmodel::findOrFail($id);

        return view('admin.model.edit', compact('pageTitle', 'categories', 'product','distinctYears','distinctClass','distinctModels','distinctMake'));
    }

    public function store(Request $request)
    {
        $this->validation($request);
        $product            = new Csvmodel();
        $product->admin_id  = auth()->guard('admin')->id();
        $product->status    = 1;
     ;
// return "SS";
        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Model added successfully'];
        return back()->withNotify($notify);
    }
    public function update(Request $request, $id)
    {
        $this->validation($request, 'nullable');
        $product = Csvmodel::findOrFail($id);
        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Model updated successfully'];
        return back()->withNotify($notify);
    }
    public function saveProduct($request, $product)
    {

        $product->Year = $request->Year;
        $product->Make = $request->Make;
        $product->Model = $request->Model;
        $product->Platform = $request->Platform;
        $product->Class = $request->Class;
        $product->Notes = $request->Notes;
        $product->admin_id = 1;


        $product->save();
        // dd('ss');
    }
    protected function validation($request){
        $request->validate([
            'Platform'           => 'required',
            'Notes'              => 'required',
            'Year'                 => 'required|numeric|gte:0',
            'Model'            => 'required',
            'Class'     => 'required',
            'Make'      => 'required',
            'specification'         => 'nullable|array',

        ]);
    }
    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $product = Csvmodel::findOrFail($request->id);
        $product->status = 1;
        $product->save();

        $notify[] = ['success', 'Product Approved Successfully'];
        return back()->withNotify($notify);
    }
}
