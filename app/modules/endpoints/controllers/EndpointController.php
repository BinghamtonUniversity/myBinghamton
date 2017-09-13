<?php
class EndpointController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		// if(isset($_GET['group_id'])){
		// 	validate::isAdmin($_GET['group_id']);
		// 	return Endpoint::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->get();
		// }
		// return Group::with('endpoints')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
		// return Endpoint::all();

		if(isset($_GET['group_id'])){
			validate::isAdmin($_GET['group_id']);
			return Enpoints::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->get();
		}
		if(isset($_GET['group'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group']) ){
				return Group::with('endpoints')->where('id', '=', $_GET['group'])->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('endpoints')->myGroups()->get();
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
		$endpoint = new Endpoint();
		$endpoint->fill($post_data);
		// if(validate::hasPermission(true, $endpoint->group_id, false, false) ){
		if(Input::get('targetpassword') !== '*****'){
			$endpoint->password = Crypt::encrypt(Input::get('targetpassword'));
		}
		$endpoint->save();
		return $endpoint;
		// }else{	
		// 	return Response::make(View::make('unauthorized'), 401);
		// }

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
		$endpoint = Endpoint::find($id);
		if(validate::hasPermission(true, $endpoint->group_id, false, false) ){
			return $endpoint;
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
	// public function live($id)
	// {
	// 	session_write_close();
	// 	$endpoint = Endpoint::find($id);
	// 	if(EndpointSubmission::where('pidm', '=', Auth::user()->pidm)->where('endpoint_id', '=', $id)->count() > 0){
	// 		$results = [];
	// 		$pSubmits = JSON_decode($endpoint->content);
	// 		$endpoint->total = 0;
	// 		foreach($pSubmits as $pSubmission){
	// 			$results[$pSubmission->label] = EndpointSubmission::where('endpoint_id', '=', $id)->where('choice', '=', $pSubmission->label)->count();
	// 			$endpoint->total += $results[$pSubmission->label];
	// 		}
	// 		$endpoint->results = $results;
	// 	}
	// 	return $endpoint;
	// }

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
		$endpoint = Endpoint::find($id);
		// if(validate::hasPermission(true, $endpoint->group_id, false, false) ){
		$endpoint->fill($post_data);
		if(Input::get('targetpassword') !== '*****' && Input::get('targetpassword') !== $endpoint->password){
			$endpoint->password = Crypt::encrypt(Input::get('targetpassword'));
		}
		$endpoint->save();
		return $endpoint;
		// }else{	
		// 	return Response::make(View::make('unauthorized'), 401);
		// }
	}

	public function destroy($id)
	{   
		$endpoint = Endpoint::find($id);
		if(validate::hasPermission(true, $endpoint->group_id, false, false) ){
			$endpoint->delete();
			return $endpoint;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

}
?>