<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : mPDF
 *  Web     : https://github.com/mpdf/mpdf
 *  License : GNU General Public License v2.0
 *          : https://github.com/mpdf/mpdf/blob/development/LICENSE.txt
 *  ==============================================================================
 */

use Mpdf\Mpdf;

class Tec_mpdf
{
    public function __construct() {
    }

    public function __get($var) {
        return get_instance()->$var;
    }

    public function generate($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $orientation = 'P') {

        if (!$output_type) {
            $output_type = 'D';
        }
        if (!$margin_top) {
            $margin_top = '0';
        }
       // echo $margin_top;
       // die();

        //$mpdf = new Mpdf('utf-8', 'A4' . $orientation, '13', '', 10, 10, $margin_top, $margin_bottom, 9, 9);
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [204, 280],
            'orientation' => 'P'
        ]);
        $mpdf->debug = (ENVIRONMENT == 'development');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->margin_header =0;


        // if you need to add protection to pdf files, please uncomment the line below or modify as you need.
        // $mpdf->SetProtection(array('print')); // You pass 2nd arg for user password (open) and 3rd for owner password (edit)
        // $mpdf->SetProtection(array('print', 'copy')); // Comment above line and uncomment this to allow copying of content
        $mpdf->SetTopMargin($margin_top);
        $mpdf->SetTitle($this->Settings->site_name);
        $mpdf->SetAuthor($this->Settings->site_name);
        $mpdf->SetCreator($this->Settings->site_name);
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents('assets/bs/bootstrap.min.css');
        // $mpdf->WriteHTML($stylesheet, 1);
        // $mpdf->SetFooter($this->Settings->site_name.'||{PAGENO}/{nbpg}', '', TRUE); // For simple text footer

        if (is_array($content)) {
            //$mpdf->SetHeader($this->Settings->site_name.'||{PAGENO}/{nbpg}', '', TRUE); // For simple text header
            $as = sizeof($content);
            $r = 1;
            foreach ($content as $page) {
                $mpdf->WriteHTML($page['content']);
                if (!empty($page['footer'])) {
                    $mpdf->SetHTMLFooter('<p class="text-center">' . $page['footer'] . '</p>', '', true);
                }
                if ($as != $r) {
                    $mpdf->AddPage();
                }
                $r++;
            }

        } else {

            $mpdf->WriteHTML($content);
           // $mpdf->SetHTMLHeader('<p class="text-center">123123' . $header . '</p>', 'E', true);
            if ($header != '') {
                $mpdf->SetHTMLHeader('<p class="text-center">123123' . $header . '</p>', 'ALL', true);
            }
            if ($footer != '') {
                $mpdf->SetHTMLFooter('<p class="text-center">' . $footer . '</p>', '', true);
            }

        }

        if ($output_type == 'S') {
            $file_content = $mpdf->Output('', 'S');
            write_file('assets/uploads/' . $name, $file_content);
            return 'assets/uploads/' . $name;
        } else {
            $mpdf->Output($name, $output_type);
        }
    }

}
