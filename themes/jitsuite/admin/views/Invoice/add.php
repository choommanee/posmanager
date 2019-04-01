<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1;
     $(document).ready(function () {
        if (localStorage.getItem('remove_invoice')) {
            if (localStorage.getItem('qaitems')) {
                localStorage.removeItem('qaitems');
            }
            if (localStorage.getItem('qaref')) {
                localStorage.removeItem('qaref');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            if (localStorage.getItem('edate')) {
                localStorage.removeItem('edate');
            }
            if (localStorage.getItem('invoice_type')) {
                localStorage.removeItem('invoice_type');
            }

            localStorage.removeItem('remove_invoice');

        }

       // var slcustomer = localStorage.getItem('slcustomer');

         if (!localStorage.getItem('sldate')) {
             $("#sldate").datetimepicker({
                 format: site.dateFormats.js_ldate,
                 fontAwesome: true,
                 language: 'sma',
                 weekStart: 1,
                 todayBtn: 1,
                 autoclose: 1,
                 todayHighlight: 1,
                 startView: 2,
                 forceParse: 0
             }).datetimepicker('update', new Date());
         }

         if (!localStorage.getItem('edate')) {
             $("#edate").datetimepicker({
                 format: site.dateFormats.js_ldate,
                 fontAwesome: true,
                 language: 'sma',
                 weekStart: 1,
                 todayBtn: 1,
                 autoclose: 1,
                 todayHighlight: 1,
                 startView: 2,
                 forceParse: 0
             }).datetimepicker('update', new Date());
         }


         //alert($('#slcustomer_id').val());
        $("#add_item").autocomplete({

            source: function (request, response) {
                if (!$('#slcustomer').val()) {
                    $('#add_item').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?=lang('select_above');?>');
                    $('#add_item').focus();
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('invoice/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#slwarehouse").val(),
                        customer_id: $("#slcustomer").val()
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            response: function (event, ui) {
                //alert($('#slcustomer').val());
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    //$(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_tonewitem_item(ui.item);
                   // if (row)
                      //  $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
            <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_invoices'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("invoice/add", $attrib);
                //if ($quote_id) {
                //    echo form_hidden('quote_id', $quote_id);
               // }

                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-6">
                            <div class="panel panel-warning">
                                <div  class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= lang("customer", "slcustomer"); ?>
                                            <div class="input-group">
                                                <?php
                                                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                                    <a href="#" id="toogle-customer-read-attr" class="external">
                                                        <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                                    <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                                    <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                        <a href="<?= admin_url('customers/add'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang("warehouse", "slwarehouse"); ?>
                                                <?php
                                                $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } else {
                                        $warehouse_input = array(
                                            'type' => 'hidden',
                                            'name' => 'warehouse',
                                            'id' => 'slwarehouse',
                                            'value' => $this->session->userdata('warehouse_id'),
                                        );

                                        echo form_input($warehouse_input);
                                    } ?>

                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang("date", "sldate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip date" id="sldate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="edate">วันที่ครบกำหนดชำระ</label>
                                    <?php echo form_input('edate', (isset($_POST['edate']) ? $_POST['edate'] : ""), 'class="form-control input-tip date" id="edate" required="required"'); ?>
                                </div>
                            </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("reference_no", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" id="slref"'); ?>
                            </div>
                        </div>
                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang("biller", "slbiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="slbiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $biller_input = array(
                                'type' => 'hidden',
                                'name' => 'biller',
                                'id' => 'slbiller',
                                'value' => $this->session->userdata('biller_id'),
                            );

                            echo form_input($biller_input);
                        } ?>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="กรุณาเรื่องใบกับกำภาษีที่ต้องการ"'); ?>

                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="slTable" class="table items table-striped table-bordered table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-1">No.</th>
                                            <th class="col-md-2">เลขที่เอกสาร</th>
                                            <th class="col-md-2">วันที่เอกสาร</th>
                                            <th class="col-md-1">ประเภทรายการ</th>
                                            <th class="col-md-2">วันครบกำหนด</th>
                                            <th class="col-md-2">จำนวนเงินในใบกำกับ</th>
                                            <th class="col-md-2">จำนวนเงินในการวางบิล</th>
                                            <th style="width: 30px !important; text-align: center;">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="invoice_type">เงื่อนไขการวางบิล</label>
                                <?php $sst = array('1' => "วางบิล", '2' => "วางบิล/รับเช็ค");
                                echo form_dropdown('invoice_type', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                               <label for="sale_status">สถานะใบวางบิล</label>
                                <?php $sst = array('completed' => lang('completed'), 'pending' => lang('pending'));
                                echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
                        <div class="row" id="bt">
                            <div class="col-md-12">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang("staff_note", "slinnote"); ?>
                                        <?php echo form_textarea('staff_note', (isset($_POST['staff_note']) ? $_POST['staff_note'] : ""), 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>


                            </div>

                        </div>
                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_sale', lang("submit"), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td>จำนวนรายการ <span class="totals_val pull-right" id="titems">0</span> รายการ</td>
                            <td>มูลค่ารวม <span class="totals_val pull-right" id="total">0.00</span></td>

                            <td>ยอดรวมในการวางบิล <span class="totals_val pull-right" id="tgrand_total">0.00</span></td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#genNo').click(function () {
            var no = generateCardNo();
            $(this).parent().parent('.input-group').children('input').val(no);
            return false;
        });
    });
</script>
