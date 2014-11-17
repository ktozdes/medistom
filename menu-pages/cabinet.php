<?php
$CabinetController = new CabinetController();
if (count($_POST)>1 && isset($_POST[CreateCabinet])){
    $result = $CabinetController->createNewCabinet($_POST);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was saved",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was not saved",'appointzilla').'</div>';
    }
    else if ($result == -2){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet with such name already exists",'appointzilla').'</div>';
    }
}
else if (count($_POST)>1 && isset($_POST[update_cabinet])){
    $result = $CabinetController->updateCabinet($_POST);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was updated",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was not saved",'appointzilla').'</div>';
    }
}
else if ($_GET[action]=='delete'){
    $result = $CabinetController->deleteCabinet($_GET);
    if ($result>0){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was deleted",'appointzilla').'</div>';
    }
    else if ($result == -1){
        $message = '<div id="message" class="updated below-h2"><p>'.__("Cabinet was not deleted",'appointzilla').'</div>';
    }
}
if ($_GET[action]=='new'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-plus-square"></i> <?php _e('New Cabinet','appointzilla');?></h3></div>
    <?php
    echo $message;
    $CabinetController->getNewCabinet();
}
else if ($_GET[action]=='update'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-plus-square"></i> <?php _e('Update Cabinet','appointzilla');?></h3></div>
    <?php
    echo $message;
    $CabinetController->getEditableCabinetTable($_GET);
}
else if ($_GET[action]=='view'){?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-plus-square"></i> <?php _e('View Cabinet','appointzilla');?></h3></div>
    <?php
    $CabinetController->viewCabinetTable($_GET);
}else{?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-dot-circle-o"></i> <?php _e('Cabinet','appointzilla');?></h3></div>
    <?php $CabinetController->getCabinetList();?>

<?php }?>