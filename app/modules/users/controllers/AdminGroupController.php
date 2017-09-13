<?php
class AdminGroupController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
 		return User::with('ownedGroups')->find(Auth::user()->pidm)['owned_groups'];
	}

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
		$group = new GroupMember();
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
		return Group::with('admins', 'members')->where('group_id', '=', $id)->first();
		//return GroupAdmin::with('user')->first();
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
		$group = Group::find($id);
		$group->fill($post_data);
		$group->last_updated_by = "me";
		$group->save();
		return $group;
	}

	public function destroy($id)
	{   
		$group = GroupAdmin::where("group_id", "=", $id)->where("pidm", "=", Auth::user()->pidm)->first();
		$group->delete();
		return $group;
	}

}
?>