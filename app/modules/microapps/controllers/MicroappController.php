<?php
use Carbon\Carbon;
class MicroappController extends BaseController {
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
				return Microapp::where('group_id', '=', $_GET['group_id'])
											->orWhere('group_id', '=', Config::get('app.global_group'))
											->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		if(isset($_GET['group'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group']) ){
				return Group::with('microapps')->where('id', '=', $_GET['group'])->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('microapps')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
	}

	public function byGroup($id)
	{
			if(validate::isSuper() || validate::isAdmin($id) ){
				return Group::with('microapps')->where('id', '=', $id)->select('id', 'name')->get();

				// return Microapp::where('group_id', '=', $id)->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
	}

	public function used($id)
	{
		$myApp = Microapp::select('id','group_id')->find($id);
		if($myApp->group_id !== 0){
			$pages = CommunityPage::where('group_id', '=',  $myApp->group_id)->get();
		}else{
			$pages = CommunityPage::select('id','name','slug', 'content','group_id')->get();
		}
		$page_list = [];
		foreach($pages as $page){
			$columns = json_decode($page->content,true);
			foreach($columns as $column){
				foreach($column as $widget){
					if($widget['widgetType'] === 'Microapp'){
						if($widget['microapp'] == $myApp->id){								
							unset($page->content);
							$page_list[] = $page;
							break 2;
						}
					}
				}
			}
		}
		return $page_list;
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
		$microapp = new Microapp();
		$microapp->fill($post_data);
		if(isset($post_data['sources'])){
			$microapp->sources = json_encode($post_data['sources']);
		}
		if(isset($post_data['options'])){
			$microapp->options = json_encode($post_data['options']);
		}
		if(validate::hasPermission(true, $microapp->group_id, false, false) ) {
			$microapp->save();
			$microapp->sources = json_decode($microapp->sources);
			$microapp->options = json_decode($microapp->options);
			$microapp->group_id = intval($microapp->group_id);
			return $microapp;
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
		$microapp = Microapp::find($id);
		if(validate::hasPermission(true, $microapp->group_id, false, false) ) {


			$first = Carbon::parse($post_data['updated_at']);
			$second = Carbon::parse($microapp->updated_at);

			if($first->gte($second) || isset($post_data['force'])){

				$microapp->fill($post_data);

				if(isset($post_data['sources'])){
					$microapp->sources = json_encode($post_data['sources']);
				}
				if(isset($post_data['options'])){
					$microapp->options = json_encode($post_data['options']);
				}

				$microapp->save();

				$microapp->sources = json_decode($microapp->sources);
				$microapp->options = json_decode($microapp->options);
				return $microapp;
			}else{

				$microapp->sources = json_decode($microapp->sources);
				$microapp->options = json_decode($microapp->options);
				App::abort(409, $microapp);
			}
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	
	}
	
	function getContents($path, $method){

			// $method = 'GET';
			// if($post){$method = 'POST';}
			$html_array = array(
	        'method'  => $method,
					'header'  => '',
					'timeout' => 30
				);
			if($method != 'GET'){
				$html_array['header'] = "Content-type: application/x-www-form-urlencoded\r\n".$html_array['header'];
				if(!is_null(Input::get('request'))){
					$html_array['content'] = http_build_query(Input::get('request'));
				}
			}else{
				if(!is_null(Input::get('request'))) {
					$tempRoute = parse_url($path);

					$data = Input::get('request');
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
		$m = new Mustache_Engine;
		$data['request'] = Input::get('request');
		return $m->render($url, $data); 
	}
	public function getSource($source, &$expiration, $method, $group_id, $options=array()){

		$data = null;
		$path = self::cleanUrl($source->path,array('user' => self::getUserData(), 'options'=>$options));
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
					switch($source->endpoint) {
						case 'Internal':
							$request = Request::create($path, 'GET');

							// Store the original input of the request and then replace the input with your request instances input.
							$originalInput = Request::input();

							Request::replace($request->input());

							$data = json_decode(Route::dispatch($request)->getContent());

							// Replace the input again with the original request input.
							Request::replace($originalInput);
						break;
						case 'External':
							$data = self::getContents($path, $method);
						break;
						default:
							$myhttps = new https_helper(Endpoint::whereIn('group_id',  array($group_id,Config::get('app.global_group')))->where('id', '=',$source->endpoint)->first());

							if($myhttps) {
								$data = null;
								if($method == 'GET') {
									$data = $myhttps->get($path, Input::get('request'));
								} else {
									$data = $myhttps->post($method, $path, Input::get('request'));
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
				switch($source->modifier) {
						case 'CSV':
							$data = array_map('str_getcsv', explode("\n", $data));
						break;
						case 'xml':
							$data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
						break;
						default:
							try{
								$try_decode = json_decode($data);
								if($try_decode){
									$data = $try_decode;
								}
							}catch(Exception $e){}
						break;
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
		$microapp = Microapp::find($id);
		$expiration = 0;
		if($microapp !== null) {

			$pagedata = Input::all();
			$data = array('user' => self::getUserData());
			if(validate::hasPermission(false, $microapp->group_id, $microapp->group_id, false,$microapp->public) ) {

				$microapp->sources = json_decode($microapp->sources);
				$microapp->options = json_decode($microapp->options);

				$options = array();
				if(isset($microapp->options) && isset($microapp->options->fields)) {
					foreach($microapp->options->fields as $input) {
						if(isset($input->name) && isset($pagedata[$input->name])) {
							$options[$input->name] = rawurlencode($pagedata[$input->name]);
						}
					}	
				}

				if(count($microapp->sources)){
					foreach ($microapp->sources as $source) {
						if(!property_exists($source, 'fetch') || $source->fetch ) {
							if(isset($source->modifier) && ($source->modifier == 'CSS' || $source->modifier == 'JS')){

							}else{							

								$data[$source->name] = self::getSource($source, $expiration, 'GET', $microapp->group_id, $options);
							}
						}
					}
				}
			}else{	
				return Response::make(View::make('unauthorized'), 401);
			}
			
			$microapp->sources = '';
			$microapp->data = $data;
			return Response::json($microapp)->header('Age', $expiration);

		}else{
			return null;
		}
	}


	public function fetchByMap($id, $name, $verb){
		session_write_close();
		$microapp = Microapp::find($id);
		if($microapp !== null){

			$pagedata = Input::all();

			if(validate::hasPermission(false, $microapp->group_id, $microapp->group_id, false, $microapp->public) ) {

				$microapp->sources = json_decode($microapp->sources);
				$microapp->options = json_decode($microapp->options);

				$expiration = 0;
				$options = array();
				if(isset($microapp->options) && isset($microapp->options->fields)) {
					foreach($microapp->options->fields as $input) {
						if(isset($input->name) && isset($pagedata[$input->name])) {
							$options[$input->name] = rawurlencode($pagedata[$input->name]);
						}
					}	
				}

				if(count($microapp->sources)) {
					foreach ($microapp->sources as $source) {
						if($source->name == $name) {
							return Response::json(self::getSource($source, $expiration, $verb, $microapp->group_id, $options))->header('Age', $expiration);
						}
					}
				}
			}else{	
				return Response::make(View::make('unauthorized'), 401);
			}
		}
	}

	public function getByMap($id, $name){
		return self::fetchByMap($id, $name, 'GET');
	}

	public function postByMap($id, $name){
		return self::fetchByMap($id, $name, 'POST');
	}

	public function putByMap($id, $name){
		return self::fetchByMap($id, $name, 'PUT');
	}

	public function deleteByMap($id, $name){
		return self::fetchByMap($id, $name, 'DELETE');
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
		$microapp = Microapp::find($id);
		if(validate::hasPermission(true, $microapp->group_id, false, false) ){
			$microapp->sources = json_decode($microapp->sources);
			$microapp->options = json_decode($microapp->options);
			return $microapp;
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
		$microapp = Microapp::select('template', 'options', 'css', 'script', 'sources', 'group_id')->find($id);
		if(validate::hasPermission(true, $microapp->group_id, false, false) ){
			$microapp->sources = json_decode($microapp->sources);
			$microapp->options = json_decode($microapp->options);
			unset($microapp->group_id);
			return $microapp;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}


	public function destroy($id)
	{   
		$microapp = Microapp::find($id);
		if(validate::hasPermission(true, $microapp->group_id, false, false) ){
			$microapp->delete();
			return $microapp;
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