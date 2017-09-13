<?php
class AnalyticsGroupController extends BaseController {
	/**
	 * Display the specified resource.
	 * GET /images
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index()
	{
		return View::make('analytics::analytics_group');
	}

	public function get_data($group_id) {

//							Session::put('groups', array_merge($myGroups, GroupComposite::whereIn('composite_id',  array_merge($myGroups, Session::get('owned') ))->lists('group_id')));

		// $group = Group::with('composites')->find($_GET['group']);
		$group = GroupComposite::where('group_id',  '=', $group_id)->lists('composite_id');


		$group[] = (int)$group_id;
		// dd(json_encode($group));
		$members = Groupmember::with(array('user'=>function($query){
			$query->select('pidm', 'first_name', 'last_name', 'bnum', 'email');
		}) )->whereIn('group_id', $group)->get();


		// $members = Groupmember::whereIn('group_id', $group)->toSql();

		// dd($members);

		$pages = CommunityPage::where('group_id', '=', $group_id)->select('id','group_id','name','slug')->get();

		if(!isset($_GET['start'])){ 
			$_GET['start'] = date("Y/m/d");
		}else{
			$dt = new DateTime($_GET['start']);
			$_GET['start'] = $dt->format('Y-m-d');
		}
		if(!isset($_GET['end'])){ 
			$_GET['end'] =  date("Y/m/d");
		}else{
			$dt = new DateTime($_GET['end']);
			if($dt > new DateTime()){
				$dt = new DateTime();
			}
			$_GET['end'] = $dt->format('Y-m-d');
		}
		
		// dd(json_encode($pages));
		
		$visits = Visit::whereIn('pageid', $pages->lists('id'))->createdBetween($_GET['start'],$_GET['end'])->get();

// dd($visits);
		$usersPages = array();
		$visitsPerUser = array();
		$pagesArray = array();
		foreach($pages as $key => $value) {
			$pagesArray[$value->id] = $value->name;
		}
		foreach($members as $key => $value) {
			$visitsPerUser[$value->pidm] = array();
			$usersPages[$value->pidm]['bnum'] = $value->user->bnum;
			$usersPages[$value->pidm]['first_name'] = $value->user->first_name;
			$usersPages[$value->pidm]['last_name'] = $value->user->last_name;
			$usersPages[$value->pidm]['email'] = $value->user->email;

			foreach($pages as $pkey => $pvalue) {
					//jayme fix$usersPages['521605'][$pvalue->name] = 0;
				$usersPages[$value->pidm][$pvalue->name] = 0;
			}
		}
			//jayme fix$visitsPerUser['521605'] = array();

		foreach($visits as $vkey => $vvalue) {
			$temp2 = (array) $vvalue->created_at;
			$temp['time'] = $temp2['date'];
			$temp['page'] = $vvalue->pageid;
			if(isset($visitsPerUser[$vvalue->pidm])) {
				array_push($visitsPerUser[$vvalue->pidm], $temp);
			}
		}
		$sessionendtime = 60;
		$interval = new DateInterval('P0Y0DT0H15M');
		$secondsInterval = $interval->days*86400 + $interval->h*3600 + $interval->i*60 + $interval->s;
		foreach($visitsPerUser as $key => $value) {
			$count = count($visitsPerUser[$key]) - 1;
			$i = 0;
			while($i < $count) {
				$pageid = $visitsPerUser[$key][$i]['page'];
				$page = $pagesArray[$pageid];
				$time1 = new DateTime($visitsPerUser[$key][$i+1]['time']);
				$time2 = new DateTime($visitsPerUser[$key][$i]['time']);
				$dif = $time2->diff($time1);
				$secondsDif = $dif->days*86400 + $dif->h*3600 + $dif->i*60 + $dif->s;	
				if($secondsInterval < $secondsDif) {
					$usersPages[$key][$page] += $sessionendtime;
				} else {
					$usersPages[$key][$page] += $secondsDif;
				}
				//echo $secondsInterval . "   :   " . $secondsDif;
				//echo "<br>";
				$i++;
			}
			if($i == $count) {
				$pageid = $visitsPerUser[$key][$i]['page'];
				$page = $pagesArray[$pageid];
				$usersPages[$key][$page] += $sessionendtime;
			}
		
		}
		$array2 = $usersPages;
		$array = array();
		
		foreach($array2 as $key => $value) {
			$temp = array();
			$temp['pidm'] = $key;
			foreach($value as $key2 => $value2) {
				$temp[$key2] = $value2;
			}
			array_push($array, $temp);
		}

		$dt = new DateTime();
		$myGroup = Group::select('slug')->find($group_id);


//		group_datetime_traffic.csv
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$myGroup->slug."_".$dt->format('Y_m_d_His')."_traffic.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		$csv = fopen("php://output", 'w');
		fputcsv($csv, array_keys($array[0]));
		foreach ($array as $row) {
		   fputcsv($csv, $row);
		}
		fclose($csv);
		//echo $csv;
				
		//	return $usersPages;
	}

}
?>