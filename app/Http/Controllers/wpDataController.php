<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use DB;
//use App\library\wordpressClass;
use App\Category;
use App\Movie;
use App\track;
use App\MovieCat;
use App\trailler;
use App\wpmigrate;
use App\artist;
use App\artistMovie;
use App\video;
use App\albumMap;
use App\featureVideo;
use App\featureVideoTrack;
use App\event;

class wpDataController extends \App\Http\Controllers\Controller
{
    //private $wpClient;
    private $wpmigrate;
    public function __construct()
    {
        //$this->wpClient = new \App\library\wordpressClass();
        $this->wpmigrate = new \App\wpmigrate();
    }
    public function movie($id)
    {
        $posts[] = $this->wpClient->getPost($id);
        if (!empty($posts)) {
            $post = array();
            foreach ($posts as $key => $val) {
                for ($customRow = 0; $customRow < count($val["custom_fields"]); $customRow++) {
                    if ($val["custom_fields"][$customRow]["key"] == "cd_meta") {
                        $playlist = @unserialize($val["custom_fields"][$customRow]["value"])['tracks'];
                    }
                    if ($val["custom_fields"][$customRow]["key"] == "playlist") {
                        $playlist = @unserialize($val["custom_fields"][$customRow]["value"]);
                    }
                    if ($val["custom_fields"][$customRow]["key"] == "artist_nameaa") {
                        $artists_id = @unserialize($val["custom_fields"][$customRow]["value"]);
                    }
                }
                $songslist = array();
                $catList = array();
                $artists = array();
                if (!empty($playlist)) {
                    foreach ($playlist as $val1) {
                        $videoName = isset($val1["buy_link_b"]) ? $val1["buy_link_b"] : null;
                        $video_url = $videoName;
                        if (!empty($videoName)) {
                            $videoName .= substr($videoName, strlen($videoName) - 1) == "/" ? "" : "/";
                        }
                        if (isset($videoName)) {
                            $videoName = explode('/', $videoName);
                            if (count($videoName) > 1) {
                                $videoName = $this->wpClient->getPostByName(["slug" => $videoName[count($videoName) - 2], "post_type" => "videos", "post_status" => "publish"])["post_content"];
                                $pat = ["[videojs", " autoplay=\"true\"]", " mpd", " mp4", "\"", "]"];
                                for ($i = 0; $i < count($pat); $i++) {
                                    $videoName = str_replace($pat[$i], "", $videoName);
                                }
                                $videoName = explode("=", $videoName);
                            } else {
                                $videoName = ["o" => "", "mpd_url" => "", "mp4_url" => ""];
                            }
                        }
                        $pat = [' ' => '-'];
                        foreach ($pat as $dot => $dotVal) {
                            $slug = str_replace($dot, $dotVal, $val1["title"]);
                        }
                        array_push($songslist, array("track_id" => isset($val1["track_id"]) ? $val1["track_id"] : "0", "title" => $val1["title"], "slug" => $slug, "audio_url" => $val1["mp3"], "mpd_url" => isset($videoName[1]) ? $videoName[1] : "", "mp4_url" => isset($videoName[2]) ? $videoName[2] : "", "share_url" => $val['link'], "video_share_url" => isset($video_url) ? $video_url : ""));
                    }
                }
                if (!empty($artists_id)) {
                    $artists = $artists_id;
                }
                if (!empty($val["terms"])) {
                    foreach ($val["terms"] as $key => $cat) {
                        if ($cat["taxonomy"] == "songs_cat") {
                            array_push($catList, ["id" => $cat["term_id"], "title" => str_replace(" ", "-", $cat["name"])]);
                        }
                    }
                }
                $post = array("ID" => $val["post_id"], "date" => $val["post_date"]->scalar, "modified_date" => $val["post_modified"]->scalar, "title" => isset($val["post_title"]) ? $val["post_title"] : "", "cover_url" => isset($val["post_thumbnail"]["thumbnail"]) ? $val["post_thumbnail"]["thumbnail"] : "", "songs" => $songslist, "terms" => $catList, "artists" => $artists, "menu_order" => isset($val["menu_order"]) ? $val["menu_order"] : 0);
            }
        }
        if (!empty($post)) {
            $this->savePost($post);
            $this->wpmigrate->post_id = $id;
            $this->wpmigrate->update_date = date("Y-m-d H:i:s", strtotime($post['modified_date']));
            $this->wpmigrate->save();
        }
    }
    public function trailler($id)
    {
        $post = $this->wpClient->getPost($id);
        $urls = $post["post_content"];
        $pat = ["[videojs", " autoplay=\"true\"]", " mpd", " mp4", "\"", "]"];
        for ($i = 0; $i < count($pat); $i++) {
            $urls = str_replace($pat[$i], "", $urls);
        }
        $urls = explode("=", $urls);
        $pat = [' ' => '-'];
        foreach ($pat as $dot => $dotVal) {
            $slug = str_replace($dot, $dotVal, isset($post["post_title"]) ? $post["post_title"] : "");
        }
        $post = array("ID" => $post["post_id"], "date" => $post["post_date"]->scalar, "modified_date" => $post["post_modified"]->scalar, "title" => isset($post["post_title"]) ? $post["post_title"] : "", "slug" => isset($post["post_name"]) ? $post["post_name"] : "", "menu_order" => isset($post["menu_order"]) ? $post["menu_order"] : 0, "cover_url" => isset($post["post_thumbnail"]["thumbnail"]) ? $post["post_thumbnail"]["thumbnail"] : "", "mpd_url" => isset($urls[1]) ? $urls[1] : "", "mp4_url" => isset($urls[2]) ? $urls[2] : "", "share_url" => isset($post["link"]) ? $post["link"] : "");
        $trailler = \App\trailler::firstOrNew(['post_id' => $post['ID']]);
        $trailler->name = $post['title'];
        $trailler->slug = $post['slug'];
        $trailler->post_id = $post['ID'];
        $trailler->post_date = date('Y-m-d H:i:s', strtotime($post['date']));
        $trailler->modified_date = date('Y-m-d H:i:s', strtotime($post['modified_date']));
        $trailler->m_order = $post["menu_order"];
        $trailler->cover_url = $post['cover_url'];
        $trailler->mpd_url = $post['mpd_url'];
        $trailler->mp4_url = $post['mp4_url'];
        $trailler->share_url = $post['share_url'];
        $trailler->save();
    }
    public function deleteTraillers()
    {
        \DB::table('traillers')->truncate();
    }
    public function deleteMovie($id)
    {
        $data = \App\Movie::where("post_id", $id)->get()->toArray();
        if (!empty($data[0]) > 0) {
            \App\MovieCat::where("movie_id", $data[0]["id"])->delete();
            \App\track::where("movie_id", $data[0]["id"])->delete();
            \App\Movie::where("post_id", $id)->delete();
        }
    }
    public function deleteArtist($id)
    {
        \App\artistMovie::where("post_id", $id)->delete();
        \App\artist::where("post_id", $id)->delete();
    }
    public function index()
    {
        $lastMigrateId = \App\wpmigrate::orderBy('id', 'desc')->first();
        $posts = json_decode($this->cURL("https://bestsongs.pk/api/rpc/mig.php?ID={$lastMigrateId->post_id}&number=1&offset=10"), true);
        $lastId = $lastMigrateId->post_id;
        foreach ($posts as $post) {
            $this->savePost($post);
            $lastId = $post["ID"];
        }
        $this->wpmigrate->post_id = $lastId;
        $this->wpmigrate->update_date = date('Y-m-d');
        $this->wpmigrate->save();
        echo "Updated";
    }
    private function savePost(array $param)
    {
        $movie_id = $this->saveMovie($param);
        $this->setCategory($param, $movie_id);
        $this->saveTracks($param, $movie_id);
    }
    private function saveMovie(array $param)
    {
        $movies = \App\Movie::firstOrNew(['post_id' => $param['ID']]);
        $movies->post_id = $param['ID'];
        $movies->name = $param['title'];
        $movies->cover_url = $param["cover_url"];
        $movies->post_date = date("Y-m-d H:i:s", strtotime($param['date']));
        $movies->modified_date = date("Y-m-d H:i:s", strtotime($param['modified_date']));
        $movies->m_order = $param["menu_order"];
        $movies->total_tracks = count($param["songs"]);
        try{
            $movies->save();
        }catch(Exception $e){

        }
        \App\artistMovie::where("movie_id", $movies->id)->delete();
        if (!empty($param['artists'])) {
            $o_aid = 0;
            foreach ($param['artists'] as $aid) {
                if ($o_aid != $aid) {
                    $o_aid = $aid;
                    $artist = \App\artist::firstOrNew(["post_id" => $aid]);
                    $artist->post_id = $aid;
                    $a_post = $this->wpClient->getPost($aid);
                    $artist->name = $a_post["post_title"];
                    $artist->cover_url = isset($a_post["post_thumbnail"]["thumbnail"]) ? $a_post["post_thumbnail"]["thumbnail"] : "";
                    $artist->save();
                    $artistMoive = \App\artistMovie::firstOrNew(['movie_id' => $movies->id, "post_id" => $aid]);
                    $artistMoive->movie_id = $movies->id;
                    $artistMoive->post_id = $artist->id;
                    $artistMoive->save();
                }
            }
        }
        return $movies->id;
    }
    private function setCategory(array $param, $movie_id)
    {
        if (!empty($param["terms"])) {
            \App\MovieCat::where('movie_id', $movie_id)->delete();
            foreach ($param["terms"] as $term) {
                $cats = \App\Category::firstOrNew(['name' => $term['title']]);
                $cats->name = $term['title'];
                $cats->save();
                $MovieCat = \App\MovieCat::firstOrNew(['movie_id' => $movie_id, 'cat_id' => $cats->id]);
                $MovieCat->movie_id = $movie_id;
                $MovieCat->cat_id = $cats->id;
                $MovieCat->save();
            }
        }
    }
    private function saveTracks(array $param, $movie_id)
    {
        if (!empty($param["songs"])) {
            \App\track::where('movie_id', $movie_id)->where("track_id", "0")->delete();
            // temp
            foreach ($param["songs"] as $track) {
                if ($track['track_id'] == "0") {
                    $tracks = \App\track::firstOrNew(['name' => $track['title'], "movie_id" => $movie_id]);
                } else {
                    $tracks = \App\track::firstOrNew(["track_id" => $track['track_id']]);
                }
                $tracks->track_id = $track['track_id'];
                $tracks->name = $track['title'];
                $tracks->slug = $track['slug'];
                $tracks->movie_id = $movie_id;
                $tracks->audio_url = $track['audio_url'];
                $tracks->mpd_url = $track['mpd_url'];
                $tracks->mp4_url = $track['mp4_url'];
                // $tracks->share_url = $track['share_url'];
                $tracks->video_share_url = $track['video_share_url'];
                $tracks->save();
            }
        }
    }
    private function cURL($url, $post = false, $header = false)
    {
        $ch = curl_init($url);
        if ($post !== false) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return $result;
    }
    public function moviePost(\Illuminate\Http\Request $req)
    {
        $posts[] = $this->wpClient->getPost($req->input("id"));
        dd($posts);
    }
    public function movie_chk(\Illuminate\Http\Request $req)
    {
        $paramQ = $req->all();
        $paramQ['ids'] = json_decode($paramQ['ids'], true);
        $data = [];
        foreach ($paramQ['ids'] as $item) {
            $posts[] = $this->wpClient->getPost($item['ID']);
            if (!empty($posts)) {
                $post = array();
                foreach ($posts as $key => $val) {
                    for ($customRow = 0; $customRow < count($val["custom_fields"]); $customRow++) {
                        if ($val["custom_fields"][$customRow]["key"] == "playlist") {
                            $playlist = @unserialize($val["custom_fields"][$customRow]["value"]);
                        }
                        if ($val["custom_fields"][$customRow]["key"] == "artist_nameaa") {
                            $artists_id = @unserialize($val["custom_fields"][$customRow]["value"]);
                        }
                    }
                    $songslist = array();
                    $catList = array();
                    $artists = array();
                    if (!empty($playlist)) {
                        foreach ($playlist as $val1) {
                            $videoName = isset($val1["buy_link_b"]) ? $val1["buy_link_b"] : null;
                            if (!empty($videoName)) {
                                $videoName .= substr($videoName, strlen($videoName) - 1) == "/" ? "" : "/";
                            }
                            if (isset($videoName)) {
                                $videoName = explode('/', $videoName);
                                if (count($videoName) > 1) {
                                    $videoName = $this->wpClient->getPostByName(["slug" => $videoName[count($videoName) - 2], "post_type" => "videos", "post_status" => "publish"])["post_content"];
                                    $pat = ["[videojs", " autoplay=\"true\"]", " mpd", " mp4", "\"", "]"];
                                    for ($i = 0; $i < count($pat); $i++) {
                                        $videoName = str_replace($pat[$i], "", $videoName);
                                    }
                                    $videoName = explode("=", $videoName);
                                } else {
                                    $videoName = ["o" => "", "mpd_url" => "", "mp4_url" => ""];
                                }
                            }
                            if (!empty($val1["buy_link_b"]) && (empty($videoName[1]) || empty($videoName[2]))) {
                                array_push($songslist, array("title" => $val1["title"], "audio_url" => $val1["mp3"], "mpd_url" => isset($videoName[1]) ? $videoName[1] : "", "mp4_url" => isset($videoName[2]) ? $videoName[2] : "", "video_link" => isset($val1["buy_link_b"]) ? $val1["buy_link_b"] : ""));
                            }
                        }
                    }
                    if (!empty($artists_id)) {
                        $artists = $artists_id;
                    }
                    if (!empty($val["terms"])) {
                        foreach ($val["terms"] as $key => $cat) {
                            if ($cat["taxonomy"] == "songs_cat") {
                                array_push($catList, ["id" => $cat["term_id"], "title" => str_replace(" ", "-", $cat["name"])]);
                            }
                        }
                    }
                    $post = array("ID" => $val["post_id"], "date" => $val["post_date"]->scalar, "modified_date" => $val["post_modified"]->scalar, "album" => isset($val["post_title"]) ? $val["post_title"] : "", "cover_url" => isset($val["post_thumbnail"]["thumbnail"]) ? $val["post_thumbnail"]["thumbnail"] : "", "songs" => $songslist);
                }
            }
            if (!empty($post) && !empty($post['songs'])) {
                array_push($data, $post);
            }
        }
        return response()->json($data);
    }
    public function savePostForVideo(\Illuminate\Http\Request $req, $id)
    {
        $posts[] = $this->wpClient->getPost($id);
        // dd($posts);
        if (!empty($posts)) {
            $post = array();
            foreach ($posts as $key => $val) {
                $terms = [];
                foreach ($val["terms"] as $val1) {
                    array_push($terms, $val1['slug']);
                }
                $videoName = $val['post_content'];
                $pat = ["[videojs", " autoplay=\"true\"]", " mpd", " mp4", "\"", "]"];
                for ($i = 0; $i < count($pat); $i++) {
                    $videoName = str_replace($pat[$i], "", $videoName);
                }
                $videoName = explode("=", $videoName);
                $post = ["ID" => $val["post_id"], "title" => $val["post_title"], "publish_date" => $val["post_date"]->scalar, "cover_url" => $val["post_thumbnail"]["thumbnail"], "terms" => $terms, "mpd_url" => $videoName[1], "mp4_url" => $videoName[2], "share_url" => $val["link"]];
            }
            // dd($post);
            $video = \App\video::firstOrNew(["post_id" => $id]);
            $video->name = $post["title"];
            $video->post_id = $post["ID"];
            $video->post_date = date("Y-m-d H:i:s", strtotime($post["publish_date"]));
            $video->type = "bollywood-gupshup";
            $video->mpd_url = $post["mpd_url"];
            $video->mp4_url = $post["mp4_url"];
            $video->cover_url = $post["cover_url"];
            $video->share_url = $post["share_url"];
            $video->save();
        }
    }
    public function dataVideoFromWP(\Illuminate\Http\Request $req, $id)
    {
        return \App\video::where("post_id", $id)->delete();
    }


    private function getAlbumById($id){
        $prefix = \DB::getTablePrefix();
        $albumdata = [];
        // For getting albums
        $album_sql = "SELECT id, name, post_id, label, geo, total_tracks, ifnull(cover_url,'') as cover_url, post_date FROM {$prefix}movies WHERE id = :id ORDER BY id";
        $category_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND NOT cat.id IN (33, 15, 32, 54, 56, 31,59) AND length(cat.name) > 3";
        $subcategory_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND length(cat.name) <= 3";
        $carousel_category_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND cat.id IN (33, 15, 32, 54, 56, 31,59,57,75)";
        // For getting tracks
        $track_sql = "SELECT track.id, track.name, track.geo as geo ,ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url FROM {$prefix}tracks as track INNER JOIN {$prefix}movies as movie ON track.movie_id=movie.id WHERE movie.id=:id";
        $albumTrack_sql = "SELECT track.id, track.name, ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url FROM tbl_albumtracks as albumtrack inner join tbl_tracks as track on albumtrack.track_id = track.id WHERE albumtrack.album_id=:id";
        // For Movie Trailer in songs
        $track_trailer_sql = "SELECT id, name, ifnull(cover_url,'') as cover_url, date_format(post_date,'%Y-%m-%d') as post_date, mpd_url, mp4_url, share_url FROM {$prefix}traillers WHERE movie_id=:movie_id";
        // For getting artists by album
        $artist_sql = "SELECT art.id, art.name,art.post_id, art.cover_url FROM tbl_artists as art INNER JOIN tbl_artist_movies as mart ON art.id=mart.post_id INNER JOIN tbl_movies as movie ON mart.movie_id=movie.id WHERE movie.id=:id";
        
        // Setting up album data
        $albums = \DB::select($album_sql, ["id" => $id]);
        foreach ($albums as $album) {
            // For adding tracks into albums start
            $category_data['category'] = \DB::select($category_sql, ["id" => $album->id]);
            $category_data['subcategory'] = \DB::select($subcategory_sql, ["id" => $album->id]);
            $category_data['homecategory'] = \DB::select($carousel_category_sql, ["id" => $album->id]);
            
            $category_data['category'] = !empty($category_data['category']) ? $category_data['category'][0] : [];
            $category_data['subcategory'] = !empty($category_data['subcategory']) ? $category_data['subcategory'][0] : [];
            $category_data['homecategory'] = !empty($category_data['homecategory']) ? $category_data['homecategory'] : [];
           
            $track_data = \DB::select($track_sql, ["id" => $album->id]);
            if (empty($track_data)){
                $track_data = \DB::select($albumTrack_sql, ["id" => $album->id]);
            }
            $tracks = [];
            foreach ($track_data as $track) {
                array_push($tracks, ["id" => $track->id, "Title" => $track->name, "cover" => $track->cover_url, "mp3" => $track->audio_url, "mpd" => $track->mpd_url, "mp4" => $track->mp4_url,"geo"=> isset($track->geo) ? $track->geo : 0]);
            }
            $mv_trailer = DB::select($track_trailer_sql,["movie_id" => $album->id]);
            $album_trailers = [];
            foreach($mv_trailer as $itemm){
                array_push($album_trailers, ["share_url"=>$itemm->share_url]);
            }
            // For adding tracks into albums end
            // For add artists into albums start
            $artist_data = \DB::select($artist_sql, ["id" => $album->id]);
            $artists = [];
            foreach ($artist_data as $artist) {
                array_push($artists, ["id" => $artist->id, "post_id" => !empty($artist->post_id) ? $artist->post_id : 0, "Title" => $artist->name, "cover_url" => $artist->cover_url]);
            }
            // For add artists into albums end
            $albumdata = ["id" => $album->id, "post_id"=>((int)$album->post_id > 0 ? $album->post_id : 0 ), "total_tracks"=>count($tracks), "Title" => $album->name,"geo"=>$album->geo, "post_date" => $album->post_date, "cover_url" => $album->cover_url, "categories" => $category_data, "artists" => $artists, "songs" => $tracks,"trailers" => $album_trailers];
            if (count($tracks) > 0){
                return $albumdata;        
            }else{
                return [];
            }
        }
        
        
    }

    private function getVideoById($id)
    {
        $video_sql = "SELECT id, post_id, name, date_format(post_date,'%Y-%m-%d') as post_date, type, feature, mpd_url, mp4_url, cover_url FROM tbl_videos WHERE id = :id";
        return \DB::select($video_sql, ["id" => $id])[0];
    }

    private function getfeaturedVideoById($id)
    {
        $featured_videodata = [];
        $featured_video_sql = "SELECT id, ifnull(post_id,0) as post_id, name,geo, cover_url, featured from tbl_feature_videos WHERE id = :id";
        $featured_video_track_sql = "SELECT track.id, track.name,track.geo,track.mpd_url, track.mp4_url from tbl_feature_video_tracks as fvt inner join tbl_tracks as track on fvt.track_id=track.id where fvt.feature_video_id=:id";
        $feturedvideos = \DB::select($featured_video_sql, ["id" => $id]);

        foreach($feturedvideos as $item){
            
            $item->tracks = \DB::select($featured_video_track_sql,["id"=>$item->id]);
            $featured_videodata = $item;
        
        }
        return $featured_videodata;
    }

    private function getartistById($id)
    {
        $artistsdata =[];
        $artist_sql = "SELECT id, name, ifnull(post_id,0) as post_id, ifnull(cover_url,'') as cover_url FROM tbl_artists WHERE id = :id";
        $artistsdata = \DB::select($artist_sql, ["id" => $id])[0];
        return  $artistsdata;

    }
    private function getTrailerById($id)
    {
        $trailer_sql = "SELECT id, ifnull(post_id,0) as post_id, name, trailer_geo as geo, feature, ifnull(cover_url,'') as cover_url, date_format(post_date,'%Y-%m-%d') as post_date, mpd_url, mp4_url FROM tbl_traillers WHERE id=:id ORDER BY post_date DESC";
        $trailer = \DB::select($trailer_sql, ["id" => $id])[0];
        return  $trailer;

    }

    public function getEvtData(\Illuminate\Http\Request $req, $flag = null)
    {
        $data = ["albums" => [], "trailers" => [], "videos" => [], "artists" => [], "featured_videos" => []];

        $evts = \DB::select("SELECT * FROM tbl_events where is_trigger = 0");
        $arrEvt = [];
        foreach($evts as $evt){
        	$arrEvt[] = $evt->id;
            //album data for wordpress
            if($evt->object_type === "album"){
                if($evt->flag == "D"){
                    $album = ["id" => $evt->object_id, "flag" => $evt->flag];
                }else{
                    $album = $this->getAlbumById($evt->object_id);
                }
                
                $flag = true;
                if(!empty($album)){
                    foreach($data["albums"] as $val){
                        // $flag = true;
                        if($val["id"] == $album["id"]){
                            $flag = false;
                        }
                    }
                    if($flag){
                        $album["flag"] = $evt->flag;
                        // $album
                        $data["albums"][] = $album;
                    }
                }
            }
            if($evt->object_type === "trailer"){
                $trailerData = $this->getTrailerById($evt->object_id);
                if(!empty($trailerData)){
                    $data["trailers"][] = $trailerData;
                }
            }
            //trailer data for wordpress
            if($evt->object_type === "video"){
                $video = $this->getVideoById($evt->object_id);
                if(!empty($video)){
                    $data["videos"][] = $video;
                }
            }

            //featured videos data for wordpress
            if($evt->object_type === "featured_videos"){
                $featured_video = $this->getfeaturedVideoById($evt->object_id);
                if(!empty($featured_video)){
                    $data["featured_videos"][] = $featured_video;
                }
            }

            //Artists data for wordpress
            if($evt->object_type === "artists"){
                $artist = $this->getartistById($evt->object_id);
                if(!empty($artist)){
                    $data["artists"][] = $artist;
                }
            }
        	    
        }
        for($i = 0; $i < count($arrEvt); $i++){
        	$uevt = event::find($arrEvt[$i]);
	        $uevt->is_trigger = 1;
	        $uevt->save();
        }
        
        return response()->json($data);
    }


    public function getDataforWP(\Illuminate\Http\Request $req, $flag = null)
    {
        $data = ['trailers' => [], 'albums' => [], "videos" => []];
        $prefix = \DB::getTablePrefix();

        // For getting Artists
        $artist_where  = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $artist_sql = "SELECT id, name, ifnull(post_id,0) as post_id, ifnull(cover_url,'') as cover_url FROM tbl_artists {$artist_where}";
        $data['artists'] = \DB::select($artist_sql);

        // For getting trailers
        $trailer_where  = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $trailer_sql = "SELECT id, ifnull(post_id,0) as post_id, name, trailer_geo as geo, feature, ifnull(cover_url,'') as cover_url, date_format(post_date,'%Y-%m-%d') as post_date, mpd_url, mp4_url FROM {$prefix}traillers " . $trailer_where . " ORDER BY post_date DESC";
        $data['trailers'] = \DB::select($trailer_sql);
        
        // For getting albums
        $album_where  = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and id!=8680" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $album_sql = "SELECT id, name, post_id, label, geo, total_tracks, ifnull(cover_url,'') as cover_url, post_date FROM {$prefix}movies " . $album_where . " ORDER BY id";
        // WHERE date_format(post_date,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')         
        
        // For getting Categories by movie_id
        $category_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND NOT cat.id IN (33, 15, 32, 54, 56, 31,59) AND length(cat.name) > 3";
        $subcategory_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND length(cat.name) <= 3";
        $carousel_category_sql = "SELECT cat.id, cat.name FROM tbl_categories as cat INNER JOIN tbl_movie_cats as mcat on cat.id = mcat.cat_id WHERE mcat.movie_id=:id AND cat.id IN (33, 15, 32, 54, 56, 31,59,57,75)";
        
        // For getting tracks
        $track_sql = "SELECT track.id, track.name, track.geo as geo ,ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url FROM {$prefix}tracks as track INNER JOIN {$prefix}movies as movie ON track.movie_id=movie.id WHERE movie.id=:id";
        
        // For Movie Trailer in songs
        $track_trailer_sql = "SELECT id, name, ifnull(cover_url,'') as cover_url, date_format(post_date,'%Y-%m-%d') as post_date, mpd_url, mp4_url, share_url FROM {$prefix}traillers WHERE movie_id=:movie_id";
        
        // For getting artists by album
        $artist_sql = "SELECT art.id, art.name,art.post_id, art.cover_url FROM tbl_artists as art INNER JOIN tbl_artist_movies as mart ON art.id=mart.post_id INNER JOIN tbl_movies as movie ON mart.movie_id=movie.id WHERE movie.id=:id";
        
        // For getting videos
        $video_where = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $video_sql = "SELECT id, post_id, name, date_format(post_date,'%Y-%m-%d') as post_date, type, feature, mpd_url, mp4_url, cover_url FROM tbl_videos " . $video_where . " ORDER BY id";
        $data["videos"] = \DB::select($video_sql);
        
        // getting playlist
        $playlist_where  = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $playlist_sql = "SELECT id, name, cover_url, ref, created_at as post_date FROM tbl_album_maps {$playlist_where}";
        $playlist_data = \DB::select($playlist_sql);
        
        // $playlist_tracks_sql = "SELECT id, track_id, name, audio_url, mpd_url, mp4_url FROM tbl_tracks WHERE id=:id";
        $playlist_tracks_sql = "SELECT track.id, track.name, ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url,track.geo FROM tbl_albumtracks as albumtrack INNER JOIN tbl_tracks as track on albumtrack.track_id=track.id WHERE albumtrack.album_id=:id";
        $featured_video_where  = ($flag == "update" ? "WHERE (post_id != 0 OR post_id != '' OR NOT post_id IS NULL) AND date_format(updated_at,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')" : "WHERE (post_id = 0 OR post_id = '' OR post_id IS NULL)");
        $featured_video_sql = "SELECT id, ifnull(post_id,0) as post_id, name,geo, cover_url, featured from tbl_feature_videos {$featured_video_where}";

        $featured_video_track_sql = "SELECT track.id, track.name,track.geo,track.mpd_url, track.mp4_url from tbl_feature_video_tracks as fvt inner join tbl_tracks as track on fvt.track_id=track.id where fvt.feature_video_id=:id";

        $albumTrack_sql = "SELECT track.id, track.name, ifnull(track.cover_url,'') as cover_url, track.audio_url, track.mpd_url, track.mp4_url FROM tbl_albumtracks as albumtrack inner join tbl_tracks as track on albumtrack.track_id = track.id WHERE albumtrack.album_id=:id";


       
        

        $data['featured_videos'] = [];
        foreach(DB::select($featured_video_sql) as $key => $item){
            $item->tracks = DB::select($featured_video_track_sql,["id"=>$item->id]);
            $data['featured_videos'][] = $item;
        }


        // Setting up album data
        $album_data = \DB::select($album_sql);
        foreach ($album_data as $album) {
            // For adding tracks into albums start
            $category_data['category'] = \DB::select($category_sql, ["id" => $album->id]);
            $category_data['subcategory'] = \DB::select($subcategory_sql, ["id" => $album->id]);
            $category_data['homecategory'] = \DB::select($carousel_category_sql, ["id" => $album->id]);
            
            $category_data['category'] = !empty($category_data['category']) ? $category_data['category'][0] : [];
            $category_data['subcategory'] = !empty($category_data['subcategory']) ? $category_data['subcategory'][0] : [];
            $category_data['homecategory'] = !empty($category_data['homecategory']) ? $category_data['homecategory'] : [];
            // $isPlaylist = false;
            // foreach($category_data['homecategory'] as $homeCatKey => $homeCatVal){
            //     if ((int)$homeCatVal->id == 57){
            //         $isPlaylist = true;
            //     }
            // }
            // if ($isPlaylist == true){
            //     $track_data = \DB::select($playlist_tracks_sql, ["id" => $album->id]);
            // }else{
            //     $track_data = \DB::select($track_sql, ["id" => $album->id]);
            // }
            $track_data = \DB::select($track_sql, ["id" => $album->id]);
            if (empty($track_data)){
                $track_data = \DB::select($albumTrack_sql, ["id" => $album->id]);
            }
            $tracks = [];
            foreach ($track_data as $track) {
                array_push($tracks, ["id" => $track->id, "Title" => $track->name, "cover" => $track->cover_url, "mp3" => $track->audio_url, "mpd" => $track->mpd_url, "mp4" => $track->mp4_url,"geo"=> isset($track->geo) ? $track->geo : 0]);
            }
            $mv_trailer = DB::select($track_trailer_sql,["movie_id" => $album->id]);
            $album_trailers = [];
            foreach($mv_trailer as $itemm){
                array_push($album_trailers, ["share_url"=>$itemm->share_url]);
            }
            // For adding tracks into albums end
            // For add artists into albums start
            $artist_data = \DB::select($artist_sql, ["id" => $album->id]);
            $artists = [];
            foreach ($artist_data as $artist) {
                array_push($artists, ["id" => $artist->id, "post_id" => !empty($artist->post_id) ? $artist->post_id : 0, "Title" => $artist->name, "cover_url" => $artist->cover_url]);
            }
            // For add artists into albums end
            $albums = ["id" => $album->id, "post_id"=>((int)$album->post_id > 0 ? $album->post_id : 0 ), "total_tracks"=>count($tracks), "Title" => $album->name,"geo"=>$album->geo, "post_date" => $album->post_date, "cover_url" => $album->cover_url, "categories" => $category_data, "artists" => $artists, "songs" => $tracks,"trailers" => $album_trailers];
            if (count($tracks) > 0){
                array_push($data['albums'], $albums);
            }
        }
        // $data["playlist"] = [];
        // foreach($playlist_data as $pVal){            
        //     $d["id"] = $pVal->id;
        //     $d["Title"] = $pVal->name;
        //     $d["post_date"] = $pVal->post_date;
        //     $d["cover_url"] = $pVal->cover_url;
        //     $refs = explode(",",$pVal->ref);
        //     $d["total_tracks"] = count($refs);
        //     $d["categories"]["category"] = ["id"=>"57", "name"=>"Featured-Playlist"];
        //     foreach($refs as $id){
        //         $tracks = \DB::select($playlist_tracks_sql,["id"=>$id]);
        //         if ($tracks){
        //             $d["songs"][] = $tracks[0];   
        //         }
        //     }
        //     array_push($data['playlist'], $d);
        // }

        // For serving data for storing data in wordpress
        return response()->json($data);
    }
    public function updateWPPostId(\Illuminate\Http\Request $req)
    {
        $param = $req->all();
        $data = json_decode($param["data"], true);
        $db = [];
        // For Artists
        foreach((!empty($data["artists"]) ? $data["artists"] : []) as $key => $val){
          $myId = 0;
          $post_id = 0;
        //   foreach($data["artists"][$key] as $artists_key => $artists_val){
            // if ((int)$key){
              $myId = $key;
              $post_id = $val;
            // } 
        //   }
          $artist = artist::find($myId);
          $artist->post_id = $post_id;
          $artist->save();
        }
        // For trailer
        foreach($data["trailers"] as $key => $value){
          $myId = 0;
          $post_id = 0;
          $tshare = "";
          foreach($data["trailers"][$key] as $trailer_key => $trailer_val){
            if ((int)$trailer_key){
              $myId = $trailer_key;
              $post_id = $trailer_val;
            }
            if ($trailer_key == "trailer_share_url"){
                $tshare = $trailer_val;
            }
          }
          $trailer = trailler::find($myId);
          $trailer->post_id = (!empty($post_id) ? $post_id : 0);
          $trailer->share_url = $tshare;
          $trailer->save();
        }
        // For trailer
        foreach($data["videos"] as $key => $value){
          $myId = 0;
          $post_id = 0;
          $share_url = "";
          foreach($data["videos"][$key]  as $video_key => $video_val){
            if ((int)$video_key){
              $myId = $video_key;
              $post_id = $video_val;
            }
            if ($video_key == "share_url"){
              $share_url = $video_val;
            }
          }
          $video = video::find($myId);
          $video->post_id = $post_id;
          $video->share_url = $share_url;
          $video->save();
        }

        foreach( (!empty($data["featured_videos"]) ? $data["featured_videos"] : []) as $key => $value){
          $myId = 0;
          $post_id = 0;
          $share_url = "";          
          foreach($data["featured_videos"][$key]  as $video_key => $video_val){
            if ((int)$video_key){
              $myId = $video_key;
              $post_id = $video_val;
            }
            if ($video_key == "share_url"){
                $share_url = $video_val;
            }
          }
          $video = featureVideo::find($myId);
          $video->post_id = $post_id;
          $video->share_url = $share_url;
          $video->save();
        }


        
        foreach((!empty($data["playlist"]) ? $data["playlist"] : []) as $key => $value){
            $myId = 0;
            $post_id = 0;
            $shareUrl = "";
            foreach ($data["playlist"][$key] as $playlist_Key => $playlist_Val) {
                if ((int) $playlist_Key) {
                    $myId = $playlist_Key;
                    $post_id = $playlist_Val;
                }
                if ($album_Key == "share_url"){
                    $shareUrl = $playlist_Val;
                }
            }
            $playlist = albumMap::find($myId);
            $playlist->post_id = $post_id;
            $playlist->share_url = $share_url;
            $playlist->save();
        }


        // For Album
        foreach ($data["albums"] as $key => $value) {
            $myId = 0;
            $post_id = 0;
            $shareUrl = "";
            $videoShareUrl = "";
            $songs = [];
            $artists = [];
            foreach ($data["albums"][$key] as $album_Key => $album_Val) {
                if ((int) $album_Key) {
                    $myId = $album_Key;
                    $post_id = $album_Val;
                } else {
                    if ($album_Key == "share_url") {
                        $shareUrl = $album_Val;
                    } else {
                        if (is_array($album_Val)) {
                            $myTrackId = 0;
                            $wpTrackId = 0;
                            $videoShareUrl = "";
                            if ($album_Key == "songs") {
                                $tracks = $data["albums"][$key][$album_Key];
                                foreach ($tracks as $track) {
                                    foreach ($track as $track_key => $track_val) {
                                        if ((int) $track_key) {
                                            $myTrackId = $track_key;
                                            $wpTrackId = $track_val;
                                        } else {
                                            if ($track_key == "video_share_url") {
                                                $videoShareUrl = $track_val;
                                            }
                                            if($track_key == "share_url"){
                                                $shareUrl = $track_val;
                                            }
                                        }
                                        $track = track::find($myTrackId);
                                        $track->track_id = $wpTrackId;
                                        // $track->share_url = $shareUrl;
                                        $track->video_share_url = $videoShareUrl;
                                        $track->save();
                                        $songs[$myTrackId] = ["track_id" => $wpTrackId,"movie_id" => $myId,"share_url" => $shareUrl,"video_share_url" => $videoShareUrl];
                                    }
                                }
                            }
                            if ($album_Key == "artists") {
                                $wpArtist = $data["albums"][$key][$album_Key];
                                if (!empty($wpArtist)){
                                  foreach ($wpArtist as $art) {
                                      foreach ($art as $art_key => $art_val) {
                                          if ((int) $art_key) {
                                              $artDB = artist::find($art_key);
                                              $artDB->post_id = $art_val;
                                              $artDB->save();
                                              $artists[$art_key] = ["post_id" => $art_val];
                                          }
                                      }
                                  }
                                }
                            }
                        }
                    }
                }
            }
            $movie = Movie::find($myId);
            if (!empty($movie)){
                $movie->post_id = $post_id;
                $movie->share_url = $shareUrl;
                $movie->save();
            }else{
                dd($myId);
            }
            $db["albums"][$myId] = ["post_id" => $post_id,"songs" => $songs,"artists" => $artists];
        }

        echo json_encode($db);
    }
}
