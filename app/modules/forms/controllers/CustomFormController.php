<?php
class CustomFormController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		if(isset($_GET['group_id'])){
			validate::isAdmin($_GET['group_id']);
			return CustomForm::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->get();
		}
		if(isset($_GET['group'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group']) ){
				return Group::with('forms')->where('id', '=', $_GET['group'])->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('forms')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function store()
	{
		$post_data = Input::all();
		$form = new CustomForm();
		$form->fill($post_data);
		if(validate::hasPermission(true, $form->group_id, false, false) ){
			$form->email = Auth::user()->email;
			$form->save();
			return $form;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

	/**
	 * Display the specified resource.
	 * GET /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$form = CustomForm::find($id);
		if(validate::hasPermission(true, $form->group_id, $form->group_id, false) ){
			return $form;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}

	}
	// /**
	//  * Display the specified resource.
	//  * GET /apps/{id}
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	public function display($id)
	{
		session_write_close();
		$comPage = CustomForm::find($id);



		$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->allGroups()->select('id','slug', 'name', 'priority')->ordered()->get();
		if($groups->count() == 0){
			$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->where('name', '=', 'default')->select('id','slug', 'name')->get();
		}
		$pages = CommunityPage::where('group_id', '=', $groups->first()->id)->ordered()->get();

		// $comPage = $pages->first();

		// $comPatge = array();
		if($comPage === null){
			return Response::view('not_found', array(), 404);
		}
		$comPage->menu = View::make('community_page_menu',  array('items'=>$pages,'group_slug'=>strtolower($groups->first()->slug)));

		$secondary = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->where('priority', '=', '0')->allGroups()->select('id', 'name', 'priority')->ordered()->get();
		$comPage->mainMenu = View::make('main_menu',  array('items'=>$groups, 'secondary'=> $groups, 'hassecondary'=>count($secondary), 'user'=> Auth::user()));

		// $comPage->content = '{}';
		$comPage->layout = '7';
		$comPage->content = '[[{"guid":"57a1f55f-c1db-4d2d-bb59-2695f2512707","widgetType":"Form","title":"'.$comPage->name.'","form":"'.$comPage->id.'","legend":false, "container":true,"collapsed":false,"device":"widget","enable_min":false,"limit":false}]]';
		$comPage->editUrl = '"/admin#form/'.$comPage->id.'"';
		$comPage->preferences = '[]';//PagePreference::where('pidm', '=', Auth::user()->pidm)->where('page_id', '=', $comPage->id)->first();
		return View::make('home', $comPage);// array('name'=>$comPage->name, 'content'=> json_decode($comPage->content)));

	}

	/**
	 * Update the specified resource in storage.
	 * POST /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$post_data = Input::all();
		$form = CustomForm::find($id);
		if(validate::hasPermission(true, $form->group_id, false, false) ){
			$form->fill($post_data);
			$form->save();
			return $form;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

	public function destroy($id)
	{   
		$form = CustomForm::find($id);

		if(validate::hasPermission(true, $form->group_id, false, false) ){
			$form->delete();
			return $form;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

}
?>