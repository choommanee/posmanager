<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var count = 1, an = 1;
    var type_opt = {'addition': '<?= lang('addition'); ?>', 'subtraction': '<?= lang('subtraction'); ?>'};
    $(document).ready(function () {
        if (localStorage.getItem('remove_proItem')) {
            if (localStorage.getItem('promotionitems')) {
                localStorage.removeItem('promotionitems');
            }
            if (localStorage.getItem('pro_id')) {
                localStorage.removeItem('pro_id');
            }
            if (localStorage.getItem('pro_code')) {
                localStorage.removeItem('pro_code');
            }
            if (localStorage.getItem('pro_name')) {
                localStorage.removeItem('pro_name');
            }
            if (localStorage.getItem('pro_total_qty')) {
                localStorage.removeItem('pro_total_qty');
            }
            if (localStorage.getItem('pro_start_date')) {
                localStorage.removeItem('pro_start_date');
            }
            if (localStorage.getItem('pro_end_date')) {
                localStorage.removeItem('pro_end_date');
            }
            if (localStorage.getItem('pro_date')) {
                localStorage.removeItem('pro_date');
            }
            localStorage.removeItem('remove_proItem');
        }
        <?php if ($promotions) { ?>
        localStorage.setItem('pro_date', '<?= $this->sma->hrld($promotions->update_date); ?>');
        localStorage.setItem('pro_id', '<?= $promotions->pro_id; ?>');
        localStorage.setItem('pro_code', '<?= $promotions->pro_code; ?>');
        localStorage.setItem('pro_name', '<?= $promotions->pro_name; ?>');
        localStorage.setItem('pro_total_qty', '<?= $promotions->pro_total_qty; ?>');
        localStorage.setItem('pro_start_date', '<?= SiteHelpers::ConvertDateDbToThai($promotions->pro_start_date); ?>');
        localStorage.setItem('pro_end_date', '<?= SiteHelpers::ConvertDateDbToThai($promotions->pro_end_date); ?>');
        localStorage.setItem('pro_type', '<?= $promotions->pro_type; ?>');
        localStorage.setItem('promotionitems', JSON.stringify(<?= $promotions_items; ?>));
        localStorage.setItem('remove_prls', '1');
        <?php } ?>

        $("#add_item").autocomplete({
            source: '<?= admin_url('promotion/get_suggestions_tonewItem'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
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
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_promotion_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_promotions'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("promotion/edit/".$promotions->pro_id, $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "qadate"); ?>
                                    <?php echo form_input('pro_date', (isset($_POST['pro_date']) ? $_POST['pro_date'] : SiteHelpers::ConvertDateDbToThai($promotions->pro_date)), 'class="form-control input-tip " id="prodate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "pro_code"); ?>
                                <?php echo form_input('pro_code', (isset($_POST['pro_code']) ? $_POST['pro_code'] : $promotions->pro_code), 'class="form-control input-tip" id="pro_code"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= "ประเภทโปรโมชั่น"; ?>
                                <?php echo form_dropdown('pro_type',array("0"=>'ซื้อตามจำนวนได้ แถม',"1"=>"ซื้อครบได้แถม","2"=>"ซื้อครบลดราคา","3"=>"ซื้อคละ"),$promotions->pro_type,"id=\"pro_type\" class=\"form-control input-tip select\""); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("promotions_name", "pro_code"); ?>
                                <?php echo form_input('pro_name', (isset($_POST['pro_name']) ? $_POST['pro_name'] : $promotions->pro_name), 'class="form-control input-tip" id="pro_name"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= "วันที่เริ่มต้น"; ?>
                                <?php echo form_input('pro_start_date', (isset($_POST['pro_start_date']) ? $_POST['pro_start_date'] : SiteHelpers::ConvertDateDbToThai($promotions->pro_start_date)), 'class="form-control input-tip" id="pro_start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= "วันที่สิ้นสุด"; ?>
                                <?php echo form_input('pro_end_date', (isset($_POST['pro_end_date']) ? $_POST['pro_end_date'] : SiteHelpers::ConvertDateDbToThai($promotions->pro_end_date)), 'class="form-control input-tip" id="pro_end_date"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= "จำกัดจำนวน"; ?>
                                <?php echo form_input('pro_total_qty', (isset($_POST['pro_total_qty']) ? $_POST['pro_total_qty'] : $promotions->pro_total_qty), 'class="form-control input-tip" id="pro_total_qty"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>

                        <div class="clearfix"></div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang("add_product_to_order") . '"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("products"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="promotionTable" class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th><?= lang("product_name") . " (" . lang("product_code") . ")"; ?></th>
                                            <th>ราคาขาย</th>
                                            <th class="col-md-1">จำนวนที่ซื้อ</th>
                                            <th class="col-md-2"><?= lang("product_unit"); ?></th>
                                            <th class="col-md-1">ลดเป็นบาท</th>
                                            <th class="col-md-1">ลดเป็น %</th>
                                            <th class="col-md-1">แถมจำนวน</th>
                                            <?php
                                            if ($Settings->product_serial) {
                                                echo '<th class="col-md-4">' . lang("serial_no") . '</th>';
                                            }
                                            ?>
                                            <th style="max-width: 30px !important; text-align: center;">
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

                        <div class="clearfix"></div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang("note", "qanote"); ?>
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="qanote" style="margin-top: 10px; height: 100px;"'); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('edit_tonewitem', lang("submit"), 'id="edit_tonewitem" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
