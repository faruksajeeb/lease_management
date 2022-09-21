<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parent_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	
	/*
	>> Error log should be added prefix Error:
	Log Prefix:
	login_attempt - Login Ateempt
	login_success
	unauthorized_access
	password_retrieve_request
	password_changed
	*/

	function test(){	
		#$this->load->library('uber');
		#$this->uber->login();
	}
	
	
	function file_download(){
		$path = $this->webspice->get_path( $this->uri->segment(2) );
		$name = $this->uri->segment(3);
		$file = $path.$name;
		$file = file_get_contents($file);
		
		$this->load->helper('download');
		force_download($file_name=$name, $data=$file);
	}
	

	
	function index(){ 
		$url_prefix = $this->webspice->settings()->site_url_prefix;

		$this->webspice->user_verify($url_prefix.'login', $url_prefix);
		$data = null;
		

		$this->load->view('index', $data);
	}
	

	function login(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data = null;
		$callback = $url_prefix;
		
		# verify user logged or not
		if( $this->webspice->get_user_id() ){
			$this->webspice->message_board('Dear '.$this->webspice->get_user("USER_NAME").', you are already Logged In. Thank you.');
			$this->webspice->force_redirect($url_prefix);
			return false;
		}
 
		if( $this->webspice->login_callback(null,'get') ){ 
			$callback = $this->webspice->login_callback(null,'get');
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('user_email','user_email','required|trim');
		$this->form_validation->set_rules('user_password','user_password','required|trim');
		
		if( !$this->form_validation->run() ){
			$this->load->view('login', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input($key = null);

		# more than 5 attempts - lock the last email address with remarks
		if( !isset($_SESSION['auth']['attempt']) ){
			$_SESSION['auth']['attempt'] = 1;
			
		}else{
			$_SESSION['auth']['attempt']++;
			
			if( $_SESSION['auth']['attempt'] >50 ){
				$data['title'] = 'Warning!';
				$data['body'] = 'We have identified that; you are trying to access this application illegally. Please stop the process immediately. We like to remind you that; we are tracing your IP address. So, if you try again, we will bound to take a legal action against you.';
				$data['footer'] = $this->webspice->settings()->site_title.' Authority';
				
				# $this->db->query("UPDATE user SET STATUS=-3, remarks=? WHERE user_email=? AND user_role!=1 LIMIT 1", array('Illegal Attempt ('.$this->webspice->now().'): '.$this->webspice->who_is() , $login_email));
				
				# log
				$this->webspice->log_me('illegal_attempt~'.$this->webspice->who_is().'~'.$input->user_email);
				$this->confirmation($data);
				return false;
			}
		}

		$user = $this->db->query("
		SELECT TBL_USER.*, TBL_USER.LOGIN_UPDATE_TIME AS LOGIN_TIME,
		TBL_ROLE.PERMISSION_NAME 
		FROM TBL_USER
		LEFT JOIN TBL_ROLE ON TBL_ROLE.ROLE_ID=TBL_USER.ROLE_ID
		WHERE TBL_USER.USER_EMAIL = ?",
		array($input->user_email));
		$user = $user->result_array();
		
		if( !$user ){
			$this->webspice->log_me('unauthorized_access: '.$input->user_email); # log
		
			$this->webspice->message_board('User ID or password is incorrect. Please try again.');
			$this->webspice->force_redirect($url_prefix.'login');
			return false;
		}
		
		# password verify
		$user_password = $this->webspice->encrypt_decrypt($user[0]['PASSWORD'], 'decrypt');
		if( $user_password != $input->user_password ){
			$this->webspice->log_me('unauthorized_access: '.$input->user_email); # log
		
			$this->webspice->message_board('User ID or password is incorrect. Please try again.');
			$this->webspice->force_redirect($url_prefix.'login');
			return false;
		}

		#check new user
		if( $user[0]['STATUS'] < 1 ){
			$this->webspice->message_board('Your account is temporarily inactive! Please contact with authority.');
			$this->webspice->force_redirect($url_prefix);
			return false;
			
		}else if( $user[0]['STATUS'] == 6 ){
			$this->webspice->message_board('You must verify your Email Address. We sent you a verification email. Please check your email inbox/spam folder.');
			$this->webspice->force_redirect($url_prefix);
			return false;
			 
		}else if( $user[0]['STATUS'] == 8 ){
			$verification_code = $this->webspice->encrypt_decrypt($user[0]['USER_EMAIL'].'|'.date("Y-m-d"), 'encrypt');
			$verification_code = str_replace('=','',$verification_code);
			$this->webspice->message_board('You must change your password.');
			$this->webspice->force_redirect($url_prefix.'change_password/'.$verification_code);
			return false;
			
		}else if( $user[0]['IS_LOGGED'] == 1 && 10 > $this->webspice->calculate_minutes_between_two_dates($this->webspice->now(), date("Y-m-d h:i:s", strtotime($user[0]['LOGIN_TIME']))) ){
			$this->webspice->message_board('The user has already Logged In!');
			$this->webspice->force_redirect($url_prefix);
			return false;
		}

		# verify password policy
		#$this->verify_password_policy($user[0], 'login');

		# create user session
		$this->db->query("UPDATE TBL_USER SET IS_LOGGED=1, SESSION_ID=(SESSION_ID+1), LOGIN_UPDATE_TIME=now() WHERE USER_EMAIL=?", array($user[0]['USER_EMAIL']));
		
		$this->webspice->create_user_session($user[0]);
		$_SESSION['auth']['attempt'] = 0;
		$this->webspice->message_board('Welcome to '.$this->webspice->settings()->domain_name.': '.$this->webspice->settings()->site_slogan);
		
		# log
		$this->webspice->log_me('login_success: '.$input->user_email);
		
		#redirect to admin index
		$this->webspice->force_redirect($url_prefix);
	}
	
	function forgot_password($data=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->load->database();
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('user_email','user_email','required|valid_email|trim');
		
		if( !$this->form_validation->run() ){
			$this->load->view('login', $data);
			return FALSE;
		}
		
		$input = $this->webspice->get_input();
		
		$get_record = $this->db->query("SELECT * FROM TBL_USER WHERE USER_EMAIL=?", array($input->user_email));
		$get_record = $get_record->result();
		if( !$get_record ){
			$this->webspice->message_board('The email address you entered is invalid! Please enter your email address.');
			$this->load->view('login', $data);
			return false;
		}
		
		$get_record = $get_record[0];

		$this->load->library('email_template');
		$this->email_template->send_retrieve_password_email1($get_record->USER_ID, $get_record->USER_NAME, $get_record->USER_EMAIL);
		
		$data['title'] = 'Request Accepted!!';
		$data['body'] = 'Your request has been accepted! The system sent you an email with a link. Please check your email Inbox or Spam folder. Using the link, you can reset your Password. <br /><br />Please note that; the link will <strong>valid only for following 3 days</strong>. So, please use the link before it will being useless.';
		$data['footer'] = $this->webspice->settings()->site_title.' Authority';
		
		# log
		$this->webspice->log_me('password_retrieve_request - '.$get_record->USER_EMAIL);
			
		$this->confirmation($data);

	}
	
	function change_password($param_user_id=null){		
		# $param_user_id -> when user's password has been expired
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$user_id = null;
		$data = null;
		$this->load->database();

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('new_password','new_password','required|trim');
		$this->form_validation->set_rules('repeat_password','repeat_password','required|trim');

		# verify access request through 'Forgot Password' - email URL
		$get_uri = $this->input->post('user_info');
		if( !$this->input->post('user_info') ){
			$get_uri = $this->uri->segment(2);
		}
		$get_uri = str_replace('=','',$get_uri);
		$get_uri = $this->webspice->encrypt_decrypt($get_uri, 'decrypt');

		$get_link = explode('|', $get_uri);

		# verify the request
		if( isset($get_link[0]) && isset($get_link[1]) && $get_link[0] ){
			$user_id = $get_link[0];
		
			# the link is valid for only 3 days
			if( ((strtotime(date("Y-m-d"))-strtotime($get_link[1]))/86400) >3 ){
				exit('Sorry! Invalid link. Your link has been expired. Please send us your request again.');
			}

		}else{
			$this->webspice->log_me('unauthorized_access_for_password_change link: '.$get_uri); # log
			exit('denied');
		}

		if( !$this->form_validation->run() ){
			# for ajax call
			if( validation_errors() ){
				exit("Submit Error:\n".strip_tags(validation_errors()));
			}
			
			$this->load->view('change_password', $data);
			return FALSE;
		}

		# get User and verify the user
		$get_user = $this->db->query("SELECT * FROM TBL_USER WHERE USER_EMAIL=?", array($user_id))->result();
		if( !$get_user ){
			exit('denied');
		}

		# call verify_password_policy
		$this->verify_password_policy($get_user[0], 'change_password');

		# encrypt password
		$new_password = $this->webspice->encrypt_decrypt($this->input->post('new_password'), 'encrypt');
		
		# generate password history - last 2 password does not allowed as a new password
		$previous_history = array();
		if($get_user[0]->PASSWORD_HISTORY){
			$previous_history = explode(',', $get_user[0]->PASSWORD_HISTORY);
		}
		
		array_unshift($previous_history, $new_password);
		if(count($previous_history) > 2){
			# store max 3 password - remove last history
			array_pop($previous_history);
		}
		
		$password_history = implode(',', $previous_history);
		
		#change status for New user
		$STATUS=$get_user[0]->STATUS;
		if( $STATUS == 6 || $STATUS == 8 ){
			$STATUS = 7;
		}
		
		$this->webspice->remove_cache('user');
		
		# update password
		$update = $this->db->query("
		UPDATE TBL_USER SET 
		PASSWORD=?, 
		UPDATED_DATE=?, PASSWORD_HISTORY=?, STATUS=? 
		WHERE USER_EMAIL=?", 
		array($new_password, $this->webspice->now(), $password_history, $STATUS, $user_id));
		if( !$update ){
			# log
			$this->webspice->log_me('error:password_changed uid: '.$user_id);
			exit('error');
		}
		
		# log
		$this->webspice->log_me('password_changed uid: '.$user_id);
		
		# user session destroy
		$this->db->query("UPDATE TBL_USER SET IS_LOGGED = 0, LOGIN_UPDATE_TIME=NULL WHERE USER_EMAIL=?", array($user_id));

		session_destroy();
		session_start();
		
		exit('update_success:login');
	}
	
	function user_login_time_update(){
		if( $this->webspice->get_user_id() ){
			$this->db->query("UPDATE TBL_USER SET LOGIN_UPDATE_TIME=? WHERE USER_ID=?", array($this->webspice->now(), $this->webspice->get_user_id()));
			return 'updated';
		}
		
		return 'no_user';
	}
	
	function logout(){
		$this->webspice->log_me('signed_out'); # log
		$session_prefix = $this->webspice->settings()->session_prefix;
		# remove user session
		if( $this->webspice->get_user_id() ){
			$this->db->query("UPDATE TBL_USER SET IS_LOGGED = 0, LOGIN_UPDATE_TIME=NULL WHERE USER_ID=?", array($this->webspice->get_user_id()));
		}
		
		unset($_SESSION[$session_prefix.'_user']);
		# session_start();
		
		$data['title'] = 'You have been signed out of this account.';
		$data['body'] = 'You have been signed out of this account. To continue using this account, you will need to sign in again.  This is done to protect your account and to ensure the privacy of your information. We hope that, you will come back soon.';
		$data['footer'] = $this->webspice->settings()->domain_name;

		$this->confirmation($data);

		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix);
	}
	
	function verify_password_policy($user, $type){
		# $type can be login or change_password
		$user = (object)$user;
		$exipiry_period = 45;

		if( $type=='login' ){
			$pwd_change_duration = strtotime(date("Y-m-d")) - strtotime($user->UPDATED_DATE);
			$pwd_change_duration = round($pwd_change_duration / ( 3600 * 24 ));

			if( $user->UPDATED_DATE && $pwd_change_duration >= $exipiry_period ){
				$this->webspice->message_board("Your password is too old. Please change your password!");
				$this->change_password($user->USER_EMAIL);
			}
			
		}elseif( $type=='change_password' ){
			$password = $this->input->post('new_password');
			$message = null;
			
			# minimum 8 charecters
			if( strlen($password) < 8 ){
				$message .= '- Password must be minimum 8 characters\n';
			}
			
			# must have at least one capital letter, one small letter, one digit and one special character
			$containsCapitalLetter  = preg_match('/[A-Z]/', $password);
			$containsSmallLetter  = preg_match('/[a-z]/', $password);
			$containsDigit   = preg_match('/\d/', $password);
			$containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);
			
			$containsAll = $containsCapitalLetter && $containsSmallLetter && $containsDigit && $containsSpecial;
			if( !$containsAll ){
				$message .= "- Password must have at least one Capital Letter\n- Password must have at least one Small Letter\n- Password must have at least one Digit\n- Password must have at least one Special Character";
			}
			
			# password history verify - not allowed last 2 password
			$password_history = $user->PASSWORD_HISTORY;
			if($password_history){
				$password_history = explode(',', $password_history);
				foreach($password_history as $k=>$v){
					if( $password == $this->webspice->encrypt_decrypt($v,'decrypt') ){ 
						$message .= '- You are not allowed to use your last 2 password'; 
					}
				}
				
			}
			
			# if policy breaks
			if( $message ){
				exit("Submit Error:\nYou must maintain the following password policy(s):\n".$message);
			}

			return true;
			
		} # end if
		
	}



	//call confirmation for redirect another url with message
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

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */