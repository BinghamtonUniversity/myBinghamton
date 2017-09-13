<?php
class AvailableGroupController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		// $group_ids = GroupMember::where('pidm', '=', Auth::user()->pidm )->lists('group_id');
		
 		return Group::where('type', '!=', 'Closed')->whereNotIn('id', Session::get('groups'))->get();
	}


	/**
	 * Update the specified resource in storage.
	 * POST /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	// public function store()
	// {
	// 	$post_data = Input::all();
	// 	if($post_data['join']){
	// 		$group = Group::find($post_data['id']);
	// 		$groupmember = new GroupMember();
	// 		$groupmember->group_id = $post_data['id'];
	// 		$groupmember->pidm = Auth::user()->pidm;
	// 		// $groupmember->membership_status = 'pending';

	// 		if($group->type == 'Private') {
	// 			if (validate::isSuper() || validate::isAdmin($post_data['id'])) {
	// 				$member->membership_status = 'pending';
	// 			}
	// 		}
	// 		$groupmember->last_updated_by = Auth::user()->pidm;
	// 		$groupmember->save();
	// 		return $groupmember;
	// 	}
	// }

	public function destroy($id)
	{   
		// $group = GroupAdmin::where("group_id", "=", $id)->where("admin_pidm", "=", 8)->first();
		// $group->delete();
		// return $group;
	}

}
?>