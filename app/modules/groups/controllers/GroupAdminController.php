
<?php
class GroupAdminController extends BaseController {
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

		return GroupAdmin::with('user')->where('group_id', '=', $_GET['group'])->get();
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
		//$user = User::findOrFail($post_data['pidm']);

		$user = User::firstOrNew(array('pidm'=>$post_data['pidm']));

		if($user->pidm == null) {
			$user->pidm = $post_data['pidm'];
			$user->first_name = $post_data['first_name'];
			$user->last_name = $post_data['last_name'];
			$user->email = $post_data['email'];
			$user->save();
		}

		// $group = new GroupAdmin();
		$group	= GroupAdmin::firstOrNew(array('pidm' => $post_data['pidm'], 'group_id' => $post_data['group_id']));

		$group->fill($post_data);
		$group->last_updated_by = Auth::user()->pidm;
		$group->save();		
		$user->invalidate = time();
		$user->save();
		return $user;
	}

	// 	/**
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
	// 	$group->save();
	// 	return $group;
	// }

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function addMember()
	{
		$post_data = Input::all();
		$user = User::findOrFail($post_data->pidm);
		$group = new GroupMember();
		$group->fill($post_data);
		$group->last_updated_by = Auth::user()->pidm;
		$group->save();
		$user->invalidate = time();
		$user->save();
		return $group;
	}	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function addAdmin()
	{
		$post_data = Input::all();
		$user = User::findOrFail($post_data->pidm);
		$groupAdmin = new GroupAdmin();
		$groupAdmin->fill($post_data);
		$groupAdmin->last_updated_by = Auth::user()->pidm;
		$groupAdmin->save();
		// $user->invalidate = time();
		// $user->save();
		return $groupAdmin;
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


	public function remove($group_id, $pidm)
	{ 
		GroupAdmin::where("group_id", "=", $group_id)->where("pidm", "=", $pidm)->delete();
		// $user = User::findOrFail(['pidm' => $pidm]);
		// $user->invalidate = time();
		// $user->save();
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