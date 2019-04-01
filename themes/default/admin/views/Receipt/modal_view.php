<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?= $assets ?>js/pdfobject.min.js"></script>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content" style="font-size: 10px;">

        <div class="modal-body">
            <div id="pdf" style="height: 700px;"></div>
            <script>PDFObject.embed("<?= admin_url('invoice/pdf/' . $inv->id); ?>?pepperType=2", "#pdf");</script>

            <?php if (!$Supplier || !$Customer) { ?>
                <div class="buttons">
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_payment/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_payment') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-dollar"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('payment') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_delivery/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_delivery') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-truck"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delivery') ?></span>
                            </a>
                        </div>
                        <?php if ($inv->attachment) { ?>
                            <div class="btn-group">
                                <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>" class="tip btn btn-primary" title="<?= lang('attachment') ?>">
                                    <i class="fa fa-chain"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('attachment') ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('email') ?>">
                                <i class="fa fa-envelope-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="#" class="tip btn btn-primary bntChangePDF" title="<?= lang('download_pdf') ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs">เปลี่ยนขนาดกระดาษ</span>
                            </a>
                        </div>
                        <?php if ( ! $inv->sale_id) { ?>
                            <div class="btn-group">
                                <a href="<?= admin_url('sales/edit/' . $inv->id) ?>" class="tip btn btn-warning sledit" title="<?= lang('edit') ?>">
                                    <i class="fa fa-edit"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                                </a>
                            </div>
                            <div class="btn-group">
                                <a href="#" class="tip btn btn-danger bpo" title="<b><?= $this->lang->line("delete_sale") ?></b>"
                                   data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('sales/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                                   data-html="true" data-placement="top">
                                    <i class="fa fa-trash-o"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
        var changeType = 1;
        $('.bntChangePDF').click(function () {

            if(changeType==1){
               // alert(changeType);
                PDFObject.embed("<?= admin_url('invoice/pdf/' . $inv->id).'&pepperType=1'; ?>", "#pdf");
                changeType=2;
            }else if(changeType==2){
                //alert(changeType);
                PDFObject.embed("<?= admin_url('invoice/pdf/' . $inv->id).'&pepperType=2'; ?>", "#pdf");
                changeType =1;
            }
            //changeType = changeType+1;
            return false    ;
        });
    });
</script>
