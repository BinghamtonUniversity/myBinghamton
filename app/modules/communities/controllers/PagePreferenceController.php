<?php
class PagePreferenceController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	// public function index()
	// {
	// 	return PagePreference::where('group_id', '=', $_GET['group_id'])->get();
	// }

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	// public function store()
	// {
	// 	$post_data = Input::all();
	// 	$community = new PagePreference();
	// 	$community->group_id = $post_data['group_id'];
	// 	$community->fill($post_data);
	// 	$community->save();
	// 	return $community;
	// }

	/**
	 * Display the specified resource.
	 * GET /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// $PagePreference = PagePreference::find($id);

		$PagePreference = PagePreference::find(array('pidm'=>Auth::user()->pidm, 'page_id' => $id));
		return $PagePreference;
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
		//$community = PagePreference::where('page_id', '=', $id)->createOrUpdate();
		//$community = new PagePreference();
		$PagePreference = PagePreference::firstOrNew(array('pidm'=>Auth::user()->pidm, 'page_id' => $id));
		$PagePreference->fill($post_data);
		// $community->page_id = $id;
		// $community->pidm = 	Auth::user()->pidm;
		// $community->createOrUpdate();
		$PagePreference->save();
		return $PagePreference;
	}

	// public function remove($group, $id)
	// { 
	// 	PagePreference::where('group_id', '=', $group)->where('pidm', '=', $id)->delete();
	// }	
}
?>