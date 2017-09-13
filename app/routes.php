<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'permission:any'), function() {

	Route::get('/admin', function()
	{
		return Redirect::to(Config::get('app.PRIMARY_DOMAIN_LOCATION').'/admin/groups/', 302); 

		$menuArray = Config::get('menu');
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$side_menu = "";//View::make('side_menu' , array("items" => Config::get('side_menu')));
		return View::make('admin' , array('menu' => $menu, 'side_menu' => $side_menu, 'content'=>''));
	});

});


Route::resource('/visits', 'VisitsController');


Route::get('/logout', function()
{
  if(phpCAS::isSessionAuthenticated()) {
    if (Auth::check()) {
    		Auth::user()->invalidate = time();
				Auth::user()->save();
      	Auth::logout();
    }
    Session::flush();
    setcookie("session", "", time()-3600);
   	phpCAS::logoutWithRedirectService(Config::get('app.PRIMARY_DOMAIN_LOCATION'));
   	exit;
  }
});

// Route::group(array('before' => 'permission:super'), function() {

// 	Route::get('/portalconfig/{id?}', function($id)
// 	{
// 		$response = [];
// 		if(Config::get('app.custom_css')){
// 			$response['css'] = file_get_contents(app()->make('path.public') . '/assets/css/customized.css');
// 		}
// 		return $response;
// 	});

// 	Route::patch('/portalconfig/{id?}', function($id)
// 	{
// 		if(Config::get('app.custom_css')){
// 			$post_data = Input::all();
// 			file_put_contents(app()->make('path.public') . '/assets/css/customized.css', $post_data['css'], LOCK_EX);
// 		}
// 	});
// });

Route::get('/get_remote', function()
{
	return Cache::get($_GET['q'], function(){
		$content = file_get_contents($_GET['q']);
		Cache::put($_GET['q'], $content, 10);
		return $content;
	});
});




Route::group(array('before' => 'permission:assert'), function() {
	Route::get('/cache/clean', function()
	{
		Cache::clean();
		return "Complete!";
	});
});

Route::get('/redirect/{target?}', function($target){
  return Redirect::to(base64_decode($target), 302); 
})->where('target', '.*');


// Route::get('/get_proxy', function()
// {
// 	$path = $_GET['q'];
// 	$path = str_replace('{{pidm}}',Auth::user()->pidm, $path);
// 	$path = str_replace('{{bnumber}}',Auth::user()->bnum, $path);
// 	if( strrpos ($path , '{{term_id}}')) {
// 		$term = Proxy::get('/banner/term_current/');
// 		$path = str_replace('{{term_id}}', $term[0]->TERM_CODE, $path);
// 	}
// 	$result = Proxy::get($path);
// 	if(!isset($result) && $result !== NULL) {App::abort(408);}
// 	return Response::json($result);
// });


App::missing(function($exception) 
{
	return Response::view('not_found', array(), 404);
    // if (Request::is('admin/*'))
    // {
    //     return Response::view('admin.missing',array(),404);
    // }
    // else
    // {
    //     return Response::view('default.missing',array(),404);
    // }
});
