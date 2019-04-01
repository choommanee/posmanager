<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('sales', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('sales_model');
        $this->load->admin_model('invoice_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
    }

    public function index($warehouse_id = null)
    {
        $this->sma->checkPermissions();
        //print_r($_GET['sale_type']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }
        //$this->data['sale_type']=$_GET['sale_type'];

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        $this->page_construct('Invoice/index', $meta, $this->data);
    }

    public function getInvoices($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('admin/invoice/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $edit_link = anchor('admin/invoice/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
         $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_sale") . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger invoice-delete' href='" . admin_url('invoice/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete_invoice') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        //$action = '<div class="text-center">' . $detail_link . ' ' . $edit_link . ' ' . $email_link . ' ' . $delete_link . '</div>';

        $this->load->library('datatables');
        ///echo $warehouse_id;
        if ($warehouse_id) {

                $this->datatables
                    ->select("{$this->db->dbprefix('invoice')}.id as id, DATE_FORMAT({$this->db->dbprefix('invoice')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('invoice')}.supplier,payment_term, status, grand_total, paid, (grand_total-paid) as balance, '' as attachment, '' as return_id")
                    ->from('invoice')
                    ->where('warehouse_id', $warehouse_id);

        } else {

            $this->datatables
                ->select("{$this->db->dbprefix('invoice')}.id as id, DATE_FORMAT({$this->db->dbprefix('invoice')}.date, '%Y-%m-%d %T') as date, reference_no, {$this->db->dbprefix('invoice')}.supplier,payment_term, status, grand_total, paid, (grand_total-paid) as balance, '' as attachment, '' as return_id")
                ->from('invoice');
        }

        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function add($quote_id = null)
    {
        $this->sma->checkPermissions();  //ตรวจสอบเรื่องสิทธิ์
        $sale_id = $this->input->get('invoice_id') ? $this->input->get('invoice_id') : NULL; //รหัส Invoice

        $this->form_validation->set_rules('customer', lang("customer"), 'required'); //ตรวจสอบ validate ข้อมูล
        $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');

        if ($this->form_validation->run() == true) { //หากผ่านการตรวจสอบ จะเข้าการบันทึก
            //รันรหัส Invoice
            $invoice_id = $this->input->post('invoice_id') ? $this->input->post('invoice_id') : $this->site->getReference('LB');

            $gtotalAmount =0;
            $gtotalpaybill=0;
            // วันที่ในการบันทึก
            $date = $this->sma->fld(trim($this->input->post('date')));
            $edate = $this->sma->fld(trim($this->input->post('edate')));
           // echo $edate;
           // print_r($_POST);
           // die();
            $invoice_type = $this->input->post('invoice_type');
            $status = $this->input->post('sale_status');

            $payment_term = $this->input->post('payment_term');
            $customer = $this->input->post('customer');
            $customer_details = $this->site->getCompanyByID($customer);
            $customer_name = !empty($customer_details->company) && $customer_details->company != '-'  ? $customer_details->company : $customer_details->name;
            //  print_r($this->input->post());
               //die();
               // บันทึกรายการวางบิล Item
               //รหัสเอกสาร
               //$refferen_no = $this->input->post('reference_no'[]);
            $i = isset($_POST['reference_no']) ? sizeof($_POST['reference_no']) : 0;
            for ($r = 0; $r < $i; $r++) {
               $refferen_no = $_POST['reference_no'][$r];
               $sl_id = $_POST['sl_id'][$r];
               $totalAmount = str_replace(",","",$_POST['totalAmount'][$r]);
               $dateCreate = $_POST['dateCreate'][$r];
               $totalpaybill = str_replace(",","",$_POST['totalpaybill'][$r]);
               $payment_term = $_POST['payment_term'][$r];

               $invoice_item = array(
                        'invoice_id' => $invoice_id,
                        'reference_no' => $refferen_no,
                        'sl_id' => $sl_id,
                        'total_amount' => str_replace(",","",$totalAmount),
                        'total_paybill' => str_replace(",","",$totalpaybill),
                    );

               $gtotalAmount =$gtotalAmount+$totalAmount;
               $gtotalpaybill = $gtotalpaybill+$totalpaybill;
               $invoice_items[] = ($invoice_item);
            }

            krsort($invoice_items);

            $invoice = array(
                            'date'=>$date,
                            'reference_no'=>$invoice_id,
                            'supplier_id'=>$customer,
                            'supplier'=>$customer_name,
                            'reference_no2'=>'',
                            'status'=>$status,
                            'payment_status'=>'0',
                            'payment_term'=>$payment_term,
                            'grand_total'=>$gtotalAmount,
                            'create_by'=>$this->session->userdata('user_id'),
                            'update_by'=>$this->session->userdata('user_id'),
                            'update_at'=>date('Y-m-d H:i:s'),
                            'remark'=>'',
                            'paid'=>$gtotalpaybill,
                            'reference_no2_date'=>'',
                            'edate'=>$edate,
                            'invoice_type'=>$invoice_type
                            );

        }
       // print_r($invoice);
       // die();
        if ($this->form_validation->run() == true) {
            $invoice_id = $this->invoice_model->addInvoice($invoice,$invoice_items);
            //echo $invoice_id;
               // $sale_id = $this->invoice_model->addInvoice($invoice, $invoice_items);
            $this->session->set_flashdata('message', lang("บันทึกใบวางบิลเรียบร้อยแล้ว"));
            $this->session->set_userdata('remove_invoice', 1);
            admin_redirect("invoice");

        } else {

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('invoices')), array('link' => '#', 'page' => lang('add_invoice')));
            $meta = array('page_title' => lang('add_invoice'), 'bc' => $bc);
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->page_construct('Invoice/add', $meta, $this->data);
        }
    }

    public function suggestions()
    {
        //คีย์เวิร์ดที่ใช้ในการค้นหา
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true); //รหัสคลังสินค้า
        $customer_id = $this->input->get('customer_id', true);//รหัสลูกค้าที่ต้องการออกใบวางบิล

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

       // $analyzed = $this->sma->analyze_term($term);
        $sr = $term;
        //$option_id = $analyzed['option_id'];


        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $rows = $this->invoice_model->getSaleRec($sr, $warehouse_id,$customer_id);
       // print_r($rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
               // unset($row->id, $row->reference_no, $row->date);
                $row->id = $row->id;
                $pr[] = array('id' => $row->reference_no,'customer_id'=>$row->customer_id, 'sl_id' => $row->id,'label'=> $row->reference_no.' เครดิต '.$row->payment_term.' วันครบกำหนด '.$row->due_date, 'reference_no' => $row->reference_no, 'date' => $row->date,
                    'row' => $row);
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function view($id = null, $showModal=null)
    {
        $this->sma->checkPermissions('index');
        //echo $showModal;
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->invoice_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->supplier_id);
        //$this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->supplier_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->invoice_model->getAllInvoiceItems($id);
        $this->data['sale_type'] = $this->input->get('sale_type');
        $this->data['id'] = $id;
        $this->data['showModal'] = $showModal;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('invoice'), 'page' => lang('invoices')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_sales_details'), 'bc' => $bc);
        $this->page_construct('invoice/view', $meta, $this->data);
    }


    /* ------------------------------------------------------------------------ */

    public function edit($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->invoice_model->getInvoiceByID($id);
        //print_r($_POST);
       // die();
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('iv_reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('biller', lang("biller"), 'required');
        $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');

        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('iv_reference_no');//รหัสเลขที่อ้างอิง
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $inv->date;
            }
            $warehouse_id = $this->input->post('warehouse');  // คลังสินค้า
            $customer_id = $this->input->post('customer'); // ข้อมูลซัพพลายเออร์
            $biller_id = $this->input->post('biller'); // รหัสเจ้าของ
            $total_items = $this->input->post('total_items'); // จำนวน Item
            $status = $this->input->post('sale_status'); // สถานะบิล
            $edate = $this->sma->fld(trim($this->input->post('edate')));
            $invoice_type = $this->input->post('invoice_type');
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = !empty($customer_details->company) && $customer_details->company != '-'  ? $customer_details->company : $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['reference_no']) ? sizeof($_POST['reference_no']) : 0;
            for ($r = 0; $r < $i; $r++) {
               $refferen_no = $_POST['reference_no'][$r];
               $sl_id = $_POST['sl_id'][$r];
               $totalAmount = str_replace(",","",$_POST['totalAmount'][$r]);
               $dateCreate = $_POST['dateCreate'][$r];
               $totalpaybill = str_replace(",","",$_POST['totalpaybill'][$r]);
               $payment_term = $_POST['payment_term'][$r];

                 $invoice_item = array(
                        'invoice_id' => $id,
                        'reference_no' => $refferen_no,
                        'sl_id' => $sl_id,
                        'total_amount' => str_replace(",","",$totalAmount),
                        'total_paybill' => str_replace(",","",$totalpaybill),
                    );
               //$payment_term =

               $gtotalAmount =$gtotalAmount+$totalAmount;
               $gtotalpaybill = $gtotalpaybill+$totalpaybill;
               $invoice_items[] = ($invoice_item);
            }

            krsort($invoice_items);

           $invoice = array(
                            'date'=>$date,
                            'supplier_id'=>$customer_id,
                            'supplier'=>$customer,
                            'reference_no2'=>'',
                            'status'=>$status,
                            'payment_status'=>'0',
                            'payment_term'=>$payment_term,
                            'grand_total'=>$gtotalAmount,
                            'update_by'=>$this->session->userdata('user_id'),
                            'update_at'=>date('Y-m-d H:i:s'),
                            'remark'=>'',
                            'paid'=>$gtotalpaybill,
                            'reference_no2_date'=>'',
                            'edate'=>$edate,
                            'invoice_type'=>$invoice_type
                            );
            //print_r($invoice);
            //print_r($invoice_items);
           // die();
        }

        if ($this->form_validation->run() == true && $this->invoice_model->updateInvoice($id, $invoice, $invoice_items)) {
            $this->session->set_userdata('remove_invoice', 1);
            $this->session->set_flashdata('message', "แก้ไขรายการใบวางบิลเรียบร้อยแล้ว");
            admin_redirect('invoice');
        } else {
            //print_r($_GET);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $this->invoice_model->getInvoiceByID($id);

            $inv_items = $this->invoice_model->getAllInvoiceItems($id);
            // krsort($inv_items);
            $c = rand(100000, 9999999);

            foreach ($inv_items as $item) {
             // print_r($item);


                $row =(object) array();
                $row->id = $item->id;
                $row->date = $item->date;
                $row->invoice_id = $item->invoice_id;
                $row->sl_id = $item->sl_id;
                $row->grand_total = $item->total_amount;
                $row->item_pay_total = $item->total_paybill;
                $row->reference_no = $item->reference_no;
                $row->customer_id = $item->customer_id;
                $row->biller_id = $item->biller_id;
                $row->biller = $item->biller;
                $row->sale_status = $item->sale_status;
                $row->payment_term = $item->payment_term;
                $row->due_date = $item->due_date;
                $row->invoice_id = $item->invoice_id;
                $row->invoice_refference_no = $item->invoice_refference_no;


               /* $pr[$ri] = array('id' => $c, 'sl_id' => $row->sl_id,
                                 'label' => $row->name . " (" . $row->code . ")",
                                 'row' => $row, 'combo_items' => $combo_items,
                                 'tax_rate' => $tax_rate, 'units' => $units,
                                 'options' => $options);*/
                //print_r($row);
                $ri = $this->Settings->item_addition ? $row->id : $c;
                $pr[$ri] = array('id' => $c,
                               'customer_id'=>$row->customer_id,
                               'sl_id' => $row->sl_id,
                               'label'=> $row->reference_no.' เครดิต '.$row->payment_term.' วันครบกำหนด '.$row->due_date,
                                'reference_no' => $row->reference_no,
                                'date' => $row->date,
                    'row' => $row);

                $c++;
            }
            //print_r($pr);
            $edate = $this->sma->fld(trim($this->input->post('edate')));
            $invoice_type = $this->input->post('invoice_type');
           // $this->data[]
            $this->data['inv_items'] = json_encode($pr);
            $this->data['edate'] = $edate;
            $this->data['invoice_type'] = $invoice_type;
            $this->data['sale_type'] = $this->input->get('sale_type');
            $this->data['id'] = $id;
            //$this->data['currencies'] = $this->site->getAllCurrencies();
            $this->data['billers'] = ($this->Owner || $this->Admin || !$this->session->userdata('biller_id')) ? $this->site->getAllCompanies('biller') : null;
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['warehouses'] = $this->site->getAllWarehouses();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('edit_sale')));
            $meta = array('page_title' => lang('edit_sale'), 'bc' => $bc);
            $this->page_construct('invoice/edit', $meta, $this->data);
        }
    }

     public function modal_view($id = null)
    {
        $this->sma->checkPermissions('index', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->invoice_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        //print_r($inv);
        $this->data['inv'] = $inv;

        $this->load->view($this->theme . 'invoice/modal_view', $this->data);
    }
     public function pdf($id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->invoice_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
       $this->data['customer'] = $this->site->getCompanyByID($inv->supplier_id);
       $this->data['user'] = $this->site->getUser($inv->created_by);
       $this->data['inv'] = $inv;
       $this->data['rows'] = $this->invoice_model->getAllInvoiceItems($id);

       $totalRec= count($this->data['rows']);
        if($this->input->get('pepperType')==1){
                $peperType=1;
            }elseif($this->input->get('pepperType')==2){
                $peperType=2;
                //$peperType =2;
            }else{
                $peperType=1;
            }

        $this->data['peperType'] = $peperType;
       // die();
       	$this->data['totalRec'] = $totalRec;
        $name = lang("invoice") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'invoice/pdf', $this->data, true);
        if (! $this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }


        //$peperType = $this->input->get('pepperType');
        if ($view) {
            $this->load->view($this->theme . 'invoice/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generatecustom_pdf($html, $name, $save_bufffer,$totalRec,$peperType);
        } else {
            $this->sma->generatecustom_pdf($html, $name, false,$totalRec,$peperType);
        }
    }


    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }



        if ($this->invoice_model->deleteInvioce($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("sale_deleted")));
            }
            //echo "test";
            $this->session->set_flashdata('message', 'ลบข้อมูลใบวางบิลเรียบร้อยแล้ว');
            admin_redirect('welcome');
        }
    }

}
