<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addInvoice($data = array(), $items = array())
    {

        //print_r($data);
        if ($this->db->insert('invoice', $data)) {
            $invoice_id = $this->db->insert_id();

            // die();
            if ($this->site->getReference('LB') == $data['reference_no']) {
                $this->site->updateReference('LB');
            }


            foreach ($items as $item) {

                $item['invoice_id'] = $invoice_id;
                $this->db->insert('invoice_items', $item);
                // $invoice_item_id = $this->db->insert_id();
                $this->db->update('sales', array('invoice_id' => $invoice_id,'invoice_refference_no'=>$data['reference_no']), array('id' => $item['sl_id']));
            }
            $this->db->last_query();
            return $invoice_id;

        }
        //echo $this->db->last_query();
        //die();
        return false;
    }

    public function updateInvoice($id, $data, $items = array())
    {

        // $this->sma->print_arrays($cost);
        //reset Stattus
        $Olditems = $this->getAllInvoiceItems($id);
        foreach ($Olditems as $item) {
                $this->db->update('sales', array('invoice_id' => "",'invoice_refference_no'=>""), array('id' => $item->sl_id));
        }

        if ($this->db->update('invoice', $data, array('id' => $id)) &&
            $this->db->delete('invoice_items', array('invoice_id' => $id)) ) {

            foreach ($items as $item) {

                $item['invoice_id'] = $id;
                $this->db->insert('invoice_items', $item);
                $sale_item_id = $this->db->insert_id();

                $this->db->update('sales', array('invoice_id' => $id,'invoice_refference_no'=>$item['reference_no']), array('id' => $item['sl_id']));

            }


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

    public function deleteInvoice($id)
    {
        $sale_items = $this->resetSaleActions($id);
        if ($this->db->delete('sale_items', array('sale_id' => $id)) &&
        $this->db->delete('sales', array('id' => $id)) &&
        $this->db->delete('costing', array('sale_id' => $id))) {
            $this->db->delete('sales', array('sale_id' => $id));
            $this->db->delete('payments', array('sale_id' => $id));
            $this->site->syncQuantity(NULL, NULL, $sale_items);
            return true;
        }
        return FALSE;
    }


    public function getInvoiceByID($id)
    {
        $q = $this->db->get_where('invoice', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllInvoiceItems($sale_id, $return_id = NULL)
    {
        $this->db->select('invoice_items.id as id ,invoice_items.sl_id,invoice_items.total_amount , invoice_items.total_paybill, sales.*')
            ->join('sales', 'sales.id=invoice_items.sl_id', 'left')
            ->group_by('invoice_items.id')
            ->order_by('invoice_items.id', 'desc');

        $this->db->where('invoice_items.invoice_id', $sale_id);

        $q = $this->db->get('invoice_items');
        //echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
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

    public function deleteInvioce($invId){
       // echo $invId;
        $Olditems = $this->getAllInvoiceItems($invId);
        //print_r($Olditems);
            foreach ($Olditems as $item) {
                //  print_r($item);
                $this->db->update('sales', array('invoice_id' => "",'invoice_refference_no'=>""), array('id' => $item->sl_id));
            }
        //}


        if ($this->db->delete('invoice_items',array('invoice_id' => $invId)) && $this->db->delete('invoice',array('id' => $invId))) {
            return true;
        }
        return false;
    }

}
