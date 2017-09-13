<?php

	Route::post('/groups/order', 'GroupController@order');
	Route::group(array('before' => 'permission:assert'), function() {
		Route::get('/groups/import', 'GroupController@import');
	});
	Route::resource('/groups', 'GroupController');

	Route::get('/groups_pages', 'GroupController@pages');
	Route::resource('/group_members', 'GroupMemberController',
	                array('except' => array('destroy')));
	Route::resource('/group_tags/my', 'GroupKeyController@my');
	Route::resource('/group_tags', 'GroupKeyController');

	Route::delete('/group_members/{group_id}/{pidm}', 'GroupMemberController@remove');

	Route::resource('/group_admins', 'GroupAdminController',
	                array('except' => array('destroy')));

	Route::delete('/group_admins/{group_id}/{pidm}', 'GroupAdminController@remove');

	Route::resource('/group_composites', 'GroupCompositeController',
	                array('except' => array('destroy')));
	Route::put('/group_composites/{group_id}/{composite_id}', 'GroupCompositeController@store');

	Route::delete('/group_composites/{group_id}/{composite_id}', 'GroupCompositeController@remove');

	Route::post('/groupAdmin', 'GroupController@addAdmin');
	Route::delete('/groupAdmin', 'GroupController@removeAdmin');
	Route::delete('/groupMember', 'GroupController@removeMember');

	// Route::get('/joins', 'GroupController@joins');
	// Route::post('/joins', 'GroupController@approve');

	// Route::get('/group_summary/{group_id}', 'GroupController@summary');

	Route::group(array('before' => 'permission:any'), function() {

		Route::get('/admin/groups', function()
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
			$groups = [];
			
			if(validate::isSuper()){
				$groups =  Group::where('id', '<>', Config::get('app.global_group'))->ordered()->get();
			}else {
				$groups = User::with('ownedGroups')->find(Auth::user()->pidm)['owned_groups'];
			}

			$script = View::make('groups::index', array('groups'=>$groups));

			return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
		});		
		Route::get('/admin/groups/{id}', function($id)
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));


			$group = Group::with(
				array('composites'=>function($query){
					$query->with(array('composite'=>function($query){
							$query->select('slug', 'id'); 
						})
					);
				})
			)	
			->with('tags')
			->with(array('pages'=>function($query){
				$query->select('id','group_id', 'name', 'slug', 'public');
			}))
			->with(array('microapps'=>function($query){
				$query->select('id','group_id', 'name', 'public');
			}))
			->with('membersCount')
			->with('adminsCount')
			->with('imagesCount')
			->with('pollsCount')
			->with('formsCount')
			->with('endpointsCount')
			->find($id);

			$script = View::make('groups::group', array('group'=>$group));

			return Response::view('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script))->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');;
		});
		Route::get('/admin/groups/{id}/admins', function($id)
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
			$script = View::make('groups::admins', array('id'=>$id));

			return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
		});		

		Route::get('/admin/groups/{id}/members', function($id)
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
			$script = View::make('groups::members', array('id'=>$id));

			return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
		});	

		Route::get('/admin/groups/{id}/tags', function($id)
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
			$script = View::make('groups::tags', array('id'=>$id));

			return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
		});	

		Route::get('/admin/groups/{id}/composites', function($id)
		{
			$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
			$script = View::make('groups::composites', array('id'=>$id));

			return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
		});

	});