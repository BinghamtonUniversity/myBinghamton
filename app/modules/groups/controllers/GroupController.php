<?php
class GroupController extends BaseController {
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
		if(isset($_GET['composites'])){
			$groups= GroupComposite::with('composite')->where('group_id', '=' ,$_GET['composites'])->get();
			return $groups->lists('composite');
		}
		if(validate::isSuper()){
			return Group::where('id', '<>', Config::get('app.global_group'))->ordered()->get();
		}else {
			return User::with('ownedGroups')->find(Auth::user()->pidm)['owned_groups'];
		}
	}

	/**/
	public function pages()
	{
		$groups = Group::with(array('composites'=>function($query){
			$query->with(array('composite'=>function($query){
				$query->select('slug', 'id', 'updated_at'); 
			}))->select('composite_id', 'group_id', 'updated_at');
		}, 'pages' => function($query) {
			$query->select('name', 'id', 'group_id','slug', 'meta_updated_at', 'order', 'device', 'groups', 'public')->where('unlist', '=', 0)->ordered()->orderBy('id', 'asc');
		}))->ordered()->where('community_flag', '=', '1')->has('pages', '>', 0)->select('id', 'slug', 'name', 'updated_at')->get();
		
		foreach($groups as $groupkey=>$group){
			$group->groups = array($group->slug);
			foreach($group->composites as $composite){
				$group->groups = array_merge($group->groups, array($composite->composite->slug));
			}
			// unset($group->slug);
			unset($group->composites);
			foreach($group->pages as $key=>$page){
				$groupList = explode(',',$page['groups']);
				if(count($groupList) > 0 && $groupList[0] !== ""){
					$group->pages[$key]['groups'] = Group::whereIn('id',$groupList)->lists('slug');
				}else{
					$group->pages[$key]['groups'] = [];
				}
			}


		}
		return $groups;
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
		$group = new Group();
		$group->fill($post_data);
		//$group->slug = str_replace(' ', '_', strtoupper($group->name));
		//$group->slug = preg_replace("#[[:punct:]]#", "", str_replace(' ', '_', strtolower($group->name)));

		$group->last_updated_by = Auth::user()->pidm;
		if($group->save()){
			return $group;
		}else{
			return Response::make(View::make('unauthorized'), 401);
		}
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
		$group->last_updated_by = Auth::user()->pidm;
		$group->save();
		return $group;
	}

	
	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function addMember()
	{
		$post_data = Input::all();
		$group = new GroupMember();
		$group->fill($post_data);
		$group->last_updated_by = Auth::user()->pidm;
		$group->save();
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
		$group = new GroupAdmin();
		$group->fill($post_data);
		$group->last_updated_by = Auth::user()->pidm;
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
		$group = Group::find($id);
		return $group;
	}

	public function destroy($id)
	{   
		$group = Group::find($id);
		if($id !== Config::get('app.global_group')){
			$group->delete();
		}
		return $group;
	}

	public function joins()
	{   
		if(validate::isSuper()){
			return GroupMember::with(array('user', 'group'))->whereHas('group', function($query){
				return $query->where('type', '=', 'Private');
			})->where('membership_status', '=', 'pending')->get();
		}else{
			return GroupMember::with(array('user', 'group'))->whereHas('group', function($query){
				return $query->myGroups()->where('type', '=', 'Private');
			})->where('membership_status', '=', 'pending')->get();
		}

	}


	public function approve()
	{		
		if (validate::isSuper() || validate::isAdmin(Input::get('group_id'))) {
			if(Input::has('approve')) {
				if(Input::get('approve') === true){
					$group = GroupMember::where("group_id", "=", Input::get('group_id'))->where("pidm", "=", Input::get('pidm'))->first();
					$group->membership_status = 'approved';
					$group->last_updated_by = Auth::user()->pidm;
					$group->save();
					$user = User::findOrFail(Input::get('pidm'));
					$user->invalidate();
					return $group;
				}else{
					$group = GroupMember::where("group_id", "=", Input::get('group_id'))->where("pidm", "=", Input::get('pidm'))->delete();
				}
			}
		}
	}

	public function order()
	{
		if(validate::isSuper()){


			$temp = Input::all();
			foreach($temp['results'] as $key=>$result){
				 $group = Group::find($result['key']);
				 $group->order = (int) $result['order'];
				 $group->save();
			}


		}
	}

	public function debug($message)
	{
		if(isset($_GET['debug'])) {
	   ob_start();
	   echo $message;
	   ob_end_flush();
	   flush();
	 }
	}

	public function import() {

   if(isset($_GET['debug'])){ob_end_clean();}

		// validate::assertSuper();
		DB::disableQueryLog();
		set_time_limit ( 600 );
		$groups = Proxy::get('/groups/list/');
		if(!is_string($groups) && count($groups)>0){
			foreach($groups as $group){
				//add group if doesn't exist

self::debug("<p>============================================</p><p>Processing group: ".$group."</p>");

				$currentgroup = Group::firstOrNew(array('slug'=>$group));
				if(!$currentgroup->exists) {
					$currentgroup->type = 'Closed';
					$currentgroup->name = $group;
					$currentgroup->slug = $group;
					$currentgroup->save();
self::debug($group." was added</p>");

				}
				$members = Proxy::get('/groups/members/'.$group);
self::debug("<p>".$group." has ".count($members)." members</p>");

   			$group_members = GroupMember::where('group_id', '=', $currentgroup->id)->lists('pidm');

self::debug( "<p>".$group." currently has ".count($group_members)." members</p>");
				//$myMembers = Proxy::post('/banner/person/basic_from_pidm/', 'pidms='.json_encode( array_values(array_diff ($members, $group_members)) ), true, 300);
				$myMembers = Proxy::post('/banner/person/basic_from_pidm/', array('pidms' => json_encode( array_values(array_diff ($members, $group_members)) )), true, 300);

self::debug("<p>Recieved info for ".count((array) $myMembers)." member(s)</p> <div style=\"word-wrap: break-word;\">");

				if(!is_string($myMembers) && count($myMembers)>0){
					// dd(array_values(array_diff ($members, $group_members)));
					// dd($myMembers);
					$member_insert = array();
					foreach($myMembers as $member) {

						if($member !== NULL) {
						 	$user = User::firstOrNew(array('pidm'=>$member->PIDM));

						 	if(!$user->exists) {
								$user->pidm = $member->PIDM;
								$user->first_name = $member->FIRST;
								$user->last_name = $member->LAST;
								$user->email = $member->EMAIL;
								$user->bnum = $member->BNUM;



								// $user->save();
self::debug("+");
						 	}

							$user->invalidate();
							$membership = GroupMember::where('group_id', '=', $currentgroup->id)->where('pidm', '=', $member->PIDM)->first();
							if($membership === NULL){
		// 						$membership = new GroupMember(array('group_id'=>$currentgroup->id, 'pidm'=>$member->PIDM));
		// 						$membership->save();
							 	$member_insert[] = array('group_id'=>$currentgroup->id, 'pidm'=>$member->PIDM);//, 'messaging_pref' => '', 'membership_status' => '', 'last_updated_by' => ''
							  if(count($member_insert) === 1000) {
									GroupMember::insert($member_insert);
									$member_insert = array();
self::debug("*");
							  }
							}
						}
self::debug(" ");
					}

					GroupMember::insert($member_insert);

					$member_insert = array();
self::debug("*");

self::debug("Complete");

				}
self::debug("</div><p>Finished importing ".$group.".</p>");

				$removables = array_values(array_diff ($group_members, $members));
				foreach($removables as $remove){
					GroupMember::where("group_id", "=", $currentgroup->id)->where("pidm", "=", $remove)->where("membership_status", "=", "")->delete();
					$user = User::findOrFail($remove);
					$user->invalidate();
self::debug("-");
				}
self::debug("<p>Completed processing group: ".$group."</p>");
			}

		}else{
self::debug('Completed with no groups!<br><br>'.$groups);
			// return ;
		}
self::debug('Completed Successfully!');
		// return 'true';
	}
}

?>