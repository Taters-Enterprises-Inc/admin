<?php defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Auth extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->ion_auth->set_message_delimiters('', '');
        $this->ion_auth->set_error_delimiters('', '');

        $this->lang->load('auth');

    }
	
	public function create_group(){
		
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('create_group_title');
		
				if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}
		
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('groupName', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');
		
				if ($this->form_validation->run() === TRUE) {
					$new_group_id = $this->ion_auth->create_group($this->input->post('groupName'), $this->input->post('description'));
					if ($new_group_id) {
						
						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->ion_auth->messages()));
						return;
					} else {
		
						$this->output->set_status_header(401);
						header('content-type: application/json');
						echo json_encode(array("message" => validation_errors()));
						return;
					}
				}else{
					// display the create group form
					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
					$this->data['groupName'] = [
						'name'  => 'groupName',
						'id'    => 'groupName',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('groupName'),
					];
					$this->data['description'] = [
						'name'  => 'description',
						'id'    => 'description',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('description'),
					];
		
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => validation_errors()));
					return;
				}

				break;
		}

		

	}
    
    public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {

                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->messages()));
                        return;
                    } else {

				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->errors()));
                        return;
                    }

                }else{ 
                    $this->output->set_status_header(401);
                    header('content-type: application/json');
                    echo json_encode(array("message" =>  validation_errors()));
                    return;
                }

                break;
        }
    }

	public function change_password(){
		switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
		        $this->data['title'] = $this->lang->line('change_password_heading');

				$user_id = $this->session->admin['user_id'];

				$user = $this->ion_auth->user($user_id)->row();

				$currentPassword = $this->input->post('currentPassword');
				$newPassword = $this->input->post('newPassword');
				$confirmPassword = $this->input->post('confirmPassword');
				$identity = $user->email;

				if ($newPassword === $confirmPassword) {
			

					$password_matches = $this->ion_auth_model->change_password($identity, $currentPassword, $newPassword);
                    
					if ($password_matches) {
						
						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->ion_auth->messages()));
						return;
					} else {

						$this->output->set_status_header('401');
						header('content-type: application/json');
						echo json_encode(array("message" => "Change Password Failed"));
						return;
					}

                }
				

                break;
        }
	}
	
	public function edit_user($id){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('edit_user_heading');

				if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}
				
				$user = $this->ion_auth->user($id)->row();
				$groups = $this->ion_auth->groups()->result_array();
				$currentGroups = $this->ion_auth->get_users_groups($id)->result();
				

				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('firstName', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('lastName', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
				$this->form_validation->set_rules('phoneNumber', $this->lang->line('edit_user_validation_phone_label'), 'trim');
				$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim');
				

				if ($this->input->post('password')) {
					$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[confirmPassword]');
					$this->form_validation->set_rules('confirmPassword', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
				}

				if ($this->form_validation->run() === TRUE) {
					$data = [
						'first_name' => $this->input->post('firstName'),
						'last_name' => $this->input->post('lastName'),
						'company' => $this->input->post('company'),
						'phone' => $this->input->post('phoneNumber'),
					];

					// update the password if it was posted
					if ($this->input->post('password')) {
						$data['password'] = $this->input->post('password');
					}

					// Only allow updating groups if user is admin
					if ($this->ion_auth->is_admin()) {
						/*

						Updated parameter for remove_from_group to determine 
						if the group is from shop or stock ordering this process
						also applies to add_to_group. 

						1 ===== shop
						2 ===== stock order 
						3 ===== sales

						*/
						// Update the groups user belongs to

						$this->ion_auth->remove_from_group('', $id, 1);
						$groupData = $this->input->post('groups');
						if (isset($groupData) && !empty($groupData)) {
							foreach ($groupData as $grp) {
								$this->ion_auth->add_to_group($grp, $id, 1);
							}
						}

						$this->ion_auth->remove_from_group('', $id, 2);
						$stockOrderData = $this->input->post('stock_order_group');
						if (isset($stockOrderData) && !empty($stockOrderData)) {
							foreach ($stockOrderData as $grp) {
								$this->ion_auth->add_to_group($grp, $id, 2);
							}
						}

						$this->ion_auth->remove_from_group('', $id, 3);
						$salesData = $this->input->post('sales_group');
						if (isset($salesData) && !empty($salesData)) {
							foreach ($salesData as $grp) {
								$this->ion_auth->add_to_group($grp, $id, 3);
							}
						}


					}

					// check to see if we are updating the user
					if ($this->ion_auth->update($user->id, $data)) {
						
						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->ion_auth->messages()));
						return;
					} else {

						$this->output->set_status_header('401');
						header('content-type: application/json');
						echo json_encode(array("message" => validation_errors()));
						return;
					}
				}else {

					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
					// pass the user to the view
					$this->data['user'] = $user;
					$this->data['groups'] = $groups;
					$this->data['currentGroups'] = $currentGroups;

					$this->data['firstName'] = [
						'name'  => 'firstName',
						'id'    => 'firstName',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('firstName', $user->first_name),
					];
					$this->data['lastName'] = [
						'name'  => 'lastName',
						'id'    => 'lastName',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('lastName', $user->last_name),
					];
					$this->data['company'] = [
						'name'  => 'company',
						'id'    => 'company',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('company', $user->company),
					];
					$this->data['phoneNumber'] = [
						'name'  => 'phoneNumber',
						'id'    => 'phoneNumber',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('phoneNumber', $user->phone),
					];
					$this->data['password'] = [
						'name' => 'password',
						'id'   => 'password',
						'type' => 'password'
					];
					$this->data['confirmPassword'] = [
						'name' => 'confirmPassword',
						'id'   => 'confirmPassword',
						'type' => 'password'
					];
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => validation_errors()));
					return;
				}
				
		}

	}
    
	public function create_user(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('create_user_heading');

				if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}
		
		
				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('firstName', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('lastName', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
				$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('phoneNumber', $this->lang->line('create_user_validation_phone_label'), 'trim');
				$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
				$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[confirmPassword]');
				$this->form_validation->set_rules('confirmPassword', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		
				if ($this->form_validation->run() === TRUE) {
					$email = strtolower($this->input->post('email'));
					$password = $this->input->post('password');
		
					$additional_data = [
						'first_name' => $this->input->post('firstName'),
						'last_name' => $this->input->post('lastName'),
						'company' => $this->input->post('company'),
						'phone' => $this->input->post('phoneNumber'),
					];
				}
				if ($this->form_validation->run() === TRUE && $this->ion_auth->register($email, $password, $email, $additional_data)) {
					// check to see if we are creating the user
					// redirect them back to the admin page
					header('content-type: application/json');
					echo json_encode(array("message" =>  $this->ion_auth->messages()));
					redirect("auth", 'refresh');
				} else {
					// display the create user form
					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
					$this->data['firstName'] = [
						'name' => 'firstName',
						'id' => 'firstName',
						'type' => 'text',
						'value' => $this->form_validation->set_value('firstName'),
					];
					$this->data['lastName'] = [
						'name' => 'lastName',
						'id' => 'lastName',
						'type' => 'text',
						'value' => $this->form_validation->set_value('lastName'),
					];
					$this->data['identity'] = [
						'name' => 'identity',
						'id' => 'identity',
						'type' => 'text',
						'value' => $this->form_validation->set_value('identity'),
					];
					$this->data['email'] = [
						'name' => 'email',
						'id' => 'email',
						'type' => 'text',
						'value' => $this->form_validation->set_value('email'),
					];
					$this->data['company'] = [
						'name' => 'company',
						'id' => 'company',
						'type' => 'text',
						'value' => $this->form_validation->set_value('company'),
					];
					$this->data['phoneNumber'] = [
						'name' => 'phoneNumber',
						'id' => 'phoneNumber',
						'type' => 'text',
						'value' => $this->form_validation->set_value('phoneNumber'),
					];
					$this->data['password'] = [
						'name' => 'password',
						'id' => 'password',
						'type' => 'password',
						'value' => $this->form_validation->set_value('password'),
					];
					$this->data['confirmPassword'] = [
						'name' => 'confirmPassword',
						'id' => 'confirmPassword',
						'type' => 'password',
						'value' => $this->form_validation->set_value('confirmPassword'),
					];
		
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => validation_errors()));
				}
				return;
			
		}
	}
	
	public function _get_csrf_nonce(){
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	public function _valid_csrf_nonce(){
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
			return TRUE;
		}
		return FALSE;
	}
    
	public function logout(){

		$this->data['title'] = "Logout";
		$this->ion_auth->logout();
	

        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}

}