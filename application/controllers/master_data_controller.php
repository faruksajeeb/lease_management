<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_data_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
	}


    


    # master data creation
	function create_vendor($data=null){
		
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_vendor');
		$this->webspice->permission_verify('create_vendor,manage_vendor');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ID'=>null,
				'VENDOR_NAME'=>null,
				'ADDRESS'=>null,
				'PHONE'=>null,
				'EMAIL'=>null,
				'CONTACT_PERSON'=>null,
				'CONTACT_PERSON_NUMBER'=>null,
				'VAT_REG_NO'=>null,
				'TRADE_LICENCE_NO'=>null,
				'TIN_NO'=>null,
				'BUSINESS_EXPERIENCE'=>null,
				'CORPORATE_STATUS'=>null,
				'STATUS'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('VENDOR_NAME','Vendor Name','required|trim');
		$this->form_validation->set_rules('ADDRESS','Address','required|trim');
		$this->form_validation->set_rules('PHONE','Phone','required|trim|min_length[11]');
		$this->form_validation->set_rules('EMAIL','E-mail','required|trim');
		$this->form_validation->set_rules('CONTACT_PERSON','Contact Person','required|trim');
		$this->form_validation->set_rules('CONTACT_PERSON_NUMBER','Contact Person Number','required|trim|min_length[11]');
		$this->form_validation->set_rules('VAT_REG_NO','VAT Reg. No.','trim');
		$this->form_validation->set_rules('TRADE_LICENCE_NO','Trade Licence No.','trim');
		$this->form_validation->set_rules('TIN_NO','TIN No.','trim');
		$this->form_validation->set_rules('BUSINESS_EXPERIENCE','Business Experience','trim');
		$this->form_validation->set_rules('CORPORATE_STATUS','Corporate Status','trim');

		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}

			$this->load->view('configuration/create_vendor', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('key');

		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM TBL_VENDOR WHERE VENDOR_NAME=? AND 
		(PHONE=? OR EMAIL=?)", array($input->VENDOR_NAME, $input->PHONE, $input->EMAIL), 'You are not allowed to enter duplicate vendor value', 'ID', $input->key, $data, 'configuration/create_vendor');

		# remove cache
		$this->webspice->remove_cache('vendor');

		# update process
		if( $input->key ){
			$sql = "
			UPDATE TBL_VENDOR SET VENDOR_NAME=?, ADDRESS=?, PHONE=?, EMAIL=?, CONTACT_PERSON=?, CONTACT_PERSON_NUMBER=?, VAT_REG_NO=?, TRADE_LICENCE_NO=?, TIN_NO=?, BUSINESS_EXPERIENCE=?, CORPORATE_STATUS=?, 
			UPDATED_BY=?, UPDATED_DATE=?
			WHERE ID=?";
			$this->db->query($sql,
			array($input->VENDOR_NAME, $input->ADDRESS, $input->PHONE, $input->EMAIL, $input->CONTACT_PERSON, $input->CONTACT_PERSON_NUMBER, $input->VAT_REG_NO, $input->TRADE_LICENCE_NO, $input->TIN_NO, $input->BUSINESS_EXPERIENCE, $input->CORPORATE_STATUS,
			$this->webspice->get_user_id(), $this->webspice->now(), $input->key));

			$this->webspice->log_me('vendor_updated - '.$input->key); # log activities
			exit('update_success');
		}

		# insert data
		$sql = "
		INSERT INTO TBL_VENDOR
		(VENDOR_NAME, ADDRESS, PHONE, EMAIL, CONTACT_PERSON, CONTACT_PERSON_NUMBER,VAT_REG_NO,TRADE_LICENCE_NO, TIN_NO, BUSINESS_EXPERIENCE, CORPORATE_STATUS,
		CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		(?, ?, ?, ?, ?,?,?,
		?,?,?,?,
		?, ?, 7)";

		$this->db->query($sql,
		array($input->VENDOR_NAME, $input->ADDRESS, $input->PHONE, $input->EMAIL, $input->CONTACT_PERSON, $input->CONTACT_PERSON_NUMBER, $input->VAT_REG_NO, $input->TRADE_LICENCE_NO, $input->TIN_NO, $input->BUSINESS_EXPERIENCE, $input->CORPORATE_STATUS,
		$this->webspice->get_user_id(), $this->webspice->now()));

		if( !$insert_id = $this->db->insert_id() ){
			exit('We could not execute your request. Please tray again later or report to authority.');
		}

		$this->webspice->log_me('created_vendor'); # log
		exit('insert_success');
	}


    # master data management
	function manage_vendor(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_vendor');
		$this->webspice->permission_verify('manage_vendor');

		$this->load->database();
		$orderby = ' ORDER BY ID DESC, TBL_VENDOR.VENDOR_NAME, TBL_VENDOR.CONTACT_PERSON';
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
		SELECT TBL_VENDOR.*
		FROM TBL_VENDOR
		";

		$countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM TBL_VENDOR
		";

   		# filtering records
		if( $this->input->get('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'TBL_VENDOR',
				$InputField = array('VENDOR_NAME'),
				$Keyword = array('VENDOR_NAME','ADDRESS','PHONE','EMAIL','CONTACT_PERSON','CONTACT_PERSON_NUMBER','VAT_REG_NO','TRADE_LICENCE_NO','TIN_NO','BUSINESS_EXPERIENCE','CORPORATE_STATUS'),
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

			$this->load->view('configuration/print_vendor',$data);
			return false;
			break;

			case 'edit':
			$this->webspice->edit_generator($TableName='TBL_VENDOR', $KeyField='ID', $key, $RedirectController='master_data_controller', $RedirectFunction='create_vendor', $PermissionName='create_vendor,manage_vendor', $StatusCheck=null, $Log='edit_vendor');
			return false;
			break;

			case 'inactive':
			$this->webspice->action_executer($TableName='TBL_VENDOR', $KeyField='ID', $key, $RedirectURL='manage_vendor', $PermissionName='manage_vendor', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='vendor', $Log='inactive_vendor');
			return false;
			break;

			case 'active':
			$this->webspice->action_executer($TableName='TBL_VENDOR', $KeyField='ID', $key, $RedirectURL='manage_vendor', $PermissionName='manage_vendor', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='vendor', $Log='active_vendor');
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
			$data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $url_prefix.'manage_vendor/page/', 10 );
		}
		# end: only for pager -- mysql

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->webspice->log_me('viewed_vendor_list'); # log

		$this->load->view('configuration/manage_vendor', $data);
	}
}