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
           margin-top: 180px;
           margin-bottom: 300px; }

        @page {
            odd-header-name: html_Header;
            even-header-name: html_Header;
            odd-footer-name: html_Footer;
            even-footer-name: html_Footer;
            margin-bottom: 180px;
            line-height: 2em;
        }
        .table td {
            padding: 4px;
            border-color: #ddd !important;
        }

        header { top: 160px; }
        footer { bottom: 200px;height: 180px; }

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

    <table style="width: 100%;font-size: 16pt;">
        <tr>
            <td style="width: 50%;"><table style="width: 100%;font-size: 16pt;">
                    <tr>
                        <td style="width: 20%;font-size: 16pt;"><img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                                                     alt="<?= $Settings->site_name; ?>"></td>
                        <td style="width: 80%;font-size: 16pt;"> บริษัทแหลมฉบังโฮมมาร์ท จำกัด (สำนักงานใหญ่)<br/>
                            <p style="font-size: 12pt;">เลขประจำตัวผู้เสียภาษีอากร 0205560022894</p>
                            88/88 ม.10 ต.ทุ่งสุขลา  อ.ศรีราชา  จ.ชลบุรี  20230<br/>
                            <p style="font-size: 16pt;">(+66) 0800950990 , 038-198 785,038-198 787<br/> Fax: 038-199 288</p></td>
                    </tr>
                </table></td>
            <td class="text-center" style="font-size: 16pt;width: 50%;border: 1px solid #0a0a0a;margin-left: 10px;padding-left: 10px;">
                 <h1>
                ใบวางบิล
                 </h1>
            </td>
        </tr>
    </table>
    <table style="width: 100%;font-size: 13px;" >
        <tr>
            <td style="width: 65%;"> ชื่อลูกค้า : <?= $customer->company ? $customer->company."<br />" : $customer->name."<br />"; ?>
                <?= $customer->company ? "" : "Attn: " . $customer->name."  Tel : " . $customer->phone . "<br />" . lang("email") . ": " . $customer->email."<br />" ?>

                <?php
                echo $customer->address . "<br />" . $customer->city . " " . $customer->postal_code . " " . $customer->state;


                if ($customer->vat_no != "-" && $customer->vat_no != "") {
                    echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                }

                ?></td>
            <td style="width: 35%;font-size: 13px;"> เลขที่ : <?= $inv->reference_no; ?><br>
                <?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                วันที่นัดชำระ : <?= $this->sma->hrld($inv->edate); ?>
                </td>
        </tr>
    </table>

</htmlpageheader>


    <div id="wrap">


    <div class="row">
        <div class="col-lg-12">

            <div class="table-responsive">
                <table class="table table-bordered" style="font-size: 12px;">

                    <thead>

                   <tr>
                            <th style="width:5%;">No.</th>
                            <th style="width:20%;">เลขที่เอกสาร</th>
                            <th style="width:10%;">วันที่เอกสาร</th>
                            <th style="width:10%;">วันครบกำหนด</th>
                            <th style="width:10%;">จำนวนเงินในใบกำกับ</th>
                            <th style="width:10%;">จำนวนเงินในการวางบิล</th> 
                        
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
                               <td style="text-align:center;vertical-align:middle;">
                                    <?= $this->sma->ConvertDateDbToThai($row->date) ?>
                                </td>
                                <td style="text-align:center; vertical-align:middle;">
                                    <?= $this->sma->ConvertDateDbToThai($row->due_date); ?>
                                </td>
                                 <td style="text-align:right; vertical-align:middle;padding-right:10px;">
                                    <?= $this->sma->formatMoney($row->total_amount) ?>
                                </td>
                                 <td style="text-align:right; vertical-align:middle;padding-right:10px;">
                                    <?= $this->sma->formatMoney($row->total_paybill) ?>
                                </td>
                            </tr>
                        <?php

                        $r++;
                    endforeach;
                    if($r < 15){
                        for($k=$r;$k<=19;$k++){
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;">&nbsp; </td>
                                <td style="vertical-align:middle;">
                                    &nbsp;
                                </td>
                                <td style="vertical-align:middle;">
                                    &nbsp;
                                </td>
                                <td style="text-align:right; vertical-align:middle;">
                                    &nbsp;
                                </td>
                                <td style="text-align:right; vertical-align:middle;padding-right:10px;">

                                </td>
                                <td style="text-align:right; vertical-align:middle;padding-right:10px;">
                                </td>
                            </tr>
                            <?php
                            //$k++;
                        }
                    }
                    ?>

                    </tbody>
                    <tfoot>
                    <?php
                    $col = 4;
                   // if ($Settings->product_discount && $inv->product_discount != 0) {
                   //     $col++;
                   // }

                   
                    ?>
                    
                        <tr>
                            <td colspan="2"
                                style="height: 41px;text-align:right; padding-right:10px;"><b>รวม   <?=$r-1;?> รายการ เป็นเงิน</b>
                            </td>
                           
                            <td style="text-align:center; padding-right:10px;" colspan="3"><b><?= $this->site->num2thai($inv->paid,2); ?></b></td>
                            <td style="text-align:right; padding-right:10px;"><b><?= $this->sma->formatDecimalMoney($inv->paid,2); ?></b></td>
                        </tr>
                    <tr>
                        <td colspan="6"
                            style="height: 50px;text-align:left; padding-right:10px;">หมายเหตุ
                        </td>

                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="clearfix"></div>

        </div>
    </div>
</div>
        <htmlpagefooter name="Footer">
            <table  style="border: none 0px;font-size: 9pt;width: 100%;line-height: 2em;">
                <tr>
                    <td style="width: 18%;border-top: 1px solid #ddd;border-left: 1px solid #ddd;padding-right: 10px;text-align: right">จ่ายเช็ค</td>
                    <td colspan="3" style="border-top: 1px solid #ddd;border-right: 1px solid #ddd;padding: 2px;">กรุณาสั่งจ่ายเช็คขีดคร่อม และขีด "หรือผู้ถือหุ้น" สั่งจ่ายในนามบริษัท แหลมฉบังโฮมมาร์ท จำกัด </td>
                </tr>
                <tr>
                    <td style="width: 18%;border-left: 1px solid #ddd;padding-right: 10px;text-align: right">โอนเข้าบัญชี</td>
                    <td colspan="3" style="border-right: 1px solid #ddd;padding: 2px;">ชื่อบัญชี บริษัท แหลมฉบังโฮมมาร์ท จำกัด ธนาคาร กรุงเทพ จำกัด เลขที่บัญชี 7283500317 สาขาแหลมฉบัง ศรีราชา</td>
                </tr>
                <tr>
                    <td style="width: 18%;border-left: 1px solid #ddd;padding: 2px;">&nbsp;</td>
                    <td colspan="3" style="border-right: 1px solid #ddd;padding: 2px;">ชื่อบัญชี บริษัท แหลมฉบังโฮมมาร์ท จำกัด ธนาคาร กรุงศรีอยุธยา จำกัด เลขที่บัญชี 5150002800 สาขาสวนอุตสาหกรรมเครือสหัฒน์ ศรีราชา</td>
                </tr>
                <tr>
                    <td style="width: 18%;border-left: 1px solid #ddd;padding-right: 10px;text-align: right">กรุณายืนยันการโอน</td>
                    <td colspan="3" style="border-right: 1px solid #ddd;padding: 2px;">Fax: 038-199 288</td>
                </tr>
            </table>

            <table class="table table-bordered" style="font-size: 9pt;">

                <tr>
                <td style="width: 25%;height: 60px;">&nbsp;</td>
                <td style="width: 25%">&nbsp;</td>
                <td style="width: 25%">&nbsp;</td>
                <td style="width: 25%">&nbsp;</td>
                </tr>
                <tr>
                    <td rowspan="2" style="text-align:center; vertical-align:middle;">ผู้จัดทำ</td>
                    <td rowspan="2" style="text-align:center; vertical-align:middle;">ผู้วางบิล</td>
                    <td rowspan="2" style="text-align:center; vertical-align:middle;">ผู้รับวางบิล</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center; vertical-align:middle;">วันที่นัดชำระ/โทรสอบถาม</td>
                </tr>
            </table>
   
</htmlpagefooter>

</body>
</html>