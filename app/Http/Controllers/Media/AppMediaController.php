<?php
namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaFromRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nette\Utils\Image;
use Illuminate\Support\Facades\Storage;

class AppMediaController extends Controller
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
            $query->where('post_type','attachment')->where('post_status','publish');
        })->orderByRaw($column.' '.$sort)->get();

        $mediaFiles = [];

        foreach($results as $row){
            $mediaFiles[] = ['file_url' => self::getFilePath($row->guid),'post_title' => $row->post_title];
        }

        return response()->json([
            'data' => $mediaFiles,
            'message' => count($mediaFiles)
        ]);
    }

   

    public function store(MediaFromRequest $request){
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


    static function getFilePath($fileName){
        return Storage::disk('public')->url('post_images/'.$fileName);
    }



   
  



}