<?php

class MedicalCartController{
    private $printer;
    function __construct($params=null)
    {
        global $pluginDIR;
        $this->printer = New PrintView(array(file=>$pluginDIR.'menu-pages/view/view_medical_cart.php'));
        $this->pageController($params);
    }

    function pageController($params)
    {
        if ($params[action]=='new'){
            $this->newMedicalCartPage($params);
        }
        else if ($params[action]=='view'){
            $this->singleMedicalCartPage($params);
        }
        else if ($params[action]=='update'){
            $this->editMedicalCartPage($params);
        }
        else if ($params[action]=='delete'){
            $this->deleteMedicalCartPage($params);
        }
        else{
            $this->clientListPage($params);
        }
    }

    function newMedicalCartPage($params)
    {
        global $wpdb;
        $medical_cart_table 	        = $wpdb->prefix . "ap_medical_cart";
        $diagnosis_table 	            = $wpdb->prefix . "ap_diagnosis";
        $table_question 	            = $wpdb->prefix . "ap_questionary";
        $questionary_relation_table 	= $wpdb->prefix . "ap_questionary_relationship";
        $_POST['tooth_list'] = '';

        $result = array();

        foreach($_POST as $key=>$value){
            if (strpos($key,'tooth-')!==false){
                if ($value!='')
                    $_POST['tooth_list'] .= ($_POST['tooth_list']=='')?$value:','.$value;
            }
        }
        if (isset($_POST['new_row_button'])){
            $updateResult = $wpdb->insert(
                $medical_cart_table,
                array(
                    'medical_cart_date' => $_POST['cart_date'],
                    'medical_cart_tooth' => $_POST['tooth_list'],
                    'medical_cart_note' => $_POST['cart_note'],
                    'medical_cart_image_ids'=>$_POST['cart_images'],
                    'medical_cart_diagnosis_id' => $_POST['diagnosis_id'],
                    'medical_cart_client_id' => $params[client_id]
                ));
            $medical_cart_id = $wpdb->insert_id;
            foreach($_POST[question_id] as $question_id=>$value){
                $tempRow = $wpdb->get_row("SELECT * FROM $table_question
                    WHERE id = $question_id",ARRAY_A);
                $other_table_name = ($tempRow[personal]=='0') ? 'medical_cart' : 'user_table';
                $other_table_id   = ($tempRow[personal]=='0') ? $medical_cart_id : $params[client_id];
                if (trim($value)!=''){
                    $wpdb->insert(
                        $questionary_relation_table,
                        array(
                            'value' => trim($value),
                            'other_table_id' => $other_table_id,
                            'other_table_name' => $other_table_name,
                            'question_id' => $question_id
                        )
                    );
                }
            }

            if ($updateResult===false){
                $result[message] = __('<span style="color:red">Field was not added.</span>','appointzilla');
            }
            else{
                $result[message] = __('<span style="color:green">Field was added successfully.</span>','appointzilla');
            }
        }

        $result[medical_cart][medical_cart_client_id] = $params[client_id];
        $result[medical_cart][medical_cart_date] = date('d-m-Y');
        $result[medical_cart][medical_cart_id] = $medical_cart_id;

        $result[diagnosis_list] = $wpdb->get_results("SELECT * FROM $diagnosis_table",ARRAY_A);
        $result[question_list] = $wpdb->get_results("SELECT *,id as question_id FROM $table_question
            ORDER BY `group`",ARRAY_A);
        foreach($result[question_list] as $key=>$singleRow){
            $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                  WHERE question_id = $singleRow[id] AND other_table_name ='medical_cart' AND other_table_id = ".$result[medical_cart][medical_cart_id], ARRAY_A);
            if ($singleRow[personal]=='0'){
                $result[question_list][$key][value] = ($tempArray!=false)?$tempArray[value]:'';
            }
            else{
                $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                  WHERE question_id = $singleRow[id] AND other_table_name ='user_table' AND other_table_id = ".$params[client_id], ARRAY_A);
                $result[question_list][$key][value] = ($tempArray!=false)?$tempArray[value]:'';
            }
        }
        $this->printer->printHtml('new_medical_item',$result);
    }

    function editMedicalCartPage($params)
    {
        global $wpdb;
        $medical_cart_table 	        = $wpdb->prefix . "ap_medical_cart";
        $table_question 	            = $wpdb->prefix . "ap_questionary";
        $questionary_relation_table 	= $wpdb->prefix . "ap_questionary_relationship";
        $diagnosis_table 	            = $wpdb->prefix . "ap_diagnosis";
        $treatment_table 	            = $wpdb->prefix . "ap_treatment";
        $medical_cart_treatment_table 	= $wpdb->prefix . "ap_medical_cart_treatment";
        $_POST['tooth_list'] = '';

        $result = array();

        foreach($_POST as $key=>$value){
            if (strpos($key,'tooth-')!==false){
                if ($value!='')
                    $_POST['tooth_list'] .= ($_POST['tooth_list']=='')?$value:','.$value;
            }
        }
        if (isset($_POST['edit_row_button']) && $_POST[cart_id]>0){
            $updateResult = $wpdb->update(
                $medical_cart_table,
                array(
                    'medical_cart_date' => $_POST['cart_date'],
                    'medical_cart_tooth' => $_POST['tooth_list'],
                    'medical_cart_note' => $_POST['cart_note'],
                    'medical_cart_image_ids'=>$_POST['cart_images'],
                    'medical_cart_diagnosis_id' => $_POST['diagnosis_id']
                ),
                array(
                    'medical_cart_id' => $_POST['cart_id']
                )
            );
            if ($updateResult===false){
                $result[message] = __('<span style="color:red">Field was not updated.</span>','appointzilla');
            }
            else{
                $result[message] = __('<span style="color:green">Field was updated successfully.</span>','appointzilla');
            }
        }

        $result[medical_cart] = $wpdb->get_row("SELECT * FROM $medical_cart_table
        WHERE medical_cart_id = $params[medical_cart_id]",ARRAY_A);
        $result[medical_cart][medical_cart_image_ids]   = explode(',',$result[medical_cart][medical_cart_image_ids]);
        $result[medical_cart][medical_cart_tooth]   = explode(',',$result[medical_cart][medical_cart_tooth]);
        $result[medical_cart][medical_cart_date]    = date('d-m-Y');

        if (isset($_POST['edit_row_button']) && $_POST[cart_id]>0){
            $checkBoxes = $wpdb->get_results("SELECT * FROM $table_question
                    WHERE type = 'bit'",ARRAY_A);
            foreach($checkBoxes as $singleCheckBox){
                $wpdb->delete($questionary_relation_table,
                    array(
                        question_id=>$singleCheckBox[id],
                        other_table_id=>$_POST[cart_id]
                    ));
            }
            foreach($_POST[question_id] as $question_id=>$value){
                $tempRow = $wpdb->get_row("SELECT * FROM $table_question WHERE $table_question.id = $question_id",ARRAY_A);
                $other_table_name = ($tempRow[personal]=='0') ? 'medical_cart' : 'user_table';
                $other_table_id   = ($tempRow[personal]=='0') ? $_POST[cart_id] : $result[medical_cart][medical_cart_client_id];
                $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                    WHERE question_id = $question_id AND other_table_name ='$other_table_name' AND other_table_id = $other_table_id", ARRAY_A);
                //echo '<br/>val:'.trim($value).'---temp:'.($tempArray==false).'----'.$wpdb->last_query;
                if (trim($value)!='' && $tempArray==false){
                    $wpdb->insert(
                        $questionary_relation_table,
                        array(
                            'value' => trim($value),
                            'other_table_id' => $other_table_id,
                            'other_table_name' => $other_table_name,
                            'question_id' => $question_id
                        )
                    );
                }
                else if (trim($value)!='' && is_array($tempArray)){
                    $wpdb->update(
                        $questionary_relation_table,
                        array(
                            'value' => trim($value)
                        ),
                        array(
                            'other_table_id' => $other_table_id,
                            'other_table_name' => $other_table_name,
                            'question_id' => $question_id
                        )
                    );
                }
            }
        }
        $result[diagnosis_list] = $wpdb->get_results("SELECT * FROM $diagnosis_table",ARRAY_A);
        $result[all_treatment_list] = $wpdb->get_results("SELECT * FROM $treatment_table",ARRAY_A);
        $result[treatment_list] = $wpdb->get_results("SELECT tr.treatment_id, tr.treatment_name, mc_tr.medical_cart_treatment_date
FROM $treatment_table as tr
            INNER JOIN $medical_cart_treatment_table as mc_tr ON mc_tr.treatment_id =  tr.treatment_id
            WHERE mc_tr.medical_cart_id = $params[medical_cart_id]
        ",ARRAY_A);
        $result[question_list] = $wpdb->get_results("SELECT *,id as question_id FROM $table_question
            ORDER BY `group`",ARRAY_A);
        foreach($result[question_list] as $key=>$singleRow){
            $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                  WHERE question_id = $singleRow[id] AND other_table_name ='medical_cart' AND other_table_id = ".$result[medical_cart][medical_cart_id], ARRAY_A);
            if ($singleRow[personal]=='0'){
                $result[question_list][$key][value] = ($tempArray!=false)?$tempArray[value]:'';
            }
            else{
                $tempArray = $wpdb->get_row("SELECT value FROM $questionary_relation_table
                  WHERE question_id = $singleRow[id] AND other_table_name ='user_table' AND other_table_id = ".$result[medical_cart][medical_cart_client_id], ARRAY_A);
                $result[question_list][$key][value] = ($tempArray!=false)?$tempArray[value]:'';
            }
        }

        $this->printer->printHtml('new_medical_item',$result);
    }

    function clientListPage($params)
    {
        global $wpdb;
        $clientTable = $wpdb->prefix . "ap_clients";
        $medicalTable = $wpdb->prefix . "ap_medical_cart";
        $filterQuery = (isset($_POST[searchname]) && strlen($_POST[searchname])>2)?"WHERE name LIKE '%$_POST[searchname]%'":'';
        $result[client_list] = $wpdb->get_results("SELECT * FROM  $clientTable INNER JOIN $medicalTable on $medicalTable.medical_cart_client_id = $clientTable.id
         GROUP BY $clientTable.id",ARRAY_A);
        $this->printer->printHtml('client_list',$result);
    }

    function deleteMedicalCartPage($params)
    {
        global $wpdb;
        $clientTable = $wpdb->prefix . "ap_clients";
        $medicalTable = $wpdb->prefix . "ap_medical_cart";
        $questionary_relation_table 	= $wpdb->prefix . "ap_questionary_relationship";
        $queryResult = $wpdb->delete( $medicalTable,
            array('medical_cart_id'=>$_GET['medical_cart_id'])
        );
        echo '<br/>'.$wpdb->last_query;
        $queryResult = $wpdb->delete(
            $questionary_relation_table,
            array(
                'other_table_id'=>$_GET['medical_cart_id'],
                'other_tabele_name'=>'medical_cart',
            )
        );
        echo '<br/>'.$wpdb->last_query;
        if ($queryResult==false){
            $result[message] = __('<span style="color:red">Field was not removed.</span>','appointzilla');
        }
        else{
            $result[message] = __('<span style="color:green">Field was removed successfully.</span>','appointzilla');
        }
        $this->singleMedicalCartPage(array_merge($params,$result));
    }

    function singleMedicalCartPage($params)
    {
        global $wpdb;
        $clientTable = $wpdb->prefix . "ap_clients";
        $medicalTable = $wpdb->prefix . "ap_medical_cart";
        $diagnosisTable = $wpdb->prefix . "ap_diagnosis";
        $filterQuery = (isset($_POST[searchname]) && strlen($_POST[searchname])>2)?"WHERE name LIKE '%$_POST[searchname]%'":'';
        $result[client_list] = $wpdb->get_results("SELECT * FROM  $clientTable
          INNER JOIN $medicalTable on $medicalTable.medical_cart_client_id = $clientTable.id
          INNER JOIN $diagnosisTable on $medicalTable.medical_cart_diagnosis_id = $diagnosisTable.diagnosis_id
        WHERE $clientTable.id = $params[client_id]",ARRAY_A);
        $this->printer->printHtml('single_medical_cart_page',$result);
    }
}