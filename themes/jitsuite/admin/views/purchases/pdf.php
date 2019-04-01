<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line("purchase") . " " . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
</head>

<body>
<div id="wrap">
    <div class="row" style="font-size: 11px;">
        <div class="col-lg-12">
            <div class="col-xs-12">
                <table>
                    <tr>
                        <td style="width: 20%;"><img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                                                     alt="<?= $Settings->site_name; ?>"></td>
                        <td style="width: 80%;"> <?= $biller->company != '-' ? $biller->company : $biller->name; ?> <br/>
                            <?php
                            echo $biller->address ;

                            if ($biller->vat_no != "-" && $biller->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $biller->vat_no;
                            }
                            echo "  Tel : " . $biller->phone ;
                            ?></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="width: 60%;">
                            <h3>ผู้จำหน่าย</h3>
                            คุณ :
                            <h2 style="margin-top:10px;"><?= $supplier->company ? $supplier->company : $supplier->name; ?></h2>
                            <?= $supplier->company ? "" : "Attn: " . $supplier->name ?>

                            <?php
                            echo $supplier->address . "<br />" . $supplier->city . " " . $supplier->postal_code . " " . $supplier->state;


                            if ($supplier->vat_no != "-" && $supplier->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $supplier->vat_no;
                            }


                            echo lang("tel") . ": " . $supplier->phone . "<br />" . lang("email") . ": " . $supplier->email;
                            ?>
                        </td>
                        <td style="width: 40%;">
                            <h2>ใบสั่งซื้อ</h2>
                            เลขที่ : <?= $inv->reference_no; ?><br>
                            <?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                            <?php if (!empty($inv->return_purchase_ref)) {
                                echo lang("return_ref").': '.$inv->return_purchase_ref;
                                if ($inv->return_id) {
                                    echo ' <a data-target="#myModal2" data-toggle="modal" href="'.admin_url('purchases/modal_view/'.$inv->return_id).'"><i class="fa fa-external-link no-print"></i></a><br>';
                                } else {
                                    echo '<br>';
                                }
                            } ?>
                            ผู้สั่งซื้อ : <?= $created_by->first_name . ' ' . $created_by->last_name; ?><br/>
                            เบอร์ติดต่อ  : <?= $created_by->phone; ?><br/>
                            <?= lang("status"); ?>: <?= lang($inv->status); ?><br>
                            <?= lang("payment_status"); ?>: <?= lang($inv->payment_status); ?>
                        </td>
                    </tr>
                </table>

                <p>&nbsp;</p>
            <div class="clearfix"></div>
            <?php
                $col = $Settings->indian_gst ? 5 : 4;
                if ($inv->status == 'partial') {
                    $col++;
                }
                if ($Settings->product_discount && $inv->product_discount != 0) {
                    $col++;
                }
                if ($Settings->tax1 && $inv->product_tax > 0) {
                    $col++;
                }
                if ( $Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 2;
                } elseif ( $Settings->product_discount && $inv->product_discount != 0) {
                    $tcol = $col - 1;
                } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 1;
                } else {
                    $tcol = $col;
                }
            ?>
            <div class="col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" style="margin-top: 15px;">
                    <thead>
                    <tr class="active">
                        <th><?=lang("no");?></th>
                        <th><?=lang("description");?></th>
                        <?php if ($Settings->indian_gst) { ?>
                            <th><?= lang("hsn_code"); ?></th>
                        <?php } ?>
                        <th><?=lang("quantity");?></th>
                        <?php
                            if ($inv->status == 'partial') {
                                echo '<th>'.lang("received").'</th>';
                            }
                        ?>
                        <th><?=lang("unit_cost");?></th>
                        <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<th>' . lang("tax") . '</th>';
                            }
                        ?>
                        <?php
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<th>' . lang("discount") . '</th>';
                            }
                        ?>
                        <th><?=lang("subtotal");?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $r = 1;
                        foreach ($rows as $row):
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?=$r;?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->supplier_part_no ? '<br>'.lang('supplier_part_no').': ' . $row->supplier_part_no : ''; ?>
                                    <?=$row->details ? '<br>' . $row->details : '';?>
                                    <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' .lang('expiry').': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?=$this->sma->formatQuantity($row->quantity).' '.$row->unit_name;?></td>
                                <?php
                                    if ($inv->status == 'partial') {
                                        echo '<td style="text-align:center;vertical-align:middle;width:120px;">'.$this->sma->formatQuantity($row->quantity_received).' '.$row->unit_name.'</td>';
                                    }
                                ?>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatDecimal($row->net_unit_cost + ($row->item_discount / $row->quantity)); ?></td>

                                <?php
                                    if ($Settings->product_discount && $inv->product_discount != 0) {
                                        echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                    }
                                ?>
                                <td style="text-align:right; width:120px;"><?=$this->sma->formatMoney($row->subtotal);?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                        if ($return_rows) {
                            echo '<tr class="warning"><td colspan="'.($col+1).'" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                            foreach ($return_rows as $row):
                            ?>
                                <tr>
                                    <td style="text-align:center; width:40px; vertical-align:middle;"><?=$r;?></td>
                                    <td style="vertical-align:middle;">
                                        <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                        <?= $row->supplier_part_no ? '<br>'.lang('supplier_part_no').': ' . $row->supplier_part_no : ''; ?>
                                        <?=$row->details ? '<br>' . $row->details : '';?>
                                        <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' .lang('expiry').': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                    </td>
                                    <?php if ($Settings->indian_gst) { ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                    <?php } ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?=$this->sma->formatQuantity($row->quantity).' '.$row->unit_name;?></td>
                                    <?php
                                        if ($inv->status == 'partial') {
                                            echo '<td style="text-align:center;vertical-align:middle;width:120px;">'.$this->sma->formatQuantity($row->quantity_received).' '.$row->unit_name.'</td>';
                                        }
                                    ?>
                                    <td style="text-align:right; width:100px;"><?= $this->sma->formatDecimal($row->net_unit_cost + ($row->item_discount / $row->quantity)); ?></td>
                                    <?php

                                        if ($Settings->product_discount && $inv->product_discount != 0) {
                                            echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                        }
                                    ?>
                                    <td style="text-align:right; width:120px;"><?=$this->sma->formatMoney($row->subtotal);?></td>
                                </tr>
                                <?php
                                $r++;
                            endforeach;
                        }
                    ?>
                    </tbody>
                    <tfoot>

                    <?php if ($inv->grand_total != $inv->total) { ?>
                        <tr>
                            <td colspan="<?= $tcol; ?>"
                                style="text-align:right;"><?= lang("total"); ?>
                            </td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_purchase ? ($inv->product_tax+$return_purchase->product_tax) : $inv->product_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_purchase ? ($inv->product_discount+$return_purchase->product_discount) : $inv->product_discount) . '</td>';
                            }
                            ?>
                            <td style="text-align:right;"><?= $this->sma->formatMoney($return_purchase ? (($inv->total + $inv->product_tax)+($return_purchase->total + $return_purchase->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php } ?>
                    <?php
                    if ($return_purchase) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("return_total") . ' </td><td style="text-align:right;">' . $this->sma->formatMoney($return_purchase->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("return_surcharge") . ' </td><td style="text-align:right;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_purchase ? $inv->cgst + $return_purchase->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . '</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_purchase ? $inv->sgst + $return_purchase->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . '</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_purchase ? $inv->igst + $return_purchase->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . '</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>
                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("order_discount") . '</td><td style="text-align:right;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatMoney($return_purchase ? ($inv->order_discount+$return_purchase->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("order_tax") . ' </td><td style="text-align:right;">' . $this->sma->formatMoney($return_purchase ? ($inv->order_tax+$return_purchase->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("shipping") . '</td><td style="text-align:right;">' . $this->sma->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang("total_amount"); ?>
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->grand_total+$return_purchase->grand_total) : $inv->grand_total); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang("paid"); ?>
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_purchase ? ($inv->paid+$return_purchase->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang("balance"); ?>
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_purchase ? ($inv->grand_total+$return_purchase->grand_total) : $inv->grand_total) - ($return_purchase ? ($inv->paid+$return_purchase->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>
                </table>
            </div>
            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_purchase ? $inv->product_tax+$return_purchase->product_tax : $inv->product_tax), true) : ''; ?>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-xs-7 pull-left">
                    <?php if ($inv->note || $inv->note != "") {?>
                        <div class="well well-sm">
                            <p class="bold"><?=lang("note");?>:</p>

                            <div><?=$this->sma->decode_html($inv->note);?></div>
                        </div>
                    <?php }
                    ?>
                </div>
                <div class="col-xs-4 pull-right">
                    <p><?=lang("order_by");?>: <?=$created_by->first_name . ' ' . $created_by->last_name;?> </p>

                    <p>&nbsp;</p>

                    <p>&nbsp;</p>
                    <hr>
                    <p><?=lang("stamp_sign");?></p>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>