<?php

namespace App\Http\Controllers\Media;


use App\Http\Controllers\Controller;
use App\Http\Requests\MediaFromRequest;
use App\Models\Post;
use App\Util\Util;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use Exception;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;



class MediaController extends Controller
{
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;




    public function __construct(){
        $this->resource     = new Post();
        $this->name         = $this->viewData['name']         =   'media';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/media');
        $this->table        = 'posts';
        $this->view         = 'media.';
    }


 
    public function index(Request $request){


        if($request->ajax()){

            $order      = $request->get('order_by','id-desc');
            $column     = explode('-',$order)[0];
            $sort       = explode('-',$order)[1];

            $results = Post::with(['user' => function($query) use ($request){}]
            )->select()->where(function ($query) use ($request) {
                $query->where('post_type','attachment');

            })->orderByRaw($column.' '.$sort)->get();
            
            
            $datatable =  Datatables::of($results)
            ->addColumn('image_thumb', function ($row) {
                $imagePath = Util::imageUrl($row->guid);
                return '<img src="'.$imagePath.'" width="75" height="75" onclick="viewImageLargeView(this);" >';
            })->addColumn('user_name', function ($row) {
                return '';
            })->addColumn('action', function ($row) {
               //  return $row;
                    $statusIcon = 'fa fa-ban';
                    if($row->post_status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                    }
                     $action = '<div class="hidden-sm hidden-xs btn-group">';
                     $action .= '<a href="'.route('post.logo.view', $row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                     $action .= '<a href="'.route('post.logo.edit', $row->id).'" class="btn btn-xs btn-info"  data-toggle="tooltip" title="Edit" ><i class="ace-icon fa fa-pencil bigger-120"></i></a>';
                     $action .= '<a href="javascript:void(0);" onclick="changeStatus('.$row->id.');" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Change Status" ><i class="ace-icon '.$statusIcon.' bigger-120"></i></a>';
                    //  $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','image_thumb','action']);

         

            //  ->orderColumn('id', 'DESC')
            return $datatable->make(true);
    }
        return view($this->view.'lists',$this->viewData);
    }

    public function create(){
        $this->viewData['category']     = [];
        $this->viewData['uoms']         = [];
        return view($this->view.'create',$this->viewData);
    }

    public function store(MediaFromRequest $request){
        $files          = $request->file('post_files');
        if($request->hasFile('post_files')){
            try{
                $mediaFiles = [];
                foreach($files as $file){
                    $media = Util::fileUploadToMediaGallery($file);
                    if(isset($media->id)){
                        $imageUrl = Util::imageUrl($media->guid);
                        $mediaFiles[] = ['file_url' => $imageUrl,'post_title' => $media->guid];
                    }
                }
                return response()->json(['data' => $mediaFiles, 'message' => count($mediaFiles).' files uploaded successfully.' ]);
            }catch(\Exception $e){
                return response()->json(['data' => [], 'message' => $e->getMessage() ],500);
            }
        }
        return response()->json(['data' => [], 'message' => 'No image found!' ],422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, $id){
        $product = Product::find($id);
        $this->viewData['row'] = $product;
        $this->viewData['categories']   = $product->categories_tree();
        return view($this->view.'detail_new',$this->viewData);
    }


    public function edit(Post $product,$id){
        
        $product = Post::find($id);
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
            'post_title' => $name,
            'post_content' => $description,
            'post_author' => auth()->user()->id,
            'post_status' => $status,
           //  'image' => $imageName
        );

       try{
            DB::beginTransaction();
            Post::where('id',$productID)->update($array);
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Post Update successfully.' ]);
            }else{
                return redirect(route('post.logo.list'))->with('success',$this->name.' added successful!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

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
