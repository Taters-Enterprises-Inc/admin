<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class User extends CI_Controller{

	public function __construct(){
		parent::__construct();

        if (!$this->ion_auth->logged_in()){
          
          //notify('admin-session','admin-no-session', true);
          $this->output->set_status_header('401');        
          exit();
        }

        $this->load->model('user_model');
    }

    public function session(){
        switch($this->input->server('REQUEST_METHOD')){
          case 'GET':
            
          // $verify_session_data = $this->verify_user_session();
          // notify('admin-session','admin-login-session', $verify_session_data);
              
          
            $data = array(
              "admin" => array(
                "identity" => $this->session->admin['identity'],
                "email" => $this->session->admin['email'],
                "user_id" => $this->session->admin['user_id'],
                "old_last_login" => $this->session->admin['old_last_login'],
                "last_check" => $this->session->admin['last_check'],
                "is_admin" => $this->ion_auth->in_group(1),
                // "is_csr_admin" => $this->ion_auth->in_group(10), 
                // "is_catering_admin" => $this->ion_auth->in_group(14),
                // "is_audit_admin" => $this->ion_auth->in_group(15),
                // "session_id" => $this->session->admin['session_id'], 
              )
            );
    
            $data["admin"]['user_details'] = $this->user_model->getUser($this->session->admin['user_id']);
    
            if($data["admin"]['user_details']){
              $data["admin"]['user_details']->groups = $this->user_model->getUserGroups($this->session->admin['user_id']);
    
    
              if($this->ion_auth->in_group(1) || $this->ion_auth->in_group(10)){
                // $data["admin"]['user_details']->stores = $this->user_model->get_all_store();
      
                // $data["admin"]['is_snackshop_available'] = true;
                // $data["admin"]['is_catering_available'] = true;
                // $data["admin"]['is_popclub_store_visit_available'] = true;
                // $data["admin"]['is_popclub_snacks_delivered_available'] = true;
              }else{
                // $stores = $this->user_model->get_store_group_order($this->session->admin['user_id']);
      
                // $data["admin"]['user_details']->stores = $stores;
      
                // $data["admin"]['is_snackshop_available'] = false;
                // $data["admin"]['is_catering_available'] = false;
                // $data["admin"]['is_popclub_store_visit_available'] = false;
                // $data["admin"]['is_popclub_snacks_delivered_available'] = false;
      
                // foreach($stores as $store){
                //   if($store->status){
                //     $data["admin"]['is_snackshop_available'] = true;
                //   }
                //   if($store->catering_status){
                //     $data["admin"]['is_catering_available'] = true;
                //   }
                //   if($store->popclub_walk_in_status){
                //     $data["admin"]['is_popclub_store_visit_available'] = true;
                //   }
                //   if($store->popclub_online_delivery_status){
                //     $data["admin"]['is_popclub_snacks_delivered_available'] = true;
                //   }
                // }
      
              }
            
            
            
            }
            
    
              $response = array(
                "message" => 'Successfully fetch admin session',
                "data" => $data,
              );
      
              header('content-type: application/json');
              echo json_encode($response);
              return;
    
    
           break;
        }
      }
      
    // public function verify_user_session(){
    //     $current_session_id = $this->session->admin['session_id'];
    //     $id = $this->ion_auth->get_user_id();
    //     $trigger = false;


    //     $stored_session_id = $this->user_model->getStoredSessionId($id);


    //     if($current_session_id && $current_session_id !== $stored_session_id || $id === null){
    //         // $this->ion_auth->logout();
    //         $trigger = true;
    //     }

    //     $data = array(
    //         'current_session_id' => $current_session_id,
    //         'stored_session_id' => $stored_session_id,
    //         'user_id' => $id,
    //         'logout' => $trigger,
    //     );

    //     return $data;
    // }



    public function user_information($user_id){
      switch($this->input->server('REQUEST_METHOD')){
        case 'GET': 
  
          $user =  $this->user_model->getUserInfo($user_id);
          $user->groups = $this->user_model->getUserGroups($user->id);
          // $user->stockOrderGroup = $this->stock_ordering_model->getUserGroups($user->id);
          // $user->salesGroup = $this->sales_model->getUserGroups($user->id);
  
  
          $response = array(
            "message" => 'Successfully fetch user information',
            "data" => $user,
          );
  
          header('content-type: application/json');
          echo json_encode($response);
          return;
      }
    }



}