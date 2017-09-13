<?php
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{//&& $request->path() !== 'userinfo/ellucianmobile'


	if($request->path() !== 'groups/import' && $request->path() !== 'cache/clean' && $request->path() !== 'groups_pages' ){

		if($request->path() === 'logout' && Cookie::get('session')){
			$myCas = new Cas($request->path());
		}

		if (!Cookie::get('session')){// && !Session::has('bnum')) {// || ((time() - Session::getMetadataBag()->getLastUsed()) > (Config::get('session.lifetime') * 60))) {
		
		if ((Input::get('nologin') === NULL) && strpos($request->path(), '_microapp') == false){

				$myCas = new Cas($request->path());
				$myCas->authenticate();
				$userinfo = phpCAS::getAttributes();
				if(!isset($userinfo['UDC_IDENTIFIER'])){return View::make('unknown');}
				setcookie('session', Crypt::encrypt($userinfo['UDC_IDENTIFIER']), time() + (60 * 60), "/");

				$user = User::where(['bnum' => $userinfo['UDC_IDENTIFIER']])->first();
				// if(/*!Session::has('groups') || */$user === NULL || empty($user->pidm) || $user->pidm === NULL){//}  || ($user->invalidate >= Session::getMetadataBag()->getLastUsed())){
					if($user === NULL || $user->pidm === NULL) {
						try{
							$pidm = Proxy::get('/banner/person/pidm/'.$userinfo['UDC_IDENTIFIER']);
						} catch(Exception $e) {
							return View::make('unknown');
						}
						if($pidm && isset($pidm->SPRIDEN_PIDM)) {
							$user = User::firstOrNew(['pidm' => $pidm->SPRIDEN_PIDM]);
							$user->pidm = $pidm->SPRIDEN_PIDM;
							$user->bnum = $userinfo['UDC_IDENTIFIER'];
							if(isset($userinfo['mail'])){
								$user->email = $userinfo['mail'];
							}
							if(isset($userinfo['firstname'])){
								$user->first_name = $userinfo['firstname'];
							}
							if(isset($userinfo['lastname'])){
								$user->last_name = $userinfo['lastname'];
							}
							$user->save();
						}else{
							return View::make('unknown');
						}
					}
					if($user !== NULL) {
						// if($user->email !== $userinfo['mail'] || $user->first_name !== $userinfo['firstname'] || $user->last_name !== $userinfo['lastname']){
						$changed = false;
						if(isset($userinfo['mail']) && $user->email !== $userinfo['mail'] ){
							$user->email = $userinfo['mail'];
							$changed = true;
						}
						if(isset($userinfo['firstname']) && $user->first_name !== $userinfo['firstname']){
							$user->first_name = $userinfo['firstname'];
							$changed = true;
						}
						if(isset($userinfo['lastname']) && $user->last_name !== $userinfo['lastname']){
							$user->last_name = $userinfo['lastname'];
							$changed = true;
						}
						if($changed){$user->save();}
							
						// }


						Session::put('bnum',  $userinfo['UDC_IDENTIFIER']);
						// Session::put('groups', GroupMember::where('pidm', '=', $user->pidm )->where('membership_status', '!=', 'pending')->lists('group_id'));
						$myGroups = GroupMember::where('pidm', '=', $user->pidm )->where('membership_status', '!=', 'pending')->lists('group_id');

						$owned = GroupAdmin::where('pidm', '=', $user->pidm )->lists('group_id');
						$isSuper = (SuperAdmin::with('user')->where('pidm', '=', $user->pidm )->first() !== NULL);
						if($isSuper) {
							array_push($owned, Config::get('app.global_group'));
						}
						

						Session::put('owned', $owned);
						Session::put('groups', array_merge($myGroups, GroupComposite::whereIn('composite_id',  array_merge($myGroups, Session::get('owned') ))->lists('group_id')));
						Session::put('SuperAdmin', $isSuper);
						Auth::login($user);
					}else{
						return View::make('unknown');
					}
			}
			// else{

			// }
		}else{
			$user = User::where(['bnum' => Cookie::get('session')])->first();
			Session::put('bnum',  Cookie::get('session'));
			setcookie('session', Crypt::encrypt(Cookie::get('session')), time() + (60*15), "/");

			$myGroups = GroupMember::where('pidm', '=', $user->pidm )->where('membership_status', '!=', 'pending')->lists('group_id');
			// Session::put('groups', GroupMember::where('pidm', '=', $user->pidm )->where('membership_status', '!=', 'pending')->lists('group_id'));

			$owned = GroupAdmin::where('pidm', '=', $user->pidm )->lists('group_id');
			$isSuper = (SuperAdmin::with('user')->where('pidm', '=', $user->pidm )->first() !== NULL);
			if($isSuper){
				array_push($owned, Config::get('app.global_group'));
			}

			Session::put('owned', $owned);

			Session::put('groups', array_merge($myGroups, GroupComposite::whereIn('composite_id',  array_merge($myGroups, Session::get('owned') ))->lists('group_id')));
			Session::put('SuperAdmin', $isSuper);

			Auth::login($user);
		}
	}
					// return View::make('unauthorized');
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::view('unauthorized', array(), 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});


// Route::filter('no-cache',function($route, $request, $response){

//     $response->header("Cache-Control","no-cache,no-store, must-revalidate");
//     $response->header("Pragma", "no-cache"); //HTTP 1.0
//     $response->header("Expires"," Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

// });

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});



// Route::filter('session.remove', function()
// {

// 	session_write_close();
// 	return Config::set('session.driver', '');
// });

Route::filter('permission', function($route, $request, $value)
{
	// dd($value);
	switch($value){
		case 'assert':
				validate::assertSuper();
			break;	
		case 'super':
			if(!validate::isSuper()) {
				return Response::make(View::make('unauthorized'), 401);
			}
			break;
		case 'adminorhigher':
			$group_id = false;
			if(Input::has('group_id')){
				$group_id = Input::get('group_id');
			}else{
				$group_id = $route->getParameter('group_id');
			}
			if(!is_numeric($group_id)){ $group_id = Group::where('slug','=',$group_id)->first()->id;}

			if(!validate::isAdmin($group_id) && !validate::isSuper()) {
				return Response::make(View::make('unauthorized'), 401);
			}
			break;
		case 'anyorhigher':
			if(!validate::isAnyAdmin() && !validate::isSuper()) {
				return Response::make(View::make('unauthorized'), 401);
			}
			break;
		case 'admin':
			$group_id = false;
			if(Input::has('group_id')){
				$group_id = Input::get('group_id');
			}else{
				$group_id = $route->getParameter('group_id');
			}
			if(!is_numeric($group_id)){ $group_id = Group::where('slug','=',$group_id)->first()->id;}

			if(!validate::isAdmin($group_id)) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		case 'memberorhigher':
			$group_id = false;
			if(Input::has('group_id')){
				$group_id = Input::get('group_id');
			}else{
				$group_id = $route->getParameter('group_id');
			}			
			if(!is_numeric($group_id)){ $group_id = Group::where('slug','=',$group_id)->first()->id;}

			if(!validate::isMember($group_id) && !validate::isAdmin($group_id) && !validate::isSuper()) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		case 'member':
			$group_id = false;
			if(Input::has('group_id')){
				$group_id = Input::get('group_id');
			}else{
				$group_id = $route->getParameter('group_id');
			}
			if(!is_numeric($group_id)){ $group_id = Group::where('slug','=',$group_id)->first()->id;}
			if(!validate::isMember($group_id)) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		case 'ownerorhigher':
			$group_id = false;
			if(Input::has('group_id')){
				$group_id = Input::get('group_id');
			}else{
				$group_id = $route->getParameter('group_id');
			}
			if(!is_numeric($group_id)){ $group_id = Group::where('slug','=',$group_id)->first()->id;}

			$pidm = false;
			if(Input::has('pidm')){
				$pidm = Input::get('pidm');
			}else{
				$pidm = $route->getParameter('pidm');
			}
			if(!validate::isOwner($pidm) && !validate::isAdmin($group_id) && !validate::isSuper()) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		case 'owner':
			$pidm = false;
			if(Input::has('pidm')){
				$pidm = Input::get('pidm');
			}else{
				$pidm = $route->getParameter('pidm');
			}
			if(!validate::isOwner($pidm)) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		case 'any':
			$pidm = false;
			if(Input::has('pidm')){
				$pidm = Input::get('pidm');
			}else{
				$pidm = $route->getParameter('pidm');
			}
			if(!validate::isAny($pidm)) {
				return Response::view('unauthorized', array(), 401);
			}
			break;
		default:
			if(!validate::isSuper()) {
				return Response::view('unauthorized', array(), 401);
			}
	}

});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});