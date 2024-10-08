<?php

namespace App\Http\Controllers\Term;


use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use Exception;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;

class TermController extends Controller
{
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;




    public function __construct(){
        $this->resource     = new Term();
        $this->name         = $this->viewData['name']         =   'term';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/term');
        $this->table        = 'terms';
        $this->view         = 'term.';
    }


 
    public function index(Request $request){


        if($request->ajax()){

            $order      = $request->get('order_by','id-desc');
            $column     = explode('-',$order)[0];
            $sort       = explode('-',$order)[1];

            $results = Term::with(['user' => function($query) use ($request){}]
            )->select()->where(function ($query) use ($request) {

            })->orderByRaw($column.' '.$sort)->get();
            
            
            $datatable =  Datatables::of($results)
            ->addColumn('action', function ($row) {
               //  return $row;
                    $statusIcon = 'fa fa-ban';
                    if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                     $action .= '<a href="'.route('term.logo_category.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                     $action .= '<a href="'.route('term.logo_category.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                     $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                    //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','action']);

         

            //  ->orderColumn('id', 'DESC')
            return $datatable->make(true);
    }


      //   $this->viewData['shops']        = $shops;
        return view($this->view.'lists',$this->viewData);
    }

    public function create(){
        $this->viewData['category']     = []; //Category::with('subCategories')->orderBy('name','DESC')->get();
        $this->viewData['uoms']         = []; // Uom::orderBy('code','ASC')->get()->pluck('code','id');
        return view($this->view.'create',$this->viewData);
    }

    public function store(Request $request){
        $name           = $request->get('name');
        $description    = $request->get('description');
        $status         = $request->get('status');
        $imageName      = null;
        
        if($request->hasFile('image')){ 
            $imageObj = $this->uploadProductImage($request);
            if(  !($imageObj[0]) ){
                return response()->json(['message' => $imageObj[1]], 500);
            }else{
                $imageName = $imageObj[1];
            }
        }

        $array = array(
            'name' => $name,
            'description' => $description,
            // 'post_author' => auth()->user()->id,
            'status' => $status,
        );


        

        try{
            DB::beginTransaction();
            $product    = Term::create($array);
            $insertArr = [];
            DB::commit();

            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Product Add successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success',$this->name.' added successful!');
            }
            
            
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error.Please Contact   Support'. $e->getMessage()], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Term $product, $id){
        $product = Term::find($id);
        $this->viewData['row'] = $product;
        $this->viewData['categories']   = $product->categories_tree();
        return view($this->view.'detail_new',$this->viewData);
    }


    public function edit(Term $product,$id){
        
        $product = Term::find($id);
        $this->viewData['product']      = $product;
        $this->viewData['categories']   = []; // $product->categories_tree();
        return view($this->view.'edit',$this->viewData);
    }


    public function update(Request $request, Post $product){

        $productID = $request->get('id');
        $name           = $request->get('name');
        $description    = $request->get('description');
        $status         = $request->get('status');

        $array = array(
            'name' => $name,
            'description' => $description,
            'status' => $status,
           //  'image' => $imageName
        );

       try{
            DB::beginTransaction();
            Term::where('id',$productID)->update($array);
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Category Update successfully.' ]);
            }else{
                return redirect(route('post.logo.list'))->with('success',$this->name.' added successful!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Product $product){
        dd('destory stop');
        $res = StockItem::where('product_id', $id)->get();
        if( count($res) > 0){
            return redirect(route('product.list'))->with('error','This product have stock item. First delete them');
        }else{
            Product::find($id)->delete();
            return redirect(route('product.list'))->with('success','Product updated successful!');
        }
    }









    function uploadProductImage(Request $request){
        $productName = Str::slug($request->get('name'));
        if($request->hasFile('product_image')){
            try{
                //Getting file name with extension
                $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
                //Get just file name
                // $filename        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                //Get just ext
                $extension       = $request->file('product_image')->getClientOriginalExtension();
                //Filename to store
                $fileNameToStore = "product-".$productName.'-'.time().'.'.$extension;
                //Upload Image
                $path = $request->file('product_image')->storeAs('public/product_images', $fileNameToStore);


                /*******   CREATE TUMBNAIL   *******/
                
               //  echo Storage::get($path); exit;

                $path = Storage::path('public/product_images/'.$fileNameToStore);

                // $publicPath = "public\product_images\"".$fileNameToStore;

                $imgFile = Image::fromFile($path);
                $imgFile->resize(150, 150, Image::EXACT );
                $imgFile->cropAuto()->save($path);
                // $imgFile->resize(150, 150, function ($constraint) {
                //     // $constraint->aspectRatio();
                //     $constraint->cropAuto();
                // })->save($path);
                // $destinationPath = public_path('/uploads');
                //$image->move($destinationPath, $input['file']);
                /**************/

                return [true,$fileNameToStore];
            }catch(Exception $e){
                return [false,$e->getMessage()];
            }
        }
        return [false,'Image not found!'];
    }


    function status($eid){
        $emp = Post::find($eid);
        if($emp->status == 0){
            $emp->status = 1;
        }else{
            $emp->status = 0;
        }
        $emp->save();
        return array('status' => true);
    }


}
