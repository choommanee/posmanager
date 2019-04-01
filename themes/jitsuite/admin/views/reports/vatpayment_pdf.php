<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line('sale') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
    <style>
        *, html {
            padding: 0;
            margin: 0;
            font-family: garuda !important;
        }
        body {
            font-family: garuda !important;
            padding: 2px 2px;
            margin: 0;
            height: 100%;
            background: #FFF !important;
            line-height: 1.15 !important;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: garuda !important;
            margin: 10px 0 !important;
        }

        body:before, body:after {
            display: none !important;
        }
        @page {
            odd-header-name: html_Header;
            even-header-name: html_Header;
            odd-footer-name: html_Footer;
            even-footer-name: html_Footer;
            margin-bottom: 20px;
            line-height: 2em;
        }
        .table td {
            padding: 4px;
            border-color: #ddd !important;
        }
        
        
        header { top: 160px; }
        footer { bottom: 20px; }

        .title {
            text-transform: uppercase !important;
        }

        .table th {
            background-color: #ffffff !important;
            text-align: center;
            padding: 5px;
            color: #000 !important;
            border-color: #ddd !important;
        }
        
        .pagenum:before { content: counter(page); }

    </style>
</head>

<body>
<htmlpageheader name="Header">

    <table style="width: 100%;font-size: 12pt;" class="table">
        <tr>
            <td style="width: 50%;"><table style="width: 100%;font-size: 12pt;">
                    <tr>
                        <td style="width: 20%;font-size: 12pt;"><img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                                                     alt="<?= $Settings->site_name; ?>"></td>
                        <td style="width: 80%;font-size: 12pt;"> บริษัทแหลมฉบังโฮมมาร์ท จำกัด (สำนักงานใหญ่)<br/>
                            <p style="font-size: 12pt;">เลขประจำตัวผู้เสียภาษีอากร 0205560022894</p>
                            88/88 ม.10 ต.ทุ่งสุขลา  อ.ศรีราชา  จ.ชลบุรี  20230<br/>
                            <p style="font-size: 12pt;">(+66) 0800950990 , 038-198 785,038-198 787<br/> Fax: 038-199 288</p></td>
                    </tr>
                </table></td>
            <td class="text-center" style="font-size: 30pt;width: 50%;border: 1px solid #0a0a0a;margin-left: 10px;padding-left: 10px;">
                <b>รายงานภาษีขาย</b>
            </td>
        </tr>
    </table>

</htmlpageheader>
<div id="wrap" >

    <div class="row">
        <div class="col-lg-12">

            <div class="table-responsive">
                <table class="table table-bordered" style="font-size: 10px;">

                    <thead>
                    <tr style="font-size: 10pt;">
                             <th style="width:5%;">No</th>
                            <th style="width:10%;"><?= lang("date"); ?></th>
                            <th style="width:15%;"><?= lang("reference_no"); ?></th>
                            <th style="width:15%;">เลขที่ผู้เสียภาษี</th>
                            <th style="width:35%;">ชื่อผู้ซื้อสินค้า</th>
                            <th style="width:10%;">จำนวนเงิน</th>
                            <th style="width:10%;">ภาษี</th>
                        </tr>
                  
                    </thead>

                    <tbody>

                    <?php 
                    $r = 1;
                    $k=1;
                    foreach ($rows as $row):
                        ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;font-size: 8pt;"><?= $r; ?></td>
                            <td style="vertical-align:middle;font-size: 8pt;">
                                <?= $this->sma->ConvertDateDbToThai($row->date); ?>
                            </td>
                             <td style="vertical-align:middle;font-size: 8pt;">
                                <?= $row->sale_ref; ?>
                            </td>
                            <td style="vertical-align:middle;font-size: 8pt;">
                                <?= $row->vat_no; ?>
                            </td>
                             <td style="vertical-align:middle;font-size: 8pt;">
                                <?= $row->customer; ?>
                            </td>
                            
                            <td style="vertical-align:middle;font-size: 8pt;text-align:right;">
                                <?= $this->sma->formatDecimalMoney($row->amount,2); ?>
                            </td>
                            <td style="vertical-align:middle;font-size: 8pt;text-align:right;">
                                <?= $this->sma->formatDecimalMoney($row->order_tax,2); ?>
                            </td>
                            
                            
                        </tr>
                        <?php
                        $totalAmount = $totalAmount+$row->amount;
                        
                        $totalTax = $totalTax+$row->order_tax;
                        $totalPerPageAmount = $totalPerPageAmount+$row->amount;
                        $totalTaxPerPage = $totalTaxPerPage+$row->order_tax;
                       // $k=$i;
                         if(($r%40)==0){
                             
                             
                             ?>
                        <tfoot>
                    <?php
                    $col = 5;
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;vertical-align:middle;font-size: 8pt;">รวมทั้งหมดหน้าที่  <?=$k?>
                        </td>
                        <?php

                            echo '<td style="text-align:right;font-size: 8pt;">' .$this->sma->formatDecimalMoney($totalPerPageAmount,2) . '</td>';
                        
                        ?>
                        <td style="text-align:right; padding-right:10px;font-size: 8pt;"><?= $this->sma->formatDecimalMoney($totalTaxPerPage,2); ?></td>
                    </tr>
                   
                    </tfoot>
                </table>
                   
                    <table class="table table-bordered" style="font-size: 10px;">
                    <thead>
                      <tr style="font-size: 10pt;">
                            <th style="width:5%;">No</th>
                            <th style="width:10%;"><?= lang("date"); ?></th>
                            <th style="width:15%;"><?= lang("reference_no"); ?></th>
                            <th style="width:15%;">เลขที่ผู้เสียภาษี</th>
                            <th style="width:35%;">ชื่อผู้ซื้อสินค้า</th>
                            <th style="width:10%;">จำนวนเงิน</th>
                            <th style="width:10%;">ภาษี</th>
                        </tr>
                    </thead>

                    <tbody>
                       <?php 
                       $totalPerPageAmount =0;
                       $totalTaxPerPage = 0;
                       $k++;
                         }
                        $r++;
                       
                    endforeach;
                    
                    ?>
                    </tbody>
                    <tfoot>
                    <?php
                    $col = 5;
                    ?>
                        <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;vertical-align:middle;font-size: 8pt;">รวมทั้งหมดหน้าที่  <?=$k?>
                        </td>
                        <?php

                            echo '<td style="text-align:right;font-size: 8pt;"><b>' . $this->sma->formatDecimalMoney($totalPerPageAmount,2) . '</b></td>';
                        
                        ?>
                        <td style="text-align:right; padding-right:10px;font-size: 8pt;"><b><?= $this->sma->formatDecimalMoney($totalTaxPerPage,2); ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right;vertical-align:middle;font-size: 8pt;">รวมทั้งสิ้น
                        </td>
                        <?php

                            echo '<td style="text-align:right;font-size: 8pt;"><b>' . $this->sma->formatDecimalMoney($totalAmount,2) . '</b></td>';
                        
                        ?>
                        <td style="text-align:right; padding-right:10px;font-size: 8pt;"><b><?= $this->sma->formatDecimalMoney($totalTax,2); ?></b></td>
                    </tr>
                   
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>