<?php
namespace App\library;

use bitcodin\Bitcodin;
use bitcodin\VideoStreamConfig;
use bitcodin\AudioStreamConfig;
use bitcodin\Job;
use bitcodin\JobConfig;
use bitcodin\Input;
use bitcodin\EncodingProfile;
use bitcodin\EncodingProfileConfig;
use bitcodin\ManifestTypes;
use bitcodin\Output;
use bitcodin\GcsOutputConfig;
use bitcodin\HttpInputConfig;
use bitcodin\WatermarkConfig;

use Auth;


class encodeClass{
  public $UID = 0;
  private $output = null;
  private $profile = null;
  private $input = null;
  private $outputId = null;
  private $profileId = null;

  private $GCPGS_Key = "GOOG7YTI3Z4IJYTVPPTB";
  private $GCPGS_Secret = "MzyMbyClMpEvVbADKvml6WZrkvFbN/Nfgq4o0Q9O";
  private $GCPGS_OutBucket = "bsongs";
  public $param = array();
  public $eType = "Movie";

  public function __construct(){
    $this->GCPGS_Key = config("bitcodin.GCS.key");
    $this->GCPGS_Secret = config("bitcodin.GCS.secret");
    $this->GCPGS_OutBucket = config("bitcodin.GCS.bucket");

    $this->profileId = config("bitcodin.profileId");
    Bitcodin::setApiToken(config("bitcodin.ApiToken")); // Your can find your api key in the settings menu. Your account (right corner) -> Settings -> API
  // $this->output = Output::get($this->outputId);
     $this->profile = EncodingProfile::get($this->profileId);
  }

  public function job($url,$output_url){
    $isGoogle = false;
    $inputUrl =  $url;
    $inputConfig = new HttpInputConfig();
    // $inputUrl = "https://storage.googleapis.com/bsongs/Best-Songs-data/Bollywood/0-9/16-December-2002/Dil-ya-Tera-Nice-song-from-Movie-16-December.mp4";
    $inputConfig->url = $inputUrl;
    $this->input = Input::create($inputConfig);
    // Creating a job
    $OutUrl = explode("/",$inputUrl);
    for($paramIndex = 0;$paramIndex < count($OutUrl);$paramIndex++ ){
      if ($OutUrl[$paramIndex] == "bsongs"){
        $isGoogle = true;
      }
    }

    // count($OutUrl) - 1 = filename
    // count($OutUrl) - 2 = movie name
    // count($OutUrl) - 3 = subcategory
    // count($OutUrl) - 4 = category

    if ($isGoogle){
      $this->CreateOutput(array(
        "name"=>"Video_" . ($OutUrl[count($OutUrl)-1]),
        "folder"=>config("bitcodin.GCS.prefix_key") . $output_url
      ));
    }else{
      if (empty($this->param["file"]) || empty($this->param["movie"])){
        echo "error in input url";
        exit();
      }
      //$this->param["file"] = $this->param["file"] . ".mp4";
      $this->CreateOutput(array(
        "name"=>"Video_" . ($this->param["file"]),
        "folder"=>config("bitcodin.GCS.prefix_key") . strtolower($this->param["cat"]) . "/" . $this->param["subcat"] ."/". str_replace(" ","-",$this->param["movie"]) . "/" . str_replace(" ","-",$this->param["file"])
      ));

    }
    return $this->CreateJob();
  }



  private function CreateOutput($args = array()){
    $outputConfig = new GcsOutputConfig();
    if (count($args) > 0){
      $outputConfig->name         = $args["name"];
      $outputConfig->accessKey    = $this->GCPGS_Key;
      $outputConfig->secretKey    = $this->GCPGS_Secret;
      $outputConfig->bucket       = $this->GCPGS_OutBucket;
      $outputConfig->prefix       = $args["folder"]; // mpd/
      $outputConfig->makePublic   = true;                            // This flag determines whether the files put on GCS will be publicly accessible via HTTP Url or not
      $this->output = Output::create($outputConfig);
    }

  }

  private function CreateProfile($name){
    $encodingProfileConfig = new EncodingProfileConfig();
    $encodingProfileConfig->name = $name;

    /* CREATE VIDEO STREAM CONFIGS */
    $videoStreamConfig1 = new VideoStreamConfig();
    $videoStreamConfig1->bitrate = 4800000;
    $videoStreamConfig1->height = 1080;
    $videoStreamConfig1->width = 1920;
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig1;

    $videoStreamConfig2 = new VideoStreamConfig();
    $videoStreamConfig2->bitrate = 2400000;
    $videoStreamConfig2->height = 720;
    $videoStreamConfig2->width = 1280;
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig2;

    $videoStreamConfig3 = new VideoStreamConfig();
    $videoStreamConfig3->bitrate = 1200000;
    $videoStreamConfig3->height = 480;
    $videoStreamConfig3->width = 854;
    $encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig3;

    /* CREATE AUDIO STREAM CONFIGS */
    $audioStreamConfig = new AudioStreamConfig();
    $audioStreamConfig->bitrate = 128000;
    $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

    /* CREATE WATERMARK */
    $watermarkConfig = new WatermarkConfig();
    $watermarkConfig->bottom = 200; // Watermark will be placed with a distance of 200 pixel to the bottom of the input video
    $watermarkConfig->right = 100;  // Watermark will be placed with a distance of 100 pixel to the right of the input video
    $watermarkConfig->image = 'http://bitdash-a.akamaihd.net/webpages/bitcodin/images/bitcodin-bitmovin-logo-small.png';
    $encodingProfileConfig->watermarkConfig = $watermarkConfig;

    /* CREATE ENCODING PROFILE */
    $encodingProfile = EncodingProfile::create($encodingProfileConfig);
    //$this->profile = $encodingProfile;
  }

  private function CreateJob(){
    $isTransfer = "No";
    $jobConfig = new JobConfig();
    // $jobConfig->speed="standard";
    $jobConfig->encodingProfile = $this->profile;
    $jobConfig->input = $this->input;
    $jobConfig->output = $this->output;
    $outUrl = "";$outId = 0;
    $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
    $jobConfig->manifestTypes[] = ManifestTypes::MPD;
    $job = Job::create($jobConfig);
    $JobLog = new \App\Job();
    $JobLog->UID = $this->UID;
    $JobLog->JID = $job->jobId;
    $JobLog->type = $this->eType;
    $JobLog->msg = "start";
    // $JobLog->Movie = "-"; //str_replace(" ","-",$this->param["movie"]);
    // $JobLog->Url =  "-"; // config("bitcodin.GCS.prefix_key") . strtolower($this->param["cat"]) . "/" . $this->param["subcat"] ."/". str_replace(" ","-",$this->param["movie"]) . "/" . str_replace(" ","-",$this->param["file"]);
     $JobLog->save();
    return $job->jobId;
  }
  public function logReader($jid){
    $job = Job::get($jid);
    $d["job"] = $job;
    $jLog = \App\Job::where("JID",$jid)->get()[0];
    try{
      $d["transfer"] = $job->getTransfers();
    } catch (\bitcodin\exceptions\BitcodinResourceNotFoundException $e) {
      $jLog->msg = "error";
    } catch (\Exception $e) {
      $jLog->msg = "error";
    }
    if (isset($d["transfer"])){
      if (strtolower($d["job"]->status) == "finished" && strtolower($d["transfer"][0]->status) == "finished"){
        //$this->deleteObj($jid);
        $jLog->msg = "finished";
      }else if (strpos(strtolower($d["job"]->status),"error") !== false || strpos(strtolower($d["transfer"][0]->status),"error") !== false){
        $jLog->msg = "error";
      }else{
        $jLog->msg = "in proc";
      }
    }else if(strpos(strtolower($d["job"]->status),"error") !== false){
        $jLog->msg = "error";
    }else{
      $jLog->msg = "in proc";
    }
    $jLog->save();
    // $content = json_encode($d);
    // file_put_contents(storage_path() . "\\logs\\jobs\\" . $jid . ".log",$content);
    return json_encode($d);
  }

  private function deleteObj($jid){
    $job = Job::get($jid);
    $in = Input::get($job->input->inputId);
    $in->delete();
    $out = Output::get($job->outputId);
    $out->delete();
    Job::delete($jid);
  }
}
