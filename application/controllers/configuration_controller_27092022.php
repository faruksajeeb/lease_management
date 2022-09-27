<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configuration_controller extends CI_Controller {
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
	
	function create_user($data=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_user');
		$this->webspice->permission_verify('create_user');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'USER_ID'=>null,
				'EMPLOYEE_ID'=>null,
				'USER_NAME'=>null,
				'USER_EMAIL'=>null,
				'USER_TYPE'=>null,
				'BRANCH_ID'=>null,
				'USER_ROLE'=>null,
				'IS_LOGGED'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('employee_id','Employee ID','required|trim|min_length[1]|max_length[100]');
		$this->form_validation->set_rules('user_name','user_name','required|trim');
		$this->form_validation->set_rules('user_email','user_email','required|valid_email|trim|min_length[1]|max_length[100]');
		$this->form_validation->set_rules('user_type','user_type','required|trim');
		$this->form_validation->set_rules('branch_id','branch_id','trim');
		$this->form_validation->set_rules('user_role','user_role','required|integer|trim|min_length[1]');
		$this->form_validation->set_rules('is_logged','is_logged','trim');
		
		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}

			$this->load->view('user/create_user', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('user_id');

		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM TBL_USER WHERE USER_EMAIL=? OR EMPLOYEE_ID=?", array($input->user_email,$input->employee_id), 'You are not allowed to enter duplicate Email and Employee ID', 'USER_ID', $input->user_id, $data, 'user/create_user');

		# remove cache
		$this->webspice->remove_cache('user');

		
		# update process
		if( $input->user_id ){
			#update query
			$sql = "
			UPDATE TBL_USER SET EMPLOYEE_ID=?, USER_NAME=?, USER_EMAIL=?, USER_TYPE=?, BRANCH_ID=?, ROLE_ID=?,
			UPDATED_BY=?, UPDATED_DATE=?
			WHERE USER_ID=?";
			$this->db->query($sql, array($input->employee_id, $input->user_name, $input->user_email, $input->user_type, $input->branch_id, $input->user_role,
			$this->webspice->get_user_id(), $this->webspice->now('datetime24'),
			$input->user_id));

			$this->webspice->log_me('user_updated - '.$input->user_email); # log activities
			exit('update_success');
		}

		# insert data
		$random_password = 1234;
		$sql = "
		INSERT INTO TBL_USER
		(EMPLOYEE_ID, USER_NAME, USER_EMAIL, PASSWORD, USER_TYPE, BRANCH_ID, ROLE_ID,
		CREATED_BY, CREATED_DATE, STATUS, SESSION_ID)
		VALUES
		(?, ?, ?, ?, ?, ?, ?,
		?, ?, 8, 1)";

		$this->db->query($sql,
		array($input->employee_id, $input->user_name, $input->user_email, $this->webspice->encrypt_decrypt($random_password, 'encrypt'), $input->user_type, $input->branch_id, $input->user_role,
		$this->webspice->get_user_id(), $this->webspice->now('datetime24')));

		if( !$insert_id = $this->db->insert_id() ){
			exit('We could not execute your request. Please try again later or report to authority.');
		}
		$this->webspice->log_me('user_created - '.$insert_id); # log

		# send verification email
		#$this->load->library('email_template');
		#$this->email_template->send_new_password_change_email($input->user_name, $input->user_email);

		exit('insert_success');
	}
	
	#manage user
	function manage_user(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_user');
		$this->webspice->permission_verify('manage_user');

		$this->load->database();
		$orderby = null;
		$groupby = null;
		$additional_where = null;
		$where = '';
		$page_index = 0;
		$no_of_record = 20;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key; 
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}

		$initialSQL = "
		SELECT TBL_RESULT.* FROM (
		SELECT TBL_USER.*, TBL_ROLE.ROLE_NAME
		FROM TBL_USER
		LEFT JOIN TBL_ROLE ON TBL_ROLE.ROLE_ID = TBL_USER.ROLE_ID
		) TBL_RESULT
		";
		
		$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM ($initialSQL) TBL_RESULT
		";
		
   		# filtering records
   		$tenor = $this->input->get('tenor');
		if( $this->input->get('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'TBL_RESULT', 
				$InputField = array('ROLE_ID'),
				$Keyword = array('USER_ID','EMPLOYEE_ID','USER_NAME','USER_EMAIL','USER_TYPE'),
				$AdditionalWhere = $additional_where,
				$DateBetween = null,
				$FixedTenor = array('CREATED_DATE', $tenor)
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
				$_SESSION['sql'] = $initialSQL . $where . $orderby;
				$_SESSION['filter_by'] = $filter_by;
			}

			$record = $this->db->query( $_SESSION['sql'] );										 		
			$data['get_record'] = $record->result();
			$data['filter_by'] = $_SESSION['filter_by'];
			
			$this->load->view('user/print_user',$data);
			return false;
			break;

			case 'edit':
				$this->webspice->edit_generator($TableName='TBL_USER', $KeyField='USER_ID', $key, $RedirectController='configuration_controller', $RedirectFunction='create_user', $PermissionName='manage_user', $StatusCheck=null, $Log='edit_user');          
				return false;
				break;

			case 'inactive':
				$this->webspice->action_executer($TableName='TBL_USER', $KeyField='USER_ID', $key, $RedirectURL='manage_user', $PermissionName='manage_user', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='user', $Log='inactive_user');
				return false;	
				break; 

			case 'active':
				$this->webspice->action_executer($TableName='TBL_USER', $KeyField='USER_ID', $key, $RedirectURL='manage_user', $PermissionName='manage_user', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='user', $Log='active_user');
				return false;	
				break;                  
		}

    	# default
		$sql = $initialSQL . $where . $groupby . $orderby . $limit;

		# start: only for pager - mysql
		if( $criteria == 'page' && !$this->input->get('filter') ){
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$sql = $sql;
			}
			$limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record);		# this is to avoid SQL Injection
			$sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'],'LIMIT'));
			$sql = $sql . $limit;
		}
		
		# load all records
		if( !$this->input->get('filter') ){
			$count_data = $this->db->query($countSQL)->row();
			$data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $url_prefix.'manage_user/page/', 10 );
		}
		# end: only for pager - mysql

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;
		
		$this->webspice->log_me('viewed_user_list'); # log
		
		$this->load->view('user/manage_user', $data);
	}
	
	# user role creation process
	function create_role($data=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_role');
		$this->webspice->permission_verify('create_role,manage_role');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ROLE_ID'=>null,
				'ROLE_NAME'=>null,
				'PERMISSION_NAME'=>null
			);
		}

		# get permission name
		$sql = "SELECT DISTINCT TBL_PERMISSION.PERMISSION_NAME,TBL_PERMISSION.GROUP_NAME
						FROM TBL_PERMISSION
						WHERE TBL_PERMISSION.STATUS = 7 AND PERMISSION_NAME !='#'
						ORDER BY TBL_PERMISSION.GROUP_NAME";
		$data['get_permission_with_group'] = $this->db->query($sql)->result();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('role_name','role_name','required|trim');

		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}

			$this->load->view('user/create_role', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('ROLE_ID');

		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM TBL_ROLE WHERE ROLE_NAME=?", array( $input->role_name), 'You are not allowed to enter duplicate role name.', 'ROLE_ID', $input->ROLE_ID, $data, 'user/create_role');

		# remove cache
		$this->webspice->remove_cache('role');

		# update data
		if( $input->ROLE_ID ){
			#update query
			$sql = "
			UPDATE TBL_ROLE SET ROLE_NAME=?, PERMISSION_NAME=?, UPDATED_BY=?, UPDATED_DATE=?
			WHERE ROLE_ID=?";
			$this->db->query($sql, array($input->role_name, implode(',',$input->permission), $this->webspice->get_user_id(), $this->webspice->now(), $input->ROLE_ID));

			$this->webspice->log_me('role_updated - '.$input->role_name); # log activities
			exit('update_success');
		}

		# insert data
		$sql = "
		INSERT INTO TBL_ROLE
		(ROLE_NAME, PERMISSION_NAME, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		(?, ?, ?, ?, 7)";

		$this->db->query($sql, array($input->role_name, implode(',',$input->permission), $this->webspice->get_user_id(), $this->webspice->now()));

		if( !$insert_id = $this->db->insert_id() ){
			exit('We could not execute your request. Please tray again later or report to authority.');
		}

		$this->webspice->log_me('role_created'); # log
		exit('insert_success');
	}
	
	# user role management process
	function manage_role(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_user');
		$this->webspice->permission_verify('manage_role');

		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = '';
		$page_index = 0;
		$no_of_record = 20;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}

		$initialSQL = "
		SELECT TBL_ROLE.*
		FROM TBL_ROLE
		";

		$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM TBL_ROLE
		";

   		# filtering records
		if( $this->input->get('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'TBL_ROLE',
				$InputField = array(),
				$Keyword = array('ROLE_ID','ROLE_NAME','PERMISSION_NAME'),
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
				$_SESSION['sql'] = $initialSQL . $where . $groupby . $orderby . $limit;
				$_SESSION['filter_by'] = $filter_by;
			}

			$record = $this->db->query( $_SESSION['sql'] );
			$data['get_record'] = $record->result();
			$data['filter_by'] = $_SESSION['filter_by'];

			$this->load->view('user/print_role',$data);
			return false;
			break;

			case 'edit':
			$this->webspice->edit_generator($TableName='TBL_ROLE', $KeyField='ROLE_ID', $key, $RedirectController='configuration_controller', $RedirectFunction='create_role', $PermissionName='create_role', $StatusCheck=null, $Log='edit_role');
			return false;
			break;

			case 'inactive':
			$this->webspice->action_executer($TableName='TBL_ROLE', $KeyField='ROLE_ID', $key, $RedirectURL='manage_role', $PermissionName='manage_role', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='role', $Log='inactive_role');
			return false;
			break;

			case 'active':
			$this->webspice->action_executer($TableName='TBL_ROLE', $KeyField='ROLE_ID', $key, $RedirectURL='manage_role', $PermissionName='manage_role', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='role', $Log='active_role');
			return false;
			break;
		}

    	# default
		$sql = $initialSQL . $where . $groupby . $orderby . $limit;

		# start: only for pager - mysql
		if( $criteria == 'page' && !$this->input->get('filter') ){
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$sql = $sql;
			}
			$limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record);		# this is to avoid SQL Injection
			$sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'],'LIMIT'));
			$sql = $sql . $limit;
		}

		# load all records for pager
		if( !$this->input->get('filter') ){
			$count_data = $this->db->query($countSQL)->row();
			$data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $url_prefix.'manage_role/page/', 10 );
		}
		# end: only for pager - mysql

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->webspice->log_me('viewed_user_role'); # log

		$this->load->view('user/manage_role', $data);
	}
	
	# master data creation
	function create_option($data=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_option');
		$this->webspice->permission_verify('create_option,manage_option');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'OPTION_ID'=>null,
				'OPTION_VALUE'=>null,
				'OPTION_VALUE_BANGLA'=>null,
				'OPTION_VALUE_2'=>null,
				'OPTION_VALUE_2_BANGLA'=>null,
				'GROUP_NAME'=>null,
				'PARENT_ID'=>null,
				'STATUS'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('option_value','option_value','required|trim');
		$this->form_validation->set_rules('option_value_bangla','option_value_bangla','required|trim');
		$this->form_validation->set_rules('option_value_2','option_value_2','trim');
		$this->form_validation->set_rules('option_value_2_bangla','option_value_2_bangla','trim');
		$this->form_validation->set_rules('group_name','group_name','required|trim');

		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}

			$this->load->view('configuration/create_option', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('key');

		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM TBL_OPTION WHERE GROUP_NAME=? AND (OPTION_VALUE=? or OPTION_VALUE_BANGLA=?)", array($input->group_name, $input->option_value, $input->option_value_bangla), 'You are not allowed to enter duplicate option value', 'OPTION_ID', $input->key, $data, 'configuration/create_option');

		# remove cache
		$this->webspice->remove_cache('option');

		# update process
		if( $input->key ){
			$sql = "
			UPDATE TBL_OPTION SET OPTION_VALUE=?, OPTION_VALUE_BANGLA=?, OPTION_VALUE_2=?, OPTION_VALUE_2_BANGLA=?,
			UPDATED_BY=?, UPDATED_DATE=?
			WHERE OPTION_ID=?";
			$this->db->query($sql,
			array($input->option_value, $input->option_value_bangla, $input->option_value_2, $input->option_value_2_bangla,
			$this->webspice->get_user_id(), $this->webspice->now(), $input->key));

			$this->webspice->log_me('option_updated - '.$input->option_value); # log activities
			exit('update_success');
		}

		# insert data
		$sql = "
		INSERT INTO TBL_OPTION
		(GROUP_NAME, OPTION_VALUE, OPTION_VALUE_BANGLA, OPTION_VALUE_2, OPTION_VALUE_2_BANGLA,
		CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		(?, ?, ?, ?, ?,
		?, ?, 7)";

		$this->db->query($sql,
		array($input->group_name, $input->option_value, $input->option_value_bangla, $input->option_value_2, $input->option_value_2_bangla,
		$this->webspice->get_user_id(), $this->webspice->now()));

		if( !$insert_id = $this->db->insert_id() ){
			exit('We could not execute your request. Please tray again later or report to authority.');
		}

		$this->webspice->log_me('created_option'); # log
		exit('insert_success');
	}
	
	# master data management
	function manage_option(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_option');
		$this->webspice->permission_verify('manage_option');

		$this->load->database();
		$orderby = ' ORDER BY OPTION_ID DESC, TBL_OPTION.GROUP_NAME, TBL_OPTION.OPTION_VALUE';
		$groupby = null;
		$where = '';
		$page_index = 0;
		$no_of_record = 20;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}

		$initialSQL = "
		SELECT TBL_OPTION.*
		FROM TBL_OPTION
		";

		$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM TBL_OPTION
		";

   		# filtering records
		if( $this->input->get('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'TBL_OPTION',
				$InputField = array('GROUP_NAME'),
				$Keyword = array('GROUP_NAME','OPTION_VALUE','OPTION_VALUE_BANGLA','OPTION_VALUE_2','OPTION_VALUE_2_BANGLA'),
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
				$_SESSION['sql'] = $initialSQL . $where . $groupby . $orderby . $limit;
				$_SESSION['filter_by'] = $filter_by;
			}

			$record = $this->db->query( $_SESSION['sql'] ); # print all record in the query
			$data['get_record'] = $record->result();
			$data['filter_by'] = $_SESSION['filter_by'];

			$this->load->view('configuration/print_option',$data);
			return false;
			break;

			case 'edit':
			$this->webspice->edit_generator($TableName='TBL_OPTION', $KeyField='OPTION_ID', $key, $RedirectController='configuration_controller', $RedirectFunction='create_option', $PermissionName='create_option,manage_option', $StatusCheck=null, $Log='edit_user');
			return false;
			break;

			case 'inactive':
			$this->webspice->action_executer($TableName='TBL_OPTION', $KeyField='OPTION_ID', $key, $RedirectURL='manage_option', $PermissionName='manage_option', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='option', $Log='inactive_option');
			return false;
			break;

			case 'active':
			$this->webspice->action_executer($TableName='TBL_OPTION', $KeyField='OPTION_ID', $key, $RedirectURL='manage_option', $PermissionName='manage_option', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='option', $Log='active_option');
			return false;
			break;
		}

    	# default
		$sql = $initialSQL . $where . $groupby . $orderby . $limit;

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
			$data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $url_prefix.'manage_option/page/', 10 );
		}
		# end: only for pager -- mysql

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->webspice->log_me('viewed_option_list'); # log

		$this->load->view('configuration/manage_option', $data);
	}
	
	# lease onboarding
	function lease_onboarding($data=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'lease_onboarding');
		$this->webspice->permission_verify('lease_onboarding, manage_lease_onboarding');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ID'=>null,
				'REGION'=>null,
				'BRANCH_ID'=>null,
				'BRANCH_CODE'=>null,
				'LEASE_NAME'=>null,
				'LICENSE_NO'=>null,
				'LICENSE_ISSUE_DATE'=>null,
				'BRANCH_OPENING_DATE'=>null,
				'TYPE'=>null,
				'ADDRESS'=>null,
				'CITY'=>null,
				'DISTRICT'=>null,
				'THANA_UPAZILLA'=>null,
				'FLOOR_SPACE'=>null,
				'RENT_PER_SQFT'=>null,
				'CONTACT_PERSON'=>null,
				'CONTACT_MOBILE_NO'=>null,
				'CONTACT_EMAIL'=>null,
				'AGREEMENT_DATE'=>null,
				'AGREEMENT_EXPIRY'=>null,
				'AGREEMENT_DOCUMENT'=>null,
				'TOTAL_AMOUINT_LOAN'=>null,
				'NO_OF_CUSTOMER_LOAN'=>null,
				'TOTAL_AMOUNT_DEPOSIT'=>null,
				'NO_OF_CUSTOMER_DEPOSIT'=>null,
				'PROFIT_LOSS'=>null,
				'VENDOR_ID'=>null,
				'LEASE_TYPE'=>null,
				'LEASE_TERM'=>null,
				'STATUS'=>null
			);
		}

		$data['cost_centers'] = $this->db->query("SELECT * FROM `tbl_option` WHERE GROUP_NAME = 'cost_center' AND STATUS =7")->result();
		$data['vendors'] = $this->db->query("SELECT * FROM `tbl_vendor` WHERE `STATUS`=7")->result();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('REGION','REGION','required|trim');
		$this->form_validation->set_rules('BRANCH_ID','BRANCH','required|trim');
		#$this->form_validation->set_rules('BRANCH_CODE','BRANCH_CODE','required|trim');
		$this->form_validation->set_rules('LEASE_NAME','Lease Name','required|trim|max_length[100]');
		$this->form_validation->set_rules('LEASE_TYPE','LEASE TYPE','required|trim|max_length[100]');
		$this->form_validation->set_rules('LEASE_TERM','LEASE TERM','required|trim|max_length[100]');
		$this->form_validation->set_rules('VENDOR_ID','VENDOR','required|trim|integer');
		$this->form_validation->set_rules('LICENSE_NO','LICENSE_NO','required|trim|max_length[100]');
		$this->form_validation->set_rules('LICENSE_ISSUE_DATE','LICENSE_ISSUE_DATE','required|trim');
		$this->form_validation->set_rules('BRANCH_OPENING_DATE','BRANCH_OPENING_DATE','required|trim');
		$this->form_validation->set_rules('TYPE','TYPE','required|trim');
		$this->form_validation->set_rules('ADDRESS','ADDRESS','required|trim|min_length[1]|max_length[255]');
		#$this->form_validation->set_rules('CITY','CITY','required|trim|max_length[100]');
		$this->form_validation->set_rules('DISTRICT','DISTRICT','required|trim|max_length[50]');
		$this->form_validation->set_rules('THANA_UPAZILLA','THANA_UPAZILLA','required|trim|max_length[50]');
		$this->form_validation->set_rules('FLOOR_SPACE','FLOOR_SPACE','required|trim|max_length[100]');
		$this->form_validation->set_rules('RENT_PER_SQFT','RENT_PER_SQFT','required|trim');
		#$this->form_validation->set_rules('CONTACT_PERSON','CONTACT_PERSON','required|trim|max_length[100]');
		#$this->form_validation->set_rules('CONTACT_MOBILE_NO','CONTACT_MOBILE_NO','required|trim|max_length[100]');
		#$this->form_validation->set_rules('CONTACT_EMAIL','CONTACT_EMAIL','required|trim|max_length[100]');
		$this->form_validation->set_rules('AGREEMENT_DATE','AGREEMENT_DATE','required|trim|required');
		$this->form_validation->set_rules('AGREEMENT_EXPIRY','AGREEMENT_EXPIRY','required|trim|required');

		#$this->form_validation->set_rules('TOTAL_AMOUINT_LOAN','TOTAL_AMOUINT_LOAN','trim');
		#$this->form_validation->set_rules('NO_OF_CUSTOMER_LOAN','NO_OF_CUSTOMER_LOAN','trim');
		#$this->form_validation->set_rules('TOTAL_AMOUNT_DEPOSIT','TOTAL_AMOUNT_DEPOSIT','trim');
		#$this->form_validation->set_rules('NO_OF_CUSTOMER_DEPOSIT','NO_OF_CUSTOMER_DEPOSIT','trim');
		#$this->form_validation->set_rules('PROFIT_LOSS','PROFIT_LOSS','trim');
		
		$this->form_validation->set_rules('cost_center_id[]','Cost Center','trim');
		$this->form_validation->set_rules('cost_center_amount[]','Amount','trim');

		$this->form_validation->set_rules('txt_from_rent[]','from_rent','trim');
		$this->form_validation->set_rules('txt_to_rent[]','to_rent','trim');
		$this->form_validation->set_rules('txt_amount_rent[]','amount_rent','trim');
		$this->form_validation->set_rules('txt_amount_rent_with_tax[]','amount_rent_with_tax','trim');
		$this->form_validation->set_rules('txt_amount_rent_with_vat[]','amount_rent_with_vat','trim');
		
		$this->form_validation->set_rules('txt_from_advance[]','from_advance','trim');
		$this->form_validation->set_rules('txt_to_advance[]','to_advance','trim');
		$this->form_validation->set_rules('txt_amount_advance[]','amount_advance','trim');
		$this->form_validation->set_rules('txt_amount_advance_with_tax[]','amount_advance_with_tax','trim');
		$this->form_validation->set_rules('txt_amount_advance_with_vat[]','amount_advance_with_vat','trim');
		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}

			$this->load->view('configuration/lease_onboarding', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('key');
		
		# duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM TBL_LEASE_ONBOARDING WHERE BRANCH_ID=? AND LEASE_NAME=?", array($input->BRANCH_ID, $input->LEASE_NAME), 'You are not allowed to enter duplicate Lease Name under a Branch', 'ID', $input->key, $data, 'configuration/lease_onboarding');
		
		# image/attachment validation
		if($_FILES['agreement_document']['name']){
			/*$image_info = getimagesize($_FILES['image']['tmp_name']);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			if($image_width != 600 && $image_height != 451){
				exit('Image size dimensions (600px X 451px)!');
			}*/

			$allowedExts = array("pdf","doc","docx");
			$this->webspice->check_file_type($allowedExts, $input_name='agreement_document', $data, 'configuration/lease_onboarding');
		}
	  
		# remove cache
		# $this->webspice->remove_cache('configuration');
		
		# update process
		if( $input->key ){
			# insert agreement doc
			$doc = null;
		 	if($_FILES['agreement_document']['name']){
		 		$doc = $this->webspice->upload_file($SourceContainer='agreement_document', $Destination=$this->webspice->get_path('agreement_full'), $NamePrefix=null);
		  	}
	  
			$sql = "
			UPDATE TBL_LEASE_ONBOARDING SET 
			REGION=?, LEASE_NAME=?, 
			LEASE_TYPE=?, LEASE_TERM=?, VENDOR_ID=?,
			LICENSE_NO=?, LICENSE_ISSUE_DATE=?, BRANCH_OPENING_DATE=?, TYPE=?,
			ADDRESS=?, DISTRICT=?, THANA_UPAZILLA=?,
			FLOOR_SPACE=?, RENT_PER_SQFT=?,
			AGREEMENT_DATE=?, AGREEMENT_EXPIRY=?, AGREEMENT_DOCUMENT=?,
			UPDATED_BY=?, UPDATED_DATE=?
			WHERE ID=?";
			$this->db->query($sql,
			array(
			$input->REGION, $input->LEASE_NAME, 
			$input->LEASE_TYPE, $input->LEASE_TERM, $input->VENDOR_ID, 
			$input->LICENSE_NO, $input->LICENSE_ISSUE_DATE, $input->BRANCH_OPENING_DATE, $input->TYPE,
			$input->ADDRESS, $input->DISTRICT, $input->THANA_UPAZILLA,
			$input->FLOOR_SPACE, $input->RENT_PER_SQFT,
			$input->AGREEMENT_DATE, $input->AGREEMENT_EXPIRY, $doc,
			$this->webspice->get_user_id(), $this->webspice->now('datetime24'),
			$input->key
			));
			
			# make history and remove agreement data
			$this->db->query("
			INSERT INTO TBL_LEASE_AGREEMENT_HISTORY (LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT)
			SELECT LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT FROM TBL_LEASE_AGREEMENT
			WHERE LEASE_ID = ?
			",
			array(
			$input->key
			));
			
			$this->db->query("
			DELETE FROM TBL_LEASE_AGREEMENT
			WHERE LEASE_ID = ?
			",
			array(
			$input->key
			));
			
			# insert lease slab
			if(isset($input->txt_from_rent)){
				foreach($input->txt_from_rent as $k=>$v){
					if($v==''){continue;}
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($input->key, $v, $input->txt_to_rent[$k], 'rent', $input->txt_amount_rent[$k], $input->txt_amount_rent_with_tax[$k], $input->txt_amount_rent_with_vat[$k])
					);
				}
			}else{
				$input->txt_from_rent = array();
				$input->txt_to_rent = array();
				$input->txt_amount_rent_with_tax = array();
			}

			
			if(isset($input->txt_from_advance)){
				foreach($input->txt_from_advance as $k=>$v){
					if($v==''){continue;}
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($input->key, $v, $input->txt_to_advance[$k], 'advance', $input->txt_amount_advance[$k], $input->txt_amount_advance_with_tax[$k], $input->txt_amount_advance_with_vat[$k])
					);
				}
			}else{
				$input->txt_from_advance = array();
				$input->txt_to_advance = array();
				$input->txt_amount_advance_with_tax = array();
			}

			# delete Cost Center details
			$this->db->query("
			DELETE FROM TBL_COST_CENTER_DETAILS
			WHERE LEASE_ID = ?
			",
			array(
			$input->key
			));

			# insert cost center details
			foreach($input->cost_center_id as $k=>$v){
				if($v==''){continue;}
				$this->db->query("
				INSERT INTO TBL_COST_CENTER_DETAILS 
				(LEASE_ID, COST_CENTER_ID, PERCENTAGE) 
				VALUES
				(?, ?, ?)",
				array($input->key, $v, $input->cost_center_amount[$k])
				);
			}
			
			# process data and store in table for faster access for report
			if($input->txt_amount_rent_with_tax OR $input->txt_amount_advance_with_tax){
				$this->process_present_value($lease_id=$input->key, $rent_from=$input->txt_from_rent, $rent_to=$input->txt_to_rent, $rent_amount=$input->txt_amount_rent_with_tax, $advance_from=$input->txt_from_advance, $advance_to=$input->txt_to_advance, $advance_amount=$input->txt_amount_advance_with_tax);
			}

			$this->webspice->log_me('configuration_updated - '.$input->key); # log activities
			exit('update_success');
		}
		
		# insert agreement doc
		$doc = null;
	 	if($_FILES['agreement_document']['name']){
	 		$doc = $this->webspice->upload_file($SourceContainer='agreement_document', $Destination=$this->webspice->get_path('agreement_full'), $NamePrefix=null);
	  	}

		# insert data
		$sql = "
		INSERT INTO TBL_LEASE_ONBOARDING(
		REGION, BRANCH_ID, LEASE_NAME, 
		LEASE_TYPE, LEASE_TERM, VENDOR_ID,
		LICENSE_NO, LICENSE_ISSUE_DATE, BRANCH_OPENING_DATE, TYPE,
		ADDRESS, DISTRICT, THANA_UPAZILLA,
		FLOOR_SPACE, RENT_PER_SQFT,
		AGREEMENT_DATE, AGREEMENT_EXPIRY, AGREEMENT_DOCUMENT,
		CREATED_BY, CREATED_DATE, 
		STATUS
		)
		VALUES(
		?, ?, ?,
		?, ?, ?,
		?, ?, ?, ?,
		?, ?, ?, 
		?, ?,
		?, ?, ?,
		?, ?, 
		?
		)
		";

		$this->db->query($sql,
		array(
		$input->REGION, $input->BRANCH_ID, $input->LEASE_NAME, 
		$input->LEASE_TYPE, $input->LEASE_TERM, $input->VENDOR_ID, 
		$input->LICENSE_NO, $input->LICENSE_ISSUE_DATE, $input->BRANCH_OPENING_DATE, $input->TYPE,
		$input->ADDRESS, $input->DISTRICT, $input->THANA_UPAZILLA,
		$input->FLOOR_SPACE, $input->RENT_PER_SQFT,
		$input->AGREEMENT_DATE, $input->AGREEMENT_EXPIRY, $doc,
		$this->webspice->get_user_id(), $this->webspice->now('datetime24'),
		7
		));
		
		$lease_id = $this->db->insert_id();
		if( !$lease_id ){
			exit('We could not execute your request. Please try again later or report to authority.');
		}

		# insert cost center details
		foreach($input->cost_center_id as $k=>$v){
			if($v==''){continue;}
			$this->db->query("
			INSERT INTO TBL_COST_CENTER_DETAILS 
			(LEASE_ID, COST_CENTER_ID, PERCENTAGE) 
			VALUES
			(?, ?, ?)",
			array($lease_id, $v, $input->cost_center_amount[$k])
			);
		}
		
		# insert lease slab
		foreach($input->txt_from_rent as $k=>$v){
			if($v==''){continue;}
			$this->db->query("
			INSERT INTO TBL_LEASE_AGREEMENT 
			(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
			VALUES
			(?, ?, ?, ?, ?, ?, ?)",
			array($lease_id, $v, $input->txt_to_rent[$k], 'rent', $input->txt_amount_rent[$k], $input->txt_amount_rent_with_tax[$k], $input->txt_amount_rent_with_vat[$k])
			);
		}
		
		foreach($input->txt_from_advance as $k=>$v){
			if($v==''){continue;}
			$this->db->query("
			INSERT INTO TBL_LEASE_AGREEMENT 
			(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
			VALUES
			(?, ?, ?, ?, ?, ?, ?)",
			array($lease_id, $v, $input->txt_to_advance[$k], 'advance', $input->txt_amount_advance[$k], $input->txt_amount_advance_with_tax[$k], $input->txt_amount_advance_with_vat[$k])
			);
		}

		# process data and store in table for faster access for report
		$this->process_present_value($lease_id=$lease_id, $rent_from=$input->txt_from_rent, $rent_to=$input->txt_to_rent, $rent_amount=$input->txt_amount_rent_with_tax, $advance_from=$input->txt_from_advance, $advance_to=$input->txt_to_advance, $advance_amount=$input->txt_amount_advance_with_tax);
			
		$this->webspice->log_me('created_lease'); # log
		exit('insert_success');
	}
	
	function manage_lease_onboarding(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_lease_onboarding');
		$this->webspice->permission_verify('manage_lease_onboarding');

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
		SELECT TBL_LEASE_ONBOARDING.*,TBL_VENDOR.VENDOR_NAME,TBL_VENDOR.EMAIL,
		TBL_OPTION.OPTION_VALUE AS REGION_NAME
		FROM TBL_LEASE_ONBOARDING
		LEFT JOIN TBL_OPTION ON TBL_OPTION.OPTION_ID=TBL_LEASE_ONBOARDING.REGION
		LEFT JOIN TBL_VENDOR ON TBL_LEASE_ONBOARDING.VENDOR_ID=TBL_VENDOR.ID
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
				$InputField = array('REGION','BRANCH_ID','VENDOR_ID'),
				$Keyword = array('LEASE_NAME','REGION_NAME','LEASE_TYPE','LEASE_TERM','VENDOR_NAME','EMAIL','LICENSE_NO'),
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

			$this->load->view('configuration/print_lease_onboarding',$data);
			return false;
			break;

			case 'edit':
			$this->webspice->edit_generator($TableName='TBL_LEASE_ONBOARDING', $KeyField='ID', $key, $RedirectController='configuration_controller', $RedirectFunction='lease_onboarding', $PermissionName='lease_onboarding', $StatusCheck=null, $Log='edit_lease key:'.$key);
			return false;
			break;

			case 'inactive':
			$this->webspice->action_executer($TableName='TBL_LEASE_ONBOARDING', $KeyField='ID', $key, $RedirectURL='manage_lease_onboarding', $PermissionName='manage_lease_onboarding', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='', $Log='inactive_lease key:'.$key);
			return false;
			break;

			case 'active':
			$this->webspice->action_executer($TableName='TBL_LEASE_ONBOARDING', $KeyField='ID', $key, $RedirectURL='manage_lease_onboarding', $PermissionName='manage_lease_onboarding', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='', $Log='active_lease key:'.$key);
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

		$this->webspice->log_me('viewed_lease_list'); # log

		$this->load->view('configuration/manage_lease_onboarding', $data);
	}
	
	function process_present_value($lease_id, array $rent_from, array $rent_to, array $rent_amount, array $advance_from, array $advance_to, array $advance_amount){
		$present_value = array();
		foreach($rent_from as $k=>$v){
			$month_count = $this->webspice->calculate_months_between_two_dates($v.'-01', $rent_to[$k].'-01');
			$tmp_date = $v.'-01';
			for($i=1; $i<=$month_count; $i++){
				$c_month = date("Y-m", strtotime($tmp_date));
				$present_value[$c_month]['rent'] = $rent_amount[$k];
				$tmp_date = date('Y-m-01', strtotime("+1 months", strtotime($tmp_date)));
			}
		}
		
		foreach($advance_from as $k=>$v){
			if(!$advance_amount[$k] || $advance_amount[$k]==0){ continue; } # if advance is 0 then avoid
			
			$month_count = $this->webspice->calculate_months_between_two_dates($v.'-01', $advance_to[$k].'-01');
			$tmp_date = $v.'-01';
			for($i=1; $i<=$month_count; $i++){
				$c_month = date("Y-m", strtotime($tmp_date));
				$present_value[$c_month]['advance'] = $advance_amount[$k];
				$tmp_date = date('Y-m-01', strtotime("+1 months", strtotime($tmp_date)));
			}	
		}
		#dd($present_value);	
		$effective_interest_rate = $this->db->query("SELECT OPTION_VALUE FROM TBL_OPTION WHERE GROUP_NAME='effective_interest_rate'")->row();
		$effective_interest_rate = $effective_interest_rate->OPTION_VALUE;
		if(!$effective_interest_rate){ $effective_interest_rate = 5; } # default
		$effective_interest_rate = $effective_interest_rate / 100;
		$effective_interest_rate = round(((1 - pow(1+($effective_interest_rate/12),-12))/12)*100,6); # in %
		
		$present_value_data = array();
		$sl = 0;
		$total_present_value = 0;
		$total_advance = 0;
		foreach($present_value as $k=>$v){
			$tmp_rent = isset($v['rent']) ? $v['rent'] : 0;
			$tmp_advance = isset($v['advance']) ? $v['advance'] : 0;
			$total_advance += $tmp_advance;
			$tmp_net_payment = ($tmp_rent - $tmp_advance);
			$tmp_interest_rate = round(pow(1/(1+($effective_interest_rate/100)),$sl),6); # allow 6 character precision
			$tmp_present_value = round(($tmp_net_payment * $tmp_interest_rate),6);
			$total_present_value = $total_present_value + $tmp_present_value;
			
			$present_value_data[] = array(
				"LEASE_ID" => $lease_id,
				"MONTH" => $k,
				"RENT" => $tmp_rent,
				"ADVANCE" => $tmp_advance,
				"NET_PAYMENT" => $tmp_net_payment,
				"INTEREST_RATE" => $tmp_interest_rate,
				"PRESENT_VALUE" => $tmp_present_value
			);
			
			$sl = $sl+1;
		}
		
		$depreciation = ($total_present_value + $total_advance) / $sl;
		
		$this->db->query("DELETE FROM TBL_PRESNT_VALUE WHERE LEASE_ID = ?", array($lease_id));
		$this->db->insert_batch($table_name='TBL_PRESNT_VALUE', $values=$present_value_data);
		
		# calculate libility
		$lease_libility_cf = round($total_present_value,6);
		$libility = array();
		foreach($present_value as $k=>$v){
			$tmp_rent = isset($v['rent']) ? $v['rent'] : 0;
			$tmp_advance = isset($v['advance']) ? $v['advance'] : 0;
			$tmp_lease_payment = $tmp_rent - $tmp_advance;
			$tmp_interest = (($lease_libility_cf - $tmp_lease_payment) * $effective_interest_rate); # allow 6 character precision
			$tmp_interest = $tmp_interest / 100; # in %
			$tmp_decrease_libility = $tmp_lease_payment - $tmp_interest;
			$tmp_lease_libility_cf = $lease_libility_cf - $tmp_decrease_libility;			
			
			$libility[] = array(
				"LEASE_ID" => $lease_id,
				"MONTH" => $k,
				"LEASE_LIBILITY_BF" => $lease_libility_cf,
				"LEASE_PAYMENT" => $tmp_lease_payment,
				"INTEREST" => $tmp_interest,
				"DECREASE_LIBILITY" => $tmp_decrease_libility,
				"LEASE_LIBILITY_CF" => $tmp_lease_libility_cf,
				"DEPRECIATION" => $depreciation
			);
			
			$lease_libility_cf = $tmp_lease_libility_cf;
		}
		
		$this->db->query("DELETE FROM TBL_LEASE_LIBILITY WHERE LEASE_ID = ?", array($lease_id));
		$this->db->insert_batch($table_name='TBL_LEASE_LIBILITY', $values=$libility);
		
		return true;
	}
	
	function data_migration($data=NULL){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data_batch = 50; # how much row(s) inserted once
		ini_set('MAX_EXECUTION_TIME', 300);
		$row_start_from=3;
		$col_start_from=2;
		#$this->webspice->user_verify($url_prefix.'login', $url_prefix.'vendor_uploader');
		#$this->webspice->permission_verify('vendor_uploader');

		if( !$_FILES || !$_FILES['attachment_file']['tmp_name'] ){
			$this->load->view('uploader/data_migration', $data);
			return FALSE;
		}
		
		# verify file type
		if( $_FILES['attachment_file']['tmp_name'] ){
			$this->webspice->check_file_type(array('xlsx'), 'attachment_file', $data, 'uploader/data_migration');
		}

		# load plugin
		require_once APPPATH.'libraries/xlsx_reader/simplexlsx.class.php';

		$data = new SimpleXLSX($_FILES['attachment_file']['tmp_name']);
		$sheet_names = $data->sheetNames();
		$sheet_columns = array("Monthly Rent", "Monthly Advance","Net Rent Payment", "0.0040559798626036","Present Value");
		
		foreach($sheet_names as $sk => $sv){
			$rent_from_array = $rent_to_array = $rent_amount_array = $advance_from_array = $advance_to_array = $advance_amount_array = array();
			# get branch_id
			$branch_id = $this->db->query("SELECT OPTION_ID FROM TBL_OPTION WHERE GROUP_NAME = ? AND OPTION_VALUE = ?",array('branch', $sv))->row('OPTION_ID');
			if(empty($branch_id)){
				# insert into tbl_option if new branch
				$sql = "
				INSERT INTO TBL_OPTION
				(GROUP_NAME, OPTION_VALUE, OPTION_VALUE_BANGLA, 
				CREATED_BY, CREATED_DATE, STATUS)
				VALUES
				(?, ?, ?, ?, ?, 7)";
				$this->db->query($sql,
				array('branch', $sv, $sv, $this->webspice->get_user_id(), $this->webspice->now()));
				$branch_id = $this->db->insert_id();
			}
			
			# verify file type and read accordingly
			$get_data = array();
			if( $_FILES['attachment_file']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['sap_file']['type'] == 'application/octet-stream' ){
				$get_data = $this->webspice->excelx_reader($_FILES['attachment_file']['tmp_name'], $sk, $sheet_columns, $row_start_from, $col_start_from);
			}elseif($_FILES['attachment_file']['type'] == 'text/csv' || $_FILES['attachment_file']['type'] == 'text/comma-separated-values' ||  $_FILES['attachment_file']['type'] = 'application/vnd.ms-excel'){
				$get_data = $this->webspice->csv_reader('attachment_file', $sheet_columns);
			}else{
				echo 'File Invalid!';
				exit;
			}
	
			if( !is_array($get_data) ){
				$this->webspice->message_board($get_data.'Please try again.');
				$this->webspice->force_redirect($url_prefix.'data_migration');
				return FALSE;
			}
			
			# lease on boarding 
			$lease_name = ucwords(str_replace('_',' ',$sv));
			$lease_id = $this->db->query("SELECT ID FROM TBL_LEASE_ONBOARDING WHERE BRANCH_ID = ? AND LEASE_NAME = ?",array($branch_id, $lease_name))->row('ID');
			if(empty($lease_id)){
				$sql = "
				INSERT INTO TBL_LEASE_ONBOARDING(
				BRANCH_ID, LEASE_NAME, ADDRESS, FLOOR_SPACE, 
				CONTACT_PERSON, CONTACT_MOBILE_NO, CONTACT_EMAIL, 
				CREATED_BY, CREATED_DATE, STATUS
				)
				VALUES(
				?, ?, ?, ?,
				?, ?, ?,
				?, ?, ?
				)
				";

				$this->db->query($sql,
				array(
				$branch_id, $lease_name, $sv, 'NA',
				'NA', 'NA', 'NA',
				$this->webspice->get_user_id(), $this->webspice->now('datetime24'), 7
				));
			
			$lease_id = $this->db->insert_id();
			}
			
			# monthly rent process
			$date = '2019-00';
			$i=1;
			$data_count=1;
			$temp_rent = 0;
			$date_from = $last_from =$date_to = '';
			
			foreach($get_data as $k=>$v){
				$monthly_rent = $v[0];
				$rent_with_tax = $monthly_rent;
				$date_from = date('Y-m',strtotime($date. ' +'.$i.' month'));
				$data_count++;
				$i++;
				if($monthly_rent=='' OR !$monthly_rent){
					if($temp_rent !=0 && $temp_rent !=""){
						$date_from = $last_from;
					}
					continue;
				}
				$last_from = $date_from;
				$monthly_rent = ($rent_with_tax*5)/100;
				$monthly_rent = $rent_with_tax-$monthly_rent;
				$rent_with_vat = ($monthly_rent*15)/100;
				$rent_with_vat +=$monthly_rent;
				if($temp_rent == 0){
					$rent_from_array[] = $date_from;
					$rent_amount_array[] = $rent_with_tax;
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($lease_id, $date_from, $date_from, 'rent', $monthly_rent, $rent_with_tax, $rent_with_vat)
					);
					$agreement_id = $this->db->insert_id();
				}
				if($temp_rent != 0 && (string)$temp_rent != (string)$monthly_rent){
					# update last inserted agreement record 
					$date_to = date('Y-m',strtotime($date_from. ' -1 month'));
					$rent_to_array[] = $date_to;
					$this->db->query("UPDATE TBL_LEASE_AGREEMENT SET DATE_TO=? WHERE ID=?",array($date_to, $agreement_id));
					
					$rent_from_array[] = $date_from;
					$rent_amount_array[] = $rent_with_tax;
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($lease_id, $date_from, $date_from, 'rent', $monthly_rent, $rent_with_tax, $rent_with_vat)
					);
					$agreement_id = $this->db->insert_id();
				}
				$temp_rent = $v[0];
			}
			# update last inserted agreement record 
			$rent_to_array[] = $date_from;
			$this->db->query("UPDATE TBL_LEASE_AGREEMENT SET DATE_TO=? WHERE ID=?",array($date_from, $agreement_id));		
			
			# monthly advance process 
			$date = '2019-00';
			$date_from = $last_from = $date_to = '';
			$temp_advance = 0;
			$i=1;$data_count=1;
			
			foreach($get_data as $k=>$v){
				$monthly_advance = $v[1];
				$advance_with_tax = $monthly_advance;
				$date_from = date('Y-m',strtotime($date. ' +'.$i.' month'));
				$i++;$data_count++;
				if($monthly_advance=='' OR !$monthly_advance){
					if($temp_advance !=0 && $temp_advance !=""){
						$date_from = $last_from;
					}
					continue;
				}
				$last_from = $date_from;
				$monthly_advance = ($advance_with_tax*5)/100;
				$monthly_advance = $advance_with_tax-$monthly_advance;
				$advance_with_vat = ($monthly_advance*15)/100;
				$advance_with_vat +=$monthly_advance;
				
				if($temp_advance == 0){
					$advance_from_array[] = $date_from;
					$advance_amount_array[] = $advance_with_tax;
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($lease_id, $date_from, $date_from, 'advance', $monthly_advance, $advance_with_tax, $advance_with_vat)
					);
					$agreement_id = $this->db->insert_id();
				}
				if($temp_advance != 0 && (string)$temp_advance != (string)$monthly_advance){
					# update last inserted agreement record 
					$date_to = date('Y-m',strtotime($date_from. ' -1 month'));
					$advance_to_array[] = $date_to;
					$this->db->query("UPDATE TBL_LEASE_AGREEMENT SET DATE_TO=? WHERE ID=?",array($date_to,$agreement_id));
					
					$advance_from_array[] = $date_from;
					$advance_amount_array[] = $advance_with_tax;
					$this->db->query("
					INSERT INTO TBL_LEASE_AGREEMENT 
					(LEASE_ID, DATE_FROM, DATE_TO, TYPE, AMOUNT, AMOUNT_WITH_TAX, AMOUNT_WITH_VAT) 
					VALUES
					(?, ?, ?, ?, ?, ?, ?)",
					array($lease_id, $date_from, $date_from, 'advance', $monthly_advance, $advance_with_tax, $advance_with_vat)
					);
					$agreement_id = $this->db->insert_id();
				}
				$temp_advance = $v[1];
			}
			# update last inserted agreement record 
			$advance_to_array[] = $date_from;
			$this->db->query("UPDATE TBL_LEASE_AGREEMENT SET DATE_TO=? WHERE ID=?",array($date_from, $agreement_id));		
		

			# process data and store in table for faster access for report
			$response = $this->process_present_value($lease_id=$lease_id, $rent_from=$rent_from_array, $rent_to=$rent_to_array, $rent_amount=$rent_amount_array, $advance_from=$advance_from_array, $advance_to=$advance_to_array, $advance_amount=$advance_amount_array);
			
			#$this->webspice->log_me($lease_id.'_data_processed'); # log
			/*dd($rent_from_array,8);
			dd($rent_to_array,8);
			dd($rent_amount_array,8);
			dd($advance_from_array,8);
			dd($advance_to_array,8);
			dd($advance_amount_array);*/
		}
		if($response){
			$this->webspice->message_board('Record has been inserted successfully.');
		}
		
		$this->webspice->force_redirect($url_prefix);
	}
	
	function data_migration_additional_info($data=NULL){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data_batch = 50; # how much row(s) inserted once
		set_time_limit(0);
		$row_start_from=1;
		$col_start_from=1;
		#$this->webspice->user_verify($url_prefix.'login', $url_prefix.'vendor_uploader');
		#$this->webspice->permission_verify('vendor_uploader');

		if( !$_FILES || !$_FILES['attachment_file']['tmp_name'] ){
			$this->load->view('uploader/data_migration', $data);
			return FALSE;
		}
		
		# verify file type
		if( $_FILES['attachment_file']['tmp_name'] ){
			$this->webspice->check_file_type(array('xlsx'), 'attachment_file', $data, 'uploader/data_migration');
		}
		
		$sheet_columns = array('Sl. No.','Branch Name','License No & Issuing Date','Branch Opening Date','Address','Name of the City Corporation/Pouoshova/union)','Thana/ Upazila','District','Type (urban /Rural) ','Floor Space(sft)','Rent        (Tk.per Sqft)','Total Amount (In Crore)','No of Customers','Total Amount (In Crore)','No of Customers','Information of Profit/(Loss) (in crore)','Comment (if any)');
		#require_once APPPATH.'libraries/xlsx_reader/simplexlsx.class.php';
		#$data = new SimpleXLSX($_FILES['attachment_file']['tmp_name']);
		#dd($data->rows(1));
		# verify file type and read accordingly
		$get_data = array();
		if( $_FILES['attachment_file']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['sap_file']['type'] == 'application/octet-stream' ){
			$get_data = $this->webspice->excelx_reader($_FILES['attachment_file']['tmp_name'], 1, $sheet_columns, $row_start_from, $col_start_from);
		}elseif($_FILES['attachment_file']['type'] == 'text/csv' || $_FILES['attachment_file']['type'] == 'text/comma-separated-values' ||  $_FILES['attachment_file']['type'] = 'application/vnd.ms-excel'){
			$get_data = $this->webspice->csv_reader('attachment_file', $sheet_columns);
		}else{
			echo 'File Invalid!';
			exit;
		}
		
		if(!$get_data OR count($get_data)==0)
		{
			$this->webspice->message_board($get_data.'Please try again.');
			$this->webspice->force_redirect($url_prefix.'data_migration_additional_info');
			return FALSE;
		}
		
		# verify data
		$data_error =  NULL;
		$values = array();
		$single_values = array();
		$writeoff_value = $input->write_off_id;
		foreach($get_data as $k=> $v)
		{
			$k=$k+1;
			$data_list = $v;
			$branch_name =  $this->webspice->clean_input($data_list[1]);
			$branch_origin =  $this->webspice->clean_input($data_list[1]);
			#if($branch_name !='Bangshal Branch'){continue;}
			$license_issue_date = $this->webspice->clean_input($data_list[2]);
			$license = trim(explode("DT",strtoupper($license_issue_date))[0]);
			$issue_date = explode("DT",strtoupper($license_issue_date))[1];
			$issue_date = date('Y-m-d',strtotime(trim($issue_date)));
			
			$branch_opening_date = $this->webspice->clean_input($data_list[3]);
			$branch_opening_date = $branch_opening_date ? $this->webspice->date_excel_to_real($branch_opening_date) : NULL;
			$address = $this->webspice->clean_input($data_list[4]);
			$city_union_corp_name = $this->webspice->clean_input($data_list[5]);
			$thana = $this->webspice->clean_input($data_list[6]);
			$district = $this->webspice->clean_input($data_list[7]);
			$type = strtolower($this->webspice->clean_input($data_list[8]));
			$floor = $this->webspice->clean_input($data_list[9]);
			$rent = $this->webspice->clean_input($data_list[10]);
			$rent = isset($rent) && is_numeric($rent) ? $rent : 0; 
			$total_amount_loan = $this->webspice->clean_input($data_list[11]);
			$total_amount_loan = isset($total_amount_loan) && is_numeric($total_amount_loan) ? $total_amount_loan : 0; 
			$no_of_customer_loan = $this->webspice->clean_input($data_list[12]);
			$no_of_customer_loan = isset($no_of_customer_loan) && is_numeric($no_of_customer_loan) ? $no_of_customer_loan : 0; 
			$total_amount_deposit = $this->webspice->clean_input($data_list[13]);
			$total_amount_deposit = isset($total_amount_deposit) && is_numeric($total_amount_deposit) ? $total_amount_deposit : 0; 
			$no_of_customer_deposit = $this->webspice->clean_input($data_list[14]);
			$no_of_customer_deposit = isset($no_of_customer_deposit) && is_numeric($no_of_customer_deposit) ? $no_of_customer_deposit : 0; 
			$profit_loss = $this->webspice->clean_input($data_list[15]);
			$profit_loss = isset($profit_loss) && is_numeric($profit_loss) ? $profit_loss : 0; 
			
			$branch_name = strtolower(trim(explode('Branch',$branch_name)[0]));
			
			$least_id = $this->db->query("SELECT ID FROM tbl_lease_onboarding WHERE LEASE_NAME LIKE '%$branch_name%'")->row('ID');
			if(empty($least_id)){
				# get branch_id
				$branch_id = $this->db->query("SELECT OPTION_ID FROM TBL_OPTION WHERE GROUP_NAME = ? AND OPTION_VALUE = ?",array('branch', $branch_origin))->row('OPTION_ID');
				if(empty($branch_id)){
					# insert into tbl_option if new branch
					$sql = "
					INSERT INTO TBL_OPTION
					(GROUP_NAME, OPTION_VALUE, OPTION_VALUE_BANGLA, 
					CREATED_BY, CREATED_DATE, STATUS)
					VALUES
					(?, ?, ?, ?, ?, 7)";
					$this->db->query($sql,
					array('branch', $branch_origin, $branch_origin, $this->webspice->get_user_id(), $this->webspice->now()));
					$branch_id = $this->db->insert_id();
				}
				
				$sql = "
				INSERT INTO TBL_LEASE_ONBOARDING(
				BRANCH_ID, LEASE_NAME, ADDRESS, FLOOR_SPACE, 
				CONTACT_PERSON, CONTACT_MOBILE_NO, CONTACT_EMAIL, 
				CREATED_BY, CREATED_DATE, STATUS
				)
				VALUES(
				?, ?, ?, ?,
				?, ?, ?,
				?, ?, ?
				)
				";

				$this->db->query($sql,
				array(
				$branch_id, $branch_origin, $branch_origin, 'NA',
				'NA', 'NA', 'NA',
				$this->webspice->get_user_id(), $this->webspice->now('datetime24'), 7
				));
			
			$lease_id = $this->db->insert_id();
				
				
			}
			
			$sql = "
			UPDATE tbl_lease_onboarding SET LICENSE_NO=?, LICENSE_ISSUE_DATE=?, BRANCH_OPENING_DATE=?, ADDRESS=?, CITY=?, 
			THANA_UPAZILLA=?, DISTRICT=?, TYPE=?, FLOOR_SPACE=?, RENT_PER_SQFT=?, 
			TOTAL_AMOUINT_LOAN=?, NO_OF_CUSTOMER_LOAN=?, TOTAL_AMOUNT_DEPOSIT=?, NO_OF_CUSTOMER_DEPOSIT=?, PROFIT_LOSS=?,
			UPDATED_BY=?, UPDATED_DATE=?
			WHERE ID=?";
			$this->db->query($sql, array(
			$license, $issue_date, $branch_opening_date, $address, $city_union_corp_name, 
			$thana, $district, $type, $floor, $rent, 
			$total_amount_loan, $no_of_customer_loan, $total_amount_deposit, $no_of_customer_deposit, $profit_loss,
			$this->webspice->get_user_id(), $this->webspice->now('datetime24'),
			$least_id));
			#dd($this->db->last_query());
		}
		
		if($data_error){
			$data_error = implode(", ",$data_error);
			$this->webspice->message_board($data_error. ' Record found has been new which are not been processed.');
			$this->webspice->force_redirect($url_prefix);
			return FALSE;
		}
		
		$this->webspice->message_board('Record has been inserted successfully.');
		#$this->webspice->log_me('data_migration_additional_info'); # log
		$this->webspice->force_redirect($url_prefix);
	
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