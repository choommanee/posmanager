<?php defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //ตรวจสอบสิทธ์ต่างๆ
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->library('form_validation');
        $this->load->admin_model('menu_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
    }

    public function index($id=null)
    {
        //print_r($this->session->userdata());
        $this->sma->checkPermissions();
        //print_r($_GET['sale_type']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        //$this->data['sale_type']=$_GET['sale_type'];
        $pos = ($this->input->get('pos')!='' ? $this->input->get('pos') : 'sidebar' );
        $rows = $this->db->get_where('sma_menu',array('menu_id'=>$id ));
        if(count($rows->result())>=1)
        {
            $row = $rows->result_array();
            $this->data['row'] =  $row[0];
        }
       // echo '<pre>';print_r($this->data['row']);echo '</pre>';exit;
        //print_r(SiteHelpers::menus( $pos ,'all'));
        $this->data['menus']		= SiteHelpers::menus( $pos ,'all');

        $this->data['active'] = $pos;
        $this->data['groups'] 		= $this->db->get('sma_groups');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => "ระบบจัดการเมนู"));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        $this->page_construct('menu/index', $meta, $this->data);
       // $this->load->view('jitsuite/admin/views/Menu/index',  $this->data);
    }

    function saveOrder( $id =0)
    {
        $rules = array(
            array('field'   => 'reorder','label'   => 'Reorder Array','rules'   => 'required'),
        );
        $this->form_validation->set_rules( $rules );
        if( $this->form_validation->run() ){
            $menus = json_decode($this->input->post('reorder'),true);
            $child = array();
            $a=0;
            foreach($menus as $m)
            {
                if(isset($m['children']))
                {
                    $b=0;
                    foreach($m['children'] as $l)
                    {
                        if(isset($l['children']))
                        {
                            $c=0;
                            foreach($l['children'] as $l2)
                            {
                                $level3[] = $l2['id'];
                                $this->db->where(array('menu_id'=>$l2['id']));
                                $this->db->update('sma_menu',array('parent_id'=> $l['id'],'ordering'=>$c));
                                $c++;
                            }
                        }
                        $this->db->where(array('menu_id'=>$l['id']));
                        $this->db->update('sma_menu',array('parent_id'=> $m['id'],'ordering'=>$b));
                        $b++;
                    }
                }
                $this->db->where(array('menu_id'=>$m['id']));
                $this->db->update('sma_menu',array('parent_id'=> 0,'ordering'=>$a));
                $a++;
            }
            admin_redirect("menu");
            //redirect('sximo/menu',301);
        } else {
           // redirect('sximo/menu',301);
            admin_redirect("menu");
        }
    }

    public function save(){
        $this->sma->checkPermissions();
      //  print_r($_POST);
        //admin_redirect("menu");
     //   die();
        $rules = array(
            array('field'   => 'menu_name','label'   => 'Menu Name','rules'   => 'required'),
            array('field'   => 'active','label'   	 => 'Active','rules'   => 'required'),
            array('field'   => 'menu_type','label'   => 'Menu Type','rules'   => 'required'),
            array('field'   => 'position','label'    => 'Position','rules'   => 'required')
        );
        $this->form_validation->set_rules( $rules );
        $pos = $this->input->post('position',true);
        if( $this->form_validation->run() ){

           // $data = $this->validatePost('sma_menu');
            $arr = array();
            $data['menu_name'] = $this->input->post('menu_name');
            $data['active'] = $this->input->post('active');
            $data['menu_type'] = $this->input->post('menu_type');
            $data['position'] = $this->input->post('position');
            $data['url'] = $this->input->post('url');
            $data['user_update'] = $this->session->userdata('user_id');
            $data['date_update'] = date('Y-m-d H:i:s');
            $data['menu_icons'] = $this->input->post('menu_icons');

            $groups = $this->db->get('sma_groups')->result();
            foreach($groups as $g)
            {
                $arr[$g->id] = (isset($_POST['groups'][$g->id]) ? "1" : "0" );
            }
            $data['access_data'] = json_encode($arr);
            $data['allow_guest'] 	= (isset($_POST['allow_guest']) ? '1' : '0');
            //echo '<pre>';print_r($data);echo '</pre>';
            //exit;
            if($this->input->post('menu_id')!=''){
                $this->menu_model->editMenu($data,$this->input->post('menu_id'));
            }else{

                $this->menu_model->addMenu($data);
            }
           // redirect('admin/menu?pos='.$pos);
            admin_redirect("menu?pos=".$pos);

        } else {
            admin_redirect("menu?pos=".$pos);
        }
    }

    public function destroy($id)
    {
        $menus = $this->db->get_where('sma_menu',array('parent_id'=>$id))->result();

        foreach($menus as $row)
        {
           // $this->model->destroy($row->menu_id);
            $this->db->delete('sma_menu', array('menu_id' => $row->menu_id));
            $pos = $row->position;
        }
        $this->db->delete('sma_menu', array('menu_id' => $id));
        //$this->model->destroy($id);
        // redirect
        $this->session->set_flashdata('message', 'Successfully deleted row!');
        admin_redirect("menu?pos=".$pos);
    }

}
