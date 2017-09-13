
<?php
class GroupMemberController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		if(isset($_GET['list'])){
			return Group::get();
		}
		$searchGroup = Group::find($_GET['group']);
		if($searchGroup->type == 'Closed' && !isset($_GET['all'])){
			return GroupMember::with(array('user'=>function($query){
				$query->select('pidm', 'first_name', 'last_name');
			}) )->where('group_id', '=', $_GET['group'])->where("membership_status", "!=", "")->select('pidm', 'membership_status')->get();
		}else{
			return GroupMember::with(array('user'=>function($query){
				$query->select('pidm', 'first_name', 'last_name');
			}) )->where('group_id', '=', $_GET['group'])->select('pidm', 'membership_status')->get();
		}

		// return Group::with(array('members'=>function($query){
		// 	$query->with(array('user'=>function($query){
		// 		$query->select('pidm', 'first_name', 'last_name');
		// 	}))->select('pidm','group_id');
		// }))->where('id', '=', $_GET['group'])->select('id', 'name')->get();
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
		// $member = new GroupMember();
		// dd($post_data);
		$member	= GroupMember::firstOrNew(array('pidm' => $post_data['pidm'], 'group_id' => $post_data['group_id']));
		$group = Group::find($post_data['group_id']);

		// if($group->type == 'Closed') {
		// 	return Response::make('Unauthorized', 401);
		// }
		$member->fill($post_data);
		$member->last_updated_by = Auth::user()->pidm;

		if($group->type == 'Private' || $group->type == 'Closed') {
			if (validate::isSuper() || validate::isAdmin($post_data['group_id'])) {
				$member->membership_status = 'approved';
			}
		}

		$member->save();

		$user = User::firstOrNew(array('pidm'=>$post_data['pidm']));

		if($user->pidm == null) {	
			$user->pidm = $post_data['pidm'];
			$user->first_name = $post_data['first_name'];
			$user->last_name = $post_data['last_name'];
			$user->email = $post_data['email'];
			// $user->save();
		}
		$user->invalidate();
		//$user = User::find($member->pidm);
		return $user;
	}

	// /**
	//  * Display the specified resource.
	//  * GET /apps/{id}
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	// public function show($id)
	// {
	// 	$group = Group::find($id);
	// 	return $group;
	// }

	// /**
	//  * Update the specified resource in storage.
	//  * POST /apps/{id}
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	// public function update($id)
	// {
	// 	$post_data = Input::all();
	// 	$group = Group::find($id);
	// 	$group->fill($post_data);
	// 	$group->last_updated_by = Auth::user()->pidm;		
	// 	$group = Group::find($post_data['group_id']);
	// 	if($group->type == 'Closed') {
	// 		return Response::make('Unauthorized', 401);
	// 	}
	// 	//$group->group_id =  str_replace(' ', '_', strtolower($group->name));
	// 	$group->save();
	// 	return $group;
	// }

	public function remove($group_id, $pidm)
	{ 		

		$group = Group::find($group_id);
		// if($group->type == 'Closed') {
		// 	return Response::make('Unauthorized', 401);
		// }
		GroupMember::where("group_id", "=", $group_id)->where("pidm", "=", $pidm)->delete();

		$user = User::findOrFail($pidm);
		$user->invalidate();
	}

	// public function joins()
	// {   
 // 		return GroupMember::with(array('user', 'group'))->whereHas('group', function($query){
 // 			return $query->where('type', '=', 'Private');
 // 		})->where('membership_status', '!=', 'approved')->get();
	// }
	// public function approve()
	// {   
 // 	 	$post_data = Input::all();
	// 	 if($post_data['approve']){
	// 		$group = GroupMember::where("group_id", "=", $post_data['group_id'])->where("group_pidm", "=", 8)->first();
	// 	// 	$group = new GroupMember();
	// 	 	$group->membership_status = 'approved';
	// 	// 	$group->group_pidm = 8;
	// 	 	$group->last_updated_by = "me";
	// 	 	$group->save();
	// 	 	return $group;
	// 	 }
	// }
}
?>