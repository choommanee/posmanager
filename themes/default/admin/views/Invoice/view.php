<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });
    });


</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= 'หมายเลขใบวางบิล ' . $inv->reference_no; ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>">
                        </i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php if ($inv->attachment) { ?>
                            <li>
                                <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>">
                                    <i class="fa fa-chain"></i> <?= lang('attachment') ?>
                                </a>
                            </li>
                        <?php } ?>

                            <?PHP
                                if(!empty($_GET['sale_type'])){
                                    $url = 'invoice/add/';
                                    ?>
                                  <li>
                                    <a href="<?= admin_url($url) ?>" class="sleadd">
                                        <i class="fa fa-plus-circle"></i> บันทึกใบวางบิล
                                    </a>
                                  </li>
                            <?php
                                }else{
                                    //$url = 'sales/add/?sale_type=1';
                                }
                            ?>


                        <li>
                            <a href="<?= admin_url('invoice/edit/' . $inv->id) ?>" class="sledit">
                                <i class="fa fa-edit"></i> แก้ไขใบวางบิล
                            </a>
                        </li>
                       
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php if (!empty($inv->return_sale_ref) && $inv->return_id) {
                    echo '<div class="alert alert-info no-print"><p>'.lang("sale_is_returned").': '.$inv->return_sale_ref;
                    echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('sales/modal_view/'.$inv->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    echo '</p></div>';
                } ?>
                <div class="print-only col-xs-12">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>" alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
                <div class="well well-sm">

                    <div class="col-xs-4 border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-user padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $customer->company ? $customer->company : $customer->name; ?></h2>
                            <?= $customer->company ? "" : "Attn: " . $customer->name ?>

                            <?php
                            echo $customer->address . "<br>" . $customer->city . " " . $customer->postal_code . " " . $customer->state . "<br>" . $customer->country;

                            echo "<p>";

                            if ($customer->vat_no != "-" && $customer->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                            }
                            if ($customer->cf1 != "-" && $customer->cf1 != "") {
                                echo "<br>" . lang("ccf1") . ": " . $customer->cf1;
                            }
                            if ($customer->cf2 != "-" && $customer->cf2 != "") {
                                echo "<br>" . lang("ccf2") . ": " . $customer->cf2;
                            }
                            if ($customer->cf3 != "-" && $customer->cf3 != "") {
                                echo "<br>" . lang("ccf3") . ": " . $customer->cf3;
                            }
                            if ($customer->cf4 != "-" && $customer->cf4 != "") {
                                echo "<br>" . lang("ccf4") . ": " . $customer->cf4;
                            }
                            if ($customer->cf5 != "-" && $customer->cf5 != "") {
                                echo "<br>" . lang("ccf5") . ": " . $customer->cf5;
                            }
                            if ($customer->cf6 != "-" && $customer->cf6 != "") {
                                echo "<br>" . lang("ccf6") . ": " . $customer->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $customer->phone . "<br>" . lang("email") . ": " . $customer->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-xs-4 border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-building padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                            <?= $biller->company ? "" : "Attn: " . $biller->name ?>

                            <?php
                            echo $biller->address . "<br>" . $biller->city . " " . $biller->postal_code . " " . $biller->state . "<br>" . $biller->country;

                            echo "<p>";

                            if ($biller->vat_no != "-" && $biller->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $biller->vat_no;
                            }
                            if ($biller->cf1 != "-" && $biller->cf1 != "") {
                                echo "<br>" . lang("bcf1") . ": " . $biller->cf1;
                            }
                            if ($biller->cf2 != "-" && $biller->cf2 != "") {
                                echo "<br>" . lang("bcf2") . ": " . $biller->cf2;
                            }
                            if ($biller->cf3 != "-" && $biller->cf3 != "") {
                                echo "<br>" . lang("bcf3") . ": " . $biller->cf3;
                            }
                            if ($biller->cf4 != "-" && $biller->cf4 != "") {
                                echo "<br>" . lang("bcf4") . ": " . $biller->cf4;
                            }
                            if ($biller->cf5 != "-" && $biller->cf5 != "") {
                                echo "<br>" . lang("bcf5") . ": " . $biller->cf5;
                            }
                            if ($biller->cf6 != "-" && $biller->cf6 != "") {
                                echo "<br>" . lang("bcf6") . ": " . $biller->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $biller->phone . "<br>" . lang("email") . ": " . $biller->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>

                    <div class="col-xs-4">
                        <div class="col-xs-2"><i class="fa fa-3x fa-building-o padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class="">ใบวางบิล</h2>
                            <?= $warehouse->name ?>

                            <?php
                            echo $warehouse->address . "<br>";
                            echo ($warehouse->phone ? lang("tel") . ": " . $warehouse->phone . "<br>" : '') . ($warehouse->email ? lang("email") . ": " . $warehouse->email : '');
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <?php if ($Settings->invoice_view == 1) { ?>
                    <div class="col-xs-12 text-center">
                        <h1><?= lang('tax_invoice'); ?></h1>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
                <div class="col-xs-7 pull-right">
                    <div class="col-xs-12 text-right order_barcodes">
                        <img src="<?= admin_url('misc/barcode/'.$this->sma->base64url_encode($inv->reference_no).'/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        <?= $this->sma->qrcode('link', urlencode(admin_url('sales/view/' . $inv->id)), 2); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="col-xs-5">
                    <div class="col-xs-2"><i class="fa fa-3x fa-file-text-o padding010 text-muted"></i></div>
                    <div class="col-xs-10">
                        <h2 class=""><?= lang("ref"); ?>: <?= $inv->reference_no; ?></h2>
                       
                        <p style="font-weight:bold;"><?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?></p>

                        <p style="font-weight:bold;"><?= lang("status"); ?>: <?= lang($inv->status); ?></p>

                        <p>&nbsp;</p>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table">

                        <thead>

                        <tr>
                             <th class="col-md-1">No.</th>
                            <th class="col-md-2">เลขที่เอกสาร</th>
                            <th class="col-md-2">วันที่เอกสาร</th>
                            <th class="col-md-1">ประเภทรายการ</th>
                            <th class="col-md-2">วันครบกำหนด</th>
                            <th class="col-md-2">จำนวนเงินในใบกำกับ</th>
                            <th class="col-md-2">จำนวนเงินในการวางบิล</th> 
                        </tr>

                        </thead>

                        <tbody>

                        <?php $r = 1;
                        foreach ($rows as $row):
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->reference_no ?>
                                </td>
                               <td style="vertical-align:middle;">
                                    <?= $row->date ?>
                                </td>
                                <td style="vertical-align:middle;">
                                    ขายเชื่อ
                                </td>
                                <td style="text-align:right; vertical-align:middle;">
                                    <?= $row->due_date."(".$row->payment_term.")" ?>
                                </td>
                                 <td style="text-align:right; vertical-align:middle;">
                                    <?= $this->sma->formatMoney($row->total_amount) ?>
                                </td>
                                 <td style="text-align:right; vertical-align:middle;">
                                    <?= $this->sma->formatMoney($row->total_paybill) ?>
                                </td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;

                       

                        ?>
                        </tbody>
                        <tfoot>
                        <?php
                        $col = 6;
                       
                        ?>
                       

                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang("total_amount"); ?>
                            </td>
                            <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total); ?></td>
                        </tr>
                        
                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang("balance"); ?>
                            </td>
                            <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid)); ?></td>
                        </tr>

                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    

                    <div class="col-xs-6">
                        <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>
                        <div class="well well-sm">
                            <p><?= lang("created_by"); ?>
                                : <?= $created_by->first_name . ' ' . $created_by->last_name; ?> </p>

                            <p><?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?></p>
                            <?php if ($inv->updated_by) { ?>
                                <p><?= lang("updated_by"); ?>
                                    : <?= $updated_by->first_name . ' ' . $updated_by->last_name;; ?></p>
                                <p><?= lang("update_at"); ?>: <?= $this->sma->hrld($inv->updated_at); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                
                
            </div>
        </div>
        <?php if (!$Supplier || !$Customer) { ?>
            <div class="buttons">
                <div class="btn-group btn-group-justified">
                    
                   
                    <div class="btn-group">
                        <a href="<?= admin_url('invoice/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                            <i class="fa fa-download"></i> <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                        </a>
                    </div>
                    <?php if ( ! $inv->sale_id) { ?>
                    
                    <div class="btn-group">
                        <a href="<?= admin_url('invoice/edit/' . $inv->id) ?>" class="tip btn btn-warning tip sledit" title="<?= lang('edit') ?>">
                            <i class="fa fa-edit"></i> <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="tip btn btn-danger bpo"
                            title="<b><?= $this->lang->line("delete_sale") ?></b>"
                            data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('invioce/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                            data-html="true" data-placement="top"><i class="fa fa-trash-o"></i>
                            <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                        </a>
                    </div>
                    <?php } ?>
                    <!--<div class="btn-group"><a href="<?= admin_url('sales/excel/' . $inv->id) ?>" class="tip btn btn-primary"  title="<?= lang('download_excel') ?>"><i class="fa fa-download"></i> <?= lang('excel') ?></a></div>-->
                </div>
            </div>
        <?php } ?>
    </div>
</div>
