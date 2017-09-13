
<?php
class GroupKeyController extends BaseController {
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
			return GroupKey::where('group_id', '=', $_GET['group'])->get();
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
		$key = new GroupKey();
		$key->fill($post_data);
		$key->save();
		return $key;
	}

	// /**
	//  * Update the specified resource in storage.
	//  * POST /apps/{id}
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	public function update($id)
	{
		$post_data = Input::all();

		$key = GroupKey::firstOrNew(array('id' => $id));

		$key->fill($post_data);
		$key->save();
		return $key;
	}

	public function destroy($id)
	{ 		

		$group = Group::find($id);

		GroupKey::where("id", "=", $id)->delete();
	}

	public function my(){
		$tags = GroupKey::whereIn('group_id', Session::get('groups'))->select('name', 'value')->get();
		$returnable_tags = array();
		foreach($tags as $tag){
			if(!isset($returnable_tags[$tag['name']]) ){
				$returnable_tags[$tag['name']] = [];
			}
			$returnable_tags[$tag['name']][] = $tag['value'];
		}
		return $returnable_tags;
	}

}
?>