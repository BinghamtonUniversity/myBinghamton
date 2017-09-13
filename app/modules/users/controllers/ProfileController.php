<?php
class ProfilesController extends BaseController {

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function store()
	{
		return array('preference'=>'portal');
		$post_data = Input::all();
		$group = new Group();
		$group->fill($post_data);
		$group->last_updated_by = "me";
		$group->save();
		return $group;
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
		// $group = Group::find($id);
		return array('preference'=>'portal');
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
		return array('preference'=>'portal');
		$post_data = Input::all();
		$group = Group::find($id);
		$group->fill($post_data);
		$group->last_updated_by = "me";
		$group->save();
		return $group;
	}


}
?>