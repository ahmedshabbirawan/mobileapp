<?php
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostFromRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $results = Post::with(['user' => function($query) use ($request){}]
        )->select()->where(function ($query) use ($request) {
            $query->where('post_type','logo')->where('post_status','publish');
        })->orderByRaw($column.' '.$sort)->get();

        return response()->json([
            'data' => $results
        ]);
    }

   

    public function store(PostFromRequest $request){
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


    public function show(Post $product, $id){
        $post = Post::find($id);
        if($post){
            return response()->json(['data' => $post, 'message' => 'Done' ]);
        }
        return response()->json(['status' => false, 'data' => array(), 'message' => 'Logo not found!' ],404);
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