<?php
class PDFPrinter
{

    public function __construct()
    {
    }
    public function printMedicalCart($params)
    {
        global $wpdb;
        $client_table               = $wpdb->prefix . "ap_clients";
        $medical_cart_table         = $wpdb->prefix . "ap_medical_cart";
        $medical_cart_treat_table   = $wpdb->prefix . "ap_medical_cart_treatment";
        $diagnosis_table            = $wpdb->prefix . "ap_diagnosis";
        $treatment_table            = $wpdb->prefix . "ap_treatment";
        $table_question 	            = $wpdb->prefix . "ap_questionary";
        $questionary_relation_table 	= $wpdb->prefix . "ap_questionary_relationship";

        require_once $_SERVER['DOCUMENT_ROOT'].'/medistom/wp-content/plugins/appointment-calendar-premium/menu-pages/tcpdf/examples/tcpdf_include.php';

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Ernis Kanimetov');
        $pdf->SetTitle('Medical Cart');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('Medical Cart, medistom');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Медицинская Карта', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('dejavusans', '', 10);

        // add a page

        global $wpdb;

        $clientDetails = $wpdb->get_row("SELECT * FROM $client_table where id = $params[client_id]",ARRAY_A);
        $medicalCartDetails = $wpdb->get_results("SELECT * FROM $medical_cart_table
            INNER JOIN  $diagnosis_table on $diagnosis_table.diagnosis_id = $medical_cart_table.medical_cart_diagnosis_id
            WHERE  medical_cart_client_id = $params[client_id]",ARRAY_A);
        //echo  $wpdb->last_query;
        //print_r($medicalCartDetails);
        // create some HTML content

        $pdf->AddPage();
        $html .= "<h1>Медицинская Карта - $clientDetails[name]</h1>";
        if (is_array($medicalCartDetails) && count($medicalCartDetails)>0){
            foreach($medicalCartDetails as $singleMedicalCartItem){
                $html .='<h2>Диагноз</h2>';
                $html .= "
                <table border='1' cellspacing='3' cellpadding='4'>
                <tr>
                    <th>Дата : $singleMedicalCartItem[medical_cart_date]</th>
                    <th>Зуб : $singleMedicalCartItem[medical_cart_tooth]</th>
                    <th>Диагноз : $singleMedicalCartItem[diagnosis_name]</th>
                </tr>
                </table>";
                $html .='<h2>Вопросник</h2>';
                $html .="<table border='1' cellspacing='3' cellpadding='4'>";
                $currentGroup = '';
                $question_list = $wpdb->get_results("SELECT *,id as question_id FROM $table_question
                ORDER BY `group`",ARRAY_A);
                if (is_array($question_list) && count($question_list)>0){
                    foreach($question_list as $key=>$singleRow){
                        $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                          WHERE question_id = $singleRow[id] AND other_table_name ='medical_cart' AND other_table_id = ".$singleMedicalCartItem['medical_cart_id'], ARRAY_A);
                        if ($singleRow['personal']=='0'){
                            $question_list[$key]['value'] = ($tempArray!=false)?$tempArray['value']:'';
                        }
                        else{
                            $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                          WHERE question_id = $singleRow[id] AND other_table_name ='user_table' AND other_table_id = ".$singleMedicalCartItem['medical_cart_client_id'], ARRAY_A);
                            $question_list[$key]['value'] = ($tempArray!=false)?$tempArray['value']:'';
                        }
                        if($currentGroup!=$singleRow['group']){
                            $html .='
                            <tr>
                                <td color="#000080" colspan="2"><h4>'.$singleRow['group'].'</h4></td>
                            </tr>';
                        }
                        $value = $singleRow['value'];
                        if ($singleRow['type']=='bit'){
                            $value = ($singleRow['value']=='1')?'Да':'Нет';
                        }
                        $style = ($key%2)==0?'bgcolor="#e6e6fa"':'';
                        $html .='
                            <tr>
                                <th '.$style.'>'. $singleRow['question'].'</th>
                                <td '.$style.'>'.$value.'</td>
                            </tr>';
                        $currentGroup = $singleRow['group'];
                    }
                }
                $html .='</table>';
                $singleMedicalCartItem[medical_cart_image_ids]  = explode(',',$singleMedicalCartItem[medical_cart_image_ids]);
                if (is_array($singleMedicalCartItem[medical_cart_image_ids]) && count($singleMedicalCartItem[medical_cart_image_ids])>0){
                    $html .='<h2>Снимки</h2>';
                    foreach($singleMedicalCartItem[medical_cart_image_ids] as $singleImageID){
                        $image = wp_get_attachment_image_src( $singleImageID, 'full' );
                        $html .= '<p><img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'"/></p>';
                    }
                }
                $treatmentList = $wpdb->get_results("SELECT * FROM $medical_cart_treat_table
            INNER JOIN  $treatment_table on $treatment_table.treatment_id = $medical_cart_treat_table.treatment_id
            WHERE  $medical_cart_treat_table.medical_cart_id = $singleMedicalCartItem[medical_cart_id]",ARRAY_A);
                if (is_array($treatmentList) && count($treatmentList)>0){
                    $html .='<h2>Лечение</h2>';
                    $html .="<table bgcolor='#cdb5ff' border='1' cellspacing='3' cellpadding='4'>
                    <tr>
                        <th>".__('Name','appointzilla')."</th>
                        <th >".__('Date','appointzilla')."</th>
                    </tr>";
                    foreach($treatmentList as $key=>$singleTreatment){
                        $style = ($key%2)==0?"bgcolor='#e6e6fa'":'';
                        $html .="<tr>
                            <td $style > $singleTreatment[treatment_name]</td>
                            <td $style > $singleTreatment[medical_cart_treatment_date]</td>
                        </tr>";
                    }
                    $html .="</table>";
                }
            }
        }
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('medical_cart.pdf', 'I');

    }
}