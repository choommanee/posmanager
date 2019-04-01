<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Mpdf\Mpdf;

class Tec_mpdf2
{
    public function __construct() {
    }

    public function __get($var) {
        return get_instance()->$var;
    }

    public function generate($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $orientation = 'P',$totalRec,$peperType=null) {

        if (!$output_type) {
            $output_type = 'D';
        }
        if (!$margin_top) {
            $margin_top = '20';
        }
       // echo $margin_top;
       // die();

        //$mpdf = new Mpdf('utf-8', 'A4' . $orientation, '13', '', 10, 10, $margin_top, $margin_bottom, 9, 9);
        // echo $peperType;
       // die();
        if(!empty($peperType)){
            // echo $peperType;
            //die();
            if($peperType==1){
                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => [204, 136],
                    'orientation' => 'P',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 10,
                    'margin_bottom' => 2,
                    'margin_header' => 2,
                    'margin_footer' => 2
                ]);
            }elseif($peperType==2){
                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => [204, 280],
                    'orientation' => 'P',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 20,
                    'margin_bottom' => 2,
                    'margin_header' =>10,
                    'margin_footer' => 10
                ]);
            }
        }else{
            if($totalRec>5){
                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => [204, 280],
                    'orientation' => 'P',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 20,
                    'margin_bottom' => 2,
                    'margin_header' =>10,
                    'margin_footer' => 10
                ]);
            }elseif($totalRec<6){
                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => [204, 136],
                    'orientation' => 'P',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 10,
                    'margin_bottom' => 2,
                    'margin_header' => 2,
                    'margin_footer' => 2
                ]);
            }
        }

        
        $mpdf->useOddEven = 1;
        $mpdf->debug = (ENVIRONMENT == 'development');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        //$mpdf->Footer()


        $mpdf->SetTopMargin(20);
        $mpdf->margin_footer="0";
        $mpdf->SetTitle($this->Settings->site_name);
        $mpdf->SetAuthor($this->Settings->site_name);
        $mpdf->SetCreator($this->Settings->site_name);
        $mpdf->SetDisplayMode('fullpage');

        if (is_array($content)) {
             //echo $as = sizeof($content);
            // die();
            $r = 1;
            foreach ($content as $page) {
                $mpdf->WriteHTML($page['content']);
                
               // if ($as != $r) {
                    $mpdf->AddPage();
                //}
                $r++;
            }
        }else{
            $mpdf->WriteHTML($content);
        }
        $mpdf->Output($name, $output_type);

    }

}
