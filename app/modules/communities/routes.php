<?php
	Route::get('/community/{group_id}/{page_name?}/{page_id?}', 'CommunityPageController@display');
	Route::get('/r/{renderer}/{group_id}/{page?}', function($renderer,$group, $page="") {
			if($renderer == 'app') {	

				if($page!==''){

						$group = Group::where('slug','=', $group)->first();
						$page = CommunityPage::where('group_id', '=', $group->id)->where('slug', '=', $page)->first();

				}else{
					if(!is_numeric($group)){
						$groupObj = Group::where('slug','=',$group)->first();
						$page = CommunityPage::where('group_id', '=', $groupObj->id)->ordered()->first();
						return Redirect::to(Config::get('app.PRIMARY_DOMAIN_LOCATION').'/r/app/'.strtolower($groupObj->slug).'/'.$page->slug, 302); 
					}else{
						$page = CommunityPage::find($group);
						$group = Group::where('id','=', $page->group_id)->first();
					}
				}
				if($page === null){
					return Response::view('not_found', array(), 404);
				}
	
		if(Input::get('nologin') === NULL){
			if(!validate::isMember($group->id) && !validate::isAdmin($group->id) && !validate::isSuper()) {
				return Response::view('unauthorized', array(), 401);
			}
		}else{
			if(Auth::user() === NULL){
				Session::set('groups', array());
				Session::set('owned', array());
			}
			if(!$page->public){
				return Response::view('unauthorized', array(), 401);
			}
		}

		$groupList = explode(',', $page['groups']);
		if(!validate::isSuper() && (count($groupList) > 0 && $groupList[0] !== "")  && empty(array_intersect(explode(',',$page->groups), array_merge(Session::get('groups'),Session::get('owned'),array(Config::get('app.global_group'))) ))) {
			return Response::make(View::make('unauthorized'), 401);
		}

		if(Auth::user() !== NULL){
				$preferences = PagePreference::where('pidm' , Auth::user()->pidm)->get();
			}else{
				$preferences = '[]';
			}
				$appMenu = '<div style="height:20px"></div>';
				$tags = GroupKey::whereIn('group_id', Session::get('groups'))->select('name', 'value')->get();
				$returnable_tags = array();
				foreach($tags as $tag){
					if(!isset($returnable_tags[$tag['name']]) ){
						$returnable_tags[$tag['name']] = [];
					}
					$returnable_tags[$tag['name']][] = $tag['value'];
				}
				$tags = $returnable_tags;

				return View::make($renderer, array('group'=>$group, 'preferences' => $preferences, 'appMenu' => $appMenu, 'page' => $page, 'tags' => $tags));// array('name'=>$comPage->name, 'content'=> json_decode($comPage->content)));
			}else{
				return Response::view('not_found', array(), 404);
			}
	});


Route::get('/mycommunity', 'CommunityPageController@my');

Route::post('/community_pages/order', 'CommunityPageController@order');
Route::group(array('before' => 'permission:adminorhigher'), function() {
	Route::get('/community_pages', 'CommunityPageController@index');
	Route::get('/community_pages/{group_id}/{page_id}', 'CommunityPageController@show');
	Route::put('/community_pages/{group_id}/{page_id}', 'CommunityPageController@update');
	Route::post('/community_pages/{group_id}/', 'CommunityPageController@store');
	Route::post('/community_pages', 'CommunityPageController@store');
	Route::put('/community_pages/{page_id}', 'CommunityPageController@update');
});
Route::delete('/community_pages/{page_id}', 'CommunityPageController@destroy');



Route::resource('/page_preference', 'PagePreferenceController');


Route::get('/', function() {
		$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->allGroups()->select('id','slug', 'name', 'priority')->ordered()->get();
		if($groups->count() == 0){
			$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->where('name', '=', 'default')->select('id','slug', 'name')->get();
			if($groups->count() == 0){
				return Response::view('groupless', array(), 200);
			}
		}

		$page = CommunityPage::where('group_id', '=', $groups->first()->id)->ordered()->first();

		return Redirect::to(Config::get('app.PRIMARY_DOMAIN_LOCATION').'/community/'.strtolower($groups->first()->slug).'/'.$page->slug, 302); 
});




Route::get('/appConfiguration/{app}/{version?}', function($app) {
	$pages = CommunityPage::with('group')->whereIn('group_id',  Session::get('groups'))->ordered()->select('name','slug', 'order', 'id', 'group_id')->get();
	$config = array('mapp'=>array());
	$order = 0;
	foreach($pages as $page){
		$config['mapp']['mapp'.$page->id] = array(
			'type'=>'web',
			'name'=> $page->name,
			'order' => $order++,
			'icon' => '',
			'urls' => array('url'=>Config::get('app.PRIMARY_DOMAIN_LOCATION').'/r/app/'.strtolower($page->group->slug).'/'.$page->slug),
			'access'=> array(
				$page->group->name
			),
			'hideBeforeLogin' => 'true'
		);
	}
	return $config;
});


Route::get('/admin/groups/{id}/pages', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('communities::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	
