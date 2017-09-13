<?php
class ServiceController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		if(isset($_GET['group_id'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group_id']) ){
				return Service::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('services')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
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
		$service = new Service();
		$service->fill($post_data);
		if(isset($post_data['sources'])){
			$service->sources = json_encode($post_data['sources']);
		}
		if(isset($post_data['form'])){
			$service->form = json_encode($post_data['form']);
		}
		if(validate::hasPermission(true, $service->group_id, false, false) ){
			$service->save();
			$service->sources = json_decode($service->sources);
			$service->form = json_decode($service->form);
			return $service;
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
		$service = Service::find($id);
		if(validate::hasPermission(true, $service->group_id, false, false) ){

			$service->fill($post_data);

			if(isset($post_data['sources'])){
				$service->sources = json_encode($post_data['sources']);
			}
			if(isset($post_data['form'])){
				$service->form = json_encode($post_data['form']);
			}

			$service->save();

			$service->sources = json_decode($service->sources);
			$service->form = json_decode($service->form);
			return $service;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}
	
	function getContents($path, $post){

			$method = 'GET';
			if($post){$method = 'POST';}
			$html_array = array(
	        'method'  => $method,
					'header'  => '',
					'timeout' => 30
				);
			if($method == 'POST'){
				$html_array['header'] = "Content-type: application/x-www-form-urlencoded\r\n".$html_array['header'];
				if(!is_null(Input::get('resource'))){
					$html_array['content'] = http_build_query(Input::get('resource'));
				}


			}else{
				if(!is_null(Input::get('resource'))) {
					$tempRoute = parse_url($path);

					$data = Input::get('resource');
					if(isset($tempRoute['query'])){
						parse_str($tempRoute['query'], $output);				
						$data = array_merge($data, $output);
					}
					$path = $tempRoute['scheme'].'://'.$tempRoute['host'].$tempRoute['path'].'?'.http_build_query($data);

				}
			}

			$context = stream_context_create(array(
				'http' => $html_array
			));

									// dd($html_array);
		return file_get_contents($path, false, $context);

		// return file_get_contents($path);
	}
	public static function getUserData() {
		if(Auth::user() !== null){
			return array(
				'pidm' =>Auth::user()->pidm,
				'bnum' => Auth::user()->bnum,
				'email' => Auth::user()->email,
				'first_name' => Auth::user()->first_name,
				'last_name' => Auth::user()->last_name,
				'pods' => explode('@', Auth::user()->email)[0]
			);
		}else{
			return array(
				'pidm' =>'',
				'bnum' => '',
				'email' => '',
				'first_name' => '',
				'last_name' => '',
				'pods' => ''
			);
		}
	}
	public static function cleanUrl($url, $data) {
		foreach($data as $key=>$item){
			$url = str_replace("{{user.$key}}", $item, $url);
		}
		return $url;
	}
	public function getSource($source, &$expiration, $post = false, $group_id){

		$data = null;
		$path = self::cleanUrl($source->path, self::getUserData());
		if(strlen ($path)) {
			if(isset($source->cache) && $source->cache == true){
				$data = Cache::get($path);
				if(!is_null($data)) {
					$expiration = time() - ($data->expiration - 600);
					$data = $data->value;
				}
			}
			if(is_null($data)) {
				try {
					switch($source->data_type) {
						case 'Proxy':
							$data = null;
							if($post) {
								$data = Proxy::post($path, Input::get('resource'));
							} else {
								$data = Proxy::get($path, Input::get('resource'));
							}
							if(!isset($data) && $data !== NULL) {App::abort(408);}
							break;
						case 'xml':
							$data = simplexml_load_string(self::getContents($path, $post), 'SimpleXMLElement', LIBXML_NOCDATA);
						break;
						case 'JSON':
							$data = json_decode(self::getContents($path, $post));
						break;
						case 'Internal':
							$request = Request::create($path, 'GET');

							// Store the original input of the request and then replace the input with your request instances input.
							$originalInput = Request::input();

							Request::replace($request->input());

							$data = json_decode(Route::dispatch($request)->getContent());

							// Replace the input again with the original request input.
							Request::replace($originalInput);
						break;
						case 'CSV':
							$data = array_map('str_getcsv', explode("\n", self::getContents($path, $post)));
						break;
						case 'HTML':
							$data = self::getContents($path, $post);
						break;
						default:
							$myhttps = new https_helper(Endpoint::whereIn('group_id',  array($group_id,Config::get('app.global_group')))->where('id', '=',$source->data_type)->first());

							if($myhttps) {
								$data = null;
								if($post) {
									$data = $myhttps->post('POST', $path, Input::get('resource'));
								} else {
									$data = $myhttps->get($path, Input::get('resource'));
								}
								if(!isset($data) && $data !== NULL) {App::abort(408);}
							}else{
								App::abort(408);
							}

						break;

					}
				}catch(Exception $e){
					$data = '';
				}

				if(isset($source->cache) && $source->cache){
					Cache::put($path, json_encode($data) , 10);
				}
			}else{
				$data = json_decode($data);
			}
		}
		return $data;
	}


	/**
	 * Display the specified resource.
	 * GET /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function fetch($id)
	{
		session_write_close();
		$service = Service::find($id);
		$expiration = 0;
		if($service !== null) {

			$pagedata = Input::all();
			$data = array('user' => self::getUserData());
			if(validate::hasPermission(false, $service->group_id, $service->group_id, false) ){

				$service->sources = json_decode($service->sources);
				$service->form = json_decode($service->form);

				if(count($service->sources)){
					foreach ($service->sources as $source) {
						if(!property_exists($source, 'fetch') || $source->fetch) {

							//use form data to modify path
							if(isset($service->form)) {
								foreach($service->form as $input) {
									if(isset($pagedata[$input->name])) {
										$source->path = str_replace('{{form.'.$input->name.'}}', rawurlencode($pagedata[$input->name]), $source->path);
									}
								}
							}
							$data[$source->map] = self::getSource($source, $expiration, false, $service->group_id);
						}
					}
				}
			}
			
			$service->sources = '';
			$service->data = $data;
			return Response::json($service)->header('Age', $expiration);

		}else{
			return null;
		}
	}


	public function getByMap($id, $map){
		session_write_close();
		$service = Service::find($id);
		if($service !== null){

			$pagedata = Input::all();
			if(validate::hasPermission(false, $service->group_id, $service->group_id, false) ){

				$service->sources = json_decode($service->sources);
				$service->form = json_decode($service->form);

				$expiration = 0;

				if(count($service->sources)) {
					foreach ($service->sources as $source) {
						if($source->map == $map) {

							//use form data to modify path
							if(isset($service->form)) {
								foreach($service->form as $input) {
									if(isset($pagedata[$input->name])) {
										$source->path = str_replace('{{form.'.$input->name.'}}', rawurlencode($pagedata[$input->name]), $source->path);
									}	
								}
							}
							return Response::json(self::getSource($source, $expiration, false, $service->group_id))->header('Age', $expiration);//->header('Max-Age', 600);

						}
					}
				}
			}
		}
	}
	public function postByMap($id, $map){
		session_write_close();
		$service = Service::find($id);
		if($service !== null){

			$pagedata = Input::all();

			if(validate::hasPermission(false, $service->group_id, $service->group_id, false) ){

				$service->sources = json_decode($service->sources);
				$service->form = json_decode($service->form);

				$expiration = 0;

				if(count($service->sources)) {
					foreach ($service->sources as $source) {
						if($source->map == $map) {

							//use form data to modify path
							if(isset($service->form)) {
								foreach($service->form as $input) {
									if(isset($pagedata[$input->name])) {
										$source->path = str_replace('{{form.'.$input->name.'}}', rawurlencode($pagedata[$input->name]), $source->path);
									}	
								}
							}
							return Response::json(self::getSource($source, $expiration, true, $service->group_id))->header('Age', $expiration);//->header('Max-Age', 600);

						}
					}
				}
			}
		}
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
		$service = Service::find($id);
		if(validate::hasPermission(true, $service->group_id, false, false) ){
			$service->sources = json_decode($service->sources);
			$service->form = json_decode($service->form);
			return $service;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}


	/**
	 * Display the specified resource.
	 * GET /apps/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function export($id)
	{
		$service = Service::select('name', 'template', 'form', 'css', 'script', 'sources', 'group_id')->find($id);
		if(validate::hasPermission(true, $service->group_id, false, false) ){
			$service->sources = json_decode($service->sources);
			$service->form = json_decode($service->form);
			unset($service->group_id);
			return $service;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}


	public function destroy($id)
	{   
		$service = Service::find($id);
		if(validate::hasPermission(true, $service->group_id, false, false) ){
			$service->delete();
			return $service;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

	public function terms()
	{
		return Proxy::get('/banner/term_current_list/');
	}


	//////////////////////
	// HELPER FUNCTIONS //
	//////////////////////

	function fetch_all_keys($prefs, $current_path, &$paths) {
		foreach($prefs as $pref_name => $pref_value) {
			$full_path = ltrim($current_path.".".$pref_name,'.');
			if (is_array($pref_value) && count($pref_value)>0){
				self::fetch_all_keys($pref_value,$full_path,$paths);
			} else {
				$paths[$full_path] = explode('.',$full_path);
			}
		}
		return $current_path.".".$pref_name;
	}


	function get_value($indexes, $arrayToAccess) {
		if(count($indexes)>1 && isset($arrayToAccess[$indexes[0]])) {
			return self::get_value(array_slice($indexes, 1), $arrayToAccess[$indexes[0]]);
		} else if (isset($arrayToAccess[$indexes[0]])) {
			if (is_bool($arrayToAccess[$indexes[0]])) {
				if ($arrayToAccess[$indexes[0]] == true) {
					return 'true';
				} else {
					return 'false';
				}
			} else {
				return $arrayToAccess[$indexes[0]];
			}
		} else {
			return 'undefined';
		}
	}


	//////////////////////////
	// END HELPER FUNCTIONS //
	//////////////////////////

	public function preferenceSummary($page_id, $widget_guid)
	{

		$myPage = CommunityPage::find($page_id);

		// Get group affiliated with the specified page id
		$group_id = $myPage['group_id'];

		// Get all group members for the specified $group_id, and get all of their page_preferences for the specified $page_id
		$group_member_page_preferences = PagePreference::where('page_id', '=', $page_id)->select('pidm', 'content')->with(array('User'=>function($query){
     $query->select('pidm', 'bnum', 'first_name', 'last_name');
		}) )->get();
		$group_member_page_preferences= $group_member_page_preferences->toArray();


		// Decode the JSON Data in $group_member_page_preferences and remove all widgets other than the one specified above
		$group_member_page_preferences_new = array();
		foreach($group_member_page_preferences as $key => $preference) {
			$widget_data = json_decode($group_member_page_preferences[$key]['content'],true);
			if(is_array($widget_data)){
				foreach($widget_data as $widget) {
					if ($widget['guid']==$widget_guid) {
						unset($widget['guid']);
						$group_member_page_preferences_new[$key]['content'] = $widget;
						break;
					}
				}
			}
		}

		// Build $all_page_preferences array containing the content-only portion of the widget preferences (Uses $group_member_page_preferences)
		$all_page_preferences = array();
		foreach($group_member_page_preferences_new as $preference) {
			$all_page_preferences[] = $preference['content'];
		}


		// Build $paths array containing all valid paths for widget preferences (Uses $all_page_preferences)
		$paths = array();
		foreach($all_page_preferences as $pref_value) {
			if (is_array($pref_value) && count($pref_value)>0){
				self::fetch_all_keys($pref_value,"",$paths);
			}
		}

		$paths_new = $paths;
		foreach($paths as $path_string => $path_array) {
			foreach($paths_new as $path_new_string => $path_new_array) {
				if ($path_string != $path_new_string) {
					if (stristr($path_string, $path_new_string)) {
						unset($paths_new[$path_new_string]);
					}
				} 
			}
		}
		asort($paths_new);

		// Build $flattened_user_data array containing all user preferences defined in $paths array for each individual user 
		$flattened_user_data = array();
		foreach($group_member_page_preferences_new as $index => $member_preferences) {
			foreach($group_member_page_preferences[$index]['user'] as $column_name => $member_data) {
				$flattened_user_data[$group_member_page_preferences[$index]['pidm']][$column_name] = $member_data;
			}
			foreach($paths_new as $path_name => $path_info_array) {
				$result =  self::get_value($path_info_array,$member_preferences['content']);
				$flattened_user_data[$group_member_page_preferences[$index]['pidm']][$path_name] = $result;
			}
		}
		
		header('Content-type: application/force-download');
		header('Content-Disposition: attachment; filename="'.$myPage['slug'].'_'.$widget_guid.'.csv"');
		$csv = '';
		foreach($group_member_page_preferences[$index]['user'] as $column_name => $member_data) {
			$csv .= $column_name.',';
		}
		foreach($paths_new as $path_name => $path_info_array) {
			$csv .= $path_name.',';
		}
		$csv .= "\r\n";
		foreach($flattened_user_data as $pref){
			$csv .= implode($pref,',')."\r\n";
		}
		return $csv;

	}



	public function publish($page_id, $widget_guid)
	{
		$myPage = CommunityPage::find($page_id);

		// Get group affiliated with the specified page id
		$group_id = $myPage['group_id'];

		// Get all group members for the specified $group_id, and get all of their page_preferences for the specified $page_id
		$group_member_page_preferences = PagePreference::where('page_id', '=', $page_id)->select('pidm', 'content')->with(array('User'=>function($query){
     $query->select('pidm', 'bnum', 'first_name', 'last_name');
		}) )->get();
		$group_member_page_preferences = $group_member_page_preferences->toArray();
		if(Input::has('target')){
			Proxy::post(Input::get('target'), $group_member_page_preferences);
		}
	}
}
?>