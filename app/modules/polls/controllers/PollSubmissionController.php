
<?php
class PollSubmissionController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		return PollSubmission::all();
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
		if(isset($post_data['choice']) && !is_null($post_data['choice'])){


			$pollSub = new PollSubmission();
			$pollSub->fill($post_data);
			$pollSub->pidm = Auth::user()->pidm;
			// $pollSub->last_updated_by = "me";
			if(PollSubmission::where('poll_id', '=', $pollSub->poll_id)->where('pidm', '=', Auth::user()->pidm)->count() == 0 ){
				$pollSub->save();
			}
			$poll = Poll::find($pollSub->poll_id);
			$results = [];
			$pollSub->total = 0;

			$pSubmits = JSON_decode($poll->content);
			foreach($pSubmits as $pSubmission){
				$results[$pSubmission->label] = PollSubmission::where('poll_id', '=', $pollSub->poll_id)->where('choice', '=', $pSubmission->label)->count();
				$pollSub->total += $results[$pSubmission->label];
			}
			$pollSub->results = $results;
			return $pollSub;
		}else{
			return "Failed";
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
		// $pollSub = Group::find($id);
		// return $pollSub;

		return PollSubmission::where('poll_id', '=', $id)->get();
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
		$pollSub = PollSubmission::find($id);
		$pollSub->fill($post_data);
		// $pollSub->last_updated_by = "me";
		$pollSub->save();
		return $pollSub;
	}

	public function remove($id)
	{ 
		PollSubmission::find($id)->delete();
	}
	public function results($id)
	{ 
		//The following is probably better but would need to be modified for non sql
		// $pSubmits = DB::table('poll_submissions')
	  //                ->select('choice', DB::raw('count(*) as data'))
	  //                ->groupBy('choice')
	  //                ->get();
		//$pSubmits = PollSubmission::where('poll_id', '=', $id)->select('choice')->groupby('choice')->count();//->distinct('choice')->get();
		//return $pSubmits;

		$poll = Poll::find($id);
		$results = [];
		$poll->total = 0;
		if(strlen($poll->content)){
			$pSubmits = JSON_decode($poll->content);
			foreach($pSubmits as $pSubmission){
				$results[$pSubmission->label] = PollSubmission::where('poll_id', '=', $id)->where('choice', '=', $pSubmission->label)->count();
				$poll->total += $results[$pSubmission->label];
			}
		}
		$poll->results = $results;
		return $poll;
	}

	public function lastresults()
	{ 
		$latestPolls;
		$count = 4;
		$inputs = Request::input();
		if(isset($inputs['count'])){$count = $inputs['count'];}

		if(isset($inputs['group_id'])){
			$latestPolls = Poll::orderBy('created_at','desc')->where('group_id', '=', $inputs['group_id'])->take($count)->get();
		}else{
			$latestPolls = Poll::orderBy('created_at','desc')->take($count)->get();
		}

		foreach($latestPolls as $poll){
			$results = [];
			$poll->total = 0;
			if(strlen($poll->content)){
				$pSubmits = JSON_decode($poll->content);
				foreach($pSubmits as $pSubmission){
					$results[$pSubmission->label] = PollSubmission::where('poll_id', '=', $poll->id)->where('choice', '=', $pSubmission->label)->count();
					$poll->total += $results[$pSubmission->label];
				}
			}
			$poll->results = $results;
		}
		return $latestPolls;
	}

}
?>