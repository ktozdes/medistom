<?php
$TreatmentController = new TreatmentController();
if (count($_POST)>1 && $_POST[new_treatment]==1){
    $result = $TreatmentController->createNewTreatment($_POST);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was saved",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was not saved",'appointzilla').'</div>';
    }
    else if ($result == -2){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment with such name already exists",'appointzilla').'</div>';
    }
}
else if (count($_POST)>1 && $_POST[update_treatment]==1){
    $result = $TreatmentController->updateTreatment($_POST);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was updated",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was not saved",'appointzilla').'</div>';
    }
}
else if ($_GET[action]=='delete'){
    $result = $TreatmentController->deleteTreatment($_GET);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was deleted",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Treatment was not deleted",'appointzilla').'</div>';
    }
}
if ($_GET[action]=='new'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-plus-square"></i> <?php _e('New Treatment','appointzilla');?></h3></div>
    <?php
    echo $message;
    $TreatmentController->getNewTreatmentTable();
}
else if ($_GET[action]=='update'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-plus-square"></i> <?php _e('Update Treatment','appointzilla');?></h3></div>
    <?php
    echo $message;
    $TreatmentController->getEditableTreatmentTable($_GET);
}
else if ($_GET[action]=='view'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-plus-square"></i> <?php _e('View Treatment','appointzilla');?></h3></div>
    <?php
    $TreatmentController->viewTreatmentTable($_GET);
}else{?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-dot-circle-o"></i> <?php _e('Treatment','appointzilla');?></h3></div>
    <?php $TreatmentController->getTreatmentListTable();?>

<?php }?>
<script>
    jQuery(document).ready(function () {
        jQuery('.jquery-tab').tabs();
        //filter change
        jQuery('.filter_row select').change(function(){
            filterTable(this);
        });
        jQuery('.filter_row input').keyup(function(){
            filterTable(this);
        });
        jQuery('#create_treatment_button').click(function(){
            jQuery('form').submit();
        });
    });
    function filterTable(thisFilter)
    {
        var thisValue = jQuery(thisFilter).val();
        var parentTable = jQuery(thisFilter).parents('table.table-hover');
        //jQuery('#messagebox').html(jQuery(thisFilter).attr('class')+' value:'+thisValue);
        jQuery(parentTable).find('tr.value_row').each(function(index, currentRow){
            var currentCell;
            var selectFilter = false;
            if (jQuery(thisFilter).attr('class')=='filter_code'){
                currentCell = jQuery(currentRow).find('td:first-child');
            }
            else if (jQuery(thisFilter).attr('class')=='filter_name'){
                currentCell = jQuery(currentRow).find('td:nth-child(2)');
            }
            else if (jQuery(thisFilter).attr('class')=='filter_action'){
                currentCell = jQuery(currentRow).find('td:nth-child(3)');
                selectFilter = true;
            }
            //jQuery('#messagebox').append('<br/>'+index+':'+currentCell.html().indexOf(thisValue)+':'+currentCell.html());
            if (jQuery(currentRow).hasClass('filtered_'+jQuery(thisFilter).attr('class'))){
                jQuery(currentRow).removeClass('filtered_'+jQuery(thisFilter).attr('class'));
                if (jQuery(currentRow).attr('class').indexOf('filtered')<0){
                    jQuery(currentRow).show();
                }

            }
            if (selectFilter==true && (currentCell.find('input[type=checkbox]').length>0 && currentCell.find('input[type=checkbox]').attr('checked')=='checked' && thisValue=='yes') || (currentCell.find('input[type=checkbox]').length>0 && currentCell.find('input[type=checkbox]').attr('checked')!='checked' && thisValue=='no')){

            }
            else if (currentCell.html().indexOf(thisValue)<0 && thisValue!=''){
                jQuery(currentRow).addClass('filtered_'+jQuery(thisFilter).attr('class'));
                jQuery(currentRow).hide();
            }
        });
    }
</script>