<?php

namespace App\Http\Controllers\Post;


use App\Http\Controllers\Controller;
use App\Http\Requests\PostFromRequest;
use App\Models\Post;
use App\Models\PostTerm;
use App\Models\SubView;
use App\Models\Term;
use App\Util\Util;
use Illuminate\Http\Request;


use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use Exception;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;



class PostController extends Controller
{
    public $resource;
    public $name;
    public $adminURL;
    public $viewData;
    public $table;
    public $view;




    public function __construct(){
        $this->resource     = new Post();
        $this->name         = $this->viewData['name']         =   'post';
        $this->adminURL     = $this->viewData['adminURL']     =   url('/post');
        $this->table        = 'posts';
        $this->view         = 'post.';
    }


    function searchImage(Request $request){
        $query = $request->get('query');

        $medias = Post::where('post_type','attachment')
        // ->whereLike('name', '%'.$query.'%')
        ->limit(15)->get();
        $mediaArr = array();
        foreach($medias as $media){
            $mediaArr[] = [
                'id' => $media->id,
                'title' => $media->post_title,
                'img' => Util::imageUrl($media->guid)
            ];
        }
        return $mediaArr;
    }


 
    public function index(Request $request){


        if($request->ajax()){

            $order      = $request->get('order_by','id-desc');
            $column     = explode('-',$order)[0];
            $sort       = explode('-',$order)[1];

            $results = Post::with(['user' => function($query) use ($request){}]
            )->select()->where(function ($query) use ($request) {
                $query->where('post_type','logo');

            })->orderByRaw($column.' '.$sort)->get();
            
            
            $datatable =  Datatables::of($results)
            ->addColumn('attributes_tags', function ($row) {
                return '';
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
            })->rawColumns(['status_label','action']);

         

            //  ->orderColumn('id', 'DESC')
            return $datatable->make(true);
    }


      //   $this->viewData['shops']        = $shops;
        return view($this->view.'lists',$this->viewData);
    }

    public function create(){
        // $this->viewData['row'] = [];
        $this->viewData['category'] = Term::all()->pluck('name','id');
        return view($this->view.'create',$this->viewData);
    }

    public function store(PostFromRequest $request){
        $name           = $request->get('name');
        $description    = $request->get('description');
        $status         = $request->get('status');
        $imageName      = null;

        $subViewIds = $request->get('sub_view_id');
        $types = $request->get('type');
        $frames = $request->get('frame');
        $texts = $request->get('text');
        $fontNames = $request->get('font_name');
        $fontSizes = $request->get('font_size');
        $textColors = $request->get('text_color');

        $templateBounds = $request->get('template_bounds');
        $subviewImageFileArr = $request->file('subview_image_file');
        $postThumbFile =  $request->file('template_thumbnail_input');
        $categories = $request->get('category');
        $media = null;

        if($postThumbFile){
            $media = $imageObject = Util::fileUploadToMediaGallery($postThumbFile);
        }
        
        
        $array = array(
            'post_title' => $name,
            'post_content' => '',
            'post_author' => auth()->user()->id,
            'post_status' => $status,
            'post_type' => 'logo',
            'thumbnail_id' => (isset($media->id))?$media->id:null,
            'template_bounds' => $templateBounds
        );

        try{
            DB::beginTransaction();
            $product    = Post::create($array);
            $index = 0;
            foreach($types as $type){
                $subViewId = $subViewIds[$index];

                $subView['type'] =  $type;
                $subView['frame'] =  $frames[$index];
                $subView['text'] =  $texts[$index];
                $subView['font_name'] =  $fontNames[$index];
                $subView['font_size'] =  $fontSizes[$index];
                $subView['text_color'] =  $textColors[$index];
               //  $subView['image_name'] =  $imageNames[$index];
                $subView['post_id'] = $product->id;

                // subview_image_file
                if(isset($subviewImageFileArr[$index])){
                    $subViewImage = null;
                    $imageObject = Util::fileUploadToMediaGallery($subviewImageFileArr[$index]);
                    if(isset($imageObject->id)){
                        $subView['image_name'] =  $imageObject->guid; // $imageObject[$index];
                        $subView['media_id'] =  $imageObject->id;
                    }
                }
                
                SubView::create($subView);
                $index++;
            }

            $catIndex = 0;
            foreach($categories as $cat){
                $categoryId = $cat[$catIndex];
                PostTerm::create([
                    'post_id' => $product->id,
                    'term_id' => $categoryId
                ]);
            }


            DB::commit();

            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Template created successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success',$this->name.' added successful!');
            }
            
            
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getLine() .' : Error : Please Contact Support'. $e->getMessage()], 500);
            // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
        }
    }


    public function show(Post $post, $id){
        $this->viewData['row'] = Post::find($id);
        $this->viewData['category'] = Term::all()->pluck('name','id');
        $this->viewData['subview'] = SubView::where('post',$id)->get();
        return view($this->view.'detail_new',$this->viewData);
    }


    public function edit(Post $product,$id){
        $template = Post::find($id);
        $this->viewData['row'] = $template;
        $this->viewData['category'] = Term::all()->pluck('name','id');

        $thumbnail = Post::find($template->thumbnail_id);
       //  dd($template->thumbnail_id, $thumbnail, $template);
        $this->viewData['thumbnail'] = (isset($thumbnail->guid))? Util::imageUrl($thumbnail->guid):'';
        $this->viewData['post_category'] = PostTerm::where('post_id',$id)->pluck('term_id')->toArray();
        $this->viewData['subview'] = SubView::where('post_id',$id)->get();
        return view($this->view.'edit',$this->viewData);
    }

    public function update(PostFromRequest $request, Post $product, $id){
        $product = Post::find($id);
        $name           = $request->get('name');
        $description    = $request->get('description');
        $status         = $request->get('status');
        $imageName      = null;
        $postSubView    = SubView::where('post_id',$id)->pluck('id')->toArray();

        $subViewIds = $request->get('sub_view_id');
        $types = $request->get('type');
        $frames = $request->get('frame');
        $texts = $request->get('text');
        $fontNames = $request->get('font_name');
        $fontSizes = $request->get('font_size');
        $textColors = $request->get('text_color');
        $categories = $request->get('category');

        $templateBounds = $request->get('template_bounds');
        $subviewImageFileArr = $request->file('subview_image_file');
        $postThumbFile =  $request->file('template_thumbnail_input');
        $categories = $request->get('category');
        $media = null;

        if($postThumbFile){
            $media = $imageObject = Util::fileUploadToMediaGallery($postThumbFile);
        }

        $array = array(
            'post_title' => $name,
            'post_content' => '',
            'post_author' => auth()->user()->id,
            'post_status' => $status,
            'post_type' => 'logo'
        );

        if( isset($media->id) ){
            $array['thumbnail_id'] = $media->id;
        }

        try{
            DB::beginTransaction();
            Post::where('id',$id)->update($array);

            foreach($postSubView as $postSubviewId){
                if(!in_array($postSubviewId,$subViewIds)){
                    SubView::where(['post_id' => $id, 'id' => $postSubviewId])->delete();
                }
            }
            
          //  dd($subViewIds);

            $index = 0;
            foreach($types as $type){
                $subViewId = $subViewIds[$index];

                $subView['type'] =  $type;
                $subView['frame'] =  $frames[$index];
                $subView['text'] =  $texts[$index];
                $subView['font_name'] =  $fontNames[$index];
                $subView['font_size'] =  $fontSizes[$index];
                $subView['text_color'] =  $textColors[$index];
                $subView['post_id'] = $product->id;


                if(isset($subviewImageFileArr[$index])){
                    $subViewImage = null;
                    $imageObject = Util::fileUploadToMediaGallery($subviewImageFileArr[$index]);
                    if(isset($imageObject->id)){
                        $subView['image_name'] =  $imageObject->guid; // $imageObject[$index];
                        $subView['media_id'] =  $imageObject->id;
                    }
                }


                if($subViewId){
                    SubView::where('id', $subViewId)->update($subView);
                }else{
                    SubView::create($subView);
                }
                $index++;
            }
            $catIndex = 0;
            foreach($categories as $cat){
                $categoryId = $cat[$catIndex];
                $postCat = [
                    'post_id' => $product->id,
                    'term_id' => $categoryId
                ];
                $postCat = PostTerm::where($postCat)->first();
                if(!$postCat){
                    PostTerm::create($postCat);
                }
            }
            DB::commit();
            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Post Update successfully.' ]);
            }else{
                return redirect(route('post.logo.list'))->with('success',$this->name.' added successful!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getLine() .' : Error : Please Contact Support'. $e->getMessage()], 500);
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
    //          DataImport                //
    function dataImport(Request $request){

        if($request->ajax()){
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

                if(!isset($templateArr[$data[0]])){
                    $templateArr[$data[0]] = [
                        'id' => $data[0],
                        'name' => $data[1],
                        'categories' => $data[2],
                        'bounds' => $data[3],
                    ];
                }
                $templateArr[$data[0]]['sub_views'][] = [
                    'type' => $data[4],
                    'frame' => $data[5],
                    'text' => $data[6],
                    'font_name' => $data[7],
                    'font_size' => $data[8],
                    'font_color' => $data[9],
                    'image_name' => $data[10]
                ];
            }

            // dd(templateArr);

            if($action == 'insert'){


                try{


                foreach($templateArr as $template){


                  

                    $post = Post::create([
                        'post_type' => 'logo',
                        'post_title' => $template['name'],
                        'post_content' => '',//json_encode($template),
                        'post_author' => 1
                    ]);

                    $categories = explode(',',$template['categories']);

                    foreach($categories as  $category){
                        $categoryId = $this->getCategoryIdByName($category);
                        PostTerm::create([
                            'post_id' => $post->id,
                            'term_id' => $categoryId
                        ]);
                    }

                    foreach($template['sub_views'] as  $subView){


                    //     dd([
                    //         'post_type' => 'logo',
                    //         'post_title' => $template['name'],
                    //         'post_content' => '', // json_encode($template),
                    //         'post_author' => 1
                    //     ],
                    //     [
                    //        //  'post_id' => $post->id,
                    //         'type' => $subView['type'],
                    //         'frame' => $subView['frame'],
                    //         'text' => $subView['text'],
                    //         'font_name' => $subView['font_name'],
                    //         'font_size' => $subView['font_size'],
                    //         'font_color' => $subView['font_color'],
                    //         'image_name' => $subView['image_name'],
                    //     ]
                    // );

                     //   dd($subView);
                        SubView::create([
                            'post_id' => $post->id,
                            'type' => $subView['type'],
                            'frame' => $subView['frame'],
                            'text' => $subView['text'],
                            'font_name' => $subView['font_name'],
                            'font_size' => $subView['font_size'],
                            'font_color' => $subView['font_color'],
                            'image_name' => $subView['image_name'],
                        ]); 
                    }

                


                }

            }catch(\Exception $e){
                return response()->json(['message' => $e->getLine() .' : Error : Please Contact Support'. $e->getMessage()], 500);
                // return redirect()->route('category.sub.list')->with('error','Error.Please Contact   Support');
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


    function getCategoryIdByName($name){
        $term = Term::where('name',$name)->first();
        if($term){
            return $term->id;
        }else{
            $term = Term::create([
                'name' => $name
            ]);
            return $term->name;
        }
    }


}
