<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotions_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addPromotions($data = array(), $items = array())
    {

        //print_r($data);
        if ($this->db->insert('promotions_header', $data)) {
            $pro_id = $this->db->insert_id();

            // die();
            //if ($this->site->getReference('PRO') == $data['reference_no']) {
            //    $this->site->updateReference('PRO');
            //}

            foreach ($items as $item) {

                $item['pro_id'] = $pro_id;
                $this->db->insert('promotions_data', $item);
                // $invoice_item_id = $this->db->insert_id();
              //  $this->db->update('sales', array('invoice_id' => $invoice_id,'invoice_refference_no'=>$data['reference_no']), array('id' => $item['sl_id']));
            }
            $this->db->last_query();
            return $pro_id;

        }
        //echo $this->db->last_query();
        //die();
        return false;
    }

    public function updatePromotions($id, $data, $items = array())
    {
        $Olditems = $this->getAllPromotionsItems($id);

        if ($this->db->update('promotions_header', $data, array('pro_id' => $id)) &&
            $this->db->delete('promotions_data', array('pro_id' => $id)) ) {
            foreach ($items as $item) {
                $item['pro_id'] = $id;
                $this->db->insert('promotions_data', $item);
                $pro_id = $this->db->insert_id();
                //$this->db->update('sales', array('invoice_id' => $id,'invoice_refference_no'=>$item['reference_no']), array('id' => $item['sl_id']));
            }
           // echo $this->db->last_query();
           // die();
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $note)
    {

        $sale = $this->getInvoiceByID($id);
        $items = $this->getAllInvoiceItems($id);
        $cost = array();
        if ($status == 'completed' && $status != $sale->sale_status) {
            foreach ($items as $item) {
                $items_array[] = (array) $item;
            }
            $cost = $this->site->costing($items_array);
        }

        if ($this->db->update('sales', array('sale_status' => $status, 'note' => $note), array('id' => $id))) {

            if ($status == 'completed' && $status != $sale->sale_status) {

                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->site->getProductByID($item['product_id'])) {
                        $item_costs = $this->site->item_costing($item);
                        foreach ($item_costs as $item_cost) {
                            $item_cost['sale_item_id'] = $item['id'];
                            $item_cost['sale_id'] = $id;
                            $item_cost['date'] = date('Y-m-d', strtotime($sale->date));
                            if(! isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        }
                    }
                }

            } elseif ($status != 'completed' && $sale->sale_status == 'completed') {
                $this->resetSaleActions($id);
            }

            if (!empty($cost)) { $this->site->syncPurchaseItems($cost); }
            return true;
        }
        return false;
    }

    public function deletePromotions($id)
    {
        //$sale_items = $this->resetSaleActions($id);
        if ($this->db->delete('promotions_data', array('pro_id' => $id))) {

            $this->db->delete('promotions_header', array('pro_id' => $id));
            return true;
        }
        return FALSE;
    }


    public function getPromotionsByID($id)
    {
        $q = $this->db->get_where('promotions_header', array('pro_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllPromotionsItems($pro_id, $return_id = NULL)
    {
        $this->db->select('promotions_data.prod_id as id ,promotions_data.prod_product_id,promotions_data.prod_unit_code , promotions_data.prod_qty, promotions_data.*,'.$this->db->dbprefix('units').'.name as unit')
            ->join('products', 'products.id=promotions_data.prod_product_id', 'left')
            ->join('units', 'promotions_data.prod_unit_code=units.id', 'left')
            ->group_by('promotions_data.prod_product_id')
            ->order_by('promotions_data.prod_product_id', 'desc');

        $this->db->where('promotions_data.pro_id', $pro_id);

        $q = $this->db->get('promotions_data');
        //echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductOptions($pid)
    {
        $q = $this->db->get_where('product_variants', array('product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductsForOnNewItemPrinting($term,$warehouse_id=null, $limit = 5)
    {
        $this->db->select($this->db->dbprefix('products') . ".id as id, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE(wp.quantity, 0) as quantity,{$this->db->dbprefix('units')}.id as unit_id,{$this->db->dbprefix('units')}.name as unit, wp.rack as rack, alert_quantity", FALSE)
            ->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} ) wp", 'products.id=wp.product_id', 'left')
            ->join('categories', 'products.category_id=categories.id', 'left')
            ->join('units', 'products.unit=units.id', 'left')
            ->join('brands', 'products.brand=brands.id', 'left')
            ->where("(" . $this->db->dbprefix('products') . ".name LIKE '%" . $term . "%' OR " . $this->db->dbprefix('products') . ".code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('products') . ".name, ' (', " . $this->db->dbprefix('products') . ".code, ')') LIKE '%" . $term . "%')")
            ->limit($limit);
        $q = $this->db->get('products');

      // $sql =  $this->db->last_query();
       // echo $sql;
       // die();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }


    public function getSaleRec($term,$warehouse_id,$customer_id,$limit=20){
        $this->db
            ->select("{$this->db->dbprefix('sales')}.id as id, DATE_FORMAT({$this->db->dbprefix('sales')}.date, '%Y-%m-%d %T') as date, reference_no, biller, {$this->db->dbprefix('sales')}.customer, sale_status, grand_total,grand_total as item_pay_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('sales')}.attachment, return_id,payment_term,	due_date,bill_status")
            ->where('invoice_refference_no =',"")
            ->where('payment_status !=', "paid")
            ->where('customer_id',$customer_id)

            ->where('payment_status !=',"pending")
            ->where('warehouse_id', $warehouse_id);
        $this->db->where("({$this->db->dbprefix('sales')}.reference_no LIKE '%" . $term . "%' OR {$this->db->dbprefix('sales')}.customer LIKE '%" . $term . "%' )");
        $this->db->limit($limit);
        $q = $this->db->get('sales');
        //echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            //print_r($data);
            return $data;
        }
    }



}
