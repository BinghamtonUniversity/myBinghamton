<?php

// Route::get('/myprofile', function() {

// 		$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->allGroups()->select('id', 'name')->ordered()->get();

// 		$pages = CommunityPage::where('group_id', '=', $groups->first()->id)->ordered()->get();
// 		$comPage = $pages->first();

// 		if($comPage === null){
// 			return Response::view('not_found', array(), 404);
// 		}
// 		$comPage->menu = 	'<div class="col-md-12"><ul class="nav nav-boxed nav-justified"><li><a href="#/mygroups">My Groups</a></li><li><a href="#/availablegroups">Available Groups</a></li></ul></div>';
// 	  //View::make('community_page_menu',  array( ));

// 		$comPage->mainMenu = View::make('main_menu',  array('items'=>$groups, 'secondary'=> $groups, 'user'=> Auth::user()));

// 		// $secondary = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->where('priority', '=', '0')->allGroups()->select('id', 'name', 'priority')->ordered()->get();
// 		// $comPage->mainMenu = View::make('main_menu',  array('items'=>$groups, 'secondary'=> $groups, 'hassecondary'=>count($secondary), 'user'=> Auth::user()));

// 		$comPage->html = '&nbsp;';//View::make('users::my_groups_view');

// 		// $comPage->editor = (GroupAdmin::with('user')->where('group_id', '=', $comPage->group_id)->where('pidm', '=', Auth::user()->pidm)->first() !== NULL);

// 		if(!Session::get('SuperAdmin')	) {
// 			$tempPage = json_decode($comPage->content, true);
// 			$myGroups = GroupMember::where('pidm', '=', Auth::user()->pidm)->lists('group_id');
// 			foreach ($tempPage[0] as $key => $value) {
// 				if(isset($value['limit']) && $value['limit']){
// 					if(!in_array ($value['group'] , $myGroups )){
// 						unset($tempPage[0][$key]);
// 					}
// 				}
// 			}
// 			$comPage->content = json_encode($tempPage);
// 		}
		
// 		$comPage->preferences = PagePreference::where('pidm', '=', Auth::user()->pidm)->where('page_id', '=', $comPage->id)->first();
// 		return View::make('home', $comPage);// array('name'=>$comPage->name, 'content'=> json_decode($comPage->content)));
// });
// Route::resource('/profile', 'ProfilesController');
// Route::resource('/mygroup', 'MyGroupController');

// Route::resource('/admingroup', 'AdminGroupController');

// Route::resource('/publicgroup', 'PublicGroupController');
// Route::resource('/privategroup', 'PrivateGroupController');
// Route::resource('/availablegroup', 'AvailableGroupController');

// Route::get('/query///{email?}', 'BannerQueryController@query');
// Route::get('/query//{last?}/{email?}', 'BannerQueryController@query');
Route::get('/query', function(){
	return Proxy::get('/banner/person/search/'.$_GET['first'].'/'.$_GET['last'].'/'.$_GET['email'],null);
});

Route::get('/users_clean', function(){
	$users = User::where('email', '=', '');
});
Route::get('/userinfo/{app}/{version?}', function(){
	$groups = Group::whereIn('id',  Session::get('groups') )->ordered()->lists('slug');
	return array("status"=>"success", "userId"=>Auth::user()->bnum, "authId"=>explode('@', Auth::user()->email)[0], "roles"=>$groups);
});

