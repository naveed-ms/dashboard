<?php
namespace App\library;

use Monolog\Logger;
use HieuLe\WordpressXmlrpcClient\Exception\NetworkException;
use HieuLe\WordpressXmlrpcClient\Exception\XmlrpcException;
use HieuLe\WordpressXmlrpcClient\WordpressClient;

class wordpressClass{
  private $wpClient;
  public function __construct(){
    # The Monolog logger instance
    $wpLog = new Logger('wp-xmlrpc');
    # Create client instance
    $this->wpClient = new WordpressClient();
    # Log error
    $this->wpClient->onError(function($error, $event) use ($wpLog){
      $wpLog->addError($error, $event);
    });
    # Set the credentials for the next requests
    $this->wpClient->setCredentials(config("wordpress.endpoint"), config("wordpress.username"), config("wordpress.password"));
  }

  public function sayHello(){
    echo "Hello from wordpress class";
  }

  public function getPosts($CatId,$page){
      $posts = $this->wpClient->getposts(
      array("post_type"=>"songs",
        "category"=>$CatId,
        "post_status"=>"publish",
        "offset"=>$page[0],
        "number"=>$page[1],
        "orderby"=>"date",
        "order"=>"DESC"
      ));
      $data = array();

      if(!empty($posts)){
        foreach($posts as $key => $val){
          for($customRow = 0; $customRow < count($val["custom_fields"]); $customRow++){
            if ($val["custom_fields"][$customRow]["key"] == "playlist"){
              $playlist = @unserialize($val["custom_fields"][$customRow]["value"]);
            }
          }
          $songslist = array();
          if (!empty($playlist)){
            foreach($playlist as $val1){
              //print_r($val1);
              $videoName = (isset($val1["buy_link_b"]) ? $val1["buy_link_b"] :  null);
              if (isset($videoName)){
                $videoName = explode('/',$videoName);
                if (count($videoName) > 1){
                  $videoName = $this->wpClient->getPostByName([
                    "slug"=>$videoName[count($videoName) - 2],
                    "post_type"=>"videos",
                    "post_status"=>"publish"])["post_content"];
                    $pat = ["[videojs"," autoplay=\"true\"]"," mpd"," mp4","\"","]"];
                    for($i = 0; $i < count($pat); $i++){
                      $videoName = str_replace($pat[$i],"",$videoName);
                    }
                    $videoName=explode("=", $videoName);
                  }else{
                    $videoName = ["o"=>"","mpd_url"=>"","mp4_url"=>""];
                  }
                }

                array_push($songslist,array(
                  "title"=>$val1["title"],
                  "audio_url"=>$val1["mp3"],
                  "mpd_url"=>isset($videoName[1]) ? $videoName[1] : "",
                  "mp4_url"=>isset($videoName[2]) ? $videoName[2] : ""
                ));
              }
            }
            array_push($data,array(
              "ID"=>$val["post_id"],
              "title"=>(isset($val["post_title"]) ? $val["post_title"] : ""),
              "cover_url"=>(isset($val["post_thumbnail"]["thumbnail"]) ? $val["post_thumbnail"]["thumbnail"] : ""),
              "songs"=>$songslist
            ));
          }
        }
        return $data;
      }
}
