<?php
class CustomCache{
	
	# starts session
	function CustomCache(){
		if(!isset($_SESSION)){
			session_start();
		}
	}

	# get user information by user id
	function user_maker($user_id, $output_filed){
		# $output_filed - get db field name

		$CI =&get_instance();
		$cache_name = 'user_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		$html = null;
		
		if( !$html = $CI->cache->get($cache_name, 'user') ){
			$html = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_USER ORDER BY USER_ID DESC");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->USER_ID.'|'.$v->ROLE_ID.'|'.$v->USER_NAME.'|'.$v->USER_EMAIL.'|'.$v->CREATED_DATE.'|'.
									$v->UPDATED_DATE.'|'.$v->STATUS.'|'.$v->EMPLOYEE_ID;
			}

			$CI->cache->save($cache_name, $html, 'user', 604800);		
		}
		
		if( !$html ){ $html = array(); }
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[0]==$user_id ){
				switch($output_filed){
					case 'USER_ID': return $Value[0]; break;
					case 'ROLE_ID': return $Value[1]; break;
					case 'USER_NAME': return $Value[2]; break;
					case 'USER_EMAIL': return $Value[3]; break;
					case 'CREATED_DATE': return $Value[4]; break;
					case 'UPDATED_DATE': return $Value[5]; break;
					case 'STATUS': return $Value[6]; break;
					case 'EMPLOYEE_ID': return $Value[7]; break;
				}
				
			}

		}

		return false;
	}

	function get_user_list($type='option')
	{
		# type = option/option_mix/option_value/list
		$CI =&get_instance();
		$group_name = 'user';
		$cache_name = 'user_list';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');

    	#$CI->cache->remove_group('user');
		$type == 'option' ? $cache_name = $cache_name.'user_list' : $cache_name = $cache_name;
		$type == 'option_value' ? $cache_name = $cache_name.'user_list_value' : $cache_name = $cache_name;
		$type == 'option_mix' ? $cache_name = 'user_list_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = 'user_list_list' : $cache_name = $cache_name;
		
		if(!$data['html'] = $CI->cache->get($cache_name, $group_name))
		{
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("
			SELECT *
			FROM TBL_USER
			WHERE STATUS=7 
			ORDER BY USER_NAME
			");
			$get_record = $get_record->result();
			foreach( $get_record as $k=>$v )
			{
				switch($type)
				{			
				case 'option':
						$data['html'] .= '<option value="'.$CI->webspice->encrypt_decrypt($v->USER_ID,'encrypt').'">'.ucwords($v->USER_NAME).' &raquo; '.$v->USER_EMAIL.'</option>';
						break;
					case 'option_value':
						$data['html'] .= '<option value="'.$v->USER_NAME.'">'.ucwords($v->USER_NAME).' &raquo; '.$v->USER_EMAIL.'</option>';
						break;
					case 'option_mix':
						//$data['html'] .= '<option value="'.$CI->webspice->encrypt_decrypt($v->USER_ID,'encrypt').'|'.$v->USER_NAME.'">'.ucwords($v->USER_NAME).'</option>';
						$data['html'] .= '<option value="'.$v->USER_ID.'|'.$v->USER_NAME.'">'.ucwords($v->USER_NAME).' &raquo; '.$v->USER_EMAIL.'</option>';
						break;
					case 'list':
					$data['html'] .= '<li class="list_item" data-id="'.$CI->webspice->encrypt_decrypt($v->USER_ID,'encrypt').'">'.ucwords($v->USER_NAME).' &raquo; '.$v->USER_EMAIL.'</li>';
						break;
					case 'option_edit':
						$data['html'] .= '<option value="'.$v->USER_ID.'">'.ucwords($v->USER_NAME).' &raquo; '.$v->USER_EMAIL.'</option>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		return $data['html'];
	}

	function get_vendor_list($type='option')
	{
		# type = option/option_mix/option_value/list
		$CI =&get_instance();
		$group_name = 'vendor';
		$cache_name = 'vendor_list';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');

    	#$CI->cache->remove_group('user');
		$type == 'option' ? $cache_name = $cache_name.'_option' : $cache_name = $cache_name;
		$type == 'option_value' ? $cache_name = $cache_name.'_option_value' : $cache_name = $cache_name;
		$type == 'option_mix' ? $cache_name = '_option_list_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = '_list' : $cache_name = $cache_name;
		
		if(!$data['html'] = $CI->cache->get($cache_name, $group_name))
		{
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM `tbl_vendor` WHERE STATUS=7 ORDER BY VENDOR_NAME");
			$get_record = $get_record->result();
			foreach( $get_record as $k=>$v )
			{
				switch($type)
				{			
				case 'option':
						$data['html'] .= '<option value="'.$CI->webspice->encrypt_decrypt($v->ID,'encrypt').'">'.ucwords($v->VENDOR_NAME).' &raquo; '.$v->EMAIL.'</option>';
						break;
					case 'option_value':
						$data['html'] .= '<option value="'.$v->ID.'">'.ucwords($v->VENDOR_NAME).' &raquo; '.$v->EMAIL.'</option>';
						break;
					case 'option_mix':
						//$data['html'] .= '<option value="'.$CI->webspice->encrypt_decrypt($v->USER_ID,'encrypt').'|'.$v->USER_NAME.'">'.ucwords($v->USER_NAME).'</option>';
						$data['html'] .= '<option value="'.$v->ID.'|'.$v->VENDOR_NAME.'">'.ucwords($v->VENDOR_NAME).' &raquo; '.$v->EMAIL.'</option>';
						break;
					case 'list':
					$data['html'] .= '<li class="list_item" data-id="'.$CI->webspice->encrypt_decrypt($v->ID,'encrypt').'">'.ucwords($v->VENDOR_NAME).' &raquo; '.$v->EMAIL.'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		return $data['html'];
	}
	

	# get user role
	function get_user_role($type='option'){
		# type = option/option_mix/option_value/list
		$CI =&get_instance();
		$group_name = 'role';
		$cache_name = 'user_role_option';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option' ? $cache_name = $cache_name.'_option' : $cache_name = $cache_name;
		$type == 'option_value' ? $cache_name = $cache_name.'_option_value' : $cache_name = $cache_name;
		$type == 'option_mix' ? $cache_name = '_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = '_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_ROLE WHERE STATUS=7 ORDER BY ROLE_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->ROLE_ID.'">'.ucwords($v->ROLE_NAME).'</option>';
						break;
					case 'option_value':
						$data['html'] .= '<option value="'.$v->ROLE_NAME.'">'.ucwords($v->ROLE_NAME).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->ROLE_ID.'|'.$v->ROLE_NAME.'">'.ucwords($v->ROLE_NAME).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->ROLE_ID.'">'.ucwords($v->ROLE_NAME).'</li>';
						break;
				}
			}
			
			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		return $data['html'];
	}
	
	# get user branch
	function get_user_branch($type='option'){
		# type = option/option_mix/option_value/list
		$CI =&get_instance();
		$group_name = 'branch';
		$cache_name = 'user_branch_option';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option' ? $cache_name = $cache_name.'_option' : $cache_name = $cache_name;
		$type == 'option_value' ? $cache_name = $cache_name.'_option_value' : $cache_name = $cache_name;
		$type == 'option_mix' ? $cache_name = '_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = '_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_OPTION WHERE GROUP_NAME='branch' AND STATUS=7 ORDER BY OPTION_VALUE");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->OPTION_ID.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'option_value':
						$data['html'] .= '<option value="'.$v->OPTION_VALUE.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->OPTION_ID.'|'.$v->OPTION_VALUE.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->OPTION_ID.'">'.ucwords($v->OPTION_VALUE).'</li>';
						break;
				}
			}
			
			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		return $data['html'];
	}

	function get_region($type='option'){
		# type = option/option_mix/option_value/list
		$CI =&get_instance();
		$group_name = 'region';
		$cache_name = 'region_option';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option' ? $cache_name = $cache_name.'_option' : $cache_name = $cache_name;
		$type == 'option_value' ? $cache_name = $cache_name.'_option_value' : $cache_name = $cache_name;
		$type == 'option_mix' ? $cache_name = '_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = '_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_OPTION WHERE GROUP_NAME='region' AND STATUS=7 ORDER BY OPTION_VALUE");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->OPTION_ID.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'option_value':
						$data['html'] .= '<option value="'.$v->OPTION_VALUE.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->OPTION_ID.'|'.$v->OPTION_VALUE.'">'.ucwords($v->OPTION_VALUE).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->OPTION_ID.'">'.ucwords($v->OPTION_VALUE).'</li>';
						break;
				}
			}
			
			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		return $data['html'];
	}

	# get information by option id
	function option_maker($option_id, $output_filed){
		# $output_filed - get db field name

		$CI =&get_instance();
		$cache_name = 'option_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		$html = null;
		
		if( !$html = $CI->cache->get($cache_name, 'option') ){
			$html = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_OPTION WHERE STATUS=7 ORDER BY OPTION_ID DESC");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->OPTION_ID.'|'.$v->PARENT_ID.'|'.$v->GROUP_NAME.'|'.$v->OPTION_VALUE.'|'.$v->OPTION_VALUE_BANGLA.'|'.$v->OPTION_VALUE_2.'|'.$v->OPTION_VALUE_2_BANGLA;
			}

			$CI->cache->save($cache_name, $html, 'option', 604800);		
		}
		
		if( !$html ){ $html = array(); }
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[0]==$option_id ){
				switch($output_filed){
					case 'OPTION_ID': return $Value[0]; break;
					case 'PARENT_ID': return $Value[1]; break;
					case 'GROUP_NAME': return $Value[2]; break;
					case 'OPTION_VALUE': return $Value[3]; break;
					case 'OPTION_VALUE_BANGLA': return $Value[4]; break;
					case 'OPTION_VALUE_2': return $Value[5]; break;
					case 'OPTION_VALUE_2_BANGLA': return $Value[6]; break;
				}
			}
		}
		
		return false;
	}

	#Get configuration maker 
	function configuration_maker($configuration_name, $output_filed){
		# $output_filed - get db field name

		$CI =&get_instance();
		$cache_name = 'configuration_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		$html = null;
		
		if( !$html = $CI->cache->get($cache_name, 'configuration') ){
			$html = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_CONFIGURATION ORDER BY ID DESC");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->ID.'|'.$v->CONFIGURATION_NAME.'|'.$v->VALUE_TYPE.'|'.$v->VALUE.'|'.$v->REFERENCE.'|'.$v->STATUS;
			}

			$CI->cache->save($cache_name, $html, 'configuration', 604800);		
		}
		
		if( !$html ){ $html = array(); }
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[1]==$configuration_name ){
				switch($output_filed){
					case 'ID': return $Value[0]; break;
					case 'CONFIGURATION_NAME': return $Value[1]; break;
					case 'VALUE_TYPE': return $Value[2]; break;
					case 'VALUE': return $Value[3]; break;
					case 'REFERENCE': return $Value[4]; break;
					case 'STATUS': return $Value[5]; break;
				}
				
			}

		}

		return false;
	}
	
}