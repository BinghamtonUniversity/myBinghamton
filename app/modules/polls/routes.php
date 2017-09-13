<?php
Route::resource('/polls', 'PollController');
// Route::resource('/group_members', 'GroupMemberController',
//                 array('except' => array('destroy')));

// Route::delete('/group_members/{group}/{id}', 'GroupMemberController@remove');

// Route::resource('/group_admins', 'GroupAdminController',
//                 array('except' => array('destroy')));

// Route::delete('/group_admins/{group}/{id}', 'GroupAdminController@remove');

// Route::post('/groupAdmin', 'GroupController@addAdmin');
// //Route::post('/groupMember', 'GroupMemberController@store');
// Route::delete('/groupAdmin', 'GroupController@removeAdmin');
// Route::delete('/groupMember', 'GroupController@removeMember');

// //Route::get('/mygroups', 'GroupsController@mygroups');
// Route::get('/joins', 'GroupController@joins');
// Route::post('/joins', 'GroupController@approve');



Route::resource('/pollsubmit', 'PollSubmissionController');
Route::get('/pollresults/{id}', 'PollSubmissionController@results');
Route::get('/pollresults', 'PollSubmissionController@lastresults');
Route::get('/polllive/{id}', 'PollController@live');
Route::get('/admin/polls', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('polls::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/polls/{id}/poll', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('polls::poll', array('id'=>$id));

		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/polls/{id}/submissions', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('polls::submissions', array('id'=>$id));

		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});

Route::get('/admin/polls/{id}/graphs', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('polls::graphs', array('id'=>$id));

		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/groups/{id}/polls', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('modules/polls/index', array('id'=>$id));
	$script = View::make('polls::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	