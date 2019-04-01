<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= admin_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4/css/metro-all.min.css">

    <link href="<?= $assets ?>styles/desktop.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/start.css" rel="stylesheet"/>


    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/pdfobject.min.js"></script>
    <!--[if lt IE 9]>

    <script src="<?= $assets ?>js/jquery.js"></script>
    <![endif]-->
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
    <?php if ($Settings->user_rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php } ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });

    </script>

    <style>
        .d-menu, .v-menu{
            float: right;
        }
    </style>

</head>

<body >
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div class="desktop">
    <div class="window-area bg-dark">
        <div id="loading"></div>
        <div class="container-fluid start-screen no-overflow">
            <h1 class="start-screen-title" style="color: #FFFFFF;">Dashboard <?php
                //print_R($_SESSION);
                include(APPPATH.'config/database.php');
                 $databaseName = $_SESSION['database_select'];
                echo $db[$databaseName]['site_name'];?> </h1>
            <?php
            $topsmenu = SiteHelpers::menus('top');?>
            <?php foreach ($topsmenu as $tmenu) { ?>
            <div class="tiles-area">
                <div class="tiles-grid tiles-group size-2 fg-white" data-group-title="<?=$tmenu['menu_name'];?>">
                <?php
                $i=1;
                foreach ($tmenu['childs'] as $tmenu2) {
                    if($i==1){?>
                        <a  <?php if($tmenu2['url']!=""){ ?>
                            onclick="createWindows('<?= admin_url($tmenu2['url']); ?>','<?=$tmenu2['menu_name'];?>');" href="#"
                        <? }else{ ?> href="#" <?php }
                        echo 'data-role="tile" class="bg-indigo fg-white"';?>>
                            <span class="<?php echo $tmenu2['menu_icons'];?> icon"></span>
                            <span class="branding-bar"> <?php echo $tmenu2['menu_name'] ;?></span>
                            <!--<span class="badge-bottom">30</span>-->
                        </a>
                    <?
                        $i++;
                        continue;

                    }

                    if(($i % 2)==0){?>
                    <a  <?php if($tmenu2['url']!=""){ ?>
                        onclick="createWindows('<?= admin_url($tmenu2['url']); ?>','<?=$tmenu2['menu_name'];?>');" href="#"
                    <? }else{ ?> href="#" <?php }
                    echo 'data-role="tile" class="bg-indigo fg-white"';?>>
                    <div data-role="tile" class="bg-cyan fg-white">
                        <span class="<?php echo $tmenu2['menu_icons'];?> icon"></span>
                        <span class="branding-bar"> <?php echo $tmenu2['menu_name'] ;?></span>
                    </div>
                    </a>
                    <? }
                    if(($i % 3)==0) {
                        ?>

                        <div <?php if($tmenu2['url']!=""){ ?>
                                onclick="createWindows('<?= admin_url($tmenu2['url']); ?>','<?=$tmenu2['menu_name'];?>');" href="#"
                                <? }?> data-role="tile" class="bg-orange fg-white" data-size="wide">
                            <span class="<?php echo $tmenu2['menu_icons'];?> icon"></span>
                            <span class="branding-bar"><?php echo $tmenu2['menu_name'] ;?></span>
                        </div>
                        <?php
                    }
                   // echo ($i%3);
                    if (($i % 3)==0) {
                        $i=1;
                      //  echo 'odd';
                    }else{
                        $i++;
                    }

                }
                ?>

                </div>


            </div>
            <?php } ?>

        </div>
    </div>
    <div class="task-bar">
        <div class="task-bar-section">
            <button class="task-bar-item" id="start-menu-toggle"><span class="mif-windows"></span></button>
            <div class="start-menu" data-role="dropdown" data-toggle-element="#start-menu-toggle">
                <div class="start-menu-inner h-100">
                    <div class="explorer">

                        <ul class="v-menu w-100 bg-brandColor2 fg-white">

                            <?php $sidebar = SiteHelpers::menus('sidebar');?>
                            <?php foreach ($sidebar as $menu) : ?>
                                <li>

                                    <a  <?php if($menu['url']!=""){?>
                                        onclick="createWindows('<?= admin_url($menu['url']); ?>',<?=$menu['menu_name'];?>);" href="#"
                                   <? }else{?>
                                        href="#"
                                    <?php
                                    }
                                    if(count($menu['childs']) > 0 ) echo 'class="dropdown-toggle"';?>>
                                        <i class="<?php echo $menu['menu_icons'];?>"></i>
				                        <?php echo $menu['menu_name'];?>
                                    </a>
                                    <?php if(count($menu['childs']) > 0) :?>
                                        <ul class="d-menu" data-role="dropdown" data="2">
                                            <?php foreach ($menu['childs'] as $menu2) : ?>
                                                <li>
                                                    <a
                                                        <?php if($menu2['url']!=""){?>
                                                        onclick="createWindows('<?= admin_url($menu2['url']); ?>','<?= $menu2['menu_name'];?>');" href="#"
                                                        <? }else{?>
                                                        href="#"
                                                        <?php
                                                            }
                                                    if(count($menu2['childs']) > 0) echo 'class="dropdown-toggle"';

                                                        ?>
                                                    >
                                                        <i class="<?php echo $menu2['menu_icons'];?>"></i>
                                                        <?php echo $menu2['menu_name'] ;?>
                                                    </a>
                                                    <?php if(count($menu2['childs']) > 0) : ?>
                                                        <ul class="d-menu" data-role="dropdown" data="3">
                                                            <?php foreach($menu2['childs'] as $menu3) : ?>
                                                                <li>
                                                                    <a <?php if($menu3['url']!=""){?>
                                                                        onclick="createWindows('<?= admin_url($menu3['url']); ?>','<?=$menu3['menu_name'];?>');" href="#"
                                                                    <? }else{?>
                                                                        href="#"
                                                                        <?php
                                                                    }
                                                                    if(count($menu3['childs']) > 0) echo 'class="dropdown-toggle"';

                                                                    ?>
                                                                    >
                                                                        <i class="<?php echo $menu3['menu_icons'];?>"></i>
                                                                        <?php echo $menu3['menu_name'];?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach;?>
                                                        </ul>
                                                    <?php endif;?>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    <?php endif;?>
                                </li>
                            <?php endforeach;?>
                            <!--Menu Admin-->
                            <li>
                                <a href="<?= admin_url('logout'); ?>"><i class="mif-enter mif-lg"></i> ออกจากระบบ</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="task-bar-section tasks">



        </div>
    </div>
</div>