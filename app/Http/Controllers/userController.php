<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Requests\userRequest;

use DB;

use App\User;

use App\Role;

use Redirect;

use Auth;

class userController extends Controller
{
    private $prefix = null;
    public function _constructor(){
      $this->prefix = DB::getTablePrefix();

    }

    public function index($id = null){
      if (!empty($id)){
        $roles = Role::where("name","<>","super")->get();
        $user = DB::select("SELECT
          user.id, user.name, user.email, user.password, role_user.role_id as role
          FROM tbl_users AS user
          INNER JOIN tbl_role_user AS role_user
          WHERE user.id=:id",[
          'id' => $id
        ])[0];
        return view('User.edit',[
          "user" => $user,
          "roles" => $roles
        ]);
      }else{
        $users = DB::table("userviews")->where("role_name","<>","super")->paginate(15);
        return view('User.index',["users" => $users]);
      }

    }
    public function profile (Request $req){
      $user_id = Auth::id();
      $user = User::find($user_id);
      if ($req->isMethod("post")){
        $param = $req->all();
        $user->name = $param['txt_name'];
        // $user->email = $param['txt_email'];
        if (!empty($param['txt_password'])){
          $user->password = bcrypt($param['txt_password']);
        }
        $user->save();
      }
      return view('User.profile',[
        "user" => $user
      ]);
    }
    public function save(userRequest $req,$id){
      $param = $req->all();
      $user = User::find($id);
      $roles = Role::where("name","<>","super")->get();
      // $user->name = $param['txt_name'];
      // $user->email = $param['txt_email'];
      if (!empty($param['txt_password'])){
        $user->password = bcrypt($param['txt_password']);
      }
      $user->save();
      if (!empty($param['txt_role'])){
        DB::table("role_user")->where("user_id",$id)->update(["role_id" => $param['txt_role']]);
      }
      return view('User.edit',[
        "user" => $user,
        "roles" => $roles
      ]);
    }
}
