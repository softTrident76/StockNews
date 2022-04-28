<?php
require_once 'webhooksdk/log.php';

class WhkData
{
	protected $values = array();	
	public function ToParamString()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}

	public function GetValues()
	{
		return $this->values;
	}

	public function GetData($key)
	{
		return $this->values[$key];
	}

	public function IsDataSet($key)
	{
		return array_key_exists($key, $this->values);
	}

	// 'bounce', 'delivery', 'injection', 'out_fo_band','policy_rejection', 'delay', 'gross_opens', 'unique_opens', 'click_through'
	public function GetBounceDetailList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_bounce where campaign_id = '" . $campaign_id . "' and type = 'bounce'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetDeliveryList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_delivery where campaign_id = '" . $campaign_id . "' and type = 'delivery'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetInjectionList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_injection where campaign_id = '" . $campaign_id . "' and type = 'injection'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetOutOfBandList($conn, $campaign_id) 
	{
		$records = array();
		$sql = "select * from tbl_out_fo_band where campaign_id = '" . $campaign_id . "' and type = 'out_fo_band'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetPolicyRejectionList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_policy_rejection where campaign_id = '" . $campaign_id . "' and type = 'policy_rejection'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetDelayList($conn, $campaign_id) 
	{
		$records = array();
		$sql = "select * from tbl_delay where campaign_id = '" . $campaign_id . "' and type = 'delay'";
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetGrossOpensList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "' order by timestamp";	
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetUniqueOpensList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "' group by rcpt_to order by timestamp";	
		$result = mysqli_query($conn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetListUnsubscribeList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_list_unsubscribe where campaign_id = '" . $campaign_id . "' order by timestamp";	
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetLinkUnsubscribeList($conn, $campaign_id)
	{
		$records = array();
		$sql = "select * from tbl_link_unsubscribe where campaign_id = '" . $campaign_id . "' order by timestamp";	
		$result = mysqli_query($conn, $sql);		
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetClickGrossThroughList($conn, $campaign_id, $target_link_url)
	{
		$records = array();
		$sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' and target_link_url = '" . $target_link_url . "' order by timestamp";
		$result = mysqli_query($conn, $sql);	
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}	
		return $records;
	}

	public function GetClickUniqueThroughList($conn, $campaign_id, $target_link_url)
	{
		$records = array();
		$sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' and target_link_url = '" . $target_link_url . "' group by rcpt_to order by timestamp";
		$result = mysqli_query($conn, $sql);	
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (Object)$row);
			}
		}
		return $records;
	}

	public function GetCampaignDetail($conn, $campaign_id, $page_type)
	{
		$ret = new stdClass();
		$ret->job_detail = $campaign_id;

		$records = array();
		$sql = "select * from tbl_campaign where campaign_id = '" . $campaign_id . "'";
		Log::INFO($sql . '		line:224 in WhkData::GetCampaignDetail ');

		$result = mysqli_query($conn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)
		{
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				array_push($records, (object)$row);
				break;
			}
		}

		$ret->job_identifier = '';
		$ret->injection_time = $records[0]->injection_time;
		$ret->campaign_id = $records[0]->campaign_id;
		$ret->job_detail = $ret->campaign_id . ' as of '. convertToNewYork(date('Y-m-d H:i'));
		$ret->timestamp = $records[0]->timestamp; 
		$ret->subject = $records[0]->subject;
		$ret->friendly_from = $records[0]->friendly_from;
		$ret->launched_by = '';
		$ret->delv_method = $records[0]->delv_method;
		$ret->list_used = $records[0]->list_used;
		$ret->ip_pool = $records[0]->ip_pool;

		$ret->count_clicked = $records[0]->count_clicked;
		$ret->count_unique_clicked = $records[0]->count_unique_clicked;

		$ret->count_rendered = $records[0]->count_rendered;
		$ret->count_unique_rendered = $records[0]->count_unique_rendered;

		$ret->count_delivered = $records[0]->count_delivered;
		$ret->count_delivered_first = $records[0]->count_delivered_first;
		$ret->count_delivered_subsequent = $records[0]->count_delivered_subsequent;
		$ret->count_delayed = $records[0]->count_delayed;
		
		$ret->count_soft_bounce = $records[0]->count_soft_bounce;
		$ret->count_hard_bounce = $records[0]->count_hard_bounce;
		$ret->count_spam_complaint = $records[0]->count_spam_complaint;
		$ret->count_rejected = $records[0]->count_rejected;

		$ret->count_sent = $records[0]->count_sent;
		$ret->count_targeted = $records[0]->count_targeted;
		$ret->count_block_bounce = $records[0]->count_block_bounce;
		$ret->count_injected = $records[0]->count_injected;		

		// open rate detail
		// 	gross opens -
		// 	unique opens -
		// $sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "'";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->gross_opens = mysqli_num_rows($result);
		$ret->gross_opens = $records[0]->count_rendered;

		// click rate detail
		// 	gross click -
		// 	unique click -
		// $sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "'";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->gross_clicks = mysqli_num_rows($result);
		$ret->gross_clicks = $records[0]->count_clicked;

		// $sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "' group by rcpt_to";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->unique_opens = mysqli_num_rows($result);
		$ret->unique_opens = $records[0]->count_unique_rendered;

		// $sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' group by rcpt_to";	
		// $result = mysqli_query($conn, $sql);	
		// $ret->unique_clicks = mysqli_num_rows($result);	
		$ret->unique_clicks = $records[0]->count_unique_clicked;

		if($page_type != '')
			return $ret;

		// open rate detail
		// 	gross opens -
		// 	unique opens -
		// $sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "'";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->gross_opens = mysqli_num_rows($result);

		// // click rate detail
		// // 	gross click -
		// // 	unique click -
		// $sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "'";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->gross_clicks = mysqli_num_rows($result);

		// $sql = "select * from tbl_open where campaign_id = '" . $campaign_id . "' group by rcpt_to";		
		// $result = mysqli_query($conn, $sql);	
		// $ret->unique_opens = mysqli_num_rows($result);

		// $sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' group by rcpt_to";	
		// $result = mysqli_query($conn, $sql);	
		// $ret->unique_clicks = mysqli_num_rows($result);			

		// unsubscribes
		// link_unsubscribe
		// list_unsubscribe
		$sql = "select * from tbl_link_unsubscribe where campaign_id = '" . $campaign_id . "'";		
		$result = mysqli_query($conn, $sql);	
		$ret->link_unsubscribe = mysqli_num_rows($result);

		$sql = "select * from tbl_list_unsubscribe where campaign_id = '" . $campaign_id . "'";		
		$result = mysqli_query($conn, $sql);	
		$ret->list_unsubscribe = mysqli_num_rows($result);	

		/*
		// click through details
		// 	link 1 - 10 total clicks(5 unique)
		// 	link 2 - 5 total clicks(2 unique)
		*/
		$records = array();
		$sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' group by target_link_url ";
		Log::INFO($sql . '		line:305 in WhkData::GetCampaignDetail ');

		$result = mysqli_query($conn, $sql);				
		if (($count = mysqli_num_rows($result)) > 0)
		{		
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				$target_link_url = $row['target_link_url'];
				
				$sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' and target_link_url = '" . $target_link_url . "'";		
				$sub_result = mysqli_query($conn, $sql);	
				$gross_link_click = mysqli_num_rows($sub_result);

				$sql = "select * from tbl_click where campaign_id = '" . $campaign_id . "' and target_link_url = '" . $target_link_url . "' group by rcpt_to";	
				$sub_result = mysqli_query($conn, $sql);	
				$unique_link_click = mysqli_num_rows($sub_result);

				array_push($records, 
									(object)	array(	'target_link_url' => $target_link_url, 
														'gross_link_click' => $gross_link_click, 
														'unique_link_click' => $unique_link_click
													)
							);
			}
		}

		$ret->link = $records;
		// echo "<br>";
		// var_dump($ret);
		
		return $ret;
	}

	public function SaveToDatabase($conn, $inDataObj)
	{
		Log::INFO($sql . '		line:340 in WhkData::SaveToDatabase ');

		if($inDataObj == null)
			return "";
		
		// Log::INFO("---SaveToDatabase inDataObj---");

		$group_type = key($inDataObj);
		$event_type = $inDataObj[$group_type]['type'];

		$this->values = $inDataObj[$group_type];

		if(!array_key_exists('event_id', $inDataObj[$group_type]))
			$event_id = '';
		else
			$event_id = $inDataObj[$group_type]['event_id'];

		if(!array_key_exists('timestamp', $inDataObj[$group_type]))
			$timestamp = '';
		else
			$timestamp = $inDataObj[$group_type]['timestamp'];

		if(!array_key_exists('campaign_id', $inDataObj[$group_type]))
			$campaign_id = '';
		else
			$campaign_id = $inDataObj[$group_type]['campaign_id'];

		if(!array_key_exists('injection_time', $inDataObj[$group_type]))
			$injection_time = '';
		else
			$injection_time = $inDataObj[$group_type]['injection_time'];

		if(!array_key_exists('subject', $inDataObj[$group_type]))
			$subject = '';
		else
			$subject = addslashes($inDataObj[$group_type]['subject']);

		if(!array_key_exists('friendly_from', $inDataObj[$group_type]))
			$friendly_from = '';
		else
			$friendly_from = $inDataObj[$group_type]['friendly_from'];

		if(!array_key_exists('rcpt_to', $inDataObj[$group_type]))
			$rcpt_to = '';
		else
			$rcpt_to = $inDataObj[$group_type]['rcpt_to'];
		
		// check if event_id is already stored //
		$sql = "SELECT id FROM tbl_event WHERE event_id = '" . $event_id . "'";
		Log::INFO($sql . '		line:384 in WhkData::SaveToDatabase ');

		// echo "<br>";
		// echo $sql;
		// Log::INFO($sql);

		$result = mysqli_query($conn, $sql);
		if (($count = mysqli_num_rows($result)) > 0)					
			return;	

		// save to tbl_event //
		$sql = "INSERT INTO tbl_event (
										group_type, 
										event_id, 
										timestamp,
										campaign_id, 
										event_type,
										injection_time,
										subject,
										friendly_from
									) 
								VALUES ('" 
											. $group_type ."', '" 
											. $event_id . "', '" 
											. $timestamp . "', '" 
											. $campaign_id . "', '" 
											. $event_type . "', '" 
											. $injection_time . "', '" 
											. $subject . "', '" 											
											. $friendly_from .
										"')";
		// echo "<br>";
		// echo $sql;		
		Log::INFO($sql . '		line:420 in WhkData::SaveToDatabase ');
		if ($conn->query($sql) === TRUE) {
			// echo "tbl_event new record created successfully";
		} else {
			// echo "Error: " . $sql . "<br>" . $conn->error;
			Log::ERROR($conn->error  . '		WhkData::SaveToDatabase ');
		}

		// save to tbl_*** //
		// var_dump($this->values);
		// echo "<br>";

		$fields = " (";
		$values = " VALUES (";
		foreach ($this->values as $k => $v)
		{
			$fields = $fields . $k. ", ";

			if($k == 'target_link_url')			
				$v = rtrim($v, '/');
			
			if($k == 'subject')
				$v = addslashes($v);

			if(is_object($v))
				$values = $values . "'".json_encode($v) . "', ";
			else if(is_array($v))
				$values = $values . "'[" . implode(",", $v) . "]', ";
			else
				$values = $values . "'". $v. "', ";			
		}
		
		$fields = rtrim($fields, ", ");
		$values = rtrim($values, ", ");
		
		$fields .= ")";
		$values .= ")";

		$sql = "INSERT INTO tbl_" . $event_type . $fields . $values;
		Log::INFO($sql . '		line:456 in WhkData::SaveToDatabase');

		// echo "<br>";
		// echo $sql;
		// Log::INFO($sql);

		if ($conn->query($sql) === TRUE) {
			// echo "tbl_" . $event_type . " new record created successfully";
		} else {
			// echo "Error: " . $sql . "<br>" . $conn->error;
			// Log::ERROR($conn->error);
			Log::ERROR($conn->error . '		WhkData::SaveToDatabase');
		}
	}
}



