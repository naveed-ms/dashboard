<?php

namespace App\Http\Controllers;

use Session;
use Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;

class categoryController extends Controller
{
    public function __construct(Request $req){

    }

    public function index(){
      $message = null;
      if (Session::has("message")){
        $message = Session::get("message");
      }
      return view("category.index",[
        "message"=>$message,"data"=>null
      ]);
    }

    public function getCategory(Request $req, $id = null){
      $model = "";

      if (isset($id)){
        $model = Category::find($id);
      }else{
        $model = Category::all();
      }
      print_r( $model->toJson() );
    }


    public function create(Request $req){
      $message = "";
        $Name =  $req->input("name");
        $Slug = str_replace(" ","-", strtolower($req->input("name")));
        $v = $this->validator($req->all());
        if (!$v->fails()){
          $newCategory = Category::create([
            "name"=>$Name,
            "slug"=>$Slug
          ]);
          $message = "Saved";
          Session::flash('message', $message);
          return redirect()->back();
        }else{
          return redirect()->back()->withErrors($v->errors());
        }
    }

    public function update($id,Request $req){
      $message = null;
      if ($req->method() == "POST"){
        $Name =  $req->input("name");
        $Slug = str_replace(" ","-", strtolower($req->input("name")));
        $v = $this->validator($req->all());
        if (!$v->fails()){
          Category::where("id",$id)->update(['name'=>$Name,'slug'=>$Slug]);
          $message = "Saved";
          Session::flash('message', $message);
          //return redirect()->back();
        }else{
          return redirect()->back()->withErrors($v->errors());
        }
      }
        if (Session::has("message")){
          $message = Session::get("message");
        }
        $data = Category::find($id);
        return view("category.index",[
          "data"=>$data,"message"=>$message
        ]);
    }

    public function delete($id){
      $updateCategory = Category::where("id","=",$id)->delete();
      $message = "Removed";
      Session::flash('message', $message);
      return redirect()->back();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|min:5',
        ]);
    }


}
