<?php
class PollController extends BaseController {
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
			return Poll::where('group_id', '=', $_GET['group_id'])->orWhere('group_id', '=', Config::get('app.global_group'))->get();
		}
		if(isset($_GET['group'])){
			if(validate::isSuper() || validate::isAdmin($_GET['group']) ){
				return Group::with('polls')->where('id', '=', $_GET['group'])->select('id', 'name')->get();
			}else{
				return Response::make(View::make('unauthorized'), 401);
			}
		}
		return Group::with('polls')->myGroups()->isCommunity()->ordered()->select('id', 'name')->get();
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
		$poll = new Poll();
		$poll->fill($post_data);
		if(validate::hasPermission(true, $poll->group_id, false, false) ){

			$poll->save();
			return $poll;
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
	public function show($id)
	{			
		$poll = Poll::find($id);
		if(validate::hasPermission(true, $poll->group_id, false, false) ){
			return $poll;
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
	public function live($id)
	{
		session_write_close();
		$poll = Poll::find($id);
		if(PollSubmission::where('pidm', '=', Auth::user()->pidm)->where('poll_id', '=', $id)->count() > 0){
			$results = [];
			$pSubmits = JSON_decode($poll->content);
			$poll->total = 0;
			foreach($pSubmits as $pSubmission){
				$results[$pSubmission->label] = PollSubmission::where('poll_id', '=', $id)->where('choice', '=', $pSubmission->label)->count();
				$poll->total += $results[$pSubmission->label];
			}
			$poll->results = $results;
		}
		return $poll;
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
		$poll = Poll::find($id);
		if(validate::hasPermission(true, $poll->group_id, false, false) ){
			$poll->fill($post_data);
			$poll->save();
			return $poll;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

	public function destroy($id)
	{   
		$poll = Poll::find($id);
		if(validate::hasPermission(true, $poll->group_id, false, false) ){
			$poll->delete();
			return $poll;
		}else{	
			return Response::make(View::make('unauthorized'), 401);
		}
	}

}
?>