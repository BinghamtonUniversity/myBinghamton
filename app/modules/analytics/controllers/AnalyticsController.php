<?php
class AnalyticsController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		return View::make('analytics::analytics');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @return Response
	 */
	public function store()
	{
		$groups = Group::select('id')->get();
		$data = json_decode($groups);

		// Global variable to provide the time difference.
		$allowedtime = 15;
		// Date variables to test data for different dates.
		// $date1 = '2015-11-16';
		// $date2 = '2015-11-17';
		$date1 = $_GET['from'];
		$date2 = $_GET['to'];
		
		if($date2<=$date1) {
			return "To Date Should Be Greater Than From Date";
		}

		$fromDate = strtotime($date1);
		$toDate = strtotime($date2);
		
		while($fromDate < $toDate) {
			$date1 = date("Y-m-d",$fromDate);
			GroupVisitsPerDay::where('date', '=', $date1)->delete();
			PageVisitsPerDay::where('date', '=', $date1)->delete();
			PageVisitsByGroup::where('date', '=', $date1)->delete();
			$fromDate += (60 * 60 * 24);
			$date2 = date("Y-m-d",$fromDate);
			
			// Global Arrays for page.
			$global_time = array();
			foreach($groups as $g ) {
				 $num_sessions=0;
				 $avg_time = 0;

				 // Array to store the time of each Person
				 $arr_time = array();
				 // Array to store different Page ID.
				 $arr_page = array();

				 $visit = Visit::select('visits.pidm','visits.pageid','visits.created_at')
				 ->leftJoin('group_members','group_members.pidm','=','visits.pidm')
				 ->whereRaw("visits.created_at >= '$date1' and visits.created_at < '$date2' and group_members.group_id='$g[id]'")
				 ->orderBy('visits.pidm')
				 ->get();
				 
				 foreach($visit as $v) {
					// Condition to populate array of Time
				 	if(empty($arr_time[$v->pidm])) $arr_time[$v->pidm] = array();
				 	$arr_time[$v->pidm][] = $v->created_at;

				 	// Condition for getting Global Data (Storing PIDM as Key and (Page Id and Created_at) as Value)
				 	if(empty($global_time[$v->pidm])) $global_time[$v->pidm] = array();
				 	if(!in_array(array($v->pageid, $v->created_at), $global_time[$v->pidm])) {
				 		$global_time[$v->pidm][] = array($v->pageid, $v->created_at);
				 	}

				 	// Condition to populate array based on Page ID
				 	if(empty($arr_page[$v->pageid])) $arr_page[$v->pageid] = array();
				 	if(!in_array($v->pidm, $arr_page[$v->pageid])) {
				 		$arr_page[$v->pageid][] = $v->pidm;
				 	}
				 }

				 // Logic to calculate Values for Group Visits Per Day
				 foreach($arr_time as $ar) {
				 	$len = count($ar);
				 	if($len>0) {
				 		$num_sessions++;
				 		$avg_time += 1.0;
				 		for($i=0;$i<$len-1;$i++) {
				 			$to_time = strtotime($ar[$i]);
							$from_time = strtotime($ar[$i+1]);
							$diff = round(abs($to_time - $from_time) / 60,2);
							if($diff < $allowedtime) {
								$avg_time += $diff;
							}
							else {
								$num_sessions += 1;
								$avg_time += 1.0;
							}
				 		}
				 	}
				 }
				 if($num_sessions != 0) $avg_time = $avg_time / $num_sessions ;
				 $unique = count($arr_time);
				 $day=date("l", strtotime($date1));

				 // Saving the data to the database
				 $gd = new GroupVisitsPerDay();
				 $gd->group_id = $g['id'];
				 $gd->num_sessions = $num_sessions;
				 $gd->unique_visits = $unique;
				 $gd->avg_session_length = $avg_time;
				 $gd->date = $date1;
				 $gd->day = $day;
				 $gd->save();

				 foreach ($arr_page as $k => $v) {
					$pd = new PageVisitsByGroup();
					$pd->group_id = $g['id'];
					$pd->page_id = $k;
					$pd->unique_visits = count($v);
					$pd->date = $date1;
					$pd->save();
				 }

			// End Group Loop	 	
			}
			$count_pages = array();
			$total_pages_time = array();
			$num_bounces = array();
			foreach($global_time as $ar) {
				$len = count($ar);
				if($len>0) {
				 	for($i=0;$i<$len-1;$i++) {
				 		$to_time = strtotime($ar[$i][1]);
						$from_time = strtotime($ar[$i+1][1]);
						$diff = round(abs($to_time - $from_time) / 60,2);
						if(empty($count_pages[$ar[$i][0]])) $count_pages[$ar[$i][0]] = 1; 
						else $count_pages[$ar[$i][0]]++;
						if($diff < $allowedtime) {
							if(empty($total_pages_time[$ar[$i][0]])) $total_pages_time[$ar[$i][0]] = 0.0; 
							$total_pages_time[$ar[$i][0]] = $diff;
						}
						else {
							if(empty($num_bounces[$ar[$i][0]])) $num_bounces[$ar[$i][0]] = 1;
							else $num_bounces[$ar[$i][0]]++;
							if(empty($total_pages_time[$ar[$i][0]])) $total_pages_time[$ar[$i][0]] = 1.0; 
							else $total_pages_time[$ar[$i][0]] += 1.0;
						}
				 	}
				 	if(empty($num_bounces[$ar[$len-1][0]])) $num_bounces[$ar[$len-1][0]] = 1;
					else $num_bounces[$ar[$len-1][0]]++;
					if(empty($count_pages[$ar[$len-1][0]])) $count_pages[$ar[$len-1][0]] = 1; 
					else $count_pages[$ar[$len-1][0]]++;
					if(empty($total_pages_time[$ar[$len-1][0]])) $total_pages_time[$ar[$len-1][0]] = 1.0; 
					else $total_pages_time[$ar[$len-1][0]] += 1.0;
				}
			}
			foreach($count_pages as $k => $v) {
				$bounce = 0;
				$avg_time = 1;

				// Calculate Bounces if available in the bounce array
				if(!empty($num_bounces[$k])) $bounce = $num_bounces[$k];

				// Divide the total time spent with total views 
				if(!empty($total_pages_time[$k])) $avg_time = $total_pages_time[$k] / $v;
				
				// Convert the date to Day
				$day=date("l", strtotime($date1));

				// Save to the database
				$pv = new PageVisitsPerDay();
				$pv->page_id = $k;
				$pv->num_visits = $v;
				$pv->num_bounces =  $bounce;
				$pv->avg_time = $avg_time;
				$pv->date = $date1;
				$pv->day = $day;
				$pv->save();
			}
		}
		return "Executed";
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

	// Can take upto 4 parameters (Date is mandatory)
	// @param1 GroupID
	// @param2 Date
	// @param3 DateTo
	// @param4 DateFrom
	// Parameter Combinations - GroupID and Date || GroupID and (DateTo till DateFrom) || Date || DateTo till DateFrom
	// Dates are Inclusive
	public function group_visits_per_day() {
		$input = Input::all();
		if(empty($input['gid']) && empty($input['date']) && empty($input['dateTo']) && empty($input['DateFrom'])) return "Error";
		if(!empty($input['gid'])){
			$gid = $input['gid'];
			$gid = explode(",", $gid);
		}
		else $gid = array(); 
		if(empty($input['date'])) {
			if(!empty($input['dateTo']) && !empty($input['dateFrom'])) {	
				$dateTo = $input['dateTo'];
				$dateFrom = $input['dateFrom'];	
				if(!empty($gid)) {	
					$visit = GroupVisitsPerDay::select('group_id', 'num_sessions','unique_visits','avg_session_length','day', 'groups.name as GroupName')
						->leftJoin('groups','group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->whereIn('group_id', $gid)	
			 			->get();
					return $visit;	
				}
				else {
					$visit = GroupVisitsPerDay::select('group_id', 'num_sessions','unique_visits','avg_session_length','day', 'groups.name as GroupName')
						->leftJoin('groups','group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->get();
					return $visit;
				}
			}
			else return "Error";
		}
		else {
			$date = $input['date'];
			if(!empty($gid)) {
				$visit = GroupVisitsPerDay::select('group_id', 'num_sessions','unique_visits','avg_session_length','day', 'groups.name as GroupName')
					->leftJoin('groups','group_id','=','groups.id')
			 		->whereRaw("date = '$date'")
			 		->whereIn('group_id', $gid)
			 		->get();
				return $visit;
			}
			else {
				$visit = GroupVisitsPerDay::select('group_id', 'num_sessions','unique_visits','avg_session_length','day', 'groups.name as GroupName')
					->leftJoin('groups','group_id','=','groups.id')
			 		->whereRaw("date = '$date'")
			 		->get();
				return $visit;
			}
		}
		return "Error";
	}

	// Can take upto 5 parameters (Date is mandatory)
	// @param1 GroupID
	// @param2 PageID
	// @param3 Date
	// @param4 DateTo
	// @param5 DateFrom
	// Parameter Combinations - GroupID/PageID and Date || GroupID/PageID and (DateTo till DateFrom) || Date
	// Dates are Inclusive
	
	public function page_visits_per_group() {
		$input = Input::all();
		if(empty($input['pid']) && empty($input['gid']) && empty($input['date']) && empty($input['dateTo']) && empty($input['DateFrom'])) return "Error";
		if(!empty($input['pid'])){
			$pid = $input['pid'];
			$pid = explode(",", $pid);
		}
		else $pid = array();
		if(!empty($input['gid'])){
			$gid = $input['gid'];
			$gid = explode(",", $gid);
		}
		else $gid = array(); 
		if(empty($input['date'])) {
			if(!empty($input['dateTo']) && !empty($input['dateFrom'])) {	
				$dateTo = $input['dateTo'];
				$dateFrom = $input['dateFrom'];	
				if(!empty($pid) && !empty($gid)) {	
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->whereIn('page_id', $pid)
			 			->whereIn('page_visits_by_groups.group_id', $gid)
			 			->whereIn('community_pages.group_id', $gid)	
			 			->get();
					return $visit;	
				}
				else if(!empty($pid)) {
						$visit = PageVisitsByGroup::select('page_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->whereIn('page_id', $pid)
			 				->get();
						return $visit;
					}
				else if(!empty($gid)) {
						$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->whereIn('group_id', $gid)
			 				->whereIn('community_pages.group_id', $gid)
			 				->get();
						return $visit;	
					}
				else {
					$visit = PageVisitsByGroup::select('page_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->get();
						return $visit;
				}
			}
			else return "Error";
		}
		else {
			$date = $input['date'];
			if(!empty($pid) && !empty($gid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_id', $pid)
			 			->whereIn('page_visits_by_groups.group_id', $gid)
			 			->whereIn('community_pages.group_id', $gid)
			 			->get();
					return $visit;
			}
			else if(!empty($pid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_id', $pid)
			 			->get();
					return $visit;
				}
			else if(!empty($gid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_visits_by_groups.group_id', $gid)
			 			->whereIn('community_pages.group_id', $gid)
			 			->get();
					return $visit;	
				}
			else {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->get();
					return $visit;
				}
				return "Error";
			}
		return "Error";
	}

	// Can take upto 4 parameters (Date is mandatory)
	// @param1 PageID
	// @param2 Date
	// @param3 DateTo
	// @param4 DateFrom
	// Parameter Combinations - PageID and Date || PageID and (DateTo till DateFrom) || Date || DateTo till DateFrom
	// Dates are Inclusive
	
	public function page_visits_per_day() {
		$input = Input::all();
		if(empty($input['pid']) && empty($input['date']) && empty($input['dateTo']) && empty($input['DateFrom'])) return "Error";
		if(!empty($input['pid'])){
			$pid = $input['pid'];
			$pid = explode(",", $pid);
		}
		else $pid = array(); 
		if(empty($input['date'])) {
			if(!empty($input['dateTo']) && !empty($input['dateFrom'])) {	
				$dateTo = $input['dateTo'];
				$dateFrom = $input['dateFrom'];	
				if(!empty($pid)) {	
					$visit = PageVisitsPerDay::select('page_id','num_visits','num_bounces','avg_time','day','community_pages.name as PageName', 'groups.name as GroupName')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','community_pages.group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->whereIn('page_id', $pid)	
			 			->get();
					return $visit;	
				}
				else {
					$visit = PageVisitsPerDay::select('page_id','num_visits','num_bounces','avg_time','day','community_pages.name as PageName', 'groups.name as GroupName')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','community_pages.group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->get();
					return $visit;
				}
			}
			else return "Error";
		}
		else {
			$date = $input['date'];
			if(!empty($pid)) {
				$visit = PageVisitsPerDay::select('page_id','num_visits','num_bounces','avg_time','day','community_pages.name as PageName', 'groups.name as GroupName')
					->leftJoin('community_pages','community_pages.id','=','page_id')
					->leftJoin('groups','community_pages.group_id','=','groups.id')
			 		->whereRaw("date = '$date'")
			 		->whereIn('page_id', $pid)
			 		->get();
				return $visit;
			}
			else {
				$visit = PageVisitsPerDay::select('page_id','num_visits','num_bounces','avg_time','day','community_pages.name as PageName', 'groups.name as GroupName')
					->leftJoin('community_pages','community_pages.id','=','page_id')
					->leftJoin('groups','community_pages.group_id','=','groups.id')
			 		->whereRaw("date = '$date'")
			 		->get();
				return $visit;
			}
		}
		return "Error";
	}

	// Can take upto 5 parameters (Date is mandatory)
	// @param1 GroupID
	// @param2 PageID
	// @param3 Date
	// @param4 DateTo
	// @param5 DateFrom
	// Parameter Combinations - GroupID/PageID and Date || GroupID/PageID and (DateTo till DateFrom) || Date
	// Dates are Inclusive
	
	public function page_visits_per_group_all() {
		$input = Input::all();
		if(empty($input['pid']) && empty($input['gid']) && empty($input['date']) && empty($input['dateTo']) && empty($input['DateFrom'])) return "Error";
		if(!empty($input['pid'])){
			$pid = $input['pid'];
			$pid = explode(",", $pid);
		}
		else $pid = array();
		if(!empty($input['gid'])){
			$gid = $input['gid'];
			$gid = explode(",", $gid);
		}
		else $gid = array(); 
		if(empty($input['date'])) {
			if(!empty($input['dateTo']) && !empty($input['dateFrom'])) {	
				$dateTo = $input['dateTo'];
				$dateFrom = $input['dateFrom'];	
				if(!empty($pid) && !empty($gid)) {	
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 			->whereIn('page_id', $pid)
			 			->whereIn('page_visits_by_groups.group_id', $gid)	
			 			->get();
					return $visit;	
				}
				else if(!empty($pid)) {
						$visit = PageVisitsByGroup::select('page_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->whereIn('page_id', $pid)
			 				->get();
						return $visit;
					}
				else if(!empty($gid)) {
						$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->whereIn('group_id', $gid)
			 				->get();
						return $visit;	
					}
				else {
					$visit = PageVisitsByGroup::select('page_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
							->leftJoin('community_pages','community_pages.id','=','page_id')
							->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 				->whereRaw("(date >= '$dateTo' and date <= '$dateFrom')")
			 				->get();
						return $visit;
				}
			}
			else return "Error";
		}
		else {
			$date = $input['date'];
			if(!empty($pid) && !empty($gid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_id', $pid)
			 			->whereIn('page_visits_by_groups.group_id', $gid)
			 			->get();
					return $visit;
			}
			else if(!empty($pid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_id', $pid)
			 			->get();
					return $visit;
				}
			else if(!empty($gid)) {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->whereIn('page_visits_by_groups.group_id', $gid)
			 			->get();
					return $visit;	
				}
			else {
					$visit = PageVisitsByGroup::select('page_id','page_visits_by_groups.group_id','unique_visits','community_pages.name as PageName', 'groups.name as GroupName','date')
						->leftJoin('community_pages','community_pages.id','=','page_id')
						->leftJoin('groups','page_visits_by_groups.group_id','=','groups.id')
			 			->whereRaw("date = '$date'")
			 			->get();
					return $visit;
				}
				return "Error";
			}
		return "Error";
	}



}
?>