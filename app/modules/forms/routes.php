<?php
Route::resource('/forms', 'CustomFormController');
Route::resource('/form', 'CustomFormController@display');

Route::post('/formsubmit/{id}', function($id){
	
	$myForm = CustomForm::find($id);
		if(validate::hasPermission(true, $myForm->group_id, $myForm->group_id, false) ){

			// if($myForm !== null){
			$values = array(
				'timestamp'=>date('Y-m-d H:i:s'),
				'B-number'=>Auth::user()->bnum, 
				'First'=>Auth::user()->first_name, 
				'Last'=>Auth::user()->last_name,
				'E-mail'=>Auth::user()->email
			);
			$input = Input::all();
			foreach($input as $key=>$value){
				$values[str_replace('_', '',$key)] = $value;
			}
			// $values = http_build_query($values);
			$path = '/google/sheets/post_to_sheet/'.$myForm->email.'/'.$myForm->gs_id;
			$response = array();
			if(strLen($myForm->gs_id)>0) {
				try{
					$response = Proxy::post($path, $values, true, 300);
				}catch(Exception $e){
					$response = array('failed'=>$response);
				}
			}else if(strLen($myForm->target)>0){
				$context = stream_context_create(array(
					'http' => array(
		        'method'  => "POST",
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'timeout' => 300,
						'content' => http_build_query($values)
					)
				));
				try{
					$response = file_get_contents($myForm->target, false, $context);
					$response = array('success'=>$response);
				} catch(Exception $e) {
					$response = array('failed'=>$response);
				}
			}

			return Response::json($response);
			// }else{
			// 	return Response::json(array('Rejected'=>'Access Denied'));
			// }
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}



});

Route::get('/admin/forms', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('forms::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/forms/{id}/form', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('forms::form', array('id'=>$id));

		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/groups/{id}/forms', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('forms::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	