<?php

namespace App\Http\Controllers\Media;


use App\Http\Controllers\Controller;
use App\Models\Post;
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
                $imagePath = self::getFilePath($row->guid);
                return '<img src="'.$imagePath.'" width="200" height="200" >';
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


      //   $this->viewData['shops']        = $shops;
        return view($this->view.'lists',$this->viewData);
    }

    public function create(){
        $this->viewData['category']     = []; //Category::with('subCategories')->orderBy('name','DESC')->get();
        $this->viewData['uoms']         = []; // Uom::orderBy('code','ASC')->get()->pluck('code','id');
        return view($this->view.'create',$this->viewData);
    }

    public function store(Request $request){

        $name           = $request->get('post_title');
        $files          = $request->file('post_files');
        $status         = 'publish'; //$request->get('post_status');
        $productName = Str::slug($request->get('post_title'));
        $imageCount = (int) Post::where('post_type','attachment')->count();
        if($request->hasFile('post_files')){
            try{
                $mediaFiles = [];
                foreach($files as $file){
                    $imageCount++;
                    $fileNameWithExt = $file->getClientOriginalName();
                    $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileNameToStore = "logo-".$imageCount.'-'.time().'.'.$extension;
                    $mimeType = $file->getClientMimeType();
                    $path = $file->storeAs('public/post_images', $fileNameToStore);
                    /*
                    $imgFile = Image::fromFile($path);
                    $imgFile->resize(150, 150, Image::EXACT );
                    $imgFile->cropAuto()->save($path);
                    */
                    Post::create([
                        'post_title' => $filename,
                        'post_author' =>  (auth()->user())? optional(auth()->user())->id:'',
                        'post_status' => $status,
                        'post_type' => 'attachment',
                        'guid' => $fileNameToStore,
                        'post_mime_type' => $mimeType
                    ]);
                    $mediaFiles[] = ['file_url' => self::getFilePath($fileNameToStore),'post_title' => $filename];
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



    /**********************************************************************************************/
    //          APIS                //

    function readXml(){
         $xmlString = file_get_contents(storage_path() . "/designs/business_74.xib");

        $xml = simplexml_load_string($xmlString);

        $json = json_encode($xml);
        
        $array = json_decode($json,TRUE);


        dd($array['objects']['view']['subviews']);


        // $xml = new \XMLReader();
        // $xml->open(storage_path() . "/designs/business_74.xib");

        // try {
        //     while ($xml->read()) {
               
        //         if ($xml->nodeType == \XMLReader::ELEMENT) {

                   

        //             //assuming the values you're looking for are for each "item" element as an example
        //             if ($xml->name == 'document') {

        //               // dd($xml);

        //                 $variable[++$counter] = new \stdClass();
        //                 $variable[$counter]->thevalueyouwanttoget = '';

        //             }
        //             if ($xml->name == 'thevalueyouwanttoget') {
        //                 $variable[$counter]->thevalueyouwanttoget = $xml->readString();
        //             }
        //         }
        //     }
        // } catch (Exception $e) {
        //     echo $e->getMessage();
        // } 
        // finally() {
        //     $xml->close();
        // }

        /*
        $reader = XmlReader::fromString($xmlString);
        // Retrieve all values as one simple array
        $subView = $reader->value('subviews')->collect();
        $image = $reader->value('subviews')->element('imageView.0')->sole()->element('rect')->getAttributes();
        dd($image);
        dd($reader->value('subviews')->collect()); 
        */

    }


    function dataImport(Request $request){

        if($request->ajax()){
            // dd($request->all());


            $file = $request->file('product_image');
            $fileContents = file($file->getPathname());

            $action = $request->get('action');



            $templateArr = array();
        
            $index = 0;
            foreach ($fileContents as $line) {
                if( $index == 0 ){
                    $index++;
                    continue;
                }
                $index++;

                $data = str_getcsv($line);

                /*
                if(!isset($templateArr[$data[0]])){
                    $templateArr[$data[0]] = [
                        'id' => $data[0],
                        'name' => $data[1],
                        'bounds' => $data[2],
                    ];
                }
                $templateArr[$data[0]][$data[3]][] = [
                    'type' => $data[4],
                    'frame' => $data[5],
                    'text' => $data[6],
                    'font_name' => $data[7],
                    'font_size' => $data[8],
                    'font_color' => $data[9],
                    'image_name' => $data[10]
                ];
                */


                // if(!isset($templateArr[$data[0]])){
                //     $templateArr[$data[0]] = [
                //         'id' => $data[0],
                //         'name' => $data[1],
                //         'bounds' => $data[2],
                //     ];
                // }
                // $templateArr[$data[0]]['layers'][$data[3]][] = [
                //     'type' => $data[4],
                //     'frame' => $data[5],
                //     'text' => $data[6],
                //     'font_name' => $data[7],
                //     'font_size' => $data[8],
                //     'font_color' => $data[9],
                //     'image_name' => $data[10]
                // ];

                if(!isset($templateArr[$data[0]])){
                    $templateArr[$data[0]] = [
                        'id' => $data[0],
                        'name' => $data[1],
                        'bounds' => $data[2],
                    ];
                }
                $templateArr[$data[0]][] = [
                    'type' => $data[3],
                    'frame' => $data[4],
                    'text' => $data[5],
                    'font_name' => $data[6],
                    'font_size' => $data[7],
                    'font_color' => $data[8],
                    'image_name' => $data[9]
                ];



            }

            if($action == 'insert'){
                foreach($templateArr as $template){
                    Post::create([
                        'post_type' => 'logo',
                        'post_title' => $template['name'],
                        'post_content' => json_encode($template),
                        'post_author' => 1
                    ]);
                }
                $view = '<p>Insert successfully!</p>';                
            }else{
                $view = json_encode($templateArr);
            }

            return response()->json([
                'message' => 'Done',
                'view' => $view 
            ]);

        }else{
            return view($this->view.'data_import');
        }

    }


    static function getFilePath($fileName){
        return Storage::disk('public')->url('post_images/'.$fileName);
    }



}
