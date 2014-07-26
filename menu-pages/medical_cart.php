
<div class="bs-docs-example tooltip-demo">
    <div id="ajax-loading-container">
        <img src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/big-loading.gif');?>"/>
    </div>
<?php
$message = '';
//new row
foreach($_POST as $key=>$value){
    $_POST[$key] = str_replace(array('\"',"\'",'\\'),array('"',"'",""),$value);
}
?>
<script>


    var custom_uploader;
    jQuery(document).ready(function($){
        jQuery('area').hover(function(){
            var tempInID = jQuery(this).attr('class').substring(5);
            jQuery('.image-hover-'+tempInID).show();
        });

        jQuery('.image-hover').hover(
            function(){
            },
            function(){
                jQuery(this).hide();
            }
        );

        jQuery('.main-image').hover(
            function(){
            },
            function(){
                jQuery('.image-hover').hide();
            }
        );

        jQuery('.image-hover').click(function(){
            var tempInID = jQuery(this).attr('class').substring(24);
            jQuery('.image-selected-'+tempInID).show();
            jQuery('input[name=tooth-'+tempInID+']').val(tempInID);
        });

        jQuery('.image-selected').click(function(){
            var tempInID = jQuery(this).attr('class').substring(30);
            jQuery(this).hide();
            jQuery('input[name=tooth-'+tempInID+']').val('');
        });

        jQuery("a.fancybox-thumbs").fancybox({
            helpers : {
                thumbs : true,
                theme : 'dark'
            }
        });

        jQuery.datepicker.setDefaults( {dateFormat:"dd-mm-yy"} );
        jQuery( ".datepicker" ).datepicker({ changeMonth: true,changeYear: true},jQuery.datepicker.regional[ "ru" ]);

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


    });

    function MediaUploadFrame(e,imageIDList)
    {
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
            selection:imageIDList,
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
        custom_uploader.on('open',function() {
            var selection = custom_uploader.state().get('selection');

            //Get ids array from
            ids = jQuery('#cart_images').val().split(',');
            ids.forEach(function(id) {
                attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            });
        });

        //Open the uploader dialog
        custom_uploader.open();
    }

    function addNewTreatment(medicalCartID)
    {
        jQuery('#ajax-loading-container').show();
        var data = {
            'action':'new_medical_cart_treatment',
            'treatment_id': jQuery("#new_treatment_id").val(),
            'medical_cart_id': medicalCartID
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            if (response==1){
                jQuery('.analysis_container table tr:last').before('<tr><td>'+jQuery("#new_treatment_id option:selected").text()+'</td><td><?php echo date('d-m-Y')?></td></tr>');
            }
            jQuery('#ajax-loading-container').hide();
        });
    }
</script>
<?php
$MedicalCartController = new MedicalCartController($_GET);?>
</div>
