<div id="AppThirdModalData">
    <div class="apcal_modal modal" id="AppThirdModal" style="z-index:10000;">
        <input name="StaffId" id="StaffId" type="hidden" value="<?php if(isset($_GET['StaffId'])) { echo $_GET['StaffId']; } ?>" />
        <input name="CabinetID" id="CabinetID" type="hidden" value="<?php if(isset($_GET['CabinetID'])) { echo $_GET['CabinetID']; } ?>" />
        <input name="AppDate" id="AppDate" type="hidden" value="<?php if(isset($_GET['AppDate'])) { echo $_GET['AppDate']; } ?>" />
        <input name="StartTime" id="StartTime" type="hidden" value="<?php if(isset($_GET['StartTime'])) { echo $_GET['StartTime']; } ?>" />
        <input name="EndTime" id="EndTime" type="hidden" value="<?php if(isset($_GET['EndTime'])) { echo $_GET['EndTime']; } ?>" />
        <div class="apcal_modal-info alert alert-info">
            <a href="javascript:void(0)" onclick="CloseModelform()" style="float:right; margin-right:40px; margin-top:21px;" id="close" ><i class="icon-remove"></i></a>
            <div class="apcal_alert apcal_alert-info">
                <p><strong><?php _e('Schedule New Appointment', 'appointzilla'); ?></strong></p>
                <?php _e('Step 3. Complete Your Booking', 'appointzilla'); ?>
            </div>
        </div>
        <div class="apcal_alert apcal_alert-info" style="margin:10px">
            <?php echo __('Start Time', 'appointzilla').':'.$_GET['StartTime']; ?>
            <?php echo __('End Time', 'appointzilla').':'.$_GET['EndTime']; ?>
        </div>
        <div class="apcal_modal-body">
            <?php if($AllCalendarSettings['apcal_user_registration'] == "yes") { ?>
                <!--check user div-->
                <div id="check-user">
                    <table width="100%" class="table">
                        <tr>
                            <td colspan="3">
                                <button id="new-user" name="new-user" class="apcal_btn apcal_btn-info" onclick="return NewUserBtn();"><i class="fa fa-user"></i> <?php _e("New User", "appointzilla"); ?></button>
                                <button id="existing-user" name="existing-user" class="apcal_btn apcal_btn-info" onclick="return ExistingUserBtn();"><i class="fa fa-user"></i> <?php _e("Existing User", "appointzilla"); ?></button>
                                <button type="button" class="apcal_btn" id="back2" name="back2" onclick="LoadSecondModal2()" style="float: right;"><i class="icon-arrow-left"></i>  <?php _e('Back', 'appointzilla'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>

                <!--new user div-->
                <div id="new-user-div" style="display: none;">
                    <table width="100%" class="table">
                        <tr>
                            <th scope="row"><?php _e('First Name', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-first-name" type="text" class="client-first-name" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Last Name', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-last-name" type="text" class="client-last-name" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-phone" type="text" class="client-phone" style="height:30px;"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-email" type="text" class="client-email" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Occupation', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-occupation" type="text" class="client-occupation" style="height:30px;"  maxlength="14"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Address', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-address" type="text" class="client-address" style="height:30px;"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Special Instruction', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><textarea name="client-si" id="client-si"></textarea></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <div id="new-user-form-btn-div">
                                    <button type="button" class="apcal_btn apcal_btn-success" id="book-now" name="book-now" onclick="return CheckValidation('NewUser',this)"><i class="icon-ok icon-white"></i>  <?php _e('Book Now', 'appointzilla'); ?></button>
                                </div>
                                <div id="new-user-form-loading-img" style="display:none;"><?php _e('Scheduling appointment, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!--existing user div-->
                <div id="existing-user-div" style="display: none;">

                    <!--div for display existing user search details-->
                    <div id="check-email-div-form" style="display: none;">
                        <table width="100%" class="table">
                            <tr>
                                <th scope="row"><?php _e('Email or Name', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td><input name="check-client-email" type="text" id="check-client-email" style="height:30px;" /></td>
                                <td>
                                    <div id="existing-user-form-btn">
                                        <button type="button" class="apcal_btn apcal_btn-success" id="check-existing-user" name="check-existing-user" onclick="return CheckExistingUser();"><i class="icon-search icon-white"></i> <?php _e('Search', 'appointzilla'); ?></button>
                                    </div>
                                    <div id="existing-user-loading-img" style="display:none;"><?php _e('Searching, please wait...', 'appointzilla'); ?></div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Select Client', 'appointzilla'); ?></th>
                                <td><strong>:</strong></td>
                                <td>
                                    <select class="client-id">
                                        <?php
                                        $client_table = $wpdb->prefix . "ap_clients";
                                        $client_list = $wpdb->get_results("SELECT * FROM `$client_table` ORDER BY name ASC",ARRAY_A);
                                        foreach($client_list as $singleClient):?>
                                            <option value="<?php echo $singleClient[id]?>"><?php echo $singleClient[name].' ( '.__('Phone','appointzilla').' : '. $singleClient[phone].' ) ';?></option>
                                        <?php endforeach;?>
                                    </select>
                                <td>
                                    <div id="existing-user-form-btn">
                                        <button type="button" class="apcal_btn apcal_btn-success" id="ex-book-now" name="ex-book-now" onclick="return CheckValidation('ExUser',this,true);"><i class="icon-ok icon-white"></i> <?php _e('Book Now', 'appointzilla'); ?></button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!--div for display existing user search details-->
                    <div id="check-email-result-div" style="display: none;">

                    </div>
                </div>
            <?php } else { // end of if registration enable ?>
                <!--user registration not enable-->
                <div id="no-user-registration">
                    <table width="100%" class="table">
                        <tr>
                            <th scope="row"><?php _e('First Name', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-first-name" type="text" id="client-first-name" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Last Name', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-last-name" type="text" id="client-last-name" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-phone" type="text" id="client-phone" style="height:30px;"  maxlength="14"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="client-email" type="text" id="client-email" style="height:30px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Special Instruction', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><textarea name="client-si" id="client-si"></textarea></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <div id="new-user-form-btn-div">
                                    <button type="button" class="apcal_btn" id="back2" name="back2" onclick="LoadSecondModal2()"><i class="icon-arrow-left"></i>  <?php _e('Back', 'appointzilla'); ?></button>
                                    <button type="button" class="apcal_btn apcal_btn-success" id="book-now" name="book-now" onclick="return CheckValidation('NewUser',this)"><i class="icon-ok icon-white"></i>  <?php _e('Book Now', 'appointzilla'); ?></button>
                                </div>
                                <div id="new-user-form-loading-img" style="display:none;"><?php _e('Scheduling appointment, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url('images/loading.gif', __FILE__); ?>" /></div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
        </div><!--end modal-body-->
    </div>
</div>