<?php

namespace App\Http\Controllers\Post;


use App\Http\Controllers\Controller;
use App\Http\Requests\PostFromRequest;
use App\Models\Post;
use App\Models\PostTerm;
use App\Models\SubView;
use App\Models\Term;
use App\Util\Util;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
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

    public function index(Request $request){
        if($request->ajax()){
            $order      = $request->get('order_by','id-desc');
            $column     = explode('-',$order)[0];
            $sort       = explode('-',$order)[1];
            $categoryId = $request->get('category_id');
            $results = Post::with([
                'user' => function($query) use ($request){}, 
                'postTerm' => function($query) use ($request, $categoryId){
                }
            ]
            )->select()
            ->whereHas('postTerm',function($query) use ($request, $categoryId){
                if($categoryId){
                    $query->where('term_id', $categoryId);
                }
                
            })
            ->where(function ($query) use ($request) {
                $query->where('post_type','logo');

            })->orderByRaw($column.' '.$sort)->get();
            
            
            $datatable =  Datatables::of($results)
            ->addColumn('categories', function ($row) {
                $categories = [];
                foreach($row->postTerm as $category){
                    $categories[] = '<a href="javascript:void(0);">'.$category->name.'</a>';
                }
                return implode(', ',$categories);
            })->addColumn('user_name', function ($row) {
                return '';
            })->addColumn('thumbnail_url', function ($row) {
                $thumb = Post::find($row->thumbnail_id);
                return (isset($thumb->guid)) ? Util::imageUrl($thumb->guid) : '';
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
                     $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->id.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                     $action .= '</div>';
                    return $action;
            })->rawColumns(['status_label','categories','action']);

         

            //  ->orderColumn('id', 'DESC')
            return $datatable->make(true);
    }

        $this->viewData['category'] = Term::all()->pluck('name','id');
        return view($this->view.'lists',$this->viewData);
    }

    public function create(){
        $this->viewData['category'] = Term::all()->pluck('name','id');
        $this->viewData['postId'] = '';
        return view($this->view.'create',$this->viewData);
    }

    public function store(PostFromRequest $request){
        $title = $request->get('name');
        $status = $request->get('status','publish');

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
            'post_title' => $title,
            'post_slug' => Str::slug($title),
            'post_content' => '',
            'post_author' => (auth()->user())? auth()->user()->id:0,
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
               //  $subViewId = $subViewIds[$index];

                $subView['type'] =  $type;
                $subView['frame'] =  $frames[$index];
                $subView['text'] =  $texts[$index];
                $subView['font_name'] =  $fontNames[$index];
                $subView['font_size'] =  $fontSizes[$index];
                $subView['text_color'] =  $textColors[$index];
                $subView['post_id'] = $product->id;
                $subView['post_status'] = 'publish';

                // subview_image_file
                if(isset($subviewImageFileArr[$index])){
                    $subViewImage = null;
                    $imageObject = Util::fileUploadToMediaGallery($subviewImageFileArr[$index]);
                    if(isset($imageObject->id)){
                        $subView['image_name'] =  $imageObject->guid;
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


            return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Template created successfully.' ]);

            if($request->ajax()){
                return response(['status' => true, 'data' => ['post' => $product], 'message' => 'Template created successfully.' ]);
            }else{
                return redirect(route('product.list'))->with('success',$this->name.' added successful!');
            }
            
            
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->fileName() .' - '.$e->getLine() .' : Error : Please Contact Support'. $e->getMessage()], 500);
        }
    }


    public function show(Post $post, $id){
        $post = Post::with('postTerm')->find($id);
        $thumb = Post::find($post->thumbnail_id);
        
        $this->viewData['thumbnail_url'] = (isset($thumb->guid)) ? Util::imageUrl($thumb->guid) : '';
        $this->viewData['row'] = $post;
        $this->viewData['categories'] = $post->postTerm;
        $this->viewData['subview'] = SubView::where('post_id',$id)->get();
        return view($this->view.'detail_new',$this->viewData);
    }


    public function edit(Post $product,$id){
        $template = Post::find($id);
        $this->viewData['row'] = $template;
        $this->viewData['category'] = Term::all()->pluck('name','id');

        $thumbnail = Post::find($template->thumbnail_id);
        $this->viewData['thumbnail'] = (isset($thumbnail->guid))? Util::imageUrl($thumbnail->guid):'';
        $this->viewData['post_category'] = PostTerm::where('post_id',$id)->pluck('term_id')->toArray();
        $this->viewData['subview'] = SubView::where('post_id',$id)->get();
        $this->viewData['postId'] = $id;
        return view($this->view.'edit',$this->viewData);
    }

    public function update(PostFromRequest $request, Post $product, $id){
        $product = Post::find($id);
        $title = $request->get('name');
        $status         = $request->get('status');
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
            'post_title' => $title,
            'post_slug' => Str::slug($title),
            'post_content' => '',
            'post_author' => auth()->user()->id,
            'post_status' => $status,
            'post_type' => 'logo',
            'template_bounds' => $templateBounds
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
                        $subView['image_name'] =  $imageObject->guid;
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
        }
    }

 
    public function destroy($id,Post $post){
        // return Util::getImagePath('logo--1727788250-9656.png');
        $post = Post::findOrFail($id);
        $thumbnail = Post::find($post->thumbnail_id);
        $subViews = SubView::where('post_id',$post->id)->get();

        foreach($subViews as $view){
            // $subView = Post::where('post_id', $view->post_id)->first();
            if($view->image_name){
                optional(Post::where('guid',$view->image_name)->first())->delete();
                Storage::delete(Util::getImagePath($view->image_name));
                $view->delete();
            }
        }
        if($thumbnail){
            Storage::delete(Util::getImagePath($thumbnail->guid));
            $thumbnail->delete();
        }
        $post->delete();
        return response()->json(['message' => 'Template Delete succussfully'], 200);
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

            if(!($file)){
                return response()->json(['message' => 'File Not Found'], 422);
            }

            $fileContents = file($file->getPathname());
            $action = $request->get('action');
            $templateArr = array();
        
            $index = 0;
            foreach ($fileContents as $line) {
                if( $index == 0 ){
                    $index++;
                    continue;
                }
                
                $data = str_getcsv($line);
                // if($index == 13){
                //     dd($data);
                // }
                
                $templateThumbnail = $data[4];
                $subViewImageName = (isset($data[11]))?$data[11]:'';
                
                $thumbnailId = Post::getMediaIdByFileName($templateThumbnail);
                $thumbnailId = ($thumbnailId)?$thumbnailId->id:'';
               //  dd($thumbnailId);
                



                $subViewImage = Post::getMediaIdByFileName($subViewImageName);
                $mediaId = ($subViewImage)?$subViewImage->id:'';



                if(!isset($templateArr[$data[0]])){
                    $templateArr[$data[0]] = [
                        'id' => $data[0],
                        'name' => $data[1],
                        'categories' => $data[2],
                        'bounds' => $data[3],
                        'thumbnail_id' => $thumbnailId
                    ];
                }
                $templateArr[$data[0]]['sub_views'][] = [
                    'type' => (isset($data[5]))?$data[5]:'',
                    'frame' => (isset($data[6]))?$data[6]:'',
                    'text' => (isset($data[7]))?$data[7]:'',
                    'font_name' => (isset($data[8]))?$data[8]:'',
                    'font_size' => (isset($data[9]))?$data[9]:'',
                    'font_color' => (isset($data[10]))?$data[10]:'',
                    'image_name' => (isset($data[10]))?$data[10]:'',
                    'media_id' => $mediaId
                ];
            }







            if($action == 'insert'){
                try{
                foreach($templateArr as $template){

                    $postTitle = $template['name'];
                    $postSlug  = Str::slug($postTitle);

                    $post = Post::where('post_slug',$postSlug)->first();

                    $postDataArr = [
                        'post_type' => 'logo',
                        'post_title' => $template['name'],
                        'post_slug' => $postSlug,
                        'post_content' => '',
                        'post_author' => 1,
                        'template_bounds' => $template['bounds'],
                        'thumbnail_id' => $template['thumbnail_id']
                    ];

                    if($post){
                        ////////////    UPDATE POST /////////////
                        $post->update($postDataArr);
                    }else{
                        ////////////    INSERT NEW POST /////////////
                        $post = Post::create($postDataArr);
                    }

                    $categories = explode(',',$template['categories']);

                        PostTerm::where('post_id',$post->id)->delete();
                        SubView::where('post_id',$post->id)->delete();
    
                        foreach($categories as  $category){
                            $categoryId = $this->getCategoryIdByName($category);
                            PostTerm::create([
                                'post_id' => $post->id,
                                'term_id' => $categoryId
                            ]);
                        }
                        foreach($template['sub_views'] as  $subView){
                            SubView::create([
                                'post_id' => $post->id,
                                'type' => $subView['type'],
                                'frame' => $subView['frame'],
                                'text' => $subView['text'],
                                'font_name' => $subView['font_name'],
                                'font_size' => $subView['font_size'],
                                'text_color' => $subView['font_color'],
                                'image_name' => $subView['image_name'],
                            ]); 
                        }



                    
                }
            }catch(\Exception $e){
                return response()->json(['message' => $e->getLine() .' : Error : Please Contact Support'. $e->getMessage()], 500);
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


}
