
<?php
class GroupCompositeController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		if(isset($_GET['list'])) {
			return Group::get();
		}

		// $myGroup = Group::with(array('composites'=>function($query){
		// 	$query->with('composite');
		// }))->find($_GET['group']);

		return GroupComposite::with('composite')->where('group_id', '=', $_GET['group'])->get();
	}

	public function show($group_id)
	{
		if(isset($_GET['list'])){
			return Group::get();
		}

		// $myGroup = Group::with(array('composites'=>function($query){
		// 	$query->with('composite');
		// }))->find($_GET['group']);

		// return GroupComposite::with('composite')->where('group_id', '=', $group_id)->get();


		$composite_groups= GroupComposite::with(array('composite'=>function($query){
			$query->select('id', 'name');
		}))->where('group_id', '=' ,$group_id)->get();
		return $composite_groups->lists('composite');
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

		$composite = GroupComposite::firstOrNew(array('group_id' => $post_data['group_id'], 'composite_id' => $post_data['composite_id']));

		$composite->fill($post_data);
		$composite->save();
		$composite = GroupComposite::with('composite')->where("group_id", "=", $post_data['group_id'])->where("composite_id", "=", $post_data['composite_id'])->first();

		return $composite;
	}



	public function remove($group_id, $composite_id)
	{ 		
		GroupComposite::where("group_id", "=", $group_id)->where("composite_id", "=", $composite_id)->delete();
	}


}
?>