<?php

global $wpdb;
$medicalCartTable = $wpdb->prefix . "ap_medical_cart";
$client_id = $_GET['client_id'];
$message = '';
//new row
foreach($_POST as $key=>$value){
    $_POST[$key] = str_replace(array('\"',"\'",'\\'),array('"',"'",""),$value);
}

if (isset($_POST['new_row_button']) && $client_id>0){
    print_r($_POST);
    $result = $wpdb->insert(
        $medicalCartTable,
        array(
            'medical_cart_date' => $_POST['cart_date'],
            'medical_cart_code' => $_POST['cart_code'],
            'medical_cart_tooth' => $_POST['cart_tooth'],
            'medical_cart_note' => $_POST['cart_note'],
            'medical_cart_image_ids'=>$_POST['cart_images'],
            'medical_cart_client_id' => $client_id
        ),
        array('%s','%s','%d','%s')
    );
    if ($result==false){
        $message = __('<span style="color:red">Field was not added.</span>','appointzilla');
    }
    else{

        $message = __('<span style="color:green">Field was added successfully.</span>','appointzilla');
    }
}
else if (isset($_POST['edit_row_button']) && $client_id>0){
    $result = $wpdb->update(
        $medicalCartTable,
        array(
            'medical_cart_date' => $_POST['cart_date'],
            'medical_cart_code' => $_POST['cart_code'],
            'medical_cart_tooth' => $_POST['cart_tooth'],
            'medical_cart_note' => $_POST['cart_note'],
            'medical_cart_image_ids'=>$_POST['cart_images']
        ),
        array(
            'medical_cart_id' => $_POST['cart_id']
        ),
        array('%s','%s','%d','%s')
    );
    if ($result==false){
        $message = __('<span style="color:red">Field was not added.</span>','appointzilla');
    }
    else{

        $message = __('<span style="color:green">Field was added successfully.</span>','appointzilla');
    }
}
else if ($_GET['action']=='delete' && is_numeric($_GET['cart_id']) && $_GET['cart_id']>0){
    echo 'delete';
    $result = $wpdb->delete( $medicalCartTable,
        array('medical_cart_id'=>$_GET['cart_id'])
    );
    if ($result==false){
        $message = __('<span style="color:red">Field was not removed.</span>','appointzilla');
    }
    else{

        $message = __('<span style="color:green">Field was removed successfully.</span>','appointzilla');
    }
}
?>

<script src="<?php echo plugins_url('/jquery-ui-custom/js/jquery.ui.datepicker-ru.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/jquery-ui-custom/js/jquery-ui-1.10.4.custom.min.js', __FILE__); ?>" type="text/javascript"></script>
<link href="<?php echo plugins_url('/jquery-ui-custom/css/ui-lightness/jquery-ui-1.10.4.custom.min.css', __FILE__); ?>" type='text/css' media='all' />
<script>
    jQuery(document).ready(function($){

        jQuery("a.fancybox-thumbs").fancybox({
            helpers : {
                thumbs : true,
                theme : 'dark'
            }
        });

        jQuery.datepicker.setDefaults( {dateFormat:"dd/mm/yy"} );
        jQuery( ".datepicker" ).datepicker({ changeMonth: true},jQuery.datepicker.regional[ "ru" ]);

        jQuery('form[name=new_row_form]').submit(function(event){
            if (jQuery('#cart_date').val()=='' || jQuery('#cart_code').val()=='' || jQuery('#cart_tooth').val()==''){
                event.preventDefault();
            }
            if (jQuery('#cart_code').val()=='')
                jQuery("#cart_code").after('<span class="error">&nbsp;<br><strong><?php _e('Cannot be blank.','appointzilla'); ?></strong></span>');
            if (jQuery('#cart_tooth').val()=='')
                jQuery("#cart_tooth").after('<span class="error">&nbsp;<br><strong><?php _e('Cannot be blank.','appointzilla'); ?></strong></span>');
            //add stuff here
        });

        var custom_uploader;


        jQuery('#upload_image_button, #upload_image').click(function(e) {

            e.preventDefault();

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: true
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                attachment = custom_uploader.state().get('selection').toJSON();
                var imageIDList = '';
                jQuery.each(attachment, function(key,singleImage){
                    imageIDList += (imageIDList.length==0) ? singleImage.id:','+singleImage.id;
                });
                jQuery('#cart_images').val(imageIDList);
            });

            //Open the uploader dialog
            custom_uploader.open();

        });
    });

    function NewCartRow()
    {
        jQuery('#gruopnamebox').show();
    }
    function editCartRow()
    {

    }
    function cancelNewRow()
    {
        jQuery('#gruopnamebox').hide();
    }
    function cancelUpdateRow()
    {
        window.location = "?page=medical_cart&client_id=<?php echo $client_id;?>";
    }
</script>
<div class="bs-docs-example tooltip-demo">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-group"></i> <?php _e('Medical Cart','appointzilla'); ?></h3>
    </div>
    <br>


    <?php if ($_GET['action']=='update' &&  is_numeric($_GET['cart_id']) && $_GET['cart_id']>0):
        $result = $wpdb->get_row("SELECT * FROM $medicalCartTable WHERE medical_cart_id = ".$_GET['cart_id'],ARRAY_A);
        $imageList = explode(',',$result['medical_cart_image_ids']);
    ?>
        <div id="gruopnamebox">
            <form method="post" name="edit_row_form" action="?page=medical_cart&client_id=<?php echo $client_id;?>">
                <table width="100%" class="detail-view table table-striped table-condensed">
                    <thead>
                    <tr>
                        <td colspan="2"><?php _e('Edit Row','appointzilla'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width="30%"><strong><?php _e('Date','appointzilla'); ?></strong></td>
                        <td width="70%">
                            <input id="cart_id" name="cart_id" type="hidden"
                                   value="<?php echo $result['medical_cart_id'];?>"/>
                            <input id="cart_date" name="cart_date" class="date datepicker" type="text"
                                               value="<?php echo $result['medical_cart_date'];?>"/></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Code','appointzilla'); ?></strong></td>
                        <td width="70%"><input id="cart_code" name="cart_code" type="text" value="<?php echo $result['medical_cart_code'];?>"/></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Tooth','appointzilla'); ?></strong></td>
                        <td width="70%"><select id="cart_tooth" name="cart_tooth">
                                <option value=""><?php _e('Select One','appointzilla'); ?></option>
                                <?php for ($i = 1; $i<33;$i++):?>
                                    <option value="<?php echo $i;?>" <?php echo ($result['medical_cart_tooth']==$i)?'selected="selected"':'';?>><?php echo $i;?></option>
                                <?php endfor;?>
                            </select></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Note','appointzilla'); ?></strong></td>
                        <td width="70%"><textarea id="cart_note" name="cart_note"><?php echo $result['medical_cart_note'];?></textarea></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Images','appointzilla'); ?></strong></td>
                    <td><div class="imglist"></div><?php
                        foreach($imageList as $singleImage){
                        $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
                            if ($imgStuff!=''):?>
                            <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
                            <?php
                            endif;
                        }?></div></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Upload image','appointzilla'); ?></strong></td>
                        <td width="70%"><label for="upload_image">
                                <input id="cart_images" type="hidden" name="cart_images" value="<?php echo $result['medical_cart_image_ids'];?>"/>
                                <input id="upload_image_button" class="button" type="button" value="Upload Image" />
                                <br />Enter a URL or upload an image
                            </label></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button style="margin-bottom:10px;" id="edit_row_button" type="submit" class="btn" name="edit_row_button"><i class="icon-ok"></i><?php _e('Update','appointzilla'); ?></button>
                            <button style="margin-bottom:10px;" id="cancel_row" type="button" class="btn" name="cancel_row" onclick="cancelUpdateRow()"><i class="icon-remove"></i><?php _e('Cancel','appointzilla'); ?> </button></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>


    <?php elseif (!isset($_GET['client_id'])):
        $clientTable = $wpdb->prefix . "ap_clients";
        $clientList = $wpdb->get_results("SELECT * FROM  $clientTable",ARRAY_A);
        ?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-group"></i> <?php _e('Select Client','appointzilla'); ?></h3>
    </div>
    <br>
    <table width="100%" class="detail-view table table-striped table-condensed">
        <thead>
        <tr>
            <th width="10%"><?php _e('Name','appointzilla'); ?> </th>
            <th width="10%"><?php _e('Phone','appointzilla'); ?></th>
            <th width="10%"><?php _e('Occupation','appointzilla'); ?></th>
            <th width="55%"><?php _e('Address','appointzilla'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientList as $singleRow):?>
            <tr>
                <td><a href="?page=medical_cart&client_id=<?php echo $singleRow['id'];?>"><?php echo $singleRow['name']; ?></a></td>
                <td><?php echo $singleRow['phone']; ?></td>
                <td><?php echo $singleRow['occupation']; ?></td>
                <td><?php echo $singleRow['address']; ?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>


    <?php else:?>


    <div class="messagebox">
        <?php echo $message;?>
    </div>

    <table width="100%" class="detail-view table table-striped table-condensed medical_cart_list">
        <thead>
        <tr>
            <th width="6%"><?php _e('Date','appointzilla'); ?> </th>
            <th width="6%"><?php _e('Code','appointzilla'); ?></th>
            <th width="6%"><?php _e('Tooth','appointzilla'); ?></th>
            <th width="26%"><?php _e('Note','appointzilla'); ?></th>
            <th width="50%"><?php _e('Images','appointzilla'); ?></th>
            <th width="6%"><?php _e('Action','appointzilla'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        global $wpdb;
        $client_id = $_GET['client_id'];
        $medicalCartRows = $wpdb->get_results("SELECT * FROM $medicalCartTable WHERE medical_cart_client_id = $client_id",ARRAY_A);

        foreach($medicalCartRows as $key=>$singleRow):
            $imageList = explode(',',$singleRow['medical_cart_image_ids']);
            ?>

            <td><?php echo $singleRow['medical_cart_date']; ?></td>
            <td><?php echo $singleRow['medical_cart_code']; ?></td>
            <td><?php echo $singleRow['medical_cart_tooth']; ?></td>
            <td><?php echo $singleRow['medical_cart_note']; ?></td>
            <td><div="imglist"><?php foreach($imageList as $singleImage):
                $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
                if ($imgStuff!=''):?>
                 <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
                <?php
                endif;
                endforeach;?></div></td>
            <td><a data-original-title="Update" rel="tooltip" href="?page=medical_cart&cart_id=<?php echo $singleRow['medical_cart_id']; ?>&action=update&client_id=<?php echo $client_id;?>"><i class="icon-pencil"></i></a>
                <a data-original-title="Delete" rel="tooltip" href="?page=medical_cart&cart_id=<?php echo $singleRow['medical_cart_id']; ?>&action=delete&client_id=<?php echo $client_id;?>" onclick="return confirm('Do you want to delete this Question?')"><i class="icon-remove"></i></td>
        </tr>
        <?php endforeach;?>
        </tbody>
        </table>
        <div id="gruopbuttonbox">
            <a data-original-title="" class="btn btn-primary" href="#" rel="tooltip" onclick="NewCartRow()"><i class="icon-plus icon-white"></i><?php _e('Add New','appointzilla'); ?></a>
        </div>
        <div id="gruopnamebox" style="display:none;">
            <form method="post" name="new_row_form">
                <table width="100%" class="detail-view table table-striped table-condensed">
                    <thead>
                    <tr>
                        <td colspan="2"><?php _e('Add New','appointzilla'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width="30%"><strong><?php _e('Date','appointzilla'); ?></strong></td>
                        <td width="70%"><input id="cart_date" name="cart_date" class="date datepicker" type="text"
                                               value="<?php echo date('d/m/Y');?>"/></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Code','appointzilla'); ?></strong></td>
                        <td width="70%"><input id="cart_code" name="cart_code" type="text"/></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Tooth','appointzilla'); ?></strong></td>
                        <td width="70%"><select id="cart_tooth" name="cart_tooth">
                                <option value=""><?php _e('Select One','appointzilla'); ?></option>
                            <?php for ($i = 1; $i<33;$i++):?>
                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                            <?php endfor;?>
                            </select></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Note','appointzilla'); ?></strong></td>
                        <td width="70%"><textarea id="cart_note" name="cart_note"></textarea></td>
                    </tr>
                    <tr>
                        <td width="30%"><strong><?php _e('Upload image','appointzilla'); ?></strong></td>
                        <td width="70%"> <label for="upload_image">
                                <input id="cart_images" type="hidden" name="cart_images"" />
                                <input id="upload_image_button" class="button" type="button" value="Upload Image" />
                                <br />Enter a URL or upload an image
                            </label></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button style="margin-bottom:10px;" id="new_row_button" type="submit" class="btn" name="new_row_button"><i class="icon-ok"></i><?php _e('Create New','appointzilla'); ?></button>
                            <button style="margin-bottom:10px;" id="cancel_row" type="button" class="btn" name="cancel_row" onclick="cancelNewRow()"><i class="icon-remove"></i><?php _e('Cancel','appointzilla'); ?> </button></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php endif;?>
</div>
