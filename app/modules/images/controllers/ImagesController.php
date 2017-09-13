<?php
class ImagesController extends BaseController {
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
			validate::isAdmin($_GET['group_id']);
			 return Image::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->get();
		}
		if(isset($_GET['group'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group']) ){
				return Group::with('images')->where('id', '=', $_GET['group'])->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('images')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
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
		$image = new Image();
		$image->fill($post_data);
		if(Input::hasFile('image_filename')) {
			$file = Input::file('image_filename');

			$image->name = $file->getClientOriginalName();
			$image->ext = $file->getClientOriginalExtension();

			if(Config::get('app.s3'))	{
				$s3 = AWS::get('s3');
				$result = $s3->putObject(array(
				    'Bucket'     => Config::get('app.s3_bucket'),
				    'Key'        => '/assets/'.$image->name,
				    'SourceFile' => $file->getRealPath()
				));
				$image->image_filename = $result['ObjectURL'];

			}else{
				$file->move(public_path() . '/imgs/',$file->getClientOriginalName());
				$image->name = $file->getClientOriginalName();
				$image->ext = $file->getClientOriginalExtension();
				$image->image_filename = '/imgs/'.$image->name;
			}

		}

		$image->save();
		return $image;
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
		$image = Image::find($id);
		return $image;
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
		$image = Image::find($id);
		$image->fill($post_data);
		if(Input::hasFile('image_filename')) {
			$file = Input::file('image_filename');
			$file->move(public_path() . '/imgs/',$file->getClientOriginalName());
			$image->image_filename = $file->getClientOriginalName();
		}
		$image->save();
		return $image;
	}

	public function destroy($id)
	{   
		$image = Image::find($id);

		if(Config::get('app.s3'))	{
			if(Config::get('app.s3'))	{
				$s3 = AWS::get('s3');
				if($s3->deleteObject(array(
				    'Bucket'     => Config::get('app.s3_bucket'),
				    'Key'        => '/assets/'.$image->name,
				))) {
					$image->delete();
				}
			}
		}else{
			// try{
			if(file_exists (public_path() . '/imgs/' . $image->name)){
				if(unlink(public_path() . '/imgs/' . $image->name) ) {
					$image->delete();
				}
			}else{
				$image->delete();
			}
			// }catch(Exception $e){}		

		}

		return $image;
	}
}
?>