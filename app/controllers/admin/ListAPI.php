<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ListAPI extends CI_Controller
{

    function __construct() {
        parent::__construct();
        //$this->load->admin_model('cron_model');
        //$this->Settings = $this->cron_model->getSettings();
    }

    function index() {
        echo '<!doctype html>
                    <html>
                        <head>
                            <title>API LIST</title>
                            <style>
                              p{background:#F5F5F5;border:1px solid #EEE; padding:15px;}
                              .padleft1{
                                padding-left: 100px;
                                background:#F5F5F5;
                                padding-top: 20px;
                              }
                              .padleft2{
                                padding-left: 175px;
                                background:#F5F5F5;
                              }
                            </style>
                        </head>
                        <body>';
        echo '<h1>API LIST.</h1>
                <h1>Products http://demo.jos180.com/api/v1/products</h1>  // to get the products 
                  <p>Options: Code   code=IT01    // to get single product Include  include=brand,category,photos,sub_units  Start from  start=1 Limit result limit=10     // max 20 order_by  name,asc or name,desc  // column,order ASC, DESC, RANDOM</p> 
 
<p style="padding-left:20px;padding-right:20px;"><b>Example:</b> // curl request curl -X GET -H "api-key: yourapikey" http://demo.jos180.com/api/v1/products?include=brand,category 
 <br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// http request http://demo.jos180.com/api/v1/products?include=brand,category&apikey=yourapikey 
 <br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;http://demo.jos180.com/api/v1/products?code=FFR01&include=brand,category& api-key=yourapikey 
</p>

<div class="padleft1"><b>Result:</b>&nbsp;&nbsp; {   "brand": null,   "category": {     "id": "2",</div>
                            <div class="padleft2">"code": "FFR",<br/>
                            "name": "Fruits",<br/>
                            "image": null,<br/>
                            "parent_id": null,<br/>
                            "slug": "fruits"<br/>
                            },<br/>
                            "code": "FFR01",<br/>
                            "id": "1",<br/>
                            "image_url": "http://demo.jos180.com/assets/uploads/99508a37a9e17ee808b79a51a4ebea27.png",<br/>
                            "name": "Grapefruit",<br/>
                             "net_price": "5.60",<br/>
                             "price": "5.60",<br/>
                             "slug": "grapefruit",<br/>
                             "tax_method": "inclusive",<br/>
                             "tax_rate": { "id": "5","name": "VAT @0%",<br/>
                             "code": "Z",<br/>
                             "rate": "0.0000",<br/> 
                             "type": "percentage" <br/>
                             },<br/>
                            "type": "standard",<br/>
                            "unit": {"id": "4",<br/>
                            "code": "pc",<br/>
                            "name": "Piece",<br/>
                            "base_unit": null,<br/>
                            "operator": null,<br/>
                            "unit_value": null,<br/>
                            "operation_value": null<br/>
                            },<br/>
                            "unit_price": "5.60" }<br/> </div>  

<h1>Sales http://demo.jos180.com/api/v1/sales    // to get the sales</h1> 
<p>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Options: Reference  reference= SALE/POS/2015/07/0001 // to get single sale Include  include=items,warehouse  Start from  start=1 Limit result limit=10     // max 20 order_by  date,asc or date,desc  // column,order ASC, DESC, RANDOM 
 </p>
 <p>
Example: // curl request curl -X GET -H "api-key: yourapikey" http://demo.jos180.com/api/v1/sales?include=items,warehouse 
 
        <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// http request http://demo.jos180.com/api/v1/sales?include=items,warehouse&apikey=yourapikey 
         
       <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; http://demo.jos180.com/api/v1/sales?reference=SALE/POS/2015/07/0001&inclu de=items,warehouse&api-key=yourapikey 
</p>
<p>
<b>Result:</b> {   "biller": "Test Biller",
            "biller_id": "3",
            "created_by": {     
                            "avatar_url": null,     
                            "email": "owner@tecdiary.com",     
                            "first_name": "Owner",     
                            "gender": "male",     
                            "id": "1",     
                            "last_name": "Owner",     
                            "username": "owner"   
                            },
            "customer": "Walk-in Customer",   
            "customer_id": "1",   
            "date": "2017-02-07 13:04:07",   
            "due_date": null,   
            "grand_total": "135.0000",   
            "id": "1",   
            "items": [{
                            "comment": null,
                            "discount": "0",
                            "item_discount": "0.0000",       
                            "item_tax": "2.5500", 
                            "net_unit_price": "42.4500",       
                            "product_code": "IT06",       
                            "product_id": "13",       
                            "product_name": "Mouse",       
                            "product_type": "standard",       
                            "product_unit_code": null,       
                            "product_unit_id": null,       
                            "product_unit_quantity": "0.0000",       
                            "product_variant_id": "6",       
                            "product_variant_name": "Red",       
                            "quantity": "1.0000",       
                            "serial_no": "",       
                            "subtotal": "45.0000",       
                            "tax": "6.0000%",       
                            "tax_rate_id": "3",       
                            "unit_price": "45.0000"     
                            },     
                            {"comment": null,       
                             "discount": "0",       
                             "item_discount": "0.0000",       
                             "item_tax": "2.5500",       
                             "net_unit_price": "42.4500",       
                             "product_code": "IT06",       
                             "product_id": "13",       
                             "product_name": "Mouse",       
                             "product_type": "standard",       
                             "product_unit_code": null,       
                             "product_unit_id": null,       
                             "product_unit_quantity": "0.0000",       
                             "product_variant_id": "5",       
                             "product_variant_name": "Black",       
                             "quantity": "1.0000",       
                             "serial_no": "",       
                             "subtotal": "45.0000",       
                             "tax": "6.0000%",       
                             "tax_rate_id": "3",       
                             "unit_price": "45.0000"     
                             },     
                             {       
                                "comment": null,       
                                "discount": "0",       
                                "item_discount": "0.0000",       
                                "item_tax": "2.5500",       
                                "net_unit_price": "42.4500",       
                                "product_code": "IT06",       
                                "product_id": "13",       
                                "product_name": "Mouse",       
                                "product_type": "standard",       
                                "product_unit_code": null,       
                                "product_unit_id": null,       
                                "product_unit_quantity": "0.0000",       
                                "product_variant_id": "7",       
                                "product_variant_name": "Blue",       
                                "quantity": "1.0000",       
                                "serial_no": "",       
                                "subtotal": "45.0000",       
                                "tax": "6.0000%",       
                                "tax_rate_id": "3",       
                                "unit_price": "45.0000"     
                             }],   
                             "note": "",   
                             "order_discount": "0.0000",   
                             "order_discount_id": null,   
                             "order_tax": "0.0000", 
                             "order_tax_id": "1",   
                             "paid": "135.0000",   
                             "payment_status": "paid",   
                             "payment_term": "0",   
                             "product_discount": "0.0000",   
                             "product_tax": "7.6500",   
                             "reference_no": "SALE/POS/2015/07/0001",   
                             "rounding": "0.0000",   
                             "sale_status": "completed",   
                             "shipping": "0.0000",   
                             "total": "127.3500",   
                             "total_discount": "0.0000",   
                             "total_items": "3",   
                             "total_tax": "7.6500",   "updated_by": null,   "warehouse": {     "id": "1",     "code": "WHI",     "name": "Warehouse 1",     "address": "Address, City",     "map": null,     "phone": "012345678",     "email": "whi@tecdiary.com",     "price_group_id": null   },   "warehouse_id": "1" } 
 </p>
 
<h1>Quotes http://ci.dev/sma/api/v1/quotes    // to get the quotations</h1>
<p> 
 <b>Options:</b> Reference  reference= QU/2017/03/0001  // to get single quotation Include  include=items,warehouse  Start from  start=1 Limit result limit=10     // max 20 order_by  date,asc or date,desc  // column,order ASC, DESC, RANDOM   
</p>
 
<h1>Purchases http://demo.jos180.comapi/v1/purchases   // to get the purchases</h1> 
<p>Options: Reference  reference= SALE/POS/2015/07/0001 // to get single purchase Include  include=items,warehouse  Start from  start=1 Limit result limit=10<br/>     
   // max 20 order_by  date,asc or date,desc  <br/>
   // column,order ASC, DESC, RANDOM<br/> 
   <b>Example:</b> <br/>
     <b>curl request curl -X GET -H "api-key: yourapikey" </b>http://demo.jos180.comapi/v1/purchases?reference=PO/2017/03/0001&include =items,warehouse
  
 
// http request http://demo.jos180.comapi/v1/purchases?reference=PO/2017/03/0001&include =items,warehouse&api-key=yourapikey 
 </p> 
 <div class="padleft1"><b>Result:</b>{   "created_by": 
                                            { "avatar_url": null,
                                              "email": "owner@tecdiary.com",     
                                              "first_name": "Owner",     
                                              "gender": "male",     
                                              "id": "1",     
                                              "last_name": "Owner",     
                                              "username": "owner"   },   "date": "2017-03-28 18:10:00",   "due_date": null,   "grand_total": "38.9000",   "id": "1",   "items": [     {       "discount": "0",       "expiry": null,       "item_discount": "0.0000",       "item_tax": "0.0000",       "net_unit_cost": "3.8900",       "product_code": "FFR01",       "product_id": "1",       "product_name": "Grapefruit",       "product_unit_code": "pc",       "product_unit_id": "4",       "product_unit_quantity": "10.0000",       "quantity": "10.0000",
                                               "status": "received",       "subtotal": "38.9000",       "tax": "0.0000",       "tax_rate_id": "5",       "unit_cost": "3.8900"     }   ],   "note": "",   "order_discount": "0.0000",   "order_discount_id": null,   "order_tax": "0.0000",   "order_tax_id": "1",   "paid": "38.9000",   "payment_status": "paid",   "payment_term": "0",   "product_discount": "0.0000",   "product_tax": "0.0000",   "reference_no": "PO/2017/03/0001",   "shipping": "0.0000",   "status": "received",   "supplier": "Fruits Supply Masters",   "supplier_id": "4",   "surcharge": "0.0000",   "total": "38.9000",   "total_discount": "0.0000",   "total_tax": "0.0000",   "updated_by": null,   "warehouse": {     "id": "1",     "code": "WHI",     "name": "Warehouse 1",     "address": "<p>Address, City</p>",     "map": null,     "phone": "012345678",     "email": "whi@tecdiary.com",     "price_group_id": null   },   "warehouse_id": "1" } 
 </div>
<h1>Transfers http://demo.jos180.com/api/v1/transfers   // to get the transfers </h1>
<p>Options: Reference  reference= TO/2017/03/0001<br/>  
        // to get single transfer Include  include=items,warehouse  Start from  start=1 Limit result limit=10<br/>     
        // max 20 order_by  date,asc or date,desc  <br/>
        // column,order ASC, DESC, RANDOM   <br/>
</p>
 
<h1>Warehouses http://demo.jos180.com/api/v1/warehouses   // to get the warehouses</h1> 
<p>Options: Code   code=WH-1 <br/>     
    // to get single warehouse Include  include=price_group  Start from  start=1 Limit result limit=10<br/>     
    // max 20 order_by  name,asc or name,desc  <br/>
    // column,order ASC, DESC, RANDOM Example: <br/>
    // curl request curl -X GET -H "api-key: yourapikey" http://demo.jos180.com/api/v1/warehouses?include=price_group
    <br/>
    
 
// http request http://demo.jos180.com/api/v1/warehouses?include=price_group&apikey=yourapikey
</p> 
 
 <div class="padleft1"><b>Result:</b>{
                                "data": [{"address": "Address, City",       
                                             "code": "WHI",       
                                             "email": "whi@tecdiary.com",       
                                             "id": "1",       
                                             "map_url": null,       
                                             "name": "Warehouse 1",       
                                             "phone": "012345678"     
                                 },     
                                 {"address": "Warehouse 2, Jalan Sultan Ismail, 54000, Kuala Lumpur",       
                                   "code": "WHII",       
                                   "email": "whii@tecdiary.com",       
                                   "id": "2",       
                                   "map_url": null,       
                                   "name": "Warehouse 2",       
                                   "phone": "0105292122"     
                                   }],   "limit": 10,   "start": 1,   "total": 2 }
</div>   
 
<h1>Companies http://demo.jos180.com/api/v1/companies   // to get the companies</h1> 
<p>Options: Name   name=IT Hypermart</br>   
    // to get single company Group   group=customer</br>   
    // customer, supplier, biller Include  include=user </br>   
    // for customer group only Start from  start=1 Limit result limit=10</br>     
    // max 20 order_by  name,asc or name,desc  </br>
    // column,order ASC, DESC, RANDOM Example: </br>
    // curl request to get customers curl -X GET -H "api-key: yourapikey" http://demo.jos180.com/api/v1/companies?group=supplier</br> 
    // curl request to get suppliers curl -X GET -H "api-key: yourapikey" http://demo.jos180.com/api/v1/companies?group=supplier </br>
    // http request http://demo.jos180.com/api/v1/companies?group=supplier&api-key=yourapikey </br>
    </p>

    <div class="padleft1"><b>Result:</b>{   
                                    "data": [{"address": "51, Section 51A",       
                                    "cf1": "",       
                                    "cf2": "",       
                                    "cf3": "",       
                                    "cf4": "",       
                                    "cf5": "",       
                                    "cf6": "",       
                                    "city": "Bangkok",       
                                    "company": "JITSUITE",       
                                    "country": "",       
                                    "email": "supplier2@jitsuite.com",       
                                    "person": "NG Lim",       
                                    "phone": "012345678",       
                                    "postal_code": "",       
                                    "state": "",       
                                    "vat_no": ""     
                                    },     
                                    {       
                                    "address": "Supplier Address",       
                                    "cf1": "-", 
                                    "cf2": "-",       
                                    "cf3": "-",       
                                    "cf4": "-",       
                                    "cf5": "-",       
                                    "cf6": "-",       
                                    "city": "Bangkok",       
                                    "company": "JITSUITE",       
                                    "country": "Thailand",       
                                    "email": "supplier@jitsuite.com",       
                                    "person": "Sakda Choommanee",       
                                    "phone": "0824898822",       
                                    "postal_code": "10220",       
                                    "state": "Saimai",       
                                    "vat_no": ""     
                                    }],   
                                    "limit": 10,   
                                    "start": 1,   
                                    "total": 3 
                                    }
                                    </div> 
                ';

        echo '</body></html>';
    }
}
?>