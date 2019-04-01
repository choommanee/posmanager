<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line('sale') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
    <style>

        @page {
            odd-header-name: html_Header;
            even-header-name: html_Header;
            odd-footer-name: html_Footer;
            even-footer-name: html_Footer;
            margin-bottom: 10px;
            line-height: 1px;
        }

        .table th {
            background-color: #ffffff !important;
            text-align: center;
            padding: 2px;
            color: #000 !important;
            border-color: #ddd !important;
        }

        .table td {
            padding: 2px;
            border-color: #FFFFFF !important;
        }


    <?php if($peperType==1){ ?>
       footer {
            text-align: center;
            padding: 5px 5px !important;
            background-color: #F9F9FF;
            height: 10px;
            color: #000;
            position: fixed;
            left: 0;
            right: 0;
            margin: 0;
        }
        header { top: 10px; }
        footer { bottom: 10px; }

        @page { margin-top: 150px;
            margin-bottom: 10px; }
   <?php }else{ ?>
        @page { margin-top: 180px;
            margin-bottom: 10px; }
   <?php }?>

    </style>
</head>

<body>
<htmlpageheader name="Header">

    <table style="width: 100%;font-size: 16pt;">
        <tr>
            <td style="width: 50%;"><table style="width: 100%;font-size: 16pt;">
                    <tr>
                        <td style="width: 20%;font-size: 16pt;"><img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                                                     alt="<?= $Settings->site_name; ?>"></td>
                        <td style="width: 80%;font-size: 16pt;"> <?= $biller->company != '-' ? $biller->company : $biller->name; ?> <br/>
                            <?php
                            echo $biller->address ;

                            if ($biller->vat_no != "-" && $biller->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $biller->vat_no;
                            }
                            echo "  Tel : " . $biller->phone ;
                            ?></td>
                    </tr>
                </table></td>
            <td class="text-center" style="font-size: 16pt;width: 50%;border: 1px solid #0a0a0a;margin-left: 10px;padding-left: 10px;">
                 
                <?php if($sale_type == '1'){ ?>
                    ใบกำกับภาษี/ใบเสร็จรับเงิน/ใบส่งของ<br/>
                    TAX INVOICE/CASH RECEIPT
                <?php }elseif ($sale_type == '2'){
                    ?>
                    ใบกำกับภาษี/ใบส่งของ<br/>
                    TAX INVOICE
                <?php }?>

            </td>
        </tr>
    </table>
    <table style="width: 100%;font-size: 13px;" >
        <tr>
            <td style="width: 75%;"> ชื่อลูกค้า : <?= $customer->company ? $customer->company."<br />" : $customer->name."<br />"; ?>
                <?= $customer->company ? "" : "Attn: " . $customer->name."  Tel : " . $customer->phone . "<br />" . lang("email") . ": " . $customer->email."<br />" ?>

                <?php
                echo $customer->address . "<br />" . $customer->city . " " . $customer->state. " " . $customer->postal_code ;


                if ($customer->vat_no != "-" && $customer->vat_no != "") {
                    echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                }

                ?></td>
            <td style="width: 25%;font-size: 13px;"> เลขที่ : <?= $inv->reference_no; ?><br>
                <?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                <?php if (!empty($inv->return_purchase_ref)) {
                    echo lang("return_ref").': '.$inv->return_purchase_ref;
                    if ($inv->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('purchases/modal_view/'.$inv->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '<br>';
                    }
                } ?>
                พนักงานขาย : <?= $user->first_name . ' ' . $user->last_name; ?><br/>
                </td>
        </tr>
    </table>
    <div style="clear: both;"></div>
</htmlpageheader>




    <div id="wrap" >


    <div class="row">
        <div class="col-lg-12">

            <div class="table-responsive">
                <table class="table table-bordered" style="font-size: 12px;">

                    <thead>

                    <tr style="font-size: 16pt;">
                        <th width="10%"><?= lang("no"); ?></th>
                        <th width="40%">รายการ</th>
                        <?php if ($Settings->indian_gst) { ?>
                            <th><?= lang("hsn_code"); ?></th>
                        <?php } ?>
                        <th width="15%"><?= lang("quantity"); ?></th>
                        <th width="10%"><?= lang("unit_price"); ?></th>
                        <?php

                       // if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th width="10%">' . lang("discount") . '</th>';
                        //}
                        ?>
                        <th width="15%"><?= lang("subtotal"); ?></th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
                    foreach ($rows as $row):
                        ?>
                        <tr>
                            <td style="text-align:center; vertical-align:middle;font-size: 12pt;"><?= $r; ?></td>
                            <td style="vertical-align:middle;font-size: 12pt;">
                                <?= $row->product_name; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) { ?>
                                <td style="text-align:center; vertical-align:middle;font-size: 12pt;"><?= $row->hsn_code; ?></td>
                            <?php } ?>
                            <td style="text-align:center; vertical-align:middle;font-size: 12pt;"><?= $this->sma->formatQuantity($row->unit_quantity,2).' '.$row->unit_name; ?></td>
                            <td style="text-align:right; width:100px;font-size: 12pt;"><?= $this->sma->formatDecimalMoney($row->real_unit_price,2); ?></td>
                            <?php

                            //if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;font-size: 12pt;">' . $this->sma->formatDecimalMoney($row->item_discount,2) . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') .'</td>';
                            //}
                            ?>
                            <td style="text-align:right; width:120px;font-size: 12pt;padding-right: 8px;"><?= $this->sma->formatDecimalMoney($row->subtotal,2); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                        foreach ($return_rows as $row):
                            ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= ' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' '.$row->unit_name; ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatDecimalMoney($row->real_unit_price,2); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . $this->sma->formatDecimalMoney($row->item_discount,2) .($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatDecimalMoney($row->subtotal,2); ?></td>
                            </tr>
                            <?php
                            //$r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <?php
                    $col = 5;
                   // if ($Settings->product_discount && $inv->product_discount != 0) {
                   //     $col++;
                   // }

                    if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 2;
                    } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
                    <?php if ($inv->grand_total != $inv->total) { ?>
                        <tr>
                            <td colspan="<?= $tcol; ?>"
                                style="text-align:right; padding-right:10px;"><?= lang("total"); ?>
                            </td>
                            <?php

                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatDecimalMoney($return_sale ? ($inv->product_discount+$return_sale->product_discount) : $inv->product_discount,2) . '</td>';
                            }
                            ?>
                            <td style="text-align:right; padding-right:10px;"><?= $this->sma->formatDecimalMoney($return_sale ? (($inv->total + $inv->product_tax)+($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax),2); ?></td>
                        </tr>
                    <?php } ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_total") . ' </td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatDecimalMoney($return_sale->grand_total,2) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_surcharge") . ' </td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatDecimalMoney($inv->surcharge,2) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . '</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' </td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' </td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>



                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang("shipping") . ' </td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>

                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;font-size: 13pt;">ราคาก่อนภาษีมูลค่าเพิ่ม
                        </td>
                        <td style="text-align:right;font-size: 13pt; padding-right:10px;border-bottom: double 1px #000000;"><?= $this->sma->formatDecimalMoney(($inv->grand_total-$inv->shipping)-$inv->order_tax,2); ?></td>
                    </tr>
                    <?php
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;font-size: 10pt; padding-right:10px;">หักส่วนลด</td><td style="font-size: 10pt;text-align:right; padding-right:10px;border-bottom: double 1px #000000;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatDecimal($return_sale ? ($inv->order_discount+$return_sale->order_discount) : $inv->order_discount,2) . '</td></tr>';

                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;font-size: 13pt;">ภาษีมูลค่าเพิ่ม 7%
                        </td>
                        <td style="text-align:right;font-size: 13pt; padding-right:10px;border-bottom: double 1px #000000;"><?= $this->sma->formatDecimalMoney($inv->order_tax,2); ?></td>
                    </tr>

                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;font-size: 13pt; "><?= lang("total_amount2"); ?>
                        </td>
                        <td style="text-align:right;font-size: 13pt; padding-right:10px;border-bottom: double 1px #000000;"><?= $this->sma->formatDecimalMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total): $inv->grand_total,2); ?></td>
                    </tr>

                    </tfoot>
                </table>
            </div>

            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

            <div class="clearfix"></div>

        </div>
    </div>
</div>
        <htmlpagefooter name="Footer">
    <div class="col-lg-12">
    <div class="col-xs-3 pull-left"  style="font-size: 14px;">
        <hr>
        <p>ผู้รับสินค้า</p>
    </div>
    <div class="clearfix"></div>
    <div class="col-xs-3 pull-right"  style="font-size: 14px;">
        <hr>
        <p>ผู้รับเงิน</p>

    </div>
    <div class="clearfix"></div>
    <div class="col-xs-3 pull-right"  style="font-size: 14px;">
        <hr>
        <p>ผู้ตรวจสอบ</p>

    </div>
    </div>
    <div class="clearfix"></div>
   
</htmlpagefooter>

</body>
</html>