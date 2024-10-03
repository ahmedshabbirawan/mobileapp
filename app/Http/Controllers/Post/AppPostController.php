<?php
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostFromRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PostResource;

use App\Models\PostTerm;
use App\Models\SubView;
use App\Models\Term;
use App\Util\Util;

class AppPostController extends Controller
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
        $order      = $request->get('order_by','id-desc');
        $column     = explode('-',$order)[0];
        $sort       = explode('-',$order)[1];

        $results = Post::with([
            // 'user' => function($query) use ($request){}, 
            'postTerm',
            'subView'
        ]
        )->select()->where(function ($query) use ($request) {
            $query->where('post_type','logo')->where('post_status','publish');
        })->orderByRaw($column.' '.$sort)->get();

        return PostResource::collection($results);

        // return response()->json([
        //     'data' => $results
        // ]);
    }

   

    public function store(PostFromRequest $request){

        return app(PostController::class)->store($request);



        $name           = $request->get('post_title');
        $description    = $request->get('post_content');
        $status         = $request->get('post_status');
        
        // if($request->hasFile('product_image')){ 
        //     $imageObj = $this->uploadProductImage($request);
        //     if(  !($imageObj[0]) ){
        //         return response()->json(['message' => $imageObj[1]], 500);
        //     }else{
        //         $imageName = $imageObj[1];
        //     }
        // }

        $array = array(
            'post_title' => $name,
            'post_content' => $description,
            'post_author' =>  (auth()->user())? optional(auth()->user())->id:'',
            'post_status' => $status,
            'post_type' => 'logo'
        );
        try{
            $product    = Post::create($array);
            return response()->json(['status' => true, 'data' => ['logo' => $product], 'message' => 'Logo Add successfully.' ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getLine(). ' : '. $e->getFile() .' : Error.Please Contact   Support'. $e->getMessage()], 500);
        }
    }


    public function show(Post $post, $id){
        $post = Post::with([
            'postTerm',
            'subView'
        ])->findOrFail($id);

       //  dd($post);

        // $thumb = Post::find($post->thumbnail_id);


        return new PostResource($post);

        
        // $this->viewData['thumbnail_url'] = (isset($thumb->guid)) ? Util::imageUrl($thumb->guid) : '';
        // $this->viewData['row'] = $post;
        // $this->viewData['categories'] = $post->postTerm;
        // $this->viewData['subview'] = SubView::where('post_id',$id)->get();


        // return response()->json(['status' => true, 'data' => $this->viewData, 'message' => 'OK' ],200);
    }


    public function update(PostFromRequest $request,  $id){
        $post = Post::find($id);
        $name = $request->get('post_title');
        $description = $request->get('post_content');
        $status = $request->get('post_status');
        $array = array(
            'post_title' => $name,
            'post_content' => $description,
            // 'post_author' => auth()->user()->id,
            'post_status' => $status
        );
       try{
            $post = $post->update($array);
            return response(['data' => $post, 'message' => 'Post Update successfully.' ]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage(), 'data' => array()], 500);
        }
    }

    public function destroy($id){
        $post = Post::find($id);
        if($post){
            $post->delete();
            return response()->json(['data' => [], 'message' => 'Delete Successfully' ]);
        }
        return response()->json(['status' => false, 'data' => array(), 'message' => 'Logo not found!'],404);
    }

    function status($eid){
        $post = Post::find($eid);
        if($post->post_status == 'publish'){
            $post->post_status = 'temporary_block';
        }else{
            $post->post_status = 'publish';
        }
        $post->save();
        return array('status' => true);
    }



   
  



}