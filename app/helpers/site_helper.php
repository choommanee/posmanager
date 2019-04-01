<?php
class SiteHelpers
{
    public static function menus( $position ='top',$active = '')
    {
        $_this = & get_Instance();
        $data = array();
        $menu = self::nestedMenu(0,$position ,$active);
        //print_r($_this->session->userdata());
        foreach ($menu as $row)
        {
            $child_level = array();
            $p = json_decode($row->access_data,true);
            //print_r($p);
            //echo $p[$_this->session->userdata('group_id')];
            if($row->allow_guest == 1)
            {
                $is_allow = 1;
            } else {
                $is_allow = (isset($p[$_this->session->userdata('group_id')]) && $p[$_this->session->userdata('group_id')]==1 ? 1 : 0);
            }
            if($is_allow ==1)
            {

                $menus2 = self::nestedMenu($row->menu_id , $position ,$active );
                if(count($menus2) > 0 )
                {
                    $level2 = array();
                    foreach ($menus2 as $row2)
                    {
                        $p = json_decode($row2->access_data,true);
                        if($row2->allow_guest == 1)
                        {
                            $is_allow = 1;
                        } else {
                            $is_allow = (isset($p[$_this->session->userdata('group_id')]) && $p[$_this->session->userdata('group_id')] ? 1 : 0);
                        }

                        if($is_allow ==1)
                        {

                            $menu2 = array(
                                'menu_id'		=> $row2->menu_id,
                                'module'		=> $row2->module,
                                'menu_type'		=> $row2->menu_type,
                                'url'			=> $row2->url,
                                'menu_name'		=> $row2->menu_name,
                                'menu_icons'	=> $row2->menu_icons,
                                //'menu_lang'		=> json_decode($row2->menu_lang,true),
                                'childs'		=> array()
                            );

                            $menus3 = self::nestedMenu($row2->menu_id , $position , $active);
                            if(count($menus3) > 0 )
                            {
                                $child_level_3 = array();
                                foreach ($menus3 as $row3)
                                {
                                    $p = json_decode($row3->access_data,true);
                                    if($row3->allow_guest == 1)
                                    {
                                        $is_allow = 1;
                                    } else {
                                        $is_allow = (isset($p[$_this->session->userdata('group_id')]) && $p[$_this->session->userdata('group_id')] ? 1 : 0);
                                    }
                                    if($is_allow ==1)
                                    {
                                        $menu3 = array(
                                            'menu_id'		=> $row3->menu_id,
                                            'module'		=> $row3->module,
                                            'menu_type'		=> $row3->menu_type,
                                            'url'			=> $row3->url,
                                            'menu_name'		=> $row3->menu_name,
                                            'menu_icons'	=> $row3->menu_icons,
                                            //	'menu_lang'		=> json_decode($row3->menu_lang,true),
                                            'childs'		=> array()
                                        );
                                        $child_level_3[] = $menu3;
                                    }
                                }
                                $menu2['childs'] = $child_level_3;
                            }
                            $level2[] = $menu2 ;
                        }

                    }
                    $child_level = $level2;

                }

                $level = array(
                    'menu_id'		=> $row->menu_id,
                    'module'		=> $row->module,
                    'menu_type'		=> $row->menu_type,
                    'url'			=> $row->url,
                    'menu_name'		=> $row->menu_name,
                    'menu_icons'	=> $row->menu_icons,
                    //'menu_lang'		=> json_decode($row->menu_lang,true),
                    'childs'		=> $child_level
                );

                $data[] = $level;
            }

        }
        //echo '<pre>';print_r($data); echo '</pre>'; exit;
        return $data;
    }

    public static function nestedMenu($parent=0,$position ='top',$active = '1')
    {
        $_this = & get_Instance();
        $active 	=  ($active =='all' ? "" : " AND active ='1' ");

        $Q = $_this->db->query("
		SELECT 
			sma_menu.*
		FROM sma_menu WHERE parent_id ='". $parent ."' ".$active."  AND position ='{$position}'
		 ORDER BY ordering			
		");

        return $Q->result();
    }

    public static  function ConvertDateThaiToDb($date){
        // thai date format mush be dd/mm/yyyy
        $date = trim($date);
        if($date==''){
            return ;
        }

        list($dd , $mm, $yy) = explode('/', $date);
        if ($yy > date('Y')) {
            $yy -= 543;
        }

        return "{$yy}-{$mm}-{$dd}";
    }
    public static  function ConvertDateDbToThai($date){
        // db date format mush be yyyy-mm-dd
        $date = substr(trim($date), 0,10);
        if($date=='' || $date=='0000-00-00'){
            return null;
        }

        list($yy , $mm, $dd) = explode('-', $date);
        $yy += 543;

        return "{$dd}/{$mm}/{$yy}";
    }

    public static  function CurrentDateToDB() {
        return date('Y-m-d');
    }

    public static  function CurrentDateToThai() {
        $date = trim(date('Y-m-d'));
        if($date=='' || $date=='0000-00-00'){
            return null;
        }

        list($yy , $mm, $dd) = explode('-', $date);
        $yy += 543;

        return "{$dd}/{$mm}/{$yy}";
        //return $this->ConvertDateDbToThai(date('Y-m-d'));
    }

}