<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends MY_Shop_Controller
{

    function __construct() {
        parent::__construct();
        if ($this->Settings->mmode) { redirect('notify/offline'); }
        $this->load->library('form_validation');
    }

    // Display Page
    function page($slug) {
        $page = $this->shop_model->getPageBySlug($slug);
        $this->data['page'] = $page;
        $this->data['page_title'] = $page->title;
        $this->data['page_desc'] = $page->description;
        $this->page_construct('pages/page', $this->data);
    }

    // Display Page
    function product($slug) {
        $product = $this->shop_model->getProductBySlug($slug);
        if (!$slug || !$product) {
            $this->session->set_flashdata('error', lang('product_not_found'));
            $this->sma->md('/');
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $product->code . '/' . $product->barcode_symbology . '/40/0') . "' alt='" . $product->code . "' class='pull-left' />";
        if ($product->type == 'combo') {
            $this->data['combo_items'] = $this->shop_model->getProductComboItems($product->id);
        }
        $this->shop_model->updateProductViews($product->id, $product->views);
        $this->data['product'] = $product;
        $this->data['unit'] = $this->site->getUnitByID($product->unit);
        $this->data['brand'] = $this->site->getBrandByID($product->brand);
        $this->data['images'] = $this->shop_model->getProductPhotos($product->id);
        $this->data['category'] = $this->site->getCategoryByID($product->category_id);
        $this->data['subcategory'] = $product->subcategory_id ? $this->site->getCategoryByID($product->subcategory_id) : NULL;
        $this->data['tax_rate'] = $product->tax_rate ? $this->site->getTaxRateByID($product->tax_rate) : NULL;
        $this->data['warehouse'] = $this->shop_model->getAllWarehouseWithPQ($product->id);
        $this->data['options'] = $this->shop_model->getProductOptionsWithWH($product->id);
        $this->data['variants'] = $this->shop_model->getProductOptions($product->id);
        $this->load->helper('text');
        $this->data['page_title'] = $product->code.' - '.$product->name;
        $this->data['page_desc'] = character_limiter(strip_tags($product->product_details), 160);
        $this->page_construct('pages/view_product', $this->data);
    }

    // Products,  categories and brands page
    function products($category_slug = NULL, $subcategory_slug = NULL, $brand_slug = NULL) {
        $this->session->set_userdata('requested_page', $this->uri->uri_string());
        if ($this->input->get('category')) { $category_slug = $this->input->get('category', TRUE); }
        if ($this->input->get('brand')) { $brand_slug = $this->input->get('brand', TRUE); }
        $reset = $category_slug || $subcategory_slug || $brand_slug ? TRUE : FALSE;

        $filters = array(
            'query' => $this->input->post('query'),
            'category' => $category_slug ? $this->shop_model->getCategoryBySlug($category_slug) : NULL,
            'subcategory' => $subcategory_slug ? $this->shop_model->getCategoryBySlug($subcategory_slug) : NULL,
            'brand' => $brand_slug ? $this->shop_model->getBrandBySlug($brand_slug) : NULL,
            'sorting' => $reset ? NULL : $this->input->get('sorting'),
            'min_price' => $reset ? NULL : $this->input->get('min_price'),
            'max_price' => $reset ? NULL : $this->input->get('max_price'),
            'in_stock' => $reset ? NULL : $this->input->get('in_stock'),
            'page' => $this->input->get('page') ? $this->input->get('page', TRUE) : 1,
        );

        $this->data['filters'] = $filters;
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products'). ' - ' .$this->shop_settings->shop_name;
        $this->data['page_desc'] = 'This is sma shop products page description';
        $this->page_construct('pages/products', $this->data);
    }

    // Search products page - ajax
    function search() {
        $filters = $this->input->post('filters') ? $this->input->post('filters', TRUE) : FALSE;
        $limit = 12;
        $total_rows = $this->shop_model->getProductsCount($filters);
        $filters['limit'] = $limit;
        $filters['offset'] = isset($filters['page']) && !empty($filters['page']) && ($filters['page'] > 1) ? (($filters['page']*$limit)-$limit) : NULL;

        if ($products = $this->shop_model->getProducts($filters)) {
            $this->load->helper(array('text', 'pagination'));
            foreach($products as &$value) {
                $value['details'] = character_limiter(strip_tags($value['details']), 140);
                $value['price'] = $this->sma->convertMoney($value['promotion'] ? $value['promo_price'] : $value['price']);
                $value['promo_price'] = $this->sma->convertMoney($value['promo_price']);
            }

            $pagination = pagination('shop/products', $total_rows, $limit);
            $info = array('page' => (isset($filters['page']) && !empty($filters['page']) ? $filters['page'] : 1), 'total' => ceil($total_rows/$limit));

            $this->sma->send_json(array('filters' => $filters, 'products' => $products, 'pagination' => $pagination, 'info' => $info));
        } else {
            $this->sma->send_json(array('filters' => $filters, 'products' => FALSE, 'pagination' => FALSE, 'info' => FALSE));
        }
    }

    // Customer quotations
    function quotes($id = NULL, $hash = NULL) {
        if (!$this->loggedIn && !$hash) { redirect('login'); }
        if ($this->Staff) { admin_redirect('quotes'); }
        if ($id) {
            if ($order = $this->shop_model->getQuote(['id' => $id, 'hash' => $hash])) {
                $this->data['inv'] = $order;
                $this->data['rows'] = $this->shop_model->getQuoteItems($id);
                $this->data['customer'] = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller'] = $this->site->getCompanyByID($order->biller_id);
                $this->data['created_by'] = $this->site->getUser($order->created_by);
                $this->data['updated_by'] = $this->site->getUser($order->updated_by);
                $this->data['page_title'] = lang('view_quote');
                $this->data['page_desc'] = '';
                $this->page_construct('pages/view_quote', $this->data);
            } else {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect('/');
            }
        } else {
            if ($this->input->get('download')) {
                $id = $this->input->get('download', TRUE);
                $order = $this->shop_model->getQuote(['id' => $id]);
                $this->data['inv'] = $order;
                $this->data['rows'] = $this->shop_model->getQuoteItems($id);
                $this->data['customer'] = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller'] = $this->site->getCompanyByID($order->biller_id);
                // $this->data['created_by'] = $this->site->getUser($order->created_by);
                // $this->data['updated_by'] = $this->site->getUser($order->updated_by);
                $this->data['Settings'] = $this->Settings;
                $html = $this->load->view($this->Settings->theme.'/shop/views/pages/pdf_quote', $this->data, TRUE);
                if ($this->input->get('view')) {
                    echo $html;
                    exit;
                } else {
                    $name = lang("quote") . "_" . str_replace('/', '_', $order->reference_no) . ".pdf";
                    $this->sma->generate_pdf($html, $name);
                }
            }
            $page = $this->input->get('page') ? $this->input->get('page', TRUE) : 1;
            $limit = 10;
            $offset = ($page*$limit)-$limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getQuotesCount();
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['orders'] = $this->shop_model->getQuotes($limit, $offset);
            $this->data['pagination'] = pagination('shop/quotes', $total_rows, $limit);
            $this->data['page_info'] = array('page' => $page, 'total' => ceil($total_rows/$limit));
            $this->data['page_title'] = lang('my_orders');
            $this->data['page_desc'] = '';
            $this->page_construct('pages/quotes', $this->data);
        }
    }

    // Customer order/orders page
    function orders($id = NULL, $hash = NULL) {
        if (!$this->loggedIn && !$hash) { redirect('login'); }
        if ($this->Staff) { admin_redirect('sales'); }
        if ($id) {
            if ($order = $this->shop_model->getOrder(['id' => $id, 'hash' => $hash])) {
                $this->data['inv'] = $order;
                $this->data['rows'] = $this->shop_model->getOrderItems($id);
                $this->data['customer'] = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller'] = $this->site->getCompanyByID($order->biller_id);
                $this->data['address'] = $this->shop_model->getAddressByID($order->address_id);
                $this->data['return_sale'] = $order->return_id ? $this->shop_model->getOrder(['id' => $id]) : NULL;
                $this->data['return_rows'] = $order->return_id ? $this->shop_model->getOrderItems($order->return_id) : NULL;
                $this->data['paypal'] = $this->shop_model->getPaypalSettings();
                $this->data['skrill'] = $this->shop_model->getSkrillSettings();
                $this->data['page_title'] = lang('view_order');
                $this->data['page_desc'] = '';
                $this->page_construct('pages/view_order', $this->data);
            } else {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect('/');
            }
        } else {
            if ($this->input->get('download')) {
                $id = $this->input->get('download', TRUE);
                $order = $this->shop_model->getOrder(['id' => $id]);
                $this->data['inv'] = $order;
                $this->data['rows'] = $this->shop_model->getOrderItems($id);
                $this->data['customer'] = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller'] = $this->site->getCompanyByID($order->biller_id);
                $this->data['address'] = $this->shop_model->getAddressByID($order->address_id);
                $this->data['return_sale'] = $order->return_id ? $this->shop_model->getOrder(['id' => $id]) : NULL;
                $this->data['return_rows'] = $order->return_id ? $this->shop_model->getOrderItems($order->return_id) : NULL;
                $this->data['Settings'] = $this->Settings;
                $html = $this->load->view($this->Settings->theme.'/shop/views/pages/pdf_invoice', $this->data, TRUE);
                if ($this->input->get('view')) {
                    echo $html;
                    exit;
                } else {
                    $name = lang("invoice") . "_" . str_replace('/', '_', $order->reference_no) . ".pdf";
                    $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
                }
            }
            $page = $this->input->get('page') ? $this->input->get('page', TRUE) : 1;
            $limit = 10;
            $offset = ($page*$limit)-$limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getOrdersCount();
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['orders'] = $this->shop_model->getOrders($limit, $offset);
            $this->data['pagination'] = pagination('shop/orders', $total_rows, $limit);
            $this->data['page_info'] = array('page' => $page, 'total' => ceil($total_rows/$limit));
            $this->data['page_title'] = lang('my_orders');
            $this->data['page_desc'] = '';
            $this->page_construct('pages/orders', $this->data);
        }
    }

    // Add new Order form shop
    function order() {
        if (!$this->loggedIn) { redirect('login'); }
        $this->form_validation->set_rules('address', lang("address"), 'trim|required');
        $this->form_validation->set_rules('note', lang("comment"), 'trim');

        if ($this->form_validation->run() == true) {

            if ($address = $this->shop_model->getAddressByID($this->input->post('address'))) {
                $customer = $this->site->getCompanyByID($this->session->userdata('company_id'));
                $biller = $this->site->getCompanyByID($this->shop_settings->biller);
                $note = $this->db->escape_str($this->input->post('comment'));
                $product_tax = 0; $total = 0;
                $gst_data = [];
                $total_cgst = $total_sgst = $total_igst = 0;
                foreach ($this->cart->contents() as $item) {
                    $item_option = NULL;
                    if ($product_details = $this->site->getProductByID($item['product_id'])) {
                        $price = $product_details->promotion ? $product_details->promo_price : $product_details->price;
                        if ($item['option']) {
                            if ($product_variant = $this->shop_model->getProductVariantByID($item['option'])) {
                                $item_option = $product_variant->id;
                                $price = $product_variant->price+$price;
                            }
                        }

                        $item_net_price = $unit_price = $price;
                        $item_quantity = $item_unit_quantity = $item['qty'];
                        $pr_item_tax = $item_tax = 0;
                        $tax = "";

                        if (!empty($product_details->tax_rate)) {

                            $tax_details = $this->site->getTaxRateByID($product_details->tax_rate);
                            $ctax = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                            $item_tax = $ctax['amount'];
                            $tax = $ctax['tax'];
                            if ($product_details->tax_method != 1) {
                                $item_net_price = $unit_price - $item_tax;
                            }
                            $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                            if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller->state == $customer->state), $tax_details)) {
                                $total_cgst += $gst_data['cgst'];
                                $total_sgst += $gst_data['sgst'];
                                $total_igst += $gst_data['igst'];
                            }
                        }

                        $product_tax += $pr_item_tax;
                        $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);

                        $unit = $this->site->getUnitByID($product_details->unit);

                        $product = [
                        'product_id' => $product_details->id,
                        'product_code' => $product_details->code,
                        'product_name' => $product_details->name,
                        'product_type' => $product_details->type,
                        'option_id' => $item_option,
                        'net_unit_price' => $item_net_price,
                        'unit_price' => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity' => $item_quantity,
                        'product_unit_id' => $unit ? $unit->id : NULL,
                        'product_unit_code' => $unit ? $unit->code : NULL,
                        'unit_quantity' => $item_unit_quantity,
                        'warehouse_id' => $this->shop_settings->warehouse,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $product_details->tax_rate,
                        'tax' => $tax,
                        'discount' => NULL,
                        'item_discount' => 0,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => NULL,
                        'real_unit_price' => $price,
                        ];

                        $products[] = ($product + $gst_data);
                        $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);

                    } else {
                        $this->session->set_flashdata('error', lang('product_x_found').' ('.$item['name'].')');
                        redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cart');
                    }
                }

                $shipping = $this->shop_settings->shipping;
                $order_tax = $this->site->calculateOrderTax($this->Settings->default_tax_rate2, ($total + $product_tax));
                $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
                $grand_total = $this->sma->formatDecimal(($total + $total_tax + $shipping), 4);

                $data = [
                'date' => date('Y-m-d H:i:s'),
                'reference_no' => $this->site->getReference('so'),
                'customer_id' => $customer->id,
                'customer' => ($customer->company && $customer->company != '-' ? $customer->company : $customer->name),
                'biller_id' => $biller->id,
                'biller' => ($biller->company && $biller->company != '-' ? $biller->company : $biller->name),
                'warehouse_id' => $this->shop_settings->warehouse,
                'note' => $note,
                'staff_note' => NULL,
                'total' => $total,
                'product_discount' => 0,
                'order_discount_id' => NULL,
                'order_discount' => 0,
                'total_discount' => 0,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->Settings->default_tax_rate2,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $shipping,
                'grand_total' => $grand_total,
                'total_items' => $this->cart->total_items(),
                'sale_status' => 'pending',
                'payment_status' => 'pending',
                'payment_term' => NULL,
                'due_date' => NULL,
                'paid' => 0,
                'created_by' => $this->session->userdata('user_id'),
                'shop' => 1,
                'address_id' => $address->id,
                'hash' => hash('sha256', microtime() . mt_rand()),
                ];
                if ($this->Settings->invoice_view == 2) {
                    $data['cgst'] = $total_cgst;
                    $data['sgst'] = $total_sgst;
                    $data['igst'] = $total_igst;
                }

                // $this->sma->print_arrays($data, $products);

                if ($sale_id = $this->shop_model->addSale($data, $products)) {
                    $this->cart->destroy();
                    $this->session->set_flashdata('info', lang('order_added_make_payment'));
                    shop_redirect('orders/'.$sale_id.'/'.($this->loggedIn ? '' : $data['hash']));
                }

            } else {
                $this->session->set_flashdata('error', lang('address_x_found'));
                redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cart/checkout');
            }


        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect('cart/checkout');
        }
    }

    // Customer address list
    function addresses() {
        if (!$this->loggedIn) { redirect('login'); }
        if ($this->Staff) { admin_redirect('customers'); }
        $this->session->set_userdata('requested_page', $this->uri->uri_string());
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['addresses'] = $this->shop_model->getAddresses();
        $this->data['page_title'] = lang('my_addresses');
        $this->data['page_desc'] = '';
        $this->page_construct('pages/addresses', $this->data);
    }

    // Add/edit customer address
    function address($id = NULL) {
        if (!$this->loggedIn) { $this->sma->send_json(array('status' => 'error', 'message' => lang('please_login'))); }
        $this->form_validation->set_rules('line1', lang("line1"), 'trim|required');
        $this->form_validation->set_rules('line2', lang("line2"), 'trim|required');
        $this->form_validation->set_rules('city', lang("city"), 'trim|required');
        $this->form_validation->set_rules('state', lang("state"), 'trim|required');
        $this->form_validation->set_rules('postal_code', lang("postal_code"), 'trim|required');
        $this->form_validation->set_rules('country', lang("country"), 'trim|required');
        $this->form_validation->set_rules('phone', lang("phone"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $user_addresses = $this->shop_model->getAddresses();
            if (count($user_addresses) >= 6) {
                $this->sma->send_json(array('status' => 'error', 'message' => lang('already_have_max_addresses'), 'level' => 'error'));
            }

            $data = ['line1' => $this->input->post('line1'),
                'line2' => $this->input->post('line2'),
                'phone' => $this->input->post('phone'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'company_id' => $this->session->userdata('company_id')];

            if ($id) {
                $this->db->update('addresses', $data, ['id' => $id]);
                $this->session->set_flashdata('message', lang('address_updated'));
                $this->sma->send_json(array('redirect' => $_SERVER['HTTP_REFERER']));
            } else {
                $this->db->insert('addresses', $data);
                $this->session->set_flashdata('message', lang('address_added'));
                $this->sma->send_json(array('redirect' => $_SERVER['HTTP_REFERER']));
            }

        } elseif ($this->input->is_ajax_request()) {
            $this->sma->send_json(array('status' => 'error', 'message' => validation_errors()));
        } else {
            shop_redirect('shop/addresses');
        }
    }

    // Customer wishlist page
    function wishlist() {
        if (!$this->loggedIn) { redirect('login'); }
        $this->session->set_userdata('requested_page', $this->uri->uri_string());
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $total = $this->shop_model->getWishlist(TRUE);
        $products = $this->shop_model->getWishlist();
        $this->load->helper('text');
        foreach($products as $product) {
            $item = $this->shop_model->getProductByID($product->product_id);
            $item->details = character_limiter(strip_tags($item->details), 140);
            $items[] = $item;
        }
        $this->data['items'] = $products ? $items : NULL;
        $this->data['page_title'] = lang('wishlist');
        $this->data['page_desc'] = '';
        $this->page_construct('pages/wishlist', $this->data);
    }

    // Send us email
    function send_message() {

        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('email', lang("email"), 'required|valid_email');
        $this->form_validation->set_rules('subject', lang("subject"), 'required');
        $this->form_validation->set_rules('message', lang("message"), 'required');

        if ($this->form_validation->run() == true) {

            try {
                if ($this->sma->send_email($this->shop_settings->email, $this->input->post('subject'), $this->input->post('message'), $this->input->post('email'), $this->input->post('name'))) {
                    $this->sma->send_json(array('status' => 'Success', 'message' => lang('message_sent')));
                }
                $this->sma->send_json(array('status' => 'error', 'message' => lang('action_failed')));
            } catch (Exception $e) {
                $this->sma->send_json(array('status' => 'error', 'message' => $e->getMessage()));
            }

        } elseif ($this->input->is_ajax_request()) {
            $this->sma->send_json(array('status' => 'Error!', 'message' => validation_errors(), 'level' => 'error'));
        } else {
            $this->session->set_flashdata('warning', 'Please try to send message from contact page!');
            shop_redirect();
        }
    }

    // Add attachment to sale on manual payment
    function manual_payment($order_id) {
        if ($_FILES['payment_receipt']['size'] > 0) {
            $this->load->library('upload');
            $config['upload_path'] = 'files/';
            $config['allowed_types'] = 'zip|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
            $config['max_size'] = 2048;
            $config['overwrite'] = FALSE;
            $config['max_filename'] = 25;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('payment_receipt')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $manual_payment = $this->upload->file_name;
            $this->db->update('sales', ['attachment' => $manual_payment], ['id' => $order_id]);
            $this->session->set_flashdata('message', lang('file_submitted'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '/shop/orders');
        }
    }

    // Digital products download
    function downloads($id = NULL, $hash = NULL) {
        if (!$this->loggedIn) { redirect('login'); }
        if ($this->Staff) { admin_redirect(); }
        if ($id && $hash && md5($id) == $hash) {
            $sale = $this->shop_model->getDownloads(1, 0, $id);
            if (!empty($sale)) {
                $product = $this->site->getProductByID($id);
                if (file_exists('./files/'.$product->file)) {
                    $this->load->helper('download');
                    force_download('./files/'.$product->file, NULL);
                    exit;
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . basename($product->file) . "\"");
                    // header('Content-Length: ' . filesize($product->file));
                    readfile($product->file);
                }
            }
            $this->session->set_flashdata('error', lang('file_x_exist'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $page = $this->input->get('page') ? $this->input->get('page', TRUE) : 1;
            $limit = 10;
            $offset = ($page*$limit)-$limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getDownloadsCount();
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['downloads'] = $this->shop_model->getDownloads($limit, $offset);
            $this->data['pagination'] = pagination('shop/download', $total_rows, $limit);
            $this->data['page_info'] = array('page' => $page, 'total' => ceil($total_rows/$limit));
            $this->data['page_title'] = lang('my_downloads');
            $this->data['page_desc'] = '';
            $this->page_construct('pages/downloads', $this->data);
        }
    }

}
