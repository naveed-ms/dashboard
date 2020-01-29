<?php
namespace App\Http\Controllers;

use Auth;
use App\library\encodeClass;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Movie;
use App\Job;
class encoderController extends Controller
{
  private $bitcodinJob;
  private $err = array();
  private $reqParam = array();
  public function __construct(Request $req){
    $this->middleware('auth');
    $this->reqParam = (null !== ( file_get_contents("php://input") ) ? json_decode(file_get_contents("php://input"),true) : $req->all());
    $this->bitcodinJob = new encodeClass();
  }
  public function index(){
    $movies = \App\Movie::orderBy("name")->get();
    return view("encoder.index",array("movies"=>$movies));
  }

  public function job(Request $req){
     $this->bitcodinJob->UID = Auth::id();
     $this->bitcodinJob->param = $req->all();
     echo $this->bitcodinJob->job($req->input("url"));
  }

  public function read(Request $req){
    echo $this->bitcodinJob->logReader($req->input("id"));
  }

  public function movie(Request $req){
    $message = "";
    if($req->method() == "POST"){
      $Name =  str_replace(" ","-",$req->input("movie_name"));
      $v = $this->validator($req->all());
      if (!$v->fails()){
        $newMovie = Movie::create([
          "name"=>$Name,"post_date"=>date('Y-m-d')
        ]);
        $message = "Saved";
      }else{
        return redirect()->back()->withErrors($v->errors());
      }
    }
    return view("encoder.movie",[
      "message"=>$message
    ]);
  }

  public function getJobData(){
    $model = "";
    if (isset($req->reqParam["Movie"])){
      $model = Job::where('Movie', '=', $req->reqParam["Movie"])
      ->where("UID","=",Auth::id())
      ->get();
    }else{
      $model = Job::where("UID","=",Auth::id())->get();
    }
    print_r( $model->toJson() );
  }

  protected function validator(array $data)
  {
      return Validator::make($data, [
          'movie_name' => 'required|min:5',
      ]);
  }


}
