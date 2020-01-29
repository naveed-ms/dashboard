<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Job;

class testController extends Controller
{

  private $params = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $req)
    {
      $this->params = (null !== file_get_contents("php://input") ? json_decode( file_get_contents("php://input"), true ) : $req->all());
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


    }
}
