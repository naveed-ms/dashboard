<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Session;
use App\banner;

class bannerController extends Controller
{
	public function index()
	{
		return view('banner.index');
	}

    public function getData($id = null){
        $banners = [];
        if (!empty($id)){
            $banners = banner::find($id);
        }else{
            $banners = banner::all();
        }
        return response()->json($banners);
    }
	
	public function save(Request $req, $id = null){
		$param = $req->all();
		if(!empty($id)){
			$banner = banner::find($id);
		}else{
			$banner = new banner;
		}
		$banner->name = $param["name"];
		if ($req->hasFile("image_url")){
			$image_file = $req->file("image_url");
			Storage::cloud()->put("banner_image/" . $image_file->getClientOriginalName(), File::get($image_file), \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
			$cdn = config("app.cdn_url") . "/" . "banner_image/" .  $image_file->getClientOriginalName();
			$banner->image_url = $cdn;
		}
		$banner->type = $param["type"];
		$banner->ref_id = $param["ref_id"];
		if($banner->save()){
            echo "saved";
        }
	}
}