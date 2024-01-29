<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
    }

    public function getUser($user_id){
        $this->db->select('
            A.id,
        ');

        $this->db->from('users A');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
    

    public function getUserGroups($user_id){
        
        $this->db->select("
            B.id,
            B.name,
            B.description,
        ");

        $this->db->from('users_groups A');
        $this->db->join('groups B', 'B.id = A.group_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    
    function get_all_store()
    {
        $this->db->select('b.store_id, b.name, c.name as menu_name');
        $this->db->from('store_tb b');
        $this->db->join('store_menu_tb c', 'c.id = b.store_menu_type_id');
        $query = $this->db->get();
        return $query->result();
    } 
    

    // function get_store_group_order($user_id)
    // {
    //     $this->db->select('
    //         a.store_id, 
    //         b.name, 
    //         b.status, 
    //         b.catering_status,
    //         b.popclub_walk_in_status, 
    //         b.popclub_online_delivery_status, 
    //         c.name as menu_name
    //     ');
    //     $this->db->from('users_store_groups a');
    //     $this->db->join('store_tb b', 'b.store_id = a.store_id' ,'left');
    //     $this->db->join('store_menu_tb c', 'c.id = b.store_menu_type_id');
    //     $this->db->where('a.user_id', $user_id);
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    
    public function getStoredSessionId($id){
        $this->db->select('session_id');
		$this->db->from('users');
		$this->db->where('id', $id);

		$query = $this->db->get();
		$row = $query->row();

        if ($row !== null) {
            return $row->session_id;
        } else {
            return ""; 
        }
    }


    public function getUserInfo($user_id){
        $this->db->select('
            A.id,
            B.first_name,
            B.last_name,
            C.contact_number, 
        ');

        $this->db->from('users A');
        $this->db->join('user_personal_details B', 'B.user_id = A.id', 'left');
        $this->db->join('user_contact_details C', 'B.user_id = A.id', 'left');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_user_module($groupData){

        $this->db->select('module_id');
        $this->db->from('groups');
        $this->db->where_in('id', $groupData);

        $groupModuleIds = $this->db->get();
        return $groupModuleIds->result();
    }

    public function insert_user_module($groupModuleIds, $user_id) {
        $data = array();
        
        foreach ($groupModuleIds as $row) {
            $data[] = array('module_id' => $row->module_id, 'user_id' => $user_id);
        }

        $this->db->trans_start();
        $this->db->insert_batch('users_module', $data);
        $this->db->trans_complete();
    }
    
    

}