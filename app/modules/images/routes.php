<?php
Route::resource('/images', 'ImagesController');

Route::get('/admin/images', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('images::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	
Route::get('/admin/groups/{id}/images', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('images::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	