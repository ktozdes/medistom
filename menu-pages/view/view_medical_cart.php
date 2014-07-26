<?php if ($section=='helloworld'):?>
Hhello world from Medical Cart
<?php elseif ($section=='new_medical_item'):?>
    <style>
        #teeth_image_container{
            position:relative;
        }
        #teeth_image_container img.image-hover,#teeth_image_container img.image-selected{
            position:absolute;
            display:none;
        }
    </style>
    <div class="bs-docs-example tooltip-demo">
    <?php if (strlen($result[message])>10):?>
    <div id="message" class="updated below-h2"><p>
        <?php echo $result[message];?>
    </p></div>
    <?php endif;?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-group"></i> <?php _e('Medical Cart Item','appointzilla'); ?></h3>
    </div>
    <form method="post">
        <table width="100%" class="detail-view table table-striped table-condensed">
            <tbody>
            <tr>
                <th width="10%"><strong><?php _e('Date','appointzilla'); ?></strong></th>
                <th width="10%"><strong><?php _e('Diagnosis','appointzilla'); ?></strong></th>
                <th width="10%"><strong><?php _e('Note','appointzilla'); ?></strong></th>
                <th width="69%"><strong><?php _e('Upload image','appointzilla'); ?></strong></th>
            </tr>
                <td>
                    <input id="cart_id" name="cart_id" type="hidden"
                           value="<?php echo $result[medical_cart]['medical_cart_id'];?>"/>
                    <input id="cart_date" name="cart_date" class="date datepicker" type="text"
                           value="<?php echo $result[medical_cart]['medical_cart_date'];?>"/></td>
                <td><select name=diagnosis_id>
                    <?php foreach($result[diagnosis_list] as $singleDiagnosis):?>
                    <option value="<?php echo $singleDiagnosis[diagnosis_id];?>" <?php echo $result[medical_cart]['medical_cart_diagnosis_id'] == $singleDiagnosis[diagnosis_id]?'selected="selected"':'';?>><?php echo $singleDiagnosis[diagnosis_name]?></option>
                    <?php endforeach;?>
        </select></td>
                <td><textarea id="cart_note" name="cart_note"><?php echo $result[medical_cart]['medical_cart_note'];?></textarea></td>
                <td><div class="imglist"><?php
                        if (is_array($result[medical_cart][medical_cart_image_ids])){
                        foreach($result[medical_cart][medical_cart_image_ids] as $singleImage){
                            $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
                            if ($imgStuff!=''):?>
                                <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
                            <?php
                            endif;
                        }}?></div>
                    <br/><label for="upload_image">
                        <input id="cart_images" type="hidden" name="cart_images" value="<?php
                        echo (is_array($result[medical_cart][medical_cart_tooth]))?implode(',',$result[medical_cart][medical_cart_image_ids]):'';?>"/>
                        <a id="upload_image_button" onclick="MediaUploadFrame(this,'<?php echo (is_array($result[medical_cart][medical_cart_tooth]))?implode(',',$result[medical_cart][medical_cart_image_ids]):''?>')" href="javascript:void(0)" class="button">Upload Image</a>
                        <br />Enter a URL or upload an image
                    </label></td>
            </tr>
            </tbody>
            </table>
                <h4><?php _e('Questionary','appointzilla'); ?></h4>
            <table width="100%" class="detail-view table table-striped table-condensed">
            <?php
            $currentGroup = '';
            foreach($result[question_list] as $singleQuestion):?>
            <?php if($currentGroup!=$singleQuestion['group']):?>
                <tr>
                    <td colspan="2"><h4><?php echo $singleQuestion['group'];?></h4></td>
                </tr>
            <?php endif;?>
            <tr>
                <th width="15%"><?php echo $singleQuestion['question'];?></th>
                <td><?php
                    if ($singleQuestion['type']=='date'):?>
                        <input type="text" class="datepicker date" <?php echo ($singleQuestion['personal']=='1' && $singleQuestion[value]!='') ? 'disabled="disabled"' : '';?> name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>" />
                    <?php elseif($singleQuestion['type']=='text'):?>
                        <input type="text" <?php echo ($singleQuestion['personal']=='1' && $singleQuestion[value]!='') ? 'disabled="disabled"' : '';?> name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>" class="numeric"/>
                    <?php elseif($singleQuestion['type']=='bit'):?>
                        <input type="checkbox" <?php echo ($singleQuestion['personal']=='1') ? 'disabled="disabled"' : '';?> name="question_id[<?php echo $singleQuestion['question_id'];?>]" <?php echo $singleQuestion['value']=='on'?'checked="checked"':'';?>/>
                    <?php elseif($singleQuestion['type']=='numeric'):?>
                        <input type="text" <?php echo ($singleQuestion['personal']=='1' && $singleQuestion[value]!='') ? 'disabled="disabled"' : '';?> name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>"/>
                    <?php endif;?>
                </td>
            </tr>
            <?php
            $currentGroup = $singleQuestion['group'];
            endforeach;?>
            </table>
            <div class="teeth_container">
                <h4><?php _e('Select Teeth','appointzilla'); ?></h4>
                <div id="teeth_image_container">
                <script>
                    jQuery(document).ready(function(){
                        <?php
                        if (is_array($result[medical_cart][medical_cart_tooth])){
                        foreach($result[medical_cart][medical_cart_tooth] as $singleTooth):?>
                        jQuery('.image-selected-<?php echo $singleTooth;?>').show();
                        jQuery('input[name=tooth-<?php echo $singleTooth;?>]').val(<?php echo $singleTooth;?>);
                        <?php
                        endforeach;
                    }
                    if (isset($_POST[new_row_button])){?>
                        location.href='?page=medical_cart&action=update&medical_cart_id=<?php echo $result[medical_cart][medical_cart_id];?>';
                        <?php }?>
                    });
                </script>
                <img class="main-image" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/32-teeth.png');?>" border="0" width="301" height="500" orgWidth="301" orgHeight="500" usemap="#main-image-map" alt="" />
                <map name="main-image-map" id="main-image-map">
                    <area shape="rect" coords="299,498,301,500" alt="Image Map" style="outline:none;" title="Image Map" />
                    <area class="area-27" alt="#27 - Левый 2-й большой коренной. Верхняя челюсть" title="#27 - Левый 2-й большой коренной. Верхняя челюсть" shape="poly" coords="241,196,246,223,279,215,282,199,273,185,246,190" style="outline:none;" target="_self"/>
                    <area class="area-26" alt="#26 - Левый 1-й большой коренной. Верхняя челюсть" title="#26 - Левый 1-й большой коренной. Верхняя челюсть"   shape="poly" coords="231,154,232,177,245,189,272,177,277,151,258,140" style="outline:none;" target="_self"/>
                    <area class="area-25" alt="#25 - Левый 2-й малый коренной. Верхняя челюсть" title="#25 - Левый 2-й малый коренной. Верхняя челюсть"   shape="poly" coords="232,120,228,131,246,140,260,138,266,124,252,113" style="outline:none;" target="_self"/>
                    <area class="area-24" alt="#24 - Левый 1-й малый коренной. Верхняя челюсть" title="#24 - Левый 1-й малый коренной. Верхняя челюсть"   shape="poly" coords="219,96,215,110,232,116,250,107,250,91,237,84" style="outline:none;" target="_self"/>
                    <area class="area-23" alt="#23 - Левый клык. Верхняя челюсть" title="#23 - Левый клык. Верхняя челюсть"   shape="poly" coords="209,59,201,79,207,91,226,85,234,75,224,62" style="border-width:1" target="_self"/>
                    <area class="area-21" alt="#21 - Левый передний резец. Верхняя челюсть" title="#21 - Левый передний резец. Верхняя челюсть"   shape="poly" coords="148,37,163,65,181,44,176,37,158,33" style="outline:none;" target="_self"/>
                    <area class="area-22" alt="#22 - Левый резец. Верхняя челюсть" title="#22 - Левый резец. Верхняя челюсть"   shape="poly" coords="185,41,182,68,190,74,204,61,210,54,195,42" style="outline:none;" target="_self"/>
                    <area class="area-28" alt="#28 - Левый 3-й большой коренной. Верхняя челюсть" title="#28 - Левый 3-й большой коренной. Верхняя челюсть"   shape="poly" coords="245,231,247,263,259,268,280,261,286,239,272,228,260,225" style="outline:none;" target="_self"/>
                    <area class="area-11" alt="#11 - Правый передний резец. Верхняя челюсть" title="#11 - Правый передний резец. Верхняя челюсть"   shape="poly" coords="112,40,117,53,132,66,141,52,147,34,135,31,124,34" style="outline:none;" target="_self"/>
                    <area class="area-12" alt="#12 - Правый резец. Верхняя челюсть" title="#12 - Правый резец. Верхняя челюсть"   shape="poly" coords="85,55,109,74,112,60,110,40,96,43" style="outline:none;" target="_self"/>
                    <area class="area-13" alt="#13 - Правый клык. Верхняя челюсть" title="#13 - Правый клык. Верхняя челюсть"   shape="poly" coords="62,79,80,90,92,89,91,78,84,57,70,60" style="outline:none;" target="_self"/>
                    <area class="area-14" alt="#14 - Правый 1-й малый коренной. Верхняя челюсть" title="#14 - Правый 1-й малый коренной. Верхняя челюсть"   shape="poly" coords="44,97,51,113,73,115,80,103,69,90,53,85" style="outline:none;" target="_self"/>
                    <area class="area-15" alt="#15 - Правый 2-й малый коренной. Верхняя челюсть" title="#15 - Правый 2-й малый коренной. Верхняя челюсть"   shape="poly" coords="30,125,38,140,57,139,67,131,56,118,46,112,38,114" style="outline:none;" target="_self"/>
                    <area class="area-16" alt="#16 - Правый 1-й большой коренной. Верхняя челюсть" title="#16 - Правый 1-й большой коренной. Верхняя челюсть"   shape="poly" coords="21,152,22,175,40,185,53,189,65,156,38,141,24,143" style="outline:none;" target="_self"/>
                    <area class="area-17" alt="#17 - Правый 2-й большой коренной. Верхняя челюсть" title="#17 - Правый 2-й большой коренной. Верхняя челюсть"   shape="poly" coords="22,185,13,197,17,218,49,225,55,216,54,193,38,188" style="outline:none;" target="_self"/>
                    <area class="area-18" alt="#18 - Правый 3-й большой коренной. Верхняя челюсть" title="#18 - Правый 3-й большой коренной. Верхняя челюсть"   shape="poly" coords="28,223,11,232,7,247,13,261,34,268,45,264,51,252,51,227" style="outline:none;" target="_self"/>
                    <area class="area-38" alt="#38 - Левый 31-й большой коренной. Нижняя челюсть" title="#38 - Левый 3-й большой коренной. Нижняя челюсть"   shape="poly" coords="238,275,237,302,253,310,275,307,279,291,272,281" style="outline:none;" target="_self"/>
                    <area class="area-37" alt="#37 - Левый 2-й большой коренной. Нижняя челюсть" title="#37 - Левый 2-й большой коренной. Нижняя челюсть"   shape="poly" coords="241,310,236,346,251,352,270,348,278,337,275,319,260,312" style="outline:none;" target="_self"/>
                    <area class="area-36" alt="#36 - Левый 1-й большой коренной. Нижняя челюсть" title="#36 - Левый 1-й большой коренной. Нижняя челюсть"   shape="poly" coords="233,356,227,365,226,385,240,395,260,394,270,383,266,365,254,355" style="outline:none;" target="_self"/>
                    <area class="area-35" alt="#35 - Левый 2-й малый коренной. Нижняя челюсть" title="#35 - Левый 2-й малый коренной. Нижняя челюсть"   shape="poly" coords="226,397,222,405,219,417,238,428,253,423,249,400,238,396" style="outline:none;" target="_self"/>
                    <area class="area-34" alt="#34 - Левый 1-й малый коренной. Нижняя челюсть" title="#34 - Левый 1-й малый коренной. Нижняя челюсть"   shape="poly" coords="212,427,209,439,217,455,233,455,239,447,236,430" style="outline:none;" target="_self"/>
                    <area class="area-33" alt="#33 - Левый клык. Нижняя челюсть" title="#33 - Левый клык. Нижняя челюсть"   shape="poly" coords="194,453,192,470,204,485,215,480,223,470,215,454,204,453" style="outline:none;" target="_self"/>
                    <area class="area-32" alt="#32 - Левый резец. Нижняя челюсть" title="#32 - Левый резец. Нижняя челюсть"   shape="poly" coords="184,467,176,479,176,491,190,490,197,486,192,476" style="outline:none;" target="_self"/>
                    <area class="area-31" alt="#31 - Левый передний резец. Нижняя челюсть" title="#31 - Левый передний резец. Нижняя челюсть"   shape="poly" coords="160,472,153,483,152,493,167,493,173,486" style="outline:none;" target="_self"/>
                    <area class="area-41" alt="#41 - Правый передний резец. Нижняя челюсть" title="#41 - Правый передний резец. Нижняя челюсть"   shape="poly" coords="137,470,128,480,125,493,141,492,150,490,145,479" style="outline:none;" target="_self"/>
                    <area class="area-42" alt="#42 - Правый резец. Нижняя челюсть" title="#42 - Правый резец. Нижняя челюсть"   shape="poly" coords="113,465,104,476,100,487,119,491,123,479" style="outline:none;" target="_self"/>
                    <area class="area-43" alt="#43 - Правый клык. Нижняя челюсть" title="#43 - Правый клык. Нижняя челюсть"   shape="poly" coords="93,453,78,457,79,473,94,484,104,476,105,458" style="outline:none;" target="_self"/>
                    <area class="area-44" alt="#44 - Правый 1-й малый коренной. Нижняя челюсть" title="#44 - Правый 1-й малый коренной. Нижняя челюсть"   shape="poly" coords="84,427,70,428,60,435,60,450,73,456,86,452,91,441" style="outline:none;" target="_self"/>
                    <area class="area-45" alt="#45 - Правый 2-й малый коренной. Нижняя челюсть" title="#45 - Правый 2-й малый коренной. Нижняя челюсть"   shape="poly" coords="68,398,51,396,45,413,56,431,76,422,78,406" style="outline:none;" target="_self"/>
                    <area class="area-46" alt="#46 - Правый 1-й большой коренной. Нижняя челюсть" title="#46 - Правый 1-й большой коренной. Нижняя челюсть"   shape="poly" coords="52,353,33,366,37,393,63,394,71,379,69,359" style="outline:none;" target="_self"/>
                    <area class="area-47" alt="#47 - Правый 2-й большой коренной. Нижняя челюсть" title="#47 - Правый 2-й большой коренной. Нижняя челюсть"   shape="poly" coords="48,310,26,319,21,333,33,349,54,351,65,344,63,319" style="outline:none;" target="_self"/>
                    <area class="area-48" alt="#48 - Правый 3-й большой коренной. Нижняя челюсть" title="#48 - Правый 3-й большой коренной. Нижняя челюсть"   shape="poly" coords="57,273,37,274,26,281,21,298,33,315,62,303,64,289" style="outline:none;" target="_self"/>
                </map>

                <img class="image-hover image-hover-18" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s18.gif');?>" alt="#18 - Правый 3-й большой коренной. Верхняя челюсть" title="#18 - Правый 3-й большой коренной. Верхняя челюсть" style="left: 6px;
    top: 219px;">
                <img class="image-selected image-selected-18" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d18.gif');?>" alt="#18 - Правый 3-й большой коренной. Верхняя челюсть" title="#18 - Правый 3-й большой коренной. Верхняя челюсть" style="left: 7px;
    top: 218px;">

                <img class="image-hover image-hover-17" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s17.gif');?>" alt="#17 - Правый 2-й большой коренной. Верхняя челюсть" title="#17 - Правый 2-й большой коренной. Верхняя челюсть" style="left: 12px;top: 182px;">
                <img class="image-selected image-selected-17" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d17.gif');?>" alt="#17 - Правый 2-й большой коренной. Верхняя челюсть" title="#17 - Правый 2-й большой коренной. Верхняя челюсть" style="left: 12px;top: 182px;">
                <img class="image-hover image-hover-16" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s16.gif');?>" alt="#16 - Правый 1-й большой коренной. Верхняя челюсть" title="#16 - Правый 1-й большой коренной. Верхняя челюсть" style="left: 18px;
    top: 139px;">
                <img class="image-selected image-selected-16" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d16.gif');?>" alt="#16 - Правый 1-й большой коренной. Верхняя челюсть" title="#16 - Правый 1-й большой коренной. Верхняя челюсть" style="left: 18px;
    top: 139px;">
                <img class="image-hover image-hover-15" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s15.gif');?>" alt="#15 - Правый 2-й малый коренной. Верхняя челюсть" title="#15 - Правый 2-й малый коренной. Верхняя челюсть" style="left: 28px;
    top: 112px;">
                <img class="image-selected image-selected-15" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d15.gif');?>" alt="#15 - Правый 2-й малый коренной. Верхняя челюсть" title="#15 - Правый 2-й малый коренной. Верхняя челюсть" style="left: 28px;
    top: 112px;">
                <img class="image-hover image-hover-14" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s14.gif');?>" alt="#14 - Правый 1-й малый коренной. Верхняя челюсть" title="#14 - Правый 1-й малый коренной. Верхняя челюсть" style="left: 41px;
    top: 84px;">
                <img class="image-selected image-selected-14" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d14.gif');?>" alt="#14 - Правый 1-й малый коренной. Верхняя челюсть" title="#14 - Правый 1-й малый коренной. Верхняя челюсть" style="left: 41px;
    top: 84px;">
                <img class="image-hover image-hover-13" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s13.gif');?>" alt="#13 - Правый клык. Верхняя челюсть" title="#13 - Правый клык. Верхняя челюсть" style="left: 59px;
    top: 56px;">
                <img class="image-selected image-selected-13" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d13.gif');?>" alt="#13 - Правый клык. Верхняя челюсть" title="#13 - Правый клык. Верхняя челюсть" style="left: 59px;
    top: 56px;">
                <img class="image-hover image-hover-12" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s12.gif');?>" alt="#12 - Правый резец. Верхняя челюсть" title="#12 - Правый резец. Верхняя челюсть" style="left: 85px;
    top: 40px;">
                <img class="image-selected image-selected-12" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d12.gif');?>" alt="#12 - Правый резец. Верхняя челюсть" title="#12 - Правый резец. Верхняя челюсть" style="left: 85px;
    top: 40px;">
                <img class="image-hover image-hover-11" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s11.gif');?>" alt="#11 - Правый передний резец. Верхняя челюсть" title="#11 - Правый передний резец. Верхняя челюсть" style="left: 112px;
    top: 31px;">
                <img class="image-selected image-selected-11" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d11.gif');?>" alt="#11 - Правый передний резец. Верхняя челюсть" title="#11 - Правый передний резец. Верхняя челюсть" style="left: 112px;
    top: 31px;">
                <img class="image-hover image-hover-21" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s21.gif');?>" alt="#21 - Левый передний резец. Верхняя челюсть" title="#21 - Левый передний резец. Верхняя челюсть" style="left: 149px;
    top: 31px;">
                <img class="image-selected image-selected-21" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d21.gif');?>" alt="#21 - Левый передний резец. Верхняя челюсть" title="#21 - Левый передний резец. Верхняя челюсть" style="left: 149px;
    top: 31px;">
                <img class="image-hover image-hover-22" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s22.gif');?>" alt="#22 - Левый резец. Верхняя челюсть" title="#22 - Левый резец. Верхняя челюсть" style="left: 180px;
    top: 40px;">
                <img class="image-selected image-selected-22" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d22.gif');?>" alt="#22 - Левый резец. Верхняя челюсть" title="#22 - Левый резец. Верхняя челюсть" style="left: 180px;
    top: 40px;">
                <img class="image-hover image-hover-23" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s23.gif');?>" alt="#23 - Левый клык. Верхняя челюсть" title="#23 - Левый клык. Верхняя челюсть" style="left: 199px;
    top: 56px;">
                <img class="image-selected image-selected-23" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d23.gif');?>" alt="#23 - Левый клык. Верхняя челюсть" title="#23 - Левый клык. Верхняя челюсть" style="left: 199px;
    top: 56px;">
                <img class="image-hover image-hover-24" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s24.gif');?>" alt="#24 - Левый 1-й малый коренной. Верхняя челюсть" title="#24 - Левый 1-й малый коренной. Верхняя челюсть" style="left: 211px;
    top: 84px;">
                <img class="image-selected image-selected-24" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d24.gif');?>" alt="#24 - Левый 1-й малый коренной. Верхняя челюсть" title="#24 - Левый 1-й малый коренной. Верхняя челюсть" style="left: 211px;
    top: 84px;">
                <img class="image-hover image-hover-25" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s25.gif');?>" alt="#25 - Левый 2-й малый коренной. Верхняя челюсть" title="#25 - Левый 2-й малый коренной. Верхняя челюсть" style="left: 223px;
    top: 112px;">
                <img class="image-selected image-selected-25" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d25.gif');?>" alt="#25 - Левый 2-й малый коренной. Верхняя челюсть" title="#25 - Левый 2-й малый коренной. Верхняя челюсть" style="left: 223px;
    top: 112px;">
                <img class="image-hover image-hover-26" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s26.gif');?>" alt="#26 - Левый 1-й большой коренной. Верхняя челюсть" title="#26 - Левый 1-й большой коренной. Верхняя челюсть" style="left: 227px;
    top: 139px;">
                <img class="image-selected image-selected-26" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d26.gif');?>" alt="#26 - Левый 1-й большой коренной. Верхняя челюсть" title="#26 - Левый 1-й большой коренной. Верхняя челюсть" style="left: 227px;
    top: 139px;">
                <img class="image-hover image-hover-27" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s27.gif');?>" alt="#27 - Левый 2-й большой коренной. Верхняя челюсть" title="#27 - Левый 2-й большой коренной. Верхняя челюсть" style="left: 233px;
    top: 182px;">
                <img class="image-selected image-selected-27" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d27.gif');?>" alt="#27 - Левый 2-й большой коренной. Верхняя челюсть" title="#27 - Левый 2-й большой коренной. Верхняя челюсть" style="left: 233px;
    top: 182px;">
                <img class="image-hover image-hover-28" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s28.gif');?>" alt="#28 - Левый 3-й большой коренной. Верхняя челюсть" title="#28 - Левый 3-й большой коренной. Верхняя челюсть" style="left: 238px;
    top: 221px;">
                <img class="image-selected image-selected-28" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d28.gif');?>" alt="#28 - Левый 3-й большой коренной. Верхняя челюсть" title="#28 - Левый 3-й большой коренной. Верхняя челюсть" style="left: 238px;
    top: 221px;">
                <img class="image-hover image-hover-37" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s37.gif');?>" alt="#37 - Левый 2-й большой коренной. Нижняя челюсть" title="#37 - Левый 2-й большой коренной. Нижняя челюсть" style="left: 228px;
    top: 309px;">
                <img class="image-selected image-selected-37" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d37.gif');?>" alt="#37 - Левый 2-й большой коренной. Нижняя челюсть" title="#37 - Левый 2-й большой коренной. Нижняя челюсть" style="left: 228px;
    top: 309px;">
                <img class="image-hover image-hover-38" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s38.gif');?>" alt="#38 - Левый 3-й большой коренной. Нижняя челюсть" title="#38 - Левый 3-й большой коренной. Нижняя челюсть" style="left: 236px;
    top: 267px;">
                <img class="image-selected image-selected-38" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d38.gif');?>" alt="#38 - Левый 3-й большой коренной. Нижняя челюсть" title="#38 - Левый 3-й большой коренной. Нижняя челюсть" style="left: 236px;
    top: 267px;">
                <img class="image-hover image-hover-36" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s36.gif');?>" alt="#36 - Левый 1-й большой коренной. Нижняя челюсть" title="#36 - Левый 1-й большой коренной. Нижняя челюсть" style="left: 222px;
    top: 351px;">
                <img class="image-selected image-selected-36" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d36.gif');?>" alt="#36 - Левый 1-й большой коренной. Нижняя челюсть" title="#36 - Левый 1-й большой коренной. Нижняя челюсть" style="left: 222px;
    top: 351px;">
                <img class="image-hover image-hover-35" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s35.gif');?>" alt="#35 - Левый 2-й малый коренной. Нижняя челюсть" title="#35 - Левый 2-й малый коренной. Нижняя челюсть" style="left: 211px;
    top: 394px;">
                <img class="image-selected image-selected-35" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d35.gif');?>" alt="#35 - Левый 2-й малый коренной. Нижняя челюсть" title="#35 - Левый 2-й малый коренной. Нижняя челюсть" style="left: 211px;
    top: 394px;">
                <img class="image-hover image-hover-34" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s34.gif');?>" alt="#34 - Левый 1-й малый коренной. Нижняя челюсть" title="#34 - Левый 1-й малый коренной. Нижняя челюсть" style="left: 204px;
    top: 424px;">
                <img class="image-selected image-selected-34" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d34.gif');?>" alt="#34 - Левый 1-й малый коренной. Нижняя челюсть" title="#34 - Левый 1-й малый коренной. Нижняя челюсть" style="left: 204px;
    top: 424px;">
                <img class="image-hover image-hover-33" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s33.gif');?>" alt="#33 - Левый клык. Нижняя челюсть" title="#33 - Левый клык. Нижняя челюсть" style="left: 189px;
    top: 450px;">
                <img class="image-selected image-selected-33" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d33.gif');?>" alt="#33 - Левый клык. Нижняя челюсть" title="#33 - Левый клык. Нижняя челюсть" style="left: 189px;
    top: 450px;">
                <img class="image-hover image-hover-32" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s32.gif');?>" alt="#32 - Левый резец. Нижняя челюсть" title="#32 - Левый резец. Нижняя челюсть" style="left: 173px;
    top: 463px;">
                <img class="image-selected image-selected-32" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d32.gif');?>" alt="#32 - Левый резец. Нижняя челюсть" title="#32 - Левый резец. Нижняя челюсть" style="left: 173px;
    top: 463px;">
                <img class="image-hover image-hover-31" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s31.gif');?>" alt="#31 - Левый передний резец. Нижняя челюсть" title="#31 - Левый передний резец. Нижняя челюсть" style="left: 150px;
    top: 468px;">
                <img class="image-selected image-selected-31" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d31.gif');?>" alt="#31 - Левый передний резец. Нижняя челюсть" title="#31 - Левый передний резец. Нижняя челюсть" style="left: 150px;
    top: 468px;">
                <img class="image-hover image-hover-41" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s41.gif');?>" alt="#41 - Правый передний резец. Нижняя челюсть" title="#41 - Правый передний резец. Нижняя челюсть" style="left: 123px;
    top: 468px;">
                <img class="image-selected image-selected-41" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d41.gif');?>" alt="#41 - Правый передний резец. Нижняя челюсть" title="#41 - Правый передний резец. Нижняя челюсть" style="left: 123px;
    top: 468px;">
                <img class="image-hover image-hover-42" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s42.gif');?>" alt="#42 - Правый резец. Нижняя челюсть" title="#42 - Правый резец. Нижняя челюсть" style="left: 99px;
    top: 463px;">
                <img class="image-selected image-selected-42" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d42.gif');?>" alt="#42 - Правый резец. Нижняя челюсть" title="#42 - Правый резец. Нижняя челюсть" style="left: 99px;
    top: 463px;	">
                <img class="image-hover image-hover-43" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s43.gif');?>" alt="#43 - Правый клык. Нижняя челюсть" title="#43 - Правый клык. Нижняя челюсть" style="left: 76px;
    top: 450px;">
                <img class="image-selected image-selected-43" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d43.gif');?>" alt="#43 - Правый клык. Нижняя челюсть" title="#43 - Правый клык. Нижняя челюсть" style="left: 76px;
    top: 450px;">
                <img class="image-hover image-hover-44" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s44.gif');?>" alt="#44 - Правый 1-й малый коренной. Нижняя челюсть" title="#44 - Правый 1-й малый коренной. Нижняя челюсть" style="left: 57px;
    top: 424px;">
                <img class="image-selected image-selected-44" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d44.gif');?>" alt="#44 - Правый 1-й малый коренной. Нижняя челюсть" title="#44 - Правый 1-й малый коренной. Нижняя челюсть" style="left: 57px;
    top: 424px;">
                <img class="image-hover image-hover-45" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s45.gif');?>" alt="#45 - Правый 2-й малый коренной. Нижняя челюсть" title="#45 - Правый 2-й малый коренной. Нижняя челюсть" style="left: 45px;
    top: 394px;">
                <img class="image-selected image-selected-45" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d45.gif');?>" alt="#45 - Правый 2-й малый коренной. Нижняя челюсть" title="#45 - Правый 2-й малый коренной. Нижняя челюсть" style="left: 45px;
    top: 394px;">
                <img class="image-hover image-hover-46" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s46.gif');?>" alt="#46 - Правый 1-й большой коренной. Нижняя челюсть" title="#46 - Правый 1-й большой коренной. Нижняя челюсть" style="left: 29px;
    top: 351px;">
                <img class="image-selected image-selected-46" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d46.gif');?>" alt="#46 - Правый 1-й большой коренной. Нижняя челюсть" title="#46 - Правый 1-й большой коренной. Нижняя челюсть" style="left: 29px;
    top: 351px;">
                <img class="image-hover image-hover-47" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s47.gif');?>" alt="#47 - Правый 2-й большой коренной. Нижняя челюсть" title="#47 - Правый 2-й большой коренной. Нижняя челюсть" style="left: 20px;
    top: 311px;">
                <img class="image-selected image-selected-47" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d47.gif');?>" alt="#47 - Правый 2-й большой коренной. Нижняя челюсть" title="#47 - Правый 2-й большой коренной. Нижняя челюсть" style="left: 20px;
    top: 311px;">
                <img class="image-hover image-hover-48" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/s48.gif');?>" alt="#47 - Правый 3-й большой коренной. Нижняя челюсть" title="#48 - Правый 3-й большой коренной. Нижняя челюсть" style="left: 20px;
    top: 271px;">
                <img class="image-selected image-selected-48" src="<?php echo plugins_url('/appointment-calendar-premium/menu-pages/images/d48.gif');?>" alt="#48 - Правый 3-й большой коренной. Нижняя челюсть" title="#48 - Правый 3-й большой коренной. Нижняя челюсть" style="left: 20px;
    top: 271px;">
                <input type="hidden" name="tooth-31"/>
                <input type="hidden" name="tooth-32"/>
                <input type="hidden" name="tooth-33"/>
                <input type="hidden" name="tooth-34"/>
                <input type="hidden" name="tooth-35"/>
                <input type="hidden" name="tooth-36"/>
                <input type="hidden" name="tooth-37"/>
                <input type="hidden" name="tooth-38"/>

                <input type="hidden" name="tooth-11"/>
                <input type="hidden" name="tooth-12"/>
                <input type="hidden" name="tooth-13"/>
                <input type="hidden" name="tooth-14"/>
                <input type="hidden" name="tooth-15"/>
                <input type="hidden" name="tooth-16"/>
                <input type="hidden" name="tooth-17"/>
                <input type="hidden" name="tooth-18"/>

                <input type="hidden" name="tooth-21"/>
                <input type="hidden" name="tooth-22"/>
                <input type="hidden" name="tooth-23"/>
                <input type="hidden" name="tooth-24"/>
                <input type="hidden" name="tooth-25"/>
                <input type="hidden" name="tooth-26"/>
                <input type="hidden" name="tooth-27"/>
                <input type="hidden" name="tooth-28"/>

                <input type="hidden" name="tooth-41"/>
                <input type="hidden" name="tooth-42"/>
                <input type="hidden" name="tooth-43"/>
                <input type="hidden" name="tooth-44"/>
                <input type="hidden" name="tooth-45"/>
                <input type="hidden" name="tooth-46"/>
                <input type="hidden" name="tooth-47"/>
                <input type="hidden" name="tooth-48"/>
                <input type="hidden" name="tooth-48"/>
                </div>
            </div>
            <div class="analysis_container">
                <h4><?php _e('Treatment','appointzilla'); ?></h4>
                <table width="100%" class="detail-view table table-striped table-condensed">
                    <tr>
                        <th width="10%"><strong><?php _e('Name','appointzilla'); ?></strong></th>
                        <th width="10%"><strong><?php _e('Date','appointzilla'); ?></strong></th>
                    </tr>
                <?php
                if (count($result[treatment_list])>0):
                foreach($result[treatment_list] as $singleTreatment):?>
                    <tr>
                        <td><?php echo $singleTreatment[treatment_name];?></td>
                        <td><?php echo $singleTreatment[medical_cart_treatment_date];?></td>
                    </tr>
                <?php endforeach;?>
                <?php endif;
                if (is_numeric($result[medical_cart]['medical_cart_id'])):?>
                    <tr>
                        <td><?php _e('New Treatment','appointzilla'); ?></td>
                        <td><select id="new_treatment_id">
                                <?php foreach($result[all_treatment_list] as $singleTreatment):?>
                                    <option value="<?php echo $singleTreatment[treatment_id]?>"><?php echo $singleTreatment[treatment_name];?></option>
                                <?php endforeach;?>
                            </select>
                            <a style="margin-bottom:10px;" id="new_treatment_button" class="btn" onclick="addNewTreatment(<?php echo $result[medical_cart]['medical_cart_id'];?>)" href="javascript:void(0)"><i class="icon-ok"></i><?php _e('Add','appointzilla'); ?></a></td>
                    </tr>
                <?php endif;?>
                </table>
            </div>
            <?php if ($_GET[action]=='new' && !isset($_POST[new_row_button])):?>
            <div style="clear:both;">
                <button style="margin-bottom:10px;" id="new_row_button" type="submit" class="btn btn-primary" name="new_row_button"><i class="icon-ok  icon-white"></i><?php _e('Create','appointzilla'); ?></button>
            <?php elseif ($_GET[action]=='update'):?>
                <button style="margin-bottom:10px;" id="edit_row_button" type="submit" class="btn btn-primary" name="edit_row_button"><i class="icon-ok icon-white"></i><?php _e('Update','appointzilla'); ?></button>
            <?php endif;?>
            <a style="margin-bottom:10px;" id="cancel_row" type="button" class="btn btn-primary" name="cancel_row" href="?page=medical_cart&action=view&client_id=<?php echo $result[medical_cart][medical_cart_client_id];?>"><i class="icon-remove  icon-white"></i><?php _e('Cancel','appointzilla'); ?> </a>
            </div>
    </form>

<?php
elseif($section=='client_list'):?>
    <?php if (strlen($result[message])>10):?>
        <div id="message" class="updated below-h2"><p>
                <?php echo $result[message];?>
            </p></div>
    <?php endif;?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-group"></i> <?php _e('Select Client','appointzilla'); ?></h3>
    </div>
    <br>

    <table width="100%" class="detail-view table table-striped table-condensed">
        <thead>
        <tr>
            <th width="10%"><?php _e('Name','appointzilla'); ?> </th>
            <th width="10%"><?php _e('Phone','appointzilla'); ?></th>
            <th width="10%"><?php _e('Email','appointzilla'); ?></th>
            <th width="10%"><?php _e('Occupation','appointzilla'); ?></th>
            <th width="55%"><?php _e('Note','appointzilla'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($result[client_list] as $singleClient):?>
            <tr>
                <td><a href="?page=medical_cart&action=view&client_id=<?php echo $singleClient['id'];?>"><?php echo $singleClient['name']; ?></a></td>
                <td><?php echo $singleClient['phone']; ?></td>
                <td><?php echo $singleClient['email']; ?></td>
                <td><?php echo $singleClient['occupation']; ?></td>
                <td><?php echo $singleClient['note']; ?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php elseif($section=='single_medical_cart_page'):?>
    <?php if (strlen($result[message])>10):?>
        <div id="message" class="updated below-h2"><p>
                <?php echo $result[message];?>
            </p></div>
    <?php endif;?>
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><i class="fa fa-group"></i> <?php _e('Medical Cart Client List','appointzilla'); ?></h3>
    </div>
    <br>

    <table width="100%" class="detail-view table table-striped table-condensed medical_cart_list">
    <thead>
    <tr>
        <th width="6%"><?php _e('Date','appointzilla'); ?> </th>
        <th width="6%"><?php _e('Diagnosis','appointzilla'); ?></th>
        <th width="6%"><?php _e('Tooth','appointzilla'); ?></th>
        <th width="26%"><?php _e('Note','appointzilla'); ?></th>
        <th width="50%"><?php _e('Images','appointzilla'); ?></th>
        <th width="6%"> <?php _e('Action','appointzilla'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php

    foreach($result[client_list] as $key=>$singleRow):
        $imageList = explode(',',$singleRow['medical_cart_image_ids']);
    ?>

        <td><?php echo $singleRow['medical_cart_date']; ?></td>
        <td><?php echo $singleRow['diagnosis_name']; ?></td>
        <td><?php echo $singleRow['medical_cart_tooth']; ?></td>
        <td><?php echo $singleRow['medical_cart_note']; ?></td>
        <td><div="imglist"><?php foreach($imageList as $singleImage):
        $imgStuff = wp_get_attachment_image_src( $singleImage, 'full' );
        if ($imgStuff!=''):?>
            <a rel="fancybox-<?php echo $key;?>" class="fancybox-thumbs" href="<?php echo $imgStuff['0'];?>"><?php echo wp_get_attachment_image( $singleImage, array(64,64), 1 );?></a>
        <?php
        endif;
    endforeach;?></div></td>
            <td><a data-original-title="Update" rel="tooltip" href="?page=medical_cart&medical_cart_id=<?php echo $singleRow['medical_cart_id']; ?>&action=update"><i class="icon-pencil"></i></a>
                <a data-original-title="Delete" rel="tooltip" href="?page=medical_cart&medical_cart_id=<?php echo $singleRow['medical_cart_id']; ?>&client_id=<?php echo $singleRow['medical_cart_client_id']; ?>&action=delete" onclick="return confirm('<?php _e('Do you want to delete this Question?','appointzilla');?>')"><i class="icon-remove"></i></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    </table>
    <div id="gruopbuttonbox">
        <a data-original-title="" class="btn btn-primary" href="?page=medical_cart&client_id=<?php echo $_GET['client_id']; ?>&action=new"><i class="icon-plus icon-white"></i><?php _e('Add New','appointzilla'); ?></a>
        <a data-original-title="" class="btn btn-primary" href="?page=medical_cart&client_id=<?php echo $_GET['client_id']; ?>&action=print" target="_blank"><i class="icon-print icon-white"></i><?php _e('Print','appointzilla'); ?></a>
    </div>
<?php else:?>
    bye world from medical cart;
<?php endif;?>