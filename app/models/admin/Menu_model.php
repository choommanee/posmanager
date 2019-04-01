<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addMenu($data = array())
    {

       // print_r($data);
        if ($this->db->insert('sma_menu', $data)){
             $menu_id = $this->db->insert_id();
            return $menu_id;
        }
        //echo $this->db->last_query();
       // die();
        return false;
    }

    public function editMenu($data = array(),$id)
    {

        // print_r($data);
        if ($this->db->update('sma_menu', $data, array('menu_id' => $id))){
           // $menu_id = $this->db->insert_id();
            return $id;
        }
        //echo $this->db->last_query();
        // die();
        return false;
    }



}
