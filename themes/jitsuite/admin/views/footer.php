<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code); ?>
<script type="text/javascript">
var dt_lang = <?=$dt_lang?>, dp_lang = <?=$dp_lang?>, site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url(), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>;
var lang = {paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>'};
</script>
<?php
$s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
foreach (lang('select2_lang') as $s2_key => $s2_line) {
    $s2_data[$s2_key] = str_replace(array('{', '}'), array('"+', '+"'), $s2_line);
}
$s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
<script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/perfect-scrollbar.min.js"></script>
<?= ($m == 'purchases' && ($v == 'add' || $v == 'edit' || $v == 'purchase_by_csv')) ? '<script type="text/javascript" src="' . $assets . 'js/purchases.js"></script>' : ''; ?>
<?= ($m == 'transfers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/transfers.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/sales.js"></script>' : ''; ?>
<?= ($m == 'quotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_tonewitem' || $v == 'edit_tonewitem')) ? '<script type="text/javascript" src="' . $assets . 'js/tonewitem.js"></script>' : ''; ?>
<?= ($m == 'invoice' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/invoice.js"></script>' : ''; ?>
<script type="text/javascript" charset="UTF-8">var oTable = '', r_u_sure = "<?=lang('r_u_sure')?>";
    <?=$s2_file_date?>
    $.extend(true, $.fn.dataTable.defaults, {"oLanguage":<?=$dt_lang?>});
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
    <?php
    // echo $showModal;
    if($m=='sales' && $v=='view' && $showModal==1){?>
        showModal('<?=$id?>','<?=$sale_type?>');
    <?php }
    // print_r($_GET);
    ?>
    <?php if($m=='quotes'){?>
    $(window).load(function () {
        $('.mm_sales').addClass('active');
        $('.mm_sales').find("ul").first().slideToggle();
        $('#<?=$m?>_<?=$v?>').addClass('active');
        $('.mm_sales a .chevron').removeClass("closed").addClass("opened");
    });
    <?php }else{?>
    $(window).load(function () {
        <?php
        if($m=='sales' ){
            if($sale_type==1){ ?>
                $('.mm_<?=$m?>').addClass('active');
                $('.mm_<?=$m?>').find("ul").first().slideToggle();
                $('#<?=$m?>_<?=$v?>1').addClass('active');
                $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
            <?php }else{ ?>
                $('.mm_<?=$m?>').addClass('active');
                $('.mm_<?=$m?>').find("ul").first().slideToggle();
                $('#<?=$m?>_<?=$v?>2').addClass('active');
                $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
            <?php }?>

    <?php }else{?>
        $('.mm_<?=$m?>').addClass('active');
        $('.mm_<?=$m?>').find("ul").first().slideToggle();
        $('#<?=$m?>_<?=$v?>').addClass('active');
        $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
        <?php } ?>

    });
    <?php }?>
</script>

<script src="https://cdn.metroui.org.ua/v4/js/metro.min.js"></script>
<script src="<?= $assets ?>js/desktop.js"></script>
<script src="<?= $assets ?>js/start.js"></script>

</body>
<script>
    var w_icons = [
        'rocket', 'apps', 'cog', 'anchor'
    ];
    var w_titles = [
        'rocket', 'apps', 'cog', 'anchor'
    ];

    function createWindow(url,name){
        var index = Metro.utils.random(0, 3);
        Desktop.createWindow({
            width: 300,
            icon: "<span class='mif-"+w_icons[index]+"'></span>",
            title: name,
            content: url
        });

    }

    function createWindowModal(){
        Desktop.createWindow({
            width: 300,
            icon: "<span class='mif-cogs'></span>",
            title: "Modal window",
            content: "<div class='p-2'>This is desktop demo created with Metro 4 Components Library</div>",
            overlay: true,
            //overlayColor: "transparent",
            modal: true,
            place: "center",
            onShow: function(win){
                win.addClass("ani-swoopInTop");
                setTimeout(function(){
                    win.removeClass("ani-swoopInTop");
                }, 1000);
            },
            onClose: function(win){
                win.addClass("ani-swoopOutTop");
            }
        });
    }

    function createWindows(url,name){
       // $.get( url, function( data ) {
            Desktop.createWindow({
                width: 1200,
                height:600,
                icon: "<span class='mif-cogs'></span>",
                title: name,
                content: "<iframe src='"+url+"' style='width: 100%;height: 100%'></iframe>",
                clsContent: "bg-dark"
            });
       // });

    }

    $(".dropdown-toggle").click(function () {
        var lv = $(this).parent("li").find("ul").attr("data");
        //alert(lv);
        if(lv == "2"){
          //  alert(lv);
            var position = $(this).parent("li").find("ul").position();

            //alert(position.left);

               // $(this).closest( "ul" ).css( "left", "132%" );
               $(this).parent("li").find("ul").css("position","relative");
                $(this).parent("li").find("ul").css("left","108%");
                $(this).parent("li").find("ul").css("top","-2.9rem");

        }else{
            $(this).closest( "ul" ).css( "left", "132%" );
            $(this).parent("li").find("ul").css("position","relative");
            $(this).parent("li").find("ul").css("left","100%");
            $(this).parent("li").find("ul").css("top","-2.8rem");
        }

      //  $(this).closest( "ul  d-menu" ).css( "left", "132%" );
        var ulselected = $(this).parent("li").find("li").size();
       // alert(ulselected);
    });



</script>
</html>
