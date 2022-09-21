<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
	}

	/*
	>> Error log should be added prefix Error:

	# Status
	1=Pending,2=Approved,3=Resolved,4=Forwarded,5=Deployed,6=New,7=Active,8=Initiated,9=On Progress,10=Delivered,
	11=Locked,12=Returned,13=Sold,14=Paid,20=Settled,21=Replaced,22=Completed,23=Confirmed,24=Honored,-24=Dishonored,
	-1=Deleted,-2=Declined,-3=Canceled,-5=Taking out,-6=Renewed,-7=Inactive;
	*/	
	function receivable_summary_report(){
		$data['page_title'] = 'Receivable Summary Report';
		if($postData = $this->input->post()){
			# Parameter Area
			$vendorId = $postData['vendor_id'];
			$fromDate = $postData['date_from'];
			$fromDateSlice = explode("/",$fromDate);
			$fromDateMonth = $fromDateSlice[0];
			$fromDateYear = $fromDateSlice[1];
			$dateFrom = date("Y-m-d",strtotime("$fromDateYear-$fromDateMonth-01"));
			$toDate = $postData['date_to'];
			$toDateSlice = explode("/",$toDate);
			$toDateMonth = $toDateSlice[0];
			$toDateYear = $toDateSlice[1];
			$dateTo = date("Y-m-d",strtotime("$toDateYear-$toDateMonth-01"));
			$bthSubmit = $postData['bth_submit'];
			$data['from_date'] = $dateFrom;
			$data['to_date'] = $dateTo;
			# Condition Area
			$additionalWhere = "";
			if($vendorId != 'all'){
				$additionalWhere .=" AND L.VENDOR_ID=$vendorId ";
			}
			# Query Area 

			$query = "SELECT ID AS LeaseId,LEASE_NAME,LEASE_TYPE,VENDOR_ID,VENDOR_NAME,TotalAmount,IFNULL(TotalReceived,0) AS TotalReceived,IFNULL((IFNULL(TotalAmount,0)-IFNULL(TotalReceived,0)),0) AS Due FROM (
				SELECT L.ID,L.LEASE_NAME,L.LEASE_TYPE,L.VENDOR_ID,tbl_vendor.VENDOR_NAME,
				(SELECT SUM(IFNULL(AMOUNT,0)) FROM tbl_lease_agreement WHERE LEASE_ID=L.ID AND TYPE='rent' GROUP BY LEASE_ID) AS TotalAmount,
				(SELECT SUM(IFNULL(AMOUNT,0)) FROM tbl_receivable WHERE LEASE_ID=L.ID AND PERIOD BETWEEN '$dateFrom' AND '$dateTo' GROUP BY LEASE_ID) AS TotalReceived 
				FROM tbl_lease_onboarding L
				LEFT JOIN tbl_vendor ON tbl_vendor.ID=L.VENDOR_ID
				WHERE L.LEASE_TYPE='receivable' $additionalWhere
			) AS MA";
			#dd($query);
			$result = $this->db->query($query)->result();
			if (!$result) {
				$this->webspice->message_board('SORRY! Lease information not found!');
				// Set flash data 
				$this->session->set_flashdata('message', 'SORRY! Lease information not found!');
				$data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor where STATUS !=-1 ")->result();		
				$this->load->view('report/receivable_summary_report', $data);
				return false;
			}
			$customResult = array();
            foreach ($result as $val) :
                $VENDOR_ID = $val->VENDOR_ID ? $val->VENDOR_ID : 'not_assigned';
                @$customResult[$VENDOR_ID]['vendor_name'] = $val->VENDOR_NAME;
                @$customResult[$VENDOR_ID]['lease_records'][] = $val;
            endforeach;
			$data['get_records'] = $customResult;
			#dd($data['records']);
			if($bthSubmit=='pdf'){
				$data['action_type'] = 'pdf';
				$filename = "receivable_summary_" . date("Y-m-d H i s");
				$html = $this->load->view('report/print_receivable_summary_report', $data, true);
				$this->webspice->pdf_generator($filename, $filename, 'custom', $html, 'yes', null, null, true);
				return false;
			}elseif($bthSubmit=='export'){
				$data['action_type'] = 'csv';
			}elseif($bthSubmit=='print'){
				$data['action_type'] = 'print';
			}elseif($bthSubmit=='view'){
				$data['action_type'] = 'view';
			}
			$this->load->view('report/print_receivable_summary_report', $data);
			return false;
		}
		$data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor where STATUS !=-1 ")->result();
		$this->load->view('report/receivable_summary_report',$data);
	}
	function payable_summary_report(){
		$data['page_title'] = 'Payable Summary Report';
		if($postData = $this->input->post()){
			# Parameter Area
			$vendorId = $postData['vendor_id'];
			$fromDate = $postData['date_from'];
			$fromDateSlice = explode("/",$fromDate);
			$fromDateMonth = $fromDateSlice[0];
			$fromDateYear = $fromDateSlice[1];
			$dateFrom = date("Y-m-d",strtotime("$fromDateYear-$fromDateMonth-01"));
			$toDate = $postData['date_to'];
			$toDateSlice = explode("/",$toDate);
			$toDateMonth = $toDateSlice[0];
			$toDateYear = $toDateSlice[1];
			$dateTo = date("Y-m-d",strtotime("$toDateYear-$toDateMonth-01"));
			$bthSubmit = $postData['bth_submit'];
			$data['from_date'] = $dateFrom;
			$data['to_date'] = $dateTo;
			# Condition Area
			$additionalWhere = "";
			if($vendorId != 'all'){
				$additionalWhere .=" AND L.VENDOR_ID=$vendorId ";
			}
			# Query Area 

			$query = "SELECT ID AS LeaseId,LEASE_NAME,LEASE_TYPE,VENDOR_ID,VENDOR_NAME,TotalAmount,IFNULL(TotalPaid,0) AS TotalPaid,IFNULL((IFNULL(TotalAmount,0)-IFNULL(TotalPaid,0)),0) AS Due FROM (
				SELECT L.ID,L.LEASE_NAME,L.LEASE_TYPE,L.VENDOR_ID,tbl_vendor.VENDOR_NAME,
				(SELECT SUM(IFNULL(AMOUNT,0)) FROM tbl_lease_agreement WHERE LEASE_ID=L.ID AND TYPE='rent' GROUP BY LEASE_ID) AS TotalAmount,
				(SELECT SUM(IFNULL(AMOUNT,0)) FROM tbl_payable WHERE LEASE_ID=L.ID AND PERIOD BETWEEN '$dateFrom' AND '$dateTo' GROUP BY LEASE_ID) AS TotalPaid 
				FROM tbl_lease_onboarding L
				LEFT JOIN tbl_vendor ON tbl_vendor.ID=L.VENDOR_ID
				WHERE L.LEASE_TYPE='payable' $additionalWhere
			) AS MA";
			#dd($query);
			$result = $this->db->query($query)->result();
			if (!$result) {
				$this->webspice->message_board('SORRY! Lease information not found!');
				// Set flash data 
				$this->session->set_flashdata('message', 'SORRY! Lease information not found!');
				$data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor where STATUS !=-1 ")->result();		
				$this->load->view('report/payable_summary_report', $data);
				return false;
			}
			$customResult = array();
            foreach ($result as $val) :
                $VENDOR_ID = $val->VENDOR_ID ? $val->VENDOR_ID : 'not_assigned';
                @$customResult[$VENDOR_ID]['vendor_name'] = $val->VENDOR_NAME;
                @$customResult[$VENDOR_ID]['lease_records'][] = $val;
            endforeach;
			$data['get_records'] = $customResult;
			#dd($data['records']);
			if($bthSubmit=='pdf'){
				$data['action_type'] = 'pdf';
				$filename = "receivable_summary_" . date("Y-m-d H i s");
				$html = $this->load->view('report/print_payable_summary_report', $data, true);
				$this->webspice->pdf_generator($filename, $filename, 'custom', $html, 'yes', null, null, true);
				return false;
			}elseif($bthSubmit=='export'){
				$data['action_type'] = 'csv';
			}elseif($bthSubmit=='print'){
				$data['action_type'] = 'print';
			}elseif($bthSubmit=='view'){
				$data['action_type'] = 'view';
			}
			$this->load->view('report/print_payable_summary_report', $data);
			return false;
		}
		$data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor where STATUS !=-1 ")->result();
		$this->load->view('report/payable_summary_report',$data);
	}
	function accounts_journal(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'accounts_journal');
		$this->webspice->permission_verify('accounts_journal');

		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = null;
		$page_index = 0;
		$no_of_record = 30;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = '';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}
		
		# restricted branch user
		if( $this->webspice->get_user('USER_TYPE')=='branch_user' ){
			$where = ' WHERE TBL_RESULT.BRANCH_ID IN('.$this->webspice->get_user('BRANCH_ID').')';
			$filter_by = 'BRANCH: '.$this->customcache->option_maker($this->webspice->get_user('BRANCH_ID'),'OPTION_VALUE');
		}

		$initialSQL = "
		SELECT TBL_PRESNT_VALUE.*,
		SUBSTRING(TBL_PRESNT_VALUE.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_PRESNT_VALUE
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_PRESNT_VALUE.LEASE_ID
		";
		
		$initialSQL_2 = "
		SELECT TBL_LEASE_LIBILITY.*,
		SUBSTRING(TBL_LEASE_LIBILITY.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_LEASE_LIBILITY
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_LEASE_LIBILITY.LEASE_ID
		";

		/*$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM ($initialSQL) TBL_RESULT
		";*/
		
		$finalSQL = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(RENT) AS TOTAL_RENT,
		SUM(ADVANCE) AS TOTAL_ADVANCE,
		SUM(NET_PAYMENT) AS TOTAL_NET_PAYMENT,
		SUM(PRESENT_VALUE) AS TOTAL_PRESENT_VALUE
		FROM (
		$initialSQL		
		) TBL_RESULT
		";
		
		$finalSQL_2 = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(LEASE_PAYMENT) AS TOTAL_LEASE_PAYMENT,
		SUM(INTEREST) AS TOTAL_INTEREST,
		SUM(DECREASE_LIBILITY) AS TOTAL_DECREASE_LIBILITY,
		SUM(DEPRECIATION) AS TOTAL_DEPRECIATION
		FROM (
		$initialSQL_2		
		) TBL_RESULT
		";

   		# filtering records
		if( $this->input->get('filter') ){
			$year = $this->input->get('drp_year');
			$month = $this->input->get('drp_month');
			$branch = $this->input->get('drp_branch');
			$tmp_where = array();
			if( $branch && $this->webspice->get_user('USER_TYPE')!='branch_user' ){
				$tmp_where[] = "BRANCH_ID IN(".implode(',',$branch).')';
				$tmp_b_name = array();
				foreach($branch as $k=>$v){
					$tmp_b_name[] = $this->customcache->option_maker($v, 'OPTION_VALUE');
				}
				$filter_by .= " BRANCH: ".implode(', ', $tmp_b_name);
			}
			
			if($year && $year != 'consolidate' && $month){
				$tmp_where[] = "`MONTH` LIKE '".$year."-".$month."%'";
				$filter_by .= " MONTH: ".$year."-".$month;
			}elseif($year && $year != 'consolidate'){
				$tmp_where[] = "`MONTH` LIKE '".$year."%'";
				$filter_by .= " YEAR: ".$year;
			}elseif($year == 'consolidate'){
				// no filter require - all data
				$filter_by .= " YEAR: Consolidate";
			}elseif(!$year && $month){
				$tmp_where[] = "`MONTH` LIKE '".date('Y')."-".$month."%'"; # current year
				$filter_by .= " MONTH: ".date('Y')."-".$month;
			}
			
			$tmp_where ? $tmp_where = implode(' AND ', $tmp_where) : $tmp_where = $tmp_where;
			
			# marge with main/top $where
			if($where && $tmp_where){
				$where = $where.' AND '.$tmp_where;
			}elseif($tmp_where){
				$where = ' WHERE '.$tmp_where;
			}
			
			#dd($where,55);
		}

    	# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
				if( isset($_SESSION['sql_aj']) && $_SESSION['sql_aj'] && isset($_SESSION['sql_aj_2']) && $_SESSION['sql_aj_2'] ){
					$record = $this->db->query( $_SESSION['sql_aj'] ); # print all record in the query from accounts_journal
					$record_2 = $this->db->query( $_SESSION['sql_aj_2'] ); # print all record in the query from accounts_journal
					$data['get_record'] = $record->row();
					$data['get_record_2'] = $record_2->row();
					$data['filter_by'] = $_SESSION['filter_by'];
		
					$this->load->view('report/print_accounts_journal',$data);
					return false;
					break;
				}
	
				return false;
				break;
		}

    	# default
		$sql = $finalSQL . $where . $groupby . $orderby;
		$sql_2 = $finalSQL_2 . $where . $groupby . $orderby;

		# default
		$data['get_record'] = array(); 
		$data['get_record_2'] = array(); 
		$data['filter_by'] = null;
		
		if( $this->input->get('filter') ){
			$_SESSION['sql_aj'] = $sql;
			$_SESSION['sql_aj_2'] = $sql_2;
			$_SESSION['filter_by'] = $filter_by;

			$result = $this->db->query($sql)->row();
			$result_2 = $this->db->query($sql_2)->row();
			$data['get_record'] = $result;
			$data['get_record_2'] = $result_2;
			$data['filter_by'] = $filter_by;
		}
		
		$this->webspice->log_me('viewed_accounts_journal'); # log

		$this->load->view('report/accounts_journal', $data);
	}
	
	function consolidate_summary(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'consolidate_summary');
		$this->webspice->permission_verify('consolidate_summary');

		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = null;
		$page_index = 0;
		$no_of_record = 30;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = '';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}
		
		# restricted branch user
		if( $this->webspice->get_user('USER_TYPE')=='branch_user' ){
			$where = ' WHERE TBL_RESULT.BRANCH_ID = '.$this->webspice->get_user('BRANCH_ID');
		}

		$finalSQL = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(RENT) AS TOTAL_RENT,
		SUM(ADVANCE) AS TOTAL_ADVANCE,
		SUM(NET_PAYMENT) AS TOTAL_NET_PAYMENT,
		SUM(PRESENT_VALUE) AS TOTAL_PRESENT_VALUE,
		MIN(MONTH) AS STARTING_MONTH,
		MAX(MONTH) AS ENDING_MONTH
		FROM (
		SELECT TBL_PRESNT_VALUE.*,
		SUBSTRING(TBL_PRESNT_VALUE.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_PRESNT_VALUE
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_PRESNT_VALUE.LEASE_ID
		) TBL_RESULT
		{$where}
		GROUP BY BRANCH_ID
		ORDER BY BRANCH_ID, MONTH
		";
		
		$finalSQL_2 = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(LEASE_PAYMENT) AS TOTAL_LEASE_PAYMENT,
		SUM(INTEREST) AS TOTAL_INTEREST,
		SUM(DECREASE_LIBILITY) AS TOTAL_DECREASE_LIBILITY,
		SUM(DEPRECIATION) AS TOTAL_DEPRECIATION
		FROM (
		SELECT TBL_LEASE_LIBILITY.*,
		SUBSTRING(TBL_LEASE_LIBILITY.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_LEASE_LIBILITY
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_LEASE_LIBILITY.LEASE_ID
		) TBL_RESULT
		{$where}
		GROUP BY BRANCH_ID
		ORDER BY BRANCH_ID, MONTH
		";

    	# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
				if( isset($_SESSION['sql_cs']) && $_SESSION['sql_cs'] && isset($_SESSION['sql_cs_2']) && $_SESSION['sql_cs_2'] ){
					$record = $this->db->query( $_SESSION['sql_cs'] ); # print all record in the query from accounts_journal
					$record_2 = $this->db->query( $_SESSION['sql_cs_2'] ); # print all record in the query from accounts_journal
					$data['get_record'] = $record->result();
					$data['get_record_2'] = $record_2->result();
					$data['filter_by'] = $_SESSION['filter_by'];
		
					$this->load->view('report/print_consolidate_summary',$data);
					return false;
					break;
				}
	
				return false;
				break;
		}

    	# default
		$sql = $finalSQL;
		$sql_2 = $finalSQL_2;

		$_SESSION['sql_cs'] = $sql;
		$_SESSION['sql_cs_2'] = $sql_2;

		$result = $this->db->query($sql)->result();
		$result_2 = $this->db->query($sql_2)->result();
		$data['get_record'] = $result;
		$data['get_record_2'] = $result_2;
		
		$this->webspice->log_me('viewed_consolidate_summary'); # log

		$this->load->view('report/consolidate_summary', $data);
	}
	
	function consolidate_summary_by_year(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'consolidate_summary_by_year');
		$this->webspice->permission_verify('consolidate_summary_by_year');

		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = null;
		$page_index = 0;
		$no_of_record = 30;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = '';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}
		
		# restricted branch user
		if( $this->webspice->get_user('USER_TYPE')=='branch_user' ){
			$where = ' WHERE TBL_RESULT.BRANCH_ID = '.$this->webspice->get_user('BRANCH_ID');
		}

		$finalSQL = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(RENT) AS TOTAL_RENT,
		SUM(ADVANCE) AS TOTAL_ADVANCE,
		SUM(NET_PAYMENT) AS TOTAL_NET_PAYMENT,
		SUM(PRESENT_VALUE) AS TOTAL_PRESENT_VALUE,
		MIN(MONTH) AS STARTING_MONTH,
		MAX(MONTH) AS ENDING_MONTH
		FROM (
		SELECT TBL_PRESNT_VALUE.*,
		SUBSTRING(TBL_PRESNT_VALUE.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_PRESNT_VALUE
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_PRESNT_VALUE.LEASE_ID
		) TBL_RESULT
		{$where}
		GROUP BY BRANCH_ID, YEAR
		ORDER BY BRANCH_ID, MONTH
		";
		
		$finalSQL_2 = "
		SELECT TBL_RESULT.BRANCH_ID,
		TBL_RESULT.YEAR,
		SUM(LEASE_PAYMENT) AS TOTAL_LEASE_PAYMENT,
		SUM(INTEREST) AS TOTAL_INTEREST,
		SUM(DECREASE_LIBILITY) AS TOTAL_DECREASE_LIBILITY,
		SUM(DEPRECIATION) AS TOTAL_DEPRECIATION
		FROM (
		SELECT TBL_LEASE_LIBILITY.*,
		SUBSTRING(TBL_LEASE_LIBILITY.MONTH, 1, 4) AS YEAR,
		TBL_LEASE_ONBOARDING.BRANCH_ID
		FROM TBL_LEASE_LIBILITY
		LEFT JOIN TBL_LEASE_ONBOARDING ON TBL_LEASE_ONBOARDING.ID=TBL_LEASE_LIBILITY.LEASE_ID
		) TBL_RESULT
		{$where}
		GROUP BY BRANCH_ID, YEAR
		ORDER BY BRANCH_ID, MONTH
		";

    	# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
				if( isset($_SESSION['sql_cs_y']) && $_SESSION['sql_cs_y'] && isset($_SESSION['sql_cs_y_2']) && $_SESSION['sql_cs_y_2'] ){
					$record = $this->db->query( $_SESSION['sql_cs_y'] ); # print all record in the query from accounts_journal
					$record_2 = $this->db->query( $_SESSION['sql_cs_y_2'] ); # print all record in the query from accounts_journal
					$data['get_record'] = $record->result();
					$data['get_record_2'] = $record_2->result();
					$data['filter_by'] = $_SESSION['filter_by'];
		
					$this->load->view('report/print_consolidate_summary_by_year',$data);
					return false;
					break;
				}
	
				return false;
				break;
		}

    	# default
		$sql = $finalSQL;
		$sql_2 = $finalSQL_2;

		$_SESSION['sql_cs_y'] = $sql;
		$_SESSION['sql_cs_y_2'] = $sql_2;

		$result = $this->db->query($sql)->result();
		$result_2 = $this->db->query($sql_2)->result();
		$data['get_record'] = $result;
		$data['get_record_2'] = $result_2;
		
		$this->webspice->log_me('viewed_consolidate_summary_by_year'); # log

		$this->load->view('report/consolidate_summary_by_year', $data);
	}

	function region_wise_lease_information(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'region_wise_lease_information');
		$this->webspice->permission_verify('region_wise_lease_information');

		$this->load->database();
		$orderby = ' ORDER BY TBL_RESULT.REGION_NAME,TBL_RESULT.LEASE_NAME DESC';
		$groupby = null;
		$where = '';
		$page_index = 0;
		$no_of_record = 30;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}
		
		# restricted branch user
		if( $this->webspice->get_user('USER_TYPE')=='branch_user' ){
			$where = ' WHERE BRANCH_ID = '.$this->webspice->get_user('BRANCH_ID');
		}

		$initialSQL = "
		SELECT TBL_LEASE_ONBOARDING.*,
		tbl_option.OPTION_VALUE AS REGION_NAME
		FROM tbl_lease_onboarding
		LEFT JOIN tbl_option ON tbl_option.OPTION_ID=tbl_lease_onboarding.REGION
		";

		$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM ($initialSQL) TBL_RESULT
		";
		
		$finalSQL = "
		SELECT TBL_RESULT.* FROM (
		$initialSQL		
		) TBL_RESULT
		";

   		# filtering records
		if( $this->input->get('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'TBL_RESULT',
				$InputField = array('REGION'),
				$Keyword = array('LEASE_NAME','BRANCH_CODE'),
				$AdditionalWhere = null,
				$DateBetween = array()
			);

			$result['where'] ? $where = $result['where'] : $where=$where;
			$result['filter'] ? $filter_by = $result['filter'] : $filter_by=$filter_by;
			$limit = null;
		}

    	# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$_SESSION['sql'] = $finalSQL . $where . $groupby . $orderby . $limit;
				$_SESSION['filter_by'] = $filter_by;
			}

			$record = $this->db->query( $_SESSION['sql'] ); # print all record in the query
			$data['get_record'] = $record->result();
			$data['filter_by'] = $_SESSION['filter_by'];

			$this->load->view('report/print_region_wise_lease_information',$data);
			return false;
			break;
		}

    	# default
		$sql = $finalSQL . $where . $groupby . $orderby . $limit;

		# start: only for pager -- mysql
		if( $criteria == 'page' && !$this->input->get('filter') ){
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$sql = $sql;
			}
			$limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record); # this is to avoid SQL Injection
			$sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'],'LIMIT'));
			$sql = $sql . $limit;
		}

		# load all records
		if( !$this->input->get('filter') ){
			$count_data = $this->db->query($countSQL)->row();
			$data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $url_prefix.'manage_lease_onboarding/page/', 10 );
		}
		# end: only for pager -- mysql

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->webspice->log_me('viewed_region_wise_rent_info'); # log

		$this->load->view('report/region_wise_lease_information', $data);
	}
	
	
	# call confirmation for redirect another url with message
	function confirmation($message){
		$_SESSION['confirmation'] = $message;
		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix.'confirmation');
	}
	function show_confirmation(){
		if( !isset($_SESSION['confirmation']) ){
			$_SESSION['confirmation'] = array();
		}
		$data = $_SESSION['confirmation'];
		$this->load->view('view_message',$data);
	}


}

	/* End of file */
/* Location: ./application/controllers/ */