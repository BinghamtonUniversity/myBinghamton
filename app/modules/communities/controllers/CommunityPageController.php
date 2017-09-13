<?php
class CommunityPageController extends BaseController {
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
			return CommunityPage::ordered()->get();
		}

		return CommunityPage::where('group_id', '=',  Input::get('group_id'))->ordered()->orderBy('id', 'asc')->get();
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
		$community = new CommunityPage();
		$community->fill($post_data);
		$community->slug = preg_replace("#[[:punct:]]#", "", str_replace(' ', '_', strtolower($community->name)));
		$community->content = '[[],[],[]]';
		$community->save();
		return $community;
	}

	/**
	 * Display the specified resource.
	 * GET /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($group_id, $id)
	{
		$group = CommunityPage::find($id);
		return $group;
	}
	/**
	 * Update the specified resource in storage.
	 * POST /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($group_id, $id=null)
	{

		$post_data = Input::all();
		$community;
		if(!isset($id)){
			$community = CommunityPage::find($group_id);
		}else{
			$community = CommunityPage::find($id);
		}
		$community->fill($post_data);
		if(isset($post_data['limit']) && $post_data['limit']){
			$community->groups = implode(',', $post_data['group']['ids']);
		}else{
			$community->groups = '';
		}
		// $community->slug = preg_replace("#[[:punct:]]#", "", str_replace(' ', '_', strtolower($community->name)));
		$community->save();
		return $community;
	}

	public function remove($group, $id)
	{ 
		GroupMember::where('group_id', '=', $group)->where('pidm', '=', $id)->delete();
	}	

	public function display($group, $slug = null)
	{ 
		if(!is_numeric($group)) {
			$groupObj = Group::where('slug','=',$group)->first();
			$group_slug = $group;
			$group = $groupObj->id;
		}else{
			$groupObj = Group::where('id','=',$group)->first();
			$group_slug = $groupObj->slug;
		}

		if(isset($slug)) {
			$comPage = CommunityPage::where('group_id', '=', $group)->where('slug', '=', $slug)->first();
		} else {
			$comPage = CommunityPage::where('group_id', '=', $group)->ordered()->first();
			return Redirect::to(Config::get('app.PRIMARY_DOMAIN_LOCATION').'/community/'.strtolower($group_slug).'/'.$comPage->slug, 302); 
		}

		if($comPage === null) {
			return Response::view('not_found', array(), 404);
		}

		if(Input::get('nologin') === NULL && !$comPage->public){
			if(!validate::isMember($group) && !validate::isAdmin($group) && !validate::isSuper()) {
				return Response::view('unauthorized', array(), 401);
			}
		}else{
			if(!$comPage->public){
				return Response::view('unauthorized', array(), 401);
			}
		}

		$groupList = explode(',', $comPage['groups']);
		if(!validate::isSuper() && (count($groupList) > 0 && $groupList[0] !== "")  && empty(array_intersect(explode(',',$comPage->groups), array_merge(Session::get('groups'),Session::get('owned'),array(Config::get('app.global_group'))) ))) {
			return Response::make(View::make('unauthorized'), 401);
		}




		if(Auth::user() !== NULL){

			$pages = CommunityPage::where('group_id', '=', $group)->where('unlist', '=', 0)->ordered()->get();
			$pages = $pages->toArray();

			$devices = ['','hidden-xs hidden-sm','hidden-xs','hidden-md hidden-lg','visible-xs-block'];
			foreach ($pages as $menu_index=>$menu_item) {
				$pagegroups = explode(',',$menu_item['groups']);
				if(	(count($pagegroups) > 1 || !empty($pagegroups[0]) ) &&
						!count(array_intersect ($pagegroups , Session::get('groups') )) &&
						!count(array_intersect ($pagegroups , Session::get('owned') )) &&
						!validate::isSuper()
					) {

					unset($pages[$menu_index]);
				}else if(isset($menu_item['device'])){
					$pages[$menu_index]['device_class'] = $devices[$menu_item['device']];
				}
			}
			$pages = array_values($pages);
			$comPage->menu = View::make('community_page_menu',  array('items'=> $pages, 'group_slug'=>strtolower($group_slug)));
			$comPage->preferences = PagePreference::where('pidm', '=', Auth::user()->pidm)->where('page_id', '=', $comPage->id)->first();
		}else{
			$comPage->menu = '';
			Session::set('groups', array());
			Session::set('owned', array());
		}
		$groups = Group::has('pages', '>', 0)->where('community_flag', '=', '1')->allGroups()->ordered()->get();
		$groups = $groups->toArray();
		$hassecondary = false;
		$count = 5;
		if(isset($groups[0]['priority'])) {
			foreach($groups as $key=>$group){
				if($groups[$key]['priority'] == 1){
					if(!$count){
						$groups[$key]['priority'] = 0;
						$hassecondary = true;
					}else{
						$count--;
					}
				}else{
					$hassecondary = true;
				}
			}
		}

		$comPage->mainMenu = View::make('main_menu',  array('items'=>$groups, 'secondary'=> $groups, 'hassecondary'=>$hassecondary, 'user'=> Auth::user()));
		// $comPage->preferences = PagePreference::where('pidm', '=', Auth::user()->pidm)->where('page_id', '=', $comPage->id)->first();

		$services = array();
		$microapps = array();
		if(!$comPage->editor) {
			$tempPage = json_decode($comPage->content, true);
			$tempPrefs = json_decode($comPage->preferences['content'], true);

			foreach ($tempPage as $col_index=>$column) {
				foreach ($column as $w_index => $widget) {
					// dd(in_array ($comPage->group_id , Session::get('owned')));

					if(	isset($widget['limit']) && $widget['limit'] && 
							!Session::get('SuperAdmin') &&
							!in_array ($widget['group'] , Session::get('groups') ) &&
							!in_array ($comPage->group_id , Session::get('owned') ) &&
							(gettype($widget['group']) == 'array') &&
							isset($widget['group']['ids']) &&
							!count(array_intersect ($widget['group']['ids'] , Session::get('groups') ))
					) {
							unset($tempPage[$col_index][$w_index]);
					} else {

						if(Input::get('nologin') === NULL && $comPage->public && Auth::user() === NULL){
							if($widget['widgetType'] == 'Poll'){
								unset($tempPage[$col_index][$w_index]);
							}
						}


						if($widget['widgetType'] == 'Service' && isset($widget['service'])){
							$service = Service::find($widget['service']);

							if($service !== null){
								$service->sources = json_decode($service->sources);
								$service->form = json_decode($service->form);

								if(count($service->sources)){
									// $serviceLib = new ServiceController();
									$data = array('user' => ServiceController::getUserData());

									foreach ($service->sources as $source) {
										if($data !== false && (!property_exists($source, 'fetch') || $source->fetch)) {


											$path = ServiceController::cleanUrl($source->path, $data['user']);
											$expiration = 0;

											if(strlen ($path)) {
												if(isset($source->cache) && $source->cache == true){
													// $data[$source->map]
													$temp = Cache::get($path);
													// $data[$source->map]=Cache::get($path);
													if(!is_null($temp)) {
														$expiration = time() -($temp->expiration - 600);
														$data[$source->map] = json_decode($temp->value);
													}else{
														$data = false;
													}
												}else{
													$data = false;
												}

											}
										}
										//only do this if they are all here
									}
									if($data !== false){
										$service->data = $data;
										$services[$widget['service']] = $service;
									}
								}
							}
						}

					if($widget['widgetType'] == 'Microapp' && isset($widget['microapp'])){
							$microapp = Microapp::find($widget['microapp']);

							if($microapp !== null && ((Auth::user() !== NULL) || $microapp->public)){
								$microapp->sources = json_decode($microapp->sources);
								$microapp->options = json_decode($microapp->options);

								if(count($microapp->sources)){
									// $microappLib = new microappController();
									$data = array('user' => MicroappController::getUserData());

									foreach ($microapp->sources as $source) {
										if($source->modifier == 'CSS' || $source->modifier == 'JS'){
											// dd($data['user']);
												if((!property_exists($source, 'fetch') || $source->fetch)) {
													if($source->modifier == 'CSS'){
								        	  Assets::add_style('', $source->path);
								        	}else{
								        		Assets::add_script('', $source->path);
								        	}
												}
										}else{
											if($data !== false && (!property_exists($source, 'fetch') || $source->fetch)) {
												$options = array();
												if(count($tempPrefs)>0){
													foreach($tempPrefs as $pref){
														if($pref['guid']==$tempPage[$col_index][$w_index]['guid']){
															$options = $pref;
														}
													}
												}
												$path = MicroappController::cleanUrl($source->path, array('user'=>$data['user'], 'options'=>$options) );

												$expiration = 0;

												if(strlen ($path)) {

													if(isset($source->cache) && $source->cache == true){
														$temp = Cache::get($path);
														if(!is_null($temp)) {
															$expiration = time() -($temp->expiration - 600);
															$data[$source->name] = json_decode($temp->value);
														}else{
															$data = false;
														}
													}else{
														$data = false;
													}
												}
											}

										}
										//only do this if they are all here
									}
									if($data !== false){
										$microapp->data = $data;
										$microapps[$widget['microapp']] = $microapp;
									}
								}
							}
						}
					}
				}
			}

			$comPage->content = json_encode($tempPage);
		}
		

		$tags = GroupKey::whereIn('group_id', Session::get('groups'))->select('name', 'value')->get();
		$returnable_tags = array();
		foreach($tags as $tag){
			if(!isset($returnable_tags[$tag['name']]) ){
				$returnable_tags[$tag['name']] = [];
			}
			$returnable_tags[$tag['name']][] = $tag['value'];
		}
		$comPage->tags = $returnable_tags;

		if(validate::isSuper() || in_array ($comPage->group_id , Session::get('owned'))){
			$composite_groups= GroupComposite::with(array('composite'=>function($query){
				$query->select('id', 'name');
			}))->where('group_id', '=' ,$comPage->group_id)->get();
			$comPage->composites = $composite_groups->lists('composite');
		}

		$comPage->services = json_encode($services);
		$comPage->microapps = json_encode($microapps);

		return View::make('home', $comPage);
	}


	public function destroy($page_id)
	{ 
		$community = CommunityPage::find($page_id);
		if(validate::hasPermission(true, $community->group_id, false, false) ){
			$community->delete();
			return $community;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

	public function order()
  {
    $temp = Input::all();
    foreach($temp['results'] as $key=>$result){
       $group = CommunityPage::find($result['key']);
       $group->order = (int) $result['order'];
       $group->save();
    }
  }


}
?>