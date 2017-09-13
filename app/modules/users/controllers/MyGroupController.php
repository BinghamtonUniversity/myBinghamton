<?php
class MyGroupController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
 		return User::with(array('groups'=>function($query){
 			return $query->where('type', '=', 'Public')->orWherePivot('membership_status', '=', 'approved');
 		}))->find(Auth::user()->pidm)['groups'];
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	// public function store()
	// {
	// 	//return array('preference'=>'portal');
	// 	$post_data = Input::all();
	// 	$groupmember = new GroupMember();
	// 	$groupmember->fill($post_data);
	// 	$groupmember->last_updated_by = Auth::user()->pidm;
	// 	$group = Group::find($post_data['group_id']);

	// 	if($group->type == 'Private') {
	// 		if (validate::isSuper() || validate::isAdmin($post_data['group_idid'])) {
	// 			$member->membership_status = 'pending';
	// 		}
	// 	}
	// 	$groupmember->save();
	// 	// Auth::user()->invalidate();
	// 	return $groupmember;
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
		Auth::user()->invalidate();
		return GroupMember::where("group_id", "=", $id)->where("pidm", "=", Auth::user()->pidm)->first();
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
		Auth::user()->invalidate();
		$post_data = Input::all();
		$group = GroupMember::where("group_id", "=", $id)->where("pidm", "=", Auth::user()->pidm)->first();
		$group->messaging_pref = $post_data['messaging_pref'];
		$group->last_updated_by = "me";
		$group->save();
		return $group;
	}

	public function destroy($id)
	{   
		Auth::user()->invalidate();
		GroupMember::where("group_id", "=", $id)->where("pidm", "=", Auth::user()->pidm)->delete();
	}

}
?>