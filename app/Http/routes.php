<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', function () {
    return view('welcome');
});

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

//Route::auth();
//Route::group(['middleware' => ['web','auth','role:Admin|Editor|super']], function () {
 Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::get('/', 'HomeController@index');
//    Route::get('/profile',['middleware'=>'auth','uses'=>'userController@profile']);
  //  Route::post('/profile',['middleware'=>'auth','uses'=>'userController@profile']);
//Route::group(['middleware' => 'auth'], function () {
    // import data from excel
    // Route::get('/excel', 'excelController@importExcel');
    // Route::get('/react', 'HomeController@react');
    // after login
//    Route::get('/', 'HomeController@index');

    // Track Editor

    Route::get('/tracks/addNew/{id?}', ['middleware' => 'auth', 'uses' => 'trackController@newTrack']);
    Route::post('/tracks/addNew/{id?}', ['middleware' => 'auth', 'uses' => 'trackController@saveTrack']);
    Route::any('/tracks/uploader/{movie_id?}',['middleware' => 'auth', 'uses' => 'trackController@uploader'])->where('movie_id','[0-9]+');
    Route::get('/tracks/edit/{track_id}', ['middleware' => 'auth', 'uses' => 'trackController@edit']);
    Route::post('/tracks/edit/{track_id}', ['middleware' => 'auth', 'uses' => 'trackController@save']);
    Route::get('/tracks/bulk/{movie_id?}',['middleware' => ['auth','role:Admin'], 'uses' => 'trackController@bulk']);
    Route::post('/tracks/bulkSave/{movie_id?}',['middleware' => ['auth','role:Admin'], 'uses' => 'trackController@bulkSave']);
    Route::get('/tracks/{movie_id?}', ['middleware' => 'auth', 'uses' => 'trackController@index'])->where('movie_id','[0-9]+');



    // Artist
    Route::get('/artist', ['middleware' => 'auth', 'uses' => 'trackController@artist']);
    Route::post('/artist/save', ['middleware' => 'auth', 'uses' => 'trackController@saveArtist']);
    // video
    Route::get('/video', ['middleware' => 'auth', 'uses' => 'videoController@index']);
    Route::post('/video/save', ['middleware' => 'auth', 'uses' => 'videoController@saveVideo']);
    Route::any('/video/uploader',['middleware' => 'auth', 'uses' => 'videoController@uploader']);
    //Trailer
    Route::get('/trailer', ['middleware' => 'auth', 'uses' => 'trailerController@index']);
    Route::post('/trailer/save', ['middleware' => 'auth', 'uses' => 'trailerController@save']);
    Route::any('/trailer/uploader',['middleware' => 'auth', 'uses' => 'trailerController@uploader']);

    // Singer
    Route::get('/singer', ['middleware' => 'auth', 'uses' => 'singerController@index']);
    Route::post('/singer/save', ['middleware' => 'auth', 'uses' => 'singerController@save']);

    // Music Director
    Route::get('/musicdirector', ['middleware' => 'auth', 'uses' => 'musicdirectorController@index']);
    Route::post('/musicdirector/save', ['middleware' => 'auth', 'uses' => 'musicdirectorController@save']);

    // Movies
    Route::get('/movie', ['middleware' => 'auth', 'uses' => 'movieController@index']);
    Route::any('/movie/uploader',['middleware' => 'auth', 'uses' => 'movieController@uploader'])->where('movie_id','[0-9]+');
    Route::post('/movie/save/{id?}', ['middleware' => 'auth', 'uses' => 'movieController@save'])->where('id','[0-9]+');

    // Trailler Mapping
    // Route::get('/traillers', ['middleware' => 'auth', 'uses' => 'traillerController@index']);
    // Route::post('/traillers', ['middleware' => 'auth', 'uses' => 'traillerController@save']);

    // Genere
    Route::get('/genere', ['middleware' => 'auth', 'uses' => 'genereController@index']);
    Route::post('/genere/save', ['middleware' => 'auth', 'uses' => 'genereController@save']);

    // Carousel
    Route::get('/carousel', ['middleware' => 'auth', 'uses' => 'CarouselController@index']);
    Route::post('/carousel/update', ['middleware' => 'auth', 'uses' => 'CarouselController@update']);
    
    // mapping 
    Route::get('/mapping', ['middleware' => ['auth','role:Admin'], 'uses' => 'mappingController@index']);
    Route::post('/mapping/save/{id?}', ['middleware' => ['auth','role:Admin'], 'uses' => 'mappingController@save']);
    Route::any('/mapping/uploader',['middleware' => ['auth','role:Admin'], 'uses' => 'mappingController@uploader']);

    // videoMap
    Route::get('/mapping/video', ['middleware' => 'auth', 'uses' => 'videoMappingController@index']);
    Route::post('/mapping/video/save/{id?}', ['middleware' => 'auth', 'uses' => 'videoMappingController@save']);
    Route::any('/mapping/video/uploader',['middleware' => 'auth', 'uses' => 'videoMappingController@uploader']);

    // Banner
    Route::get('/banner',['middleware' => 'auth','uses' =>'bannerController@index']);
    Route::post('/banner/{id?}',['middleware' => 'auth','uses' =>'bannerController@save']);

    // get all data and by id also
    Route::get('/api/get/Carousel',['middleware' => 'auth', 'uses' => 'getDataController@getCarouselData']);
    Route::any('/api/get/Trailler/{movie_id?}/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getTraillerData']);
    Route::any('/api/get/Tracks/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getTrackData']);
    Route::get('/api/endcoder/{id}/{job_id}', ['middleware' => 'auth', 'uses' => 'trackController@getEncoderStatus']);
    Route::get('/api/endcoder/trailer/{id}/{job_id}', ['middleware' => 'auth', 'uses' => 'movieController@getEncoderStatus']);
    Route::get('/api/endcoder/video/{video_id}/{job_id}', ['middleware' => 'auth', 'uses' => 'videoController@getEncoderStatus']);
    Route::any('/api/get/Artist/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getArtistData']);
    Route::any('/api/get/Singer/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getSingerData']);
    Route::any('/api/get/musicdirector/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getMusicDirectorData']);
    Route::any('/api/get/Genere/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getGenereData']);
    Route::any('/api/get/Movies/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getMovies']);
	Route::any('/api/get/Albums/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getAlbum']);
    Route::any('/api/get/video/{id?}', ['middleware' => 'auth', 'uses' => 'getDataController@getVideos']);
    Route::any('/api/get/TrackByMovie/{id}', ['middleware' => 'auth', 'uses' => 'getDataController@getTrackByMovie']);
    Route::get('/api/get/Category',['middleware' => 'auth', 'uses' => 'getDataController@getCategoryData']);
    Route::get('/api/get/SubCategory',['middleware' => 'auth', 'uses' => 'getDataController@getSubCategoryData']);
    Route::get('/api/get/Banner/{id?}',['middleware' => 'auth', 'uses' => 'bannerController@getData']);
    Route::get('/api/get/Playlist/{id?}',['middleware' => 'auth', 'uses' => 'getDataController@getPlaylist']);
    Route::get('/api/get/virtualmap/{id?}',['middleware' => 'auth', 'uses' => 'getDataController@getVirtualMaps']);

    //event create rout
    Route::post('/event/create', ['middleware' => 'auth', 'uses' => 'getDataController@create_event']);

    // User Management
    Route::get('/users/{id?}',['middleware'=>['auth','role:super'],'uses'=>'userController@index']);
    Route::post('/users/edit/{id}',['middleware'=>['auth','role:super'],'uses'=>'userController@save']);
    
    Route::get('/profile',['middleware'=>'auth','uses'=>'userController@profile']);
    Route::post('/profile',['middleware'=>'auth','uses'=>'userController@profile']);
    // Elasticsearch
    // Route::get('/es','esController@save');

    // Media Uploader
    // Route::get('/media', ['middleware' => 'auth', 'uses' => 'uploadController@upload']);
    // Route::post('/media/upload', ['middleware' => 'auth', 'uses' => 'uploadController@addNew']);
});

route::group(['middleware'=>'auth.basic'], function(){
        //  route::group(['middleware' => ['auth.basic','role:super']],function(){
              Route::get("/delTracks",'delController@delTracks');
              Route::get("/delMovie/{id}",'delController@delMovie');
              Route::get("/delTrailer",'delController@delTrailer');
          });

//});
Route::group(['middleware' => 'api'], function() {

});


Route::get('/wp_mig/{flag?}','wpDataController@getEvtData');
Route::post('/wp_mig','wpDataController@updateWPPostId');  