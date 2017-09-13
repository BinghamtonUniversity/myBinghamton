<?php
class VisitsController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		return Visit::all();
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function store()
	{
		session_write_close();
		$post_data = Input::all();
		$visit = new Visit();
		$visit->pidm = Auth::user()->pidm;
		$visit->path = $post_data['path'];
		$visit->width = $post_data['width'];
		$visit->referrer = $post_data['referrer'];
		$visit->pageid = $post_data['id'];
		$visit->save();
		return $visit;
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

	}

}
?>