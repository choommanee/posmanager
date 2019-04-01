<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion extends MY_Controller
{
    //
    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('products', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('promotions_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
    }


    /* ----------------------------------------------------------------------------- */
    // จัดการโปรโมชั่นสินค้า

    function index($warehouse_id = NULL)
    {
        //ตรวจสอบสิทธิ์การทำงาน
        $this->sma->checkPermissions('promotion');
        //กรณีที่เป็นสิทธิ์ Owner และ Admin
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            //กรณีที่ไม่ได้เป็นสิทธิ์ Owner และ Admin
            $this->data['warehouses'] = null;
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('quantity_tonewitem')));
        $meta = array('page_title' => lang('promotions_name'), 'bc' => $bc);
        $this->page_construct('promotions/index', $meta, $this->data);
    }

    public function view($id)
    {
        //กำหนดสิทธิ์การใช้งาน
        $this->sma->checkPermissions('promotion', TRUE);

         //ข้อมูลรายการที่ต้องการแยกจำนวน
        $adjustment = $this->products_model->getAdjustmentByID($id);
        //
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        //กำหนดค่าต่างๆ
        echo $this->theme;
        $this->data['inv'] = $adjustment;
        $this->data['rows'] = $this->products_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme.'products/view_tonewitem', $this->data);
    }

    //เพิ่มรายการโปรโมชั่น
    function add($count_id = NULL)
    {
        // ตรวจสอบการได้สิทธิ์การทำงาน
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');


        // หากตรวจสอบรายการแล้วเป็นจริง ก็ให้ทำการบันทึกได้
        if ($this->input->post('pro_code') !='') {
            $pro_id = $this->input->post('id');
            // print_r($promotions);
            $pro_code = ($this->input->post('pro_code')!='AutoRunning') ? "Teset".$this->input->post('pro_code') : $this->site->getReference('PRO');
           // echo $pro_code;
           // die();
            $pro_date= $this->input->post('pro_date');
            $pro_code = $pro_code;
            $pro_type = $this->input->post('pro_type');
            $pro_start_date = SiteHelpers::ConvertDateThaiToDb($this->input->post('pro_start_date'));
            $pro_end_date = SiteHelpers::ConvertDateThaiToDb($this->input->post('pro_end_date'));
            $pro_total_qty = $this->input->post('pro_total_qty');
            $pro_name= $this->input->post('pro_name');
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data =array(
                'pro_code'=>$pro_code,
                'pro_name'=>$pro_name,
                'pro_start_date'=>$pro_start_date,
                'pro_end_date'=>$pro_end_date,
                'pro_type'=>$pro_type,
                'user_update'=>$this->session->userdata('user_id'),
                'date_update'=>date('Y-m-d H:i:s'),
                'pro_total_qty'=>$pro_total_qty,
                'pro_total_qty2'=>'0',
                'total_limit'=>'0',
                'pro_date'=>$pro_date,
                'note'=>$note
            );

            //print_r($data);

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $product_id = $_POST['product_id'][$r];
                $prod_free= $_POST['prod_free'][$r];
                $quantity_buy= $_POST['quantity_buy'][$r];
                $reduce_bath= ($_POST['reduce_bath'][$r]!='')?$_POST['reduce_bath'][$r]:0;
                $reduce_percent= ($_POST['reduce_percent'][$r]!='')?$_POST['reduce_percent'][$r]:0;
                $unit_id= $_POST['unit_id'][$r];



                $products[] = array(
                    'prod_product_id' => $product_id,
                    'prod_unit_code' => $unit_id,
                    'prod_qty' => $quantity_buy,
                    'reduce_bath' => $reduce_bath,
                    'reduce_percent' => $reduce_percent,
                    'prod_free' => $prod_free,
                    'prod_type' => $pro_type,
                    'prod_dd' => 0,
                    'pro_id' => $pro_id,
                    'prod_total_qty' => 0,
                );

            }
           // print_r($products);


        }
        // กรณีที่มีการบันทึกเมือบันทึกได้แล้วให้ไปที่หน้าแรกของรายการ
        if ($this->input->post('pro_code') !='' && $this->promotions_model->addPromotions($data, $products)) {
            $this->site->updateReference('PRO');
            $this->session->set_userdata('remove_proItem', 1);
            $this->session->set_flashdata('message', lang("promotion_added"));
            admin_redirect('promotion');
        } else {
            // หน้ารายการโปรโมชั่นที่ต้องการ
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items = $this->products_model->getStockCountItems($count_id);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    if ($item->counted != $item->expected) {
                        $product = $this->site->getProductByID($item->product_id);
                        $row = json_decode('{}');
                        $row->id = $item->product_id;
                        $row->product_id = $item->product_id;
                        $row->code = $product->code;
                        $row->name = $product->name;
                        $row->qty = $item->counted-$item->expected;
                        $row->type = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty = $row->qty > 0 ? $row->qty : (0-$row->qty);
                        $options = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'options' => $options);
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : FALSE;
            $this->data['warehouse_id'] = $count_id ? $stock_count->warehouse_id : FALSE;
            $this->data['count_id'] = $count_id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_adjustment')));
            $meta = array('page_title' => lang('add_adjustment'), 'bc' => $bc);
            $this->page_construct('promotions/add', $meta, $this->data);

        }
    }

    // กรณีมีการแก้ไขรายการโปรโมชั่น
    function edit($id)
    {

        $this->sma->checkPermissions('promotions', true);
        $promotions = $this->promotions_model->getPromotionsByID($id);
       // print_r($promotions);
        if (!$id || !$promotions) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        //print_r($_POST);
        //$this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->input->post('pro_code') !='') {
            $pro_id = $this->input->post('id');
           // print_r($promotions);

            $pro_date= SiteHelpers::ConvertDateThaiToDb($this->input->post('pro_date'));
            $pro_code = $this->input->post('pro_code');
            $pro_type = $this->input->post('pro_type');
            $pro_start_date = SiteHelpers::ConvertDateThaiToDb($this->input->post('pro_start_date'));
            $pro_end_date = SiteHelpers::ConvertDateThaiToDb($this->input->post('pro_end_date'));
            $pro_total_qty = $this->input->post('pro_total_qty');
            $pro_name= $this->input->post('pro_name');
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data =array(
                'pro_code'=>$pro_code,
                'pro_name'=>$pro_name,
                'pro_start_date'=>$pro_start_date,
                'pro_end_date'=>$pro_end_date,
                'pro_type'=>$pro_type,
                'user_update'=>$this->session->userdata('user_id'),
                'date_update'=>date('Y-m-d H:i:s'),
                'pro_total_qty'=>$pro_total_qty,
                'pro_total_qty2'=>'0',
                'total_limit'=>'0',
                'pro_date'=>$pro_date,
                'note'=>$note
            );

            //print_r($data);

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $product_id = $_POST['product_id'][$r];
                $prod_free= $_POST['prod_free'][$r];
                $quantity_buy= $_POST['quantity_buy'][$r];
                $reduce_bath= ($_POST['reduce_bath'][$r]!='')?$_POST['reduce_bath'][$r]:0;
                $reduce_percent= ($_POST['reduce_percent'][$r]!='')?$_POST['reduce_percent'][$r]:0;
                $unit_id= $_POST['unit_id'][$r];



                $products[] = array(
                    'prod_product_id' => $product_id,
                    'prod_unit_code' => $unit_id,
                    'prod_qty' => $quantity_buy,
                    'reduce_bath' => $reduce_bath,
                    'reduce_percent' => $reduce_percent,
                    'prod_free' => $prod_free,
                    'prod_type' => $pro_type,
                    'prod_dd' => 0,
                    'pro_id' => $pro_id,
                    'prod_total_qty' => 0,
                );

            }

            //die();


            // $this->sma->print_arrays($data, $products);

        }

        if ($this->input->post('pro_code') !='' && $this->promotions_model->updatePromotions($id, $data, $products)) {
            $this->session->set_userdata('remove_proItem', 1);
            $this->session->set_flashdata('message', "แก้ไขรายการโปรโมชั่นเรียบร้อยแล้ว");
            admin_redirect('promotion');
        } else {

            $pro_items = $this->promotions_model->getAllPromotionsItems($id,$promotions->pro_id);
          // print_r($pro_items);
           // echo count($pro_items);
            // krsort($inv_items);
           // die();
            $c = rand(100000, 9999999);
            if($pro_items <> false && count($pro_items)>0 ){
                foreach ($pro_items as $item) {
                    $product = $this->site->getProductByID($item->prod_product_id);
                   // print_r($product);
                    $row = json_decode('{}');
                    $row->prod_id = $item->prod_id;
                    $row->id = $item->prod_product_id;
                    $row->product_id = $item->prod_product_id;
                    $row->prod_unit_code = $item->prod_unit_code;
                    $row->unit_id = $item->prod_unit_code;
                    $row->unit = $item->unit;
                    $row->price = $product->price;
                    $row->prod_qty = $item->prod_qty;
                    $row->code = $product->code;
                    $row->name = $product->name;
                    $row->reduce_bath = $item->reduce_bath;
                    $row->reduce_percent = $item->reduce_percent;
                    $row->prod_free = $item->prod_free;
                    $row->prod_type = $item->prod_type;
                    $row->reduce_percent = $item->reduce_percent;
                    //$options = $this->products_model->getProductOptions($product->id);
                    $row->option = $item->option_id ? $item->option_id : 0;
                    $row->serial = $item->serial_no ? $item->serial_no : '';
                    $ri = $this->Settings->item_addition ? $row->prod_id : $c;

                    $pr[$ri] = array('id' => str_replace(".", "", microtime(true)),
                                     'item_id' => $row->prod_id,
                                     'label' => $row->name . " (" . $row->code . ")",
                                     'row' => $row);
                    $c++;
                }
            }
            //print_r($promotions);
            $this->data['promotions'] = $promotions;
            $this->data['promotions_items'] = json_encode($pr);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_tonewitem')));
            $meta = array('page_title' => lang('edit_promotions'), 'bc' => $bc);
            $this->page_construct('promotions/edit', $meta, $this->data);

        }
    }

    function delete_promotions($pro_id){
        if($this->promotions_model->deletePromotions($pro_id)){
            $this->sma->send_json(array('error' => 0, 'msg' => 'Promotion is deleted'));
        }else{
            $this->sma->send_json(array('error' => 1, 'msg' =>'cannot delete Promotion data'));
        }
        return false;
    }

    function getpromotions($warehouse_id = NULL)
    {
        //echo "test";
        $this->sma->checkPermissions('promotions');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_promotions") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('promotion/delete_promotions/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('promotions_header')}.pro_id as id, date_update as date, pro_code,pro_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, pro_type,'' as img ")
            ->from('promotions_header')
            ->join('users', 'users.id=promotions_header.user_update', 'left');

        $this->datatables->add_column("Actions", "<div class='text-center'><a href='" . admin_url('promotion/edit/$1') . "' class='tip' title='" . lang("edit_tonewitem") . "'><i class='fa fa-edit'></i></a> " . $delete_link . "</div>", "id");
        //echo $this->db->last_query();

        echo $this->datatables->generate();


    }

    //ค้นหารายการสินค้า
    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->promotions_model->getPromotionsNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id,
                    'label' => $row->name . " (" . $row->code . ")",
                    'code' => $row->code,
                    'name' => $row->name,
                    'price' => $row->price,
                    'qty' => 1);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function get_suggestions()
    {

        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductsForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->products_model->getProductOptions($row->id);
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants);
            }
            //  print_r($pr);
            // die();
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }


    public function get_suggestions_tonewItem($warehouse_id=null)
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        //print_r($analyzed);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->promotions_model->getProductsForOnNewItemPrinting($sr,$warehouse_id);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $options = $this->promotions_model->getProductOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';

                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options);

            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }


    //เพิ่มสินค้าด้วย Ajax
    function addByAjax()
    {
        if (!$this->mPermissions('add')) {
            exit(json_encode(array('msg' => lang('access_denied'))));
        }
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_is_required'))));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(array('msg' => lang('product_name_is_required'))));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(array('msg' => lang('product_category_is_required'))));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(array('msg' => lang('product_unit_is_required'))));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(array('msg' => lang('product_price_is_required'))));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(array('msg' => lang('product_cost_is_required'))));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_already_exist'))));
            }
            if ($row = $this->products_model->addAjaxProduct($product)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_product'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }

    }
    /* --------------------------------------------------------------------------------------------- */

    function modal_view($id = NULL)
    {
        $this->sma->checkPermissions('index', TRUE);

        $pr_details = $this->site->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->sma->md();
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);

        $this->load->view($this->theme.'products/modal_view', $this->data);
    }


    function pdf($id = NULL, $view = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'products/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'products/pdf', $this->data, TRUE);
            if (! $this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }




}
