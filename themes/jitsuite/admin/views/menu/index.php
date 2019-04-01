<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/bootstrap/css/bootstrap.css");?>" type="text/css"  />
<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
<link href="<? echo site_url("sximo/css/sximo.css");?>" rel="stylesheet">
<link rel="stylesheet" href="<? echo site_url("sximo/css/icons.min.css");?>" type="text/css"  />
<link media="all" type="text/css" rel="stylesheet" href="<? echo site_url("sximo/js/plugins/select2/select2.css");?>/>
<link rel="stylesheet" href="<? echo site_url("sximo/fonts/awesome/css/font-awesome.min.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/iCheck/skins/square/green.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/markitup/skins/simple/style.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/markitup/sets/default/style.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/fancybox/jquery.fancybox.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/datetimepicker/jquery.datetimepicker.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/toastr/toastr.css");?>" type="text/css"  />
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/bootstrap.summernote/summernote.css");?>" rel="stylesheet">

<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/calendar/CalendarControl.css");?>" rel="stylesheet">
<link rel="stylesheet" href="<? echo site_url("sximo/js/plugins/calendar/dhtmlgoodies_calendar.css");?>" rel="stylesheet">

<script src="<? echo site_url("sximo/js/plugins/jquery.min.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/jquery.cookie.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/jquery-ui.min.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/bootstrap/js/bootstrap.min.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/select2/select2.min.js");?>"></script>

<script src="<? echo site_url("sximo/js/plugins/iCheck/icheck.js?v=1.0.2");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/prettify.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/fancybox/jquery.fancybox.js");?>"></script>

<script src="<? echo site_url("sximo/js/plugins/jquery.jCombo.min.js");?>"></script>

<script src="<? echo site_url("sximo/js/plugins/toastr/toastr.js");?>"></script>


<script src="<? echo site_url("sximo/js/plugins/jquery-validation-1.14.0/dist/jquery.validate.min.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/jquery.maskMoney.js");?>"></script>
<script type="text/javascript" src="<? echo site_url("sximo/js/autoNumeric/autoNumeric.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/bootstrap.summernote/summernote.min.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/datetimepicker/jquery.datetimepicker.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/calendar/CalendarControl.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/calendar/dhtmlgoodies_calendar.js");?>"></script>
<script src="<? echo site_url("sximo/js/plugins/big/big.js");?>"></script>
<script src="<? echo site_url("sximo/js/sximo.js");?>"></script>


<style>

    body {
        font-family: 'THSarabunNew';
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        background: #FFFFFF;
        color: #676a6c;
        overflow-x: hidden;
    }

</style>
<script type="text/javascript" src="<?php echo base_url().'themes/jitsuite/admin/assets/js/jquery.nestable.js';?>"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                    class="fa-fw fa fa-heart"></i>
           ระบบบริหารจัดการเมนู
        </h2>

    </div>
    <div class="box-content">
<div class="page-content row">
    <div class="page-content-wrapper m-t">


        <ul class="nav nav-tabs css-content" style="margin:10px 0;">
            <li <?php if($active == 'top') echo 'class="active"';?>><a href="<?php echo  admin_url('menu/index?pos=top');?>"><i class="icon-paragraph-justify2"></i><?php echo "Panel Menu"; ?> </a></li>
            <li <?php if($active == 'sidebar') echo 'class="active"'?>><a href="<?php echo  admin_url('menu/index?pos=sidebar');?>"><i class="icon-paragraph-justify2"></i><?php echo "Start Up Menu"; ?> </a></li>
        </ul>


        <div class="col-sm-5">
            <div class="box ">
                <div id="list2" class="dd" style="min-height:350px;">
                    <ol class="dd-list">
                        <?php foreach ($menus as $menu) : ?>
                            <li data-id="<?php echo $menu['menu_id'];?>" class="dd-item dd3-item">
                                <div class="dd-handle dd3-handle"></div><div class="dd3-content"><?php echo  $menu['menu_name'];?>
                                    <span class="pull-right">
						<a href="<?php echo admin_url('menu/index/'.$menu['menu_id'].'?pos='.$active);?>"><i class="fa fa-cogs"></i></a></span>
                                </div>
                                <?php if(count($menu['childs']) > 0) : ?>
                                    <ol class="dd-list" style="">
                                        <?php foreach ($menu['childs'] as $menu2) : ?>
                                            <li data-id="<?php echo $menu2['menu_id'];?>" class="dd-item dd3-item">
                                                <div class="dd-handle dd3-handle"></div><div class="dd3-content"><?php echo $menu2['menu_name'];?>
                                                    <span class="pull-right">
									<a href="<?php echo  admin_url('menu/index/'.$menu2['menu_id'].'?pos='.$active);?>"><i class="fa fa-cogs"></i></a></span>
                                                </div>
                                                <?php if(count($menu2['childs']) > 0) : ?>
                                                    <ol class="dd-list" style="">
                                                        <?php foreach($menu2['childs'] as $menu3) : ?>
                                                            <li data-id="<?php echo $menu3['menu_id'];?>" class="dd-item dd3-item">
                                                                <div class="dd-handle dd3-handle"></div><div class="dd3-content"><?php echo  $menu3['menu_name'] ;?>
                                                                    <span class="pull-right">
												<a href="<?php echo  admin_url('menu/index/'.$menu3['menu_id'].'?pos='.$active);?>"><i class="fa fa-cogs"></i></a>
												</span>
                                                                </div>
                                                            </li>
                                                        <?php endforeach ;?>
                                                    </ol>
                                                <?php endif;?>
                                            </li>
                                        <?php endforeach;?>
                                    </ol>
                                <?php endif;?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <?PHP
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("menu/saveOrder", $attrib);
                ?>
                    <input type="hidden" name="reorder" id="reorder" value="" />
                    <div class="infobox infobox-danger fade in">
                        <p><?php echo "Note ! , Menu only support 3 level"; ?>	</p>
                    </div>

                    <button type="submit" class="btn btn-primary ">Reload Menu</button>
                </form>
            </div>
        </div>
        <div class="col-sm-7">
           <?PHP
           $attrib = array('data-toggle' => 'validator', 'role' => 'form');
           echo admin_form_open_multipart("menu/save", $attrib);
           ?>
            <div class=" box">


                    <input type="hidden" name="menu_id" id="menu_id" value="<?php echo  $row['menu_id'] ;?>" required="required"/>
                    <div class="form-group  " style="display:none;">
                        <label for="ipt" class=" control-label col-md-4 text-right"> Parent Id </label>
                        <div class="col-md-8">
                            <input type="text" name="parent_id" id="reorder" value="<?php  echo $row['parent_id'];?>" class="form-control" />

                        </div>

                    </div>
                    <div class="form-group  css-content"  style="width: 100%;">
                        <label for="ipt" class=" control-label col-md-4 text-right">Name / Title </label>
                        <div class="col-md-8">
                            <input type="text" name="menu_name" id="menu_name" value="<?php  echo $row['menu_name'];?>" class="form-control" required="required"/>

                        </div>
                    </div>
                    <div class="form-group   css-content "  style="width: 100%;display: none;">
                        <label for="ipt" class=" control-label col-md-4 text-right"><?php echo "Menu Type"; ?> </label>
                        <div class="col-md-8 menutype">
                            <label class="radio-inline  ">
                                <input type="hidden" name="menu_type" value="external">

                        </div>
                    </div>

                    <div class="form-group  ext-link  css-content"  style="width: 100%;">
                        <label for="ipt" class=" control-label col-md-4 text-right"> Url  </label>
                        <div class="col-md-8">
                            <input type="text" name="url" id="url" value="<?php echo $row['url'];?>" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group  css-content "  style="width: 100%;">
                        <label for="ipt" class=" control-label col-md-4 text-right"><?php echo "Position"; ?> </label>
                        <div class="col-md-8">
                            <input type="radio" name="position"  value="sidebar"  required <?php if($row['position']=='sidebar' ) echo 'checked="checked"';?>  /> Start Up Menu
                            <input type="radio" name="position"  value="top"  required <?php if($row['position']=='top' ) echo 'checked="checked"';?> /> Panel Menu
                        </div>

                    </div>
                    <div class="form-group  css-content "  style="width: 100%;">
                        <label for="ipt" class=" control-label col-md-4 text-right"><?php echo "Icon Class"; ?> </label>
                        <div class="col-md-8">
                            <input type="text" name="menu_icons" id="menu_icons" value="<?php echo $row['menu_icons'];?>" class="form-control" />

                            <p> Example : <span class="label label-info"> fa fa-desktop </span>  , <span class="label label-info"> fa fa-cloud-upload </span> </p>
                            <p>Usage :
                                <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank"> Font Awesome </a> class name</p>
                        </div>
                    </div>
                    <div class="form-group    css-content "  style="width: 100%;">
                        <label for="ipt" class=" control-label col-md-4 text-right"><?php echo "Active"; ?> </label>
                        <div class="col-md-8">
                            <input type="radio" name="active"  value="1"
                                <?php if($row['active']=='1' ) echo 'checked="checked"';?> /> Active
                            <input type="radio" name="active" value="0"
                                <?php if($row['active']=='0' ) echo 'checked="checked"';?> /> Inactive


                        </div>
                    </div>
                <div class="form-group   css-content " style="width: 100%;">
                    <label for="ipt" class=" control-label col-md-4"> Access   <code>*</code></label>
                    <div class="col-md-8">
                        <?php
                        $pers = json_decode($row['access_data'],true);
                        foreach($groups->result() as $group) {
                            $checked = '';
                            if(isset($pers[$group->id]) && $pers[$group->id]=='1')
                            {
                                $checked= ' checked="checked"';
                            }
                            ?>
                            <label class="checkbox">
                                <input type="checkbox" name="groups[<?php echo $group->id;?>]" value="<?php echo $group->id;?>" <?php echo $checked;?>  />
                                <?php echo $group->name;?>
                            </label>

                        <?php } ?>
                    </div>
                </div>


                <div class="form-group   css-content  " style="width: 100%;" >
                        <label for="ipt" class=" control-label col-md-4"><?php echo "Public"; ?> </label>
                        <div class="col-md-8">
                            <label class="checkbox"><input  type='checkbox' name='allow_guest'
                                    <?php if($row['allow_guest'] ==1 ) echo 'checked'; ?>
                                                            value="1"	/> Yes  </lable>
                            </label>
                        </div>
                    </div>

                    <div class="form-group   css-content "  style="width: 100%;">
                        <label class="col-sm-4 text-right"> </label>
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-primary "><?php echo "Submit"; ?> </button>
                            <?php if($row['menu_id'] !='') :?>
                                <button type="button"onclick="SximoConfirmDelete('<?php echo  site_url('admin/menu/destroy/'.$row['menu_id']);?>')" class="btn btn-danger ">  Delete </button>
                            <?php endif ;?>
                        </div>

                    </div>

                </div>

            </form>



        </div>
    </div>
    <div style="clear:both;"></div>

</div>

</div>

<script>
    $(document).ready(function(){
        $('.dd').nestable();
        update_out('#list2',"#reorder");

        $('#list2').on('change', function() {
            var out = $('#list2').nestable('serialize');
            $('#reorder').val(JSON.stringify(out));

        });
        $('.ext-link').hide();

        $('.menutype input:radio').on('ifClicked', function() {
            val = $(this).val();
            mType(val);

        });

        mType('<?php echo trim($row['menu_type']);?>');


    });

    function mType( val )
    {
        if(val == 'external') {
            $('.ext-link').show();
            $('.int-link').hide();
        } else {
            $('.ext-link').hide();
            $('.int-link').show();
        }
        $('.ext-link').show();
    }


    function update_out(selector, sel2){

        var out = $(selector).nestable('serialize');
        $(sel2).val(JSON.stringify(out));

    }
</script>
