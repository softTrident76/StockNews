<?php

require_once "lib/WhkData.php";
require_once 'log.php';
// require_once 'infusionsoft-php-sdk/Infusionsoft/infusionsoft.php';

$path = __DIR__ .'/../infusionsoft-sdk/Infusionsoft/';
require_once($path.'infusionsoft.php');
require_once($path.'config.php');

$logDatabase = new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logDatabase, 15);

class WhkEventManager
{	
	/**
	 * configuration for database
	 */
	// private $username = "root";
	// private $password = "admin";
	// private $hostname = "localhost";
	// private $dabasename = "sparkpostwebhook";

	private $username = '';
	private $password = '';
	private $hostname = '';
	private $dabasename = '';

	private $dbconn = null;

	/**
	 * enumarate for event
	 */
	public $event_list =  array(
								'bounce' => '0',
								'delivery' => '1',
								'injection' => '2',
								'spam_complaint' => '3',
								'out_of_band' => '4',
								'policy_rejection' => '5',
								'delay' => '6',
								'click' => '7',
								'open' => '8',
								'initial_open' => '9',
								'amp_click' => '10',
								'amp_open' => '11',
								'amp_initial_open'	 => '12',
								'generation_failure' => '13',
								'generation_rejection'	 => '14',
								'list_unsubscribe' => '15',
								'link_unsubscribe'	 => '16',
								'relay_injection' => '17',
								'relay_rejection' => '18',
								'relay_delivery' => '19',
								'relay_tempfail' => '20',
								'relay_permfail' => '21',
								);
	
	public function __construct()
	{
		//connection to the database
		require 'define.php';
		$this->username = $_DB_CONNECTOR->username;
		$this->password = $_DB_CONNECTOR->password;
		$this->hostname = $_DB_CONNECTOR->hostname;
		$this->dabasename = $_DB_CONNECTOR->dabasename;

		$this->dbconn = mysqli_connect($this->hostname, $this->username, $this->password, $this->dabasename);
		if (!$this->dbconn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		// echo "Connected successfully";
	}	
	
	public function __destruct()
	{	
		mysqli_close($this->dbconn);
	}

	/**
	 * interface from webhook callback
	 * save onto database
	 * @param [indataStr]: object json_decoded from callback-request
	 */
	public function SetIndata($indataStr) 
	{
		// Log::INFO('----SetIndata-----');
		// choose which type of event //
		$inDataObj = $indataStr['msys'];
		if( !isset($inDataObj) || $inDataObj == null)
		{
			Log::ERROR('	indataStr not include msys property');
			return;
		}			

		// create event object suitable for its template //
		$event = new WhkData();

		// save data into storage //
		if($event)
			$event->SaveToDatabase($this->dbconn, $inDataObj);
	}

	/**
	 * get detailed information for focused campaign, it's main search moudule.
	 * @param [campagin_id]: focused campagin's id, never empty
	 * @param [request_type]: comprehensive information if empty,  each type not if, and here $request_type matches each table name 'tbl_' + request_type
	 * @param [target_link_url]: only when request_type = click
	 */
	public function GetDetail($campagin_id, $page_type = "", $request_type = "", $target_link_url = "") 
	{			
		$event = new WhkData();
		if( $request_type == '')
			return $event->GetCampaignDetail($this->dbconn, $campagin_id, $page_type);

		// 'bounce', 'delivery', 'injection', 'out_fo_band','policy_rejection', 'delay', 'gross_opens', 'unique_opens', 'click_through'
		switch ($request_type)
		{
			case 'bounce':
				return $event->GetBounceDetailList($this->dbconn, $campagin_id);
			break;

			case 'delivery':
				return $event->GetDeliveryList($this->dbconn, $campagin_id);
			break;

			case 'injection':
				return $event->GetInjectionList($this->dbconn, $campagin_id);
			break;

			case 'out_fo_band':
				return $event->GetOutOfBandList($this->dbconn, $campagin_id);
			break;

			case 'policy_rejection':
				return $event->GetPolicyRejectionList($this->dbconn, $campagin_id);
			break;

			case 'delay':
				return $event->GetDelayList($this->dbconn, $campagin_id);
			break;

			case 'gross_opens':
				return $event->GetGrossOpensList($this->dbconn, $campagin_id);
			break;

			case 'unique_opens':
				return $event->GetUniqueOpensList($this->dbconn, $campagin_id);
			break;

			case 'click_gross_through':
				return $event->GetClickGrossThroughList($this->dbconn, $campagin_id, $target_link_url);
			break;

			case 'click_unique_through':
				return $event->GetClickUniqueThroughList($this->dbconn, $campagin_id, $target_link_url);
			break;

			case 'link_unsubscribe':
				return $event->GetLinkUnsubscribeList($this->dbconn, $campagin_id);
			break;

			case 'list_unsubscribe':
				return $event->GetListUnsubscribeList($this->dbconn, $campagin_id);
			break;
		}
		
	}

	public function ToParamString($indataObj)
	{
		$buff = "";
		foreach ($indataObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = rtrim($buff, "&");
		return $buff;
	}

	public function GetCampaignCounts($cond = null, $from = null, $to = null)
	{		
		$sql = "SELECT count(*) as count FROM tbl_campaign WHERE 1=1 ";

		$filter = '';
		if($cond != null && count($cond) > 0)
		{
			foreach($cond as $key=>$value) 
			{
				$filter .= ($key . " LIKE '%" . $value. "%'");
				$filter .= "and";
			}
			$filter = " and " . rtrim($filter, "and");
		}

		if($from != null && $from != '')
			$filter .= ( " and injection_time > '" . $from . "'" );

		if($to != null && $to != '')
			$filter .= ( " and injection_time < '" . $to . "'");

		$sql = $sql . $filter;

		// echo "<br>";
		// echo $sql;
		Log::INFO($sql . ' line:193 in WhkEventManager::GetCampaignCounts ');

		$result = mysqli_query($this->dbconn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				return $row['count'];
			}
		}
		return 0;
	}

	
	public function GetMailEvent($rcpt_to, $friendly_from, $subject, $campaign_id, $type, $datetime)
	{
		date_default_timezone_set("UTC");

		$ret = array();
		foreach ($this->event_list as $key=>$value)
		{
			switch($key)
			{
				case 'policy_rejection': 
				case 'generation_failure':
						// no subject field
						$sql = "SELECT * FROM tbl_" . $key . " WHERE " .
								"rcpt_to = '" . $rcpt_to . "' and " .
								"friendly_from like '%" . $friendly_from . "%' and " . 						
								"campaign_id like '%" . $campaign_id . "%' and " . 
								"type like '%" . $type . "%'";
				break;

				case 'relay_injection':				
				case 'relay_rejection':
				case 'relay_delivery':
				case 'relay_tempfail':
				case 'relay_permfail':			
						// no subject, no friend_from, no campaign	
						$sql = "SELECT * FROM tbl_" . $key . " WHERE " .
								"rcpt_to = '" . $rcpt_to . "' and " .								
								"type like '%" . $type . "%'";
				break;

				default:						
						$sql = "SELECT * FROM tbl_" . $key . " WHERE " .
								"rcpt_to = '" . $rcpt_to . "' and " .
								"friendly_from like '%" . $friendly_from . "%' and " . 
								"subject like '%" . $subject . "%' and " . 
								"campaign_id like '%" . $campaign_id . "%' and " . 
								"type like '%" . $type . "%'";
				break;
			}	
					// "type = '" . $type . "' and " . 
					// "timestamp like '%" . $datetime . "%' and" ;
			
			// echo $sql . '<br>';

			$result = mysqli_query($this->dbconn, $sql);
			if (($count = mysqli_num_rows($result)) > 0)
			{
				// output data of each row
				while($row = mysqli_fetch_assoc($result)) {
					array_push($ret, (object)$row);
				}
			}

			// sorting by timestamp //
			// for( $i = 0; $i < count($ret); $i++ )			
		}
		return $ret;
	}

	public function UpdateRecipients($tags)
	{		
		require 'define.php';
		
		// $appName = $_INFUSIONSOFT_APPNAME;
		// $apiKey = $_INFUSIONSOFT_APIKEY;
		// $app = new Infusionsoft_App($appName, $apiKey);

		// //Add the Infusionsoft App to the AppPool class
		// Infusionsoft_AppPool::addApp($app);

		$tagArray = array();
		foreach($tags as $key => $val) {
			array_push($tagArray, $key);
		}

		$people = array();
		// foreach($tags as $key => $val) {
		// 	$object = new stdClass();
   		// 	$object->Groups = $key;
		// 	$groups = array_push($groups, $object);
		// }
		
		$people = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Id' => '%'), $limit = 1000, $page = 0, array('Groups', 'Email', 'FirstName', 'LastName'));
		$this->get_RecursiveAll($people, $people, 1);

		// $people = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => '253'),  $limit = 1000, $page = 0, array('Groups', 'Email', 'FirstName', 'LastName'));		
		// $people = array_merge($people, $recipient);

		$ret = array();
		if( count($people) <= 0 )
			return $ret;
				
		// update entire database //
		$sql = "TRUNCATE TABLE tbl_recipient";
		$result = mysqli_query($this->dbconn, $sql);

		$first = $people[0]->toArray();
		$sql = 'INSERT INTO tbl_recipient (';
		foreach($first as $key => $value)
		{
			$sql .= strtolower($key);
			$sql .= ',';
		}
		$sql = rtrim($sql, ',') . ') VALUES ';		

		foreach($people as $individual)
		{
			$groups = array();
			$inds = $individual->toArray();
			$val = '(';
			foreach($inds as $key => $value)
			{
				$val .= '"' . strtolower($value) . '"';
				$val .= ',';

				if( $key == 'Groups')				
					$groups = explode(",", $value);					
			}

			$val = rtrim($val, ',') . ')';		
			$diff = array_diff($tagArray, $groups);
	
			if(count($diff) != count($tagArray))
				array_push($ret, $sql.$val);
		}

		// var_dump($ret);

		// $sql = rtrim($sql, ',');
		// echo $sql;

		// // // store to database //
		// // $result = mysqli_query($this->dbconn, $sql);
		// // Log::INFO(' line:280 in WhkEventManager::UpdateRecipients ' . count($people));
		
		// echo '<br>';
		// echo count($people);
		return $ret;
	}
	
	public function GetRecipients($option, $days, $campaginid)
	{
		date_default_timezone_set("UTC");
		$daysago = time() - ((int)($days) * 24 * 60 * 60);
		switch($option)
		{
			case 'open':
				// $sql = "SELECT * FROM (SELECT ip_address, rcpt_to, type, (SELECT count(*) FROM tbl_open b WHERE b.rcpt_to = a. rcpt_to and b.timestamp > " . $daysago . ") as count_of_campaign  FROM tbl_open a GROUP BY rcpt_to) as c  WHERE c.count_of_campaign > 0 ";
				$sql = "SELECT ip_address, rcpt_to, campaign_id, type, rcpt_meta, COUNT(rcpt_to) as count_of_campaign 
						from tbl_open 
						WHERE timestamp > " . $daysago . " AND campaign_id like '%" . $campaginid . "%' " .
						" GROUP BY rcpt_to ";
			break;

			case 'click':
				// $sql = "SELECT * FROM (SELECT ip_address, rcpt_to, type, (SELECT count(*) FROM tbl_click b WHERE b.rcpt_to = a. rcpt_to and b.timestamp > " . $daysago . ") as count_of_campaign  FROM tbl_click a GROUP BY rcpt_to) as c  WHERE c.count_of_campaign > 0 ";
				$sql = "SELECT ip_address, rcpt_to, campaign_id, type, rcpt_meta, COUNT(rcpt_to) as count_of_campaign 
						from tbl_click 
						WHERE timestamp > " . $daysago . " AND campaign_id like '%" . $campaginid . "%' " .
						" GROUP BY rcpt_to ";
			break;
			
			case 'no_open':
				$sql = "SELECT ip_address, rcpt_to, campaign_id, type, rcpt_meta, COUNT(rcpt_to) as count_of_campaign 
						from tbl_open 
						WHERE timestamp > " . $daysago . " AND campaign_id like '%" . $campaginid . "%' " .
						" GROUP BY rcpt_to ";
			break;

			case 'no_click':
				$sql = "SELECT ip_address, rcpt_to, campaign_id, type, rcpt_meta, COUNT(rcpt_to) as count_of_campaign 
						from tbl_click 
						WHERE timestamp > " . $daysago . " AND campaign_id like '%" . $campaginid . "%' " .
						" GROUP BY rcpt_to ";
			break;

			default:
				return;
		}

		// echo $sql . '<br>';
		Log::INFO($sql . ' line:222 in WhkEventManager::GetRecipients ');
	
		$ret = array();
		$result = mysqli_query($this->dbconn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($ret, (object)$row);
				// if((int)$row['count_of_campaign'] > 0 )
				// 	array_push($ret, (object)$row);
				//echo "id: " . $row["id"]. " - campaign_id: " . $row["campaign_id"]. " " . $row["group_type"]. "<br	>";
			}
		}

		// echo "<br>" . $count . " results";
		if($option == 'click' || $option =='open') 
		{
			return $ret;
		}
		else
		{
			$no_answers = array();

			// get transmission history //			
			$sql = "SELECT email as rcpt_to FROM tbl_transmission
					WHERE timestamp > " . $daysago . " AND campaign_id like '%" . $campaginid . "%' GROUP BY email ";

			$result = mysqli_query($this->dbconn, $sql);
			if( ($count = mysqli_num_rows($result)) <= 0)
				return $no_answers;
			
			$res = array();
			while($row = mysqli_fetch_assoc($result)) {
				array_push($res, (object)$row);
			}

			// echo count($ret) . ' => opened <br>';			
			// filter no_open or no_click recipient //		
			// search items to filter not in event, even though in transmission	//
			foreach($res as $each ) {
				if( !$this->findInAnsweredList($each->rcpt_to, $ret))
				{
					$item = new stdClass();
					$item->type = $option;
					$item->rcpt_to = $each->rcpt_to;
					$item->campaign_id = $campaginid;
					$item->ip_address = '';
					$item->count_of_campaign = '0';

					array_push($no_answers, $item);
				}
			}
			
			// echo count($no_answers) . ' => no opened <br>';
			// echo count($res) . ' => total <br>';
			return $no_answers;
		}
	}

	protected function get_Recursive(&$collector, $recursive, $page_depth = 0, $groupid)
	{
		if ($recursive != null && count($recursive) <= 1000 && count($recursive) > 0) 
		{     
			$people_temp = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Groups' => $groupid), $limit = 1000, $page = $page_depth, array('Email'));

			if($people_temp == null || count($recursive) == 0)
				return;

			$collector = array_merge($collector, $people_temp);

			$page_depth++;

			$this->get_Recursive($collector, $people_temp, $page_depth, $groupid);    
		}
	}


	protected function get_RecursiveAll(&$collector, $recursive, $page_depth = 0) 
	{
		if ($recursive != null && count($recursive) <= 1000 && count($recursive) > 0) 
		{    
			$people_temp = Infusionsoft_DataService::query(new Infusionsoft_Contact(), array('Id' => '%'), $limit = 1000, $page = $page_depth, array('Groups', 'Email', 'FirstName', 'LastName'));
			// $people_temp = Infusionsoft_DataService::query(new Infusionsoft_Contact(), $groups, $limit = 1000, $page = $page_depth, array('Groups', 'Email', 'FirstName', 'LastName'));

			if($people_temp == null || count($recursive) == 0)
				return;

			$collector = array_merge($collector, $people_temp);

			$page_depth++;

			$this->get_RecursiveAll($collector, $people_temp, $page_depth);    
		}
	}

	protected function findInAnsweredList($email, $answeredList)
	{
		foreach($answeredList as $each)
		{
			$s1 = $email;
			$s2 = $each->rcpt_to;
			if( $s1 == $s2)
				return TRUE;
		}
		return FALSE;
	}

	public function GetCampaignList($start_offset = -1, $end_offset = -1, $cond = null, $from = null, $to = null)
	{		
		$sql = "SELECT * FROM tbl_campaign WHERE 1=1 ";
		$filter = "";

		if($cond != null && count($cond) > 0)
		{
			foreach($cond as $key=>$value) 
			{
				$filter .= ($key . " LIKE '%" . $value. "%'");
				$filter .= "and";
			}
			$filter = " and " . rtrim($filter, "and");
		}		

		if($from != null && $from != '')
			$filter .= ( " and injection_time > '" . $from . "'" );

		if($to != null && $to != '')
			$filter .= ( " and injection_time < '" . $to . "'");

		$limit = $end_offset - $start_offset + 1;
		if( $end_offset >= 0 )
			$filter .= " Order By injection_time desc LIMIT " . $limit . " OFFSET " . $start_offset;
		
		$sql = $sql . $filter;

		// echo "<br>";
		// echo $sql;
		Log::INFO($sql . ' line:210 in WhkEventManager::GetCampaignList ');
		
		$ret = array();
		$result = mysqli_query($this->dbconn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($ret, $row);
				//echo "id: " . $row["id"]. " - campaign_id: " . $row["campaign_id"]. " " . $row["group_type"]. "<br	>";
			}
			// echo "<br>" . $count . " results";
			return $ret;
		}
		return array();
	}

	public function SaveCampaignToDatabase($list)
	{
		if(count($list) == 0) return;

		$sql = "INSERT INTO tbl_campaign";		
		$key_field = '';
		$value_filed = '';
		$campaign = $list[0];
		foreach($campaign as $key => $value)		
			$key_field = $key_field . $key . ',';
		$key_field = '(' . rtrim($key_field, ',') . ')';	

		foreach($list as $campaign) 
		{	
			$value_item = '';
			foreach($campaign as $key => $value)			
				$value_item = $value_item . '"' . $value . '",';
			$value_item = '(' . rtrim($value_item, ',') . ')';
			$value_filed = $value_filed . $value_item . ',';
		}
		$value_filed = rtrim($value_filed, ',');

		$sql = $sql . " " . $key_field . " VALUES " . $value_filed;
		
		Log::INFO($sql . '	line:253 in WhkEventManager::SaveCampaignToDatabase ');
		// echo $sql;

		if ($this->dbconn->query($sql) === TRUE) {			
			// echo "tbl_campaign new record created successfully";
		} else {
			Log::ERROR($this->dbconn->error . '	WhkEventManager::SaveCampaignToDatabase ');
			// echo "Error: " . $sql . "<br>" . $this->dbconn->error;
		}
		// echo '<br>';
	}

	public function ConfigureTestDatabase()
	{
		// truncate all field //
		$sql = "TRUNCATE TABLE tbl_event";
		$result = mysqli_query($this->dbconn, $sql);

		foreach( $this->event_list as $key=>$value) {
			echo "<br>";
			$sql = "TRUNCATE TABLE tbl_" . $key;
			$result = mysqli_query($this->dbconn, $sql);

		}

		// load data from ini file //
		// $ini_array = parse_ini_file("webhood_testdata.ini", true);
		// foreach($ini_array as $key=>$value)
		// {
		// 	$webhook_indata = $value["data"];
		// 	$this->SetIndata($webhook_indata);
		// 	echo "<br>";
		// }
		
		//print_r($webhook_indata);
		//$eventMgr->SetIndata($webhook_indata);
	}
	
	public function DeleteCampaign($campagin_id)
	{
		$sql = "DELETE FROM tbl_campaign WHERE campaign_id = '" . $campagin_id . "'";
		Log::INFO($sql . '	line:294 in WhkEventManager::DeleteCampaign ');
		$result = mysqli_query($this->dbconn, $sql);
	}

	public function SaveWebhookError($batchid)
	{
		// check if event_id is already stored //
		$sql = "SELECT id FROM tbl_webhook_error WHERE batchid = '" . $batchid . "'";

		// echo "<br>";
		// echo $sql;
		Log::INFO($sql . '	line:298 in WhkEventManager::SaveWebhookError ');
		$result = mysqli_query($this->dbconn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// echo "<br>" . $count . " results";
			return;
		}

		// save to tbl_event //
		$sql = "INSERT INTO tbl_webhook_error ( batchid ) VALUES ('" . $batchid . "')";
		Log::INFO($sql . '	line:308 in WhkEventManager::SaveWebhookError ');

		// echo "<br>";
		// echo $sql;
		if ($this->dbconn->query($sql) === TRUE) {
			// echo "tbl_event new record created successfully";
		} else {
			Log::ERROR($this->dbconn->error . '	WhkEventManager::SaveWebhookError');
			// echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
}
