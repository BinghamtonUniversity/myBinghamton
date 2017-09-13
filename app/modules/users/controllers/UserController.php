<?php
class UserController extends BaseController {

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function store()
	{
		//return array('preference'=>'portal');
		$post_data = Input::all();
		$user = new User();
		$user->fill($post_data);
		$user->last_updated_by = "me";
		$user->save();
		return $user;
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
	}


}
?>