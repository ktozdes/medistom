<!--validation js lib-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/jquery-ui-custom/js/jquery.ui.datepicker-ru.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/jquery-ui-custom/js/jquery-ui-1.10.4.custom.min.js', __FILE__); ?>" type="text/javascript"></script>
<link href="<?php echo plugins_url('/jquery-ui-custom/css/ui-lightness/jquery-ui-1.10.4.custom.min.css', __FILE__); ?>" type='text/css' media='all' />

<div class="bs-docs-example tooltip-demo">
    <?php global $wpdb;
    $DateFormat = get_option('apcal_date_format');
    // add new cliens and update clients
    if(isset($_GET['updateclient'])) {
        $updateclient=$_GET['updateclient'];
        $table_name = $wpdb->prefix . "ap_clients";
        $UpdateClientDetail= $wpdb->get_row("SELECT * FROM `$table_name` WHERE `id` = '$updateclient'"); ?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <h3><i class="fa fa-group"></i> <?php _e('Manage Client','appointzilla'); ?></h3>
        </div>

        <form action="" method="post" name="client-manage">  <!--clint left box -->
            <table width="100%" class="detail-view table table-striped table-condensed">
            <tr>
				<th width="15%"><?php _e('Name','appointzilla'); ?> </th>
                <td width="4%"><strong>:</strong></td>
                <td width="81%"><input type="text" name="client_name" id="client_name" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->name; ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Name.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>			</td>
			</tr>
			<tr>
				<th><?php _e('Email','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input type="text" name="client_email" id="client_email" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->email; ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Email.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a>			</td>
			</tr>
            <tr>
				<th><?php _e('Phone','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input type="text" name="client_phone" id="client_phone" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->phone; ?>"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Phone Number.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
			</tr>
            <tr>
				<th><?php _e('Address','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input type="text" name="client_address" id="client_address" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->address; ?>"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Address.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
			</tr>
            <tr>
				<th><?php _e('Occupation','appointzilla'); ?></th>
                <td><strong>:</strong></td>
                <td><input type="text" name="client_occupation" id="client_occupation" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->occupation; ?>"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Phone Number.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
			</tr>
			<tr>
                <th><?php _e('Special Note','appointzilla'); ?> </th>
                <td><strong>:</strong></td>
                <td><textarea type="text" name="client_desc" id="client_desc"><?php if($UpdateClientDetail) echo $UpdateClientDetail->note; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Client Note.','appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
			</tr>
            <tr>
				<td></td><td></td>
				<td>
                <?php if($updateclient=='new')
                    { ?>
                <button name="clientcreate" class="btn" type="submit" id="clientcreate"><i class="icon-ok"></i> <?php _e('Create','appointzilla'); ?> </button>
                <?php } else { ?>
                <button name="clientupdate" class="btn" type="submit" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->id; ?>" id="clientupdate"><i class="icon-pencil"></i> <?php _e('Update','appointzilla'); ?></button>
                <?php } ?>
                <a href="?page=client" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla'); ?></a>
				</td>
			</tr>
          </table>
		  <table width="100%" class="detail-view table table-striped table-condensed">
		  <tr>
			<td colspan="2"><h3><i class="fa fa-group"></i> <?php _e('Manage Questions','appointzilla'); ?></h3></td>
			</tr>
		  <?php
		if ($updateclient=='new'){
			$table_question 	 = $wpdb->prefix . "ap_clients_questions";
			$groupList = $wpdb->get_results("
			SELECT `id` as question_id, `question`, `type`, `group` FROM $table_question
			order by `group`",ARRAY_A);
		}
		  //update user
		else{
			$table_question 	 = $wpdb->prefix . "ap_clients_questions";
			$table_user 		 = $wpdb->prefix . "ap_clients";
			$table_question_user = $wpdb->prefix . "ap_clients_questions_relationship";
			$groupList = $wpdb->get_results("
			SELECT $table_question.question, $table_question.type, $table_question.group,question_id, $table_question_user.value FROM $table_user
				LEFT JOIN $table_question_user on $table_question_user.client_id = $table_user.id
				LEFT JOIN $table_question on $table_question_user.question_id = $table_question.id
			WHERE
				$table_user.id = $updateclient
			order by `group`",ARRAY_A);
			if (count($groupList)<2){
				$groupList = $wpdb->get_results("SELECT `id` as question_id, `question`, `type`, `group` FROM $table_question order by `group`",ARRAY_A);
				foreach($groupList as $key=>$singleQuestion){
					$table_question_user = $wpdb->prefix . "ap_clients_questions_relationship";
					$wpdb->insert( 
						$table_question_user, 
						array(
							'client_id' => $updateclient, 
							'question_id' => $singleQuestion[question_id]
						)
					);
				}
			}
			$groupList = $wpdb->get_results("
			SELECT question, type, $table_question.id 'question_id', client_id, value, `group` FROM $table_question
            LEFT JOIN $table_question_user on $table_question_user.question_id = $table_question.id
            AND client_id = $updateclient ORDER BY `group`
			",ARRAY_A);
		}
        //echo $wpdb->last_query;
		$currentGroup = '';
		foreach($groupList as $singleQuestion):?>
		<?php if($currentGroup!=$singleQuestion['group']):?>
			<tr>
				<td colspan="2" style="background:#8291FF;"><h4><?php echo $singleQuestion['group'];?></h4></td>
			</tr>
		<?php endif;?>
			<tr>
				<th width="15%"><?php echo $singleQuestion['question'];?></th>
				<td><?php if ($singleQuestion['type']=='date'):?>
					<input type="text" class="datepicker date" name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>" />
				<?php elseif($singleQuestion['type']=='text'):?>
					<input type="text" name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>" class="numeric"/>
				<?php elseif($singleQuestion['type']=='bit'):?>
					<input type="checkbox" name="question_id[<?php echo $singleQuestion['question_id'];?>]" <?php echo $singleQuestion['value']=='on'?'checked="checked"':'';?>/>
				<?php elseif($singleQuestion['type']=='numeric'):?>
				<input type="text" name="question_id[<?php echo $singleQuestion['question_id'];?>]" value="<?php echo $singleQuestion['value'];?>"/>
				<?php endif;?>
				</td>
			</tr>
			<?php 
		$currentGroup = $singleQuestion['group'];
		endforeach;?>		  
			<tr>
				<td></td>
				<td>
			<?php if($updateclient=='new')
            { ?>
                <button name="clientcreate" class="btn" type="submit" id="clientcreate"><i class="icon-ok"></i> <?php _e('Create','appointzilla'); ?> </button>
            <?php } else { ?>
                <button name="clientupdate" class="btn" type="submit" value="<?php if($UpdateClientDetail) echo $UpdateClientDetail->id; ?>" id="clientupdate"><i class="icon-pencil"></i> <?php _e('Update','appointzilla'); ?></button>
            <?php } ?>
                <a href="?page=client" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla'); ?></a>
				</td>
			</tr>
		</table>
		</form><?php
     } // end of update if


    // view of clients details
    if(isset($_GET['viewid'])) {
        $clientid = $_GET['viewid'];
        $table_name = $wpdb->prefix . "ap_clients";
        $ClientDetails = $wpdb->get_row("SELECT * FROM $table_name WHERE `id` ='$clientid'"); ?>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><i class="fa fa-group"></i> <?php _e('View Client','appointzilla');?> - <?php echo $ClientDetails->name; ?></h3></div>
            <div style="float:left; width:28%; height:auto; border:0px solid #000000;" id="left_client_box">
                <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"> <h3> <?php _e('Client Details','appointzilla'); ?>  </h3> </div>
                <table width="100%" class="detail-view table table-striped table-condensed">
                    <tr>
                        <th width="25%"><?php _e('Name','appointzilla'); ?> </th>
                        <td width="5%"><strong>:</strong></td>
                        <td width="70%"><?php echo ucwords($ClientDetails->name); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Email','appointzilla'); ?> </th><td><strong>:</strong></td>
                        <td><?php echo strtolower($ClientDetails->email); ?></td>
					</tr>
                    <tr>
						<th><?php _e('Phone','appointzilla'); ?></th><td><strong>:</strong></td>
                        <td><?php echo $ClientDetails->phone; ?></td>
                    </tr>
                    <tr>
						<th><?php _e('Address','appointzilla'); ?></th><td><strong>:</strong></td>
                        <td><?php echo $ClientDetails->address; ?></td>
                    </tr>
                    <tr>
						<th><?php _e('Occupation','appointzilla'); ?></th><td><strong>:</strong></td>
                        <td><?php echo $ClientDetails->occupation; ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Special Note','appointzilla'); ?></th>
                        <td><strong>:</strong></td>
                        <td><?php echo ucfirst($ClientDetails->note); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><a class="btn" href="?page=client"><i class="icon-arrow-left"></i> <?php _e('Back','appointzilla'); ?></a></td>
                    </tr>
                </table>
            </div>

            <div style="float:right; width:70%; height:auto; border:0px solid #000000;" id="right_client_box">
                <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"> <h3>
                   <?php if(isset($_GET['appviewid']))  _e('Client Detailed Appointment History','appointzilla'); else  _e('All Appointment History','appointzilla'); ?> </h3>
                </div>
                 <?php
                 //View appointment details
                 if(isset($_GET['appviewid'])) {
                     $appid = $_GET['appviewid'];
                     if(isset($_GET['updateclient'])) 
						$fromback =$_GET['updateclient'];
                     $table_name = $wpdb->prefix . "ap_appointments";
                     $appdetails = "SELECT * FROM $table_name WHERE `id` ='$appid'";
                     $appdetails = $wpdb->get_row($appdetails); ?>
                    <table width="100%" class="detail-view table table-striped table-condensed">
                        <tr>
                             <th scope="row"><?php _e('Appointment Creation Date', 'appointzilla'); ?></th>
                             <td><strong>:</strong></td>
                             <td><?php echo date($DateFormat." h:i:s", strtotime("$appdetails->book_date")); ?></td>
                        </tr>
                        <tr>
                            <th width="28%" scope="row"><?php _e('Name', 'appointzilla'); ?></th>
                            <td width="4%"><strong>:</strong></td>
                            <td width="68%"><em><?php echo ucwords($appdetails->name); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo $appdetails->email; ?></em></td>
                        </tr>
                        <tr><th scope="row"><?php _e('Service', 'appointzilla'); ?></th><td><strong>:</strong></td>
                            <td>
                                <em>
                                <?php $table_name = $wpdb->prefix . "ap_services";
                                $ServiceDetails = $wpdb->get_row("SELECT * FROM $table_name WHERE `id` ='$appdetails->service_id'");
                                echo ucwords($ServiceDetails->name);
                                ?>
                                </em>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Staff', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td>
                                <em>
                                <?php $staff_table_name = $wpdb->prefix . "ap_staff";
                                $staffdetails= $wpdb->get_row("SELECT * FROM $staff_table_name WHERE `id` ='$appdetails->staff_id'");
                                echo ucwords($staffdetails->name);
                                ?>
                                </em>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Phone', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo $appdetails->phone; ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Start Time', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo $appdetails->start_time; ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('End Time', 'appointzilla'); ?> </th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo $appdetails->end_time; ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Date', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo date($DateFormat, strtotime($appdetails->date)); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Description', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo ucfirst($appdetails->note); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Appointment Key', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo $appdetails->appointment_key; ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Status', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo _e(ucfirst($appdetails->status), 'appointzilla');?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Repeat', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo _e(ucfirst($appdetails->recurring), 'appointzilla'); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Repeat Type', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo _e(ucfirst($appdetails->recurring_type), 'appointzilla'); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Repeat Start Date', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo date($DateFormat, strtotime($appdetails->recurring_st_date)); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Repeat End Date', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo date($DateFormat, strtotime($appdetails->recurring_ed_date)); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Appointment By', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo _e(ucfirst($appdetails->appointment_by), 'appointzilla'); ?></em></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Payment Status', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><em><?php echo _e(ucfirst($appdetails->payment_status), 'appointzilla');?></em></td>
                        </tr>
                        <tr>
                            <th scope="row">&nbsp;</th><td>&nbsp;</td>
                            <td><a href="?page=client-manage&viewid=<?php echo $_GET['viewid']; ?>" class="btn"><i class="icon-arrow-left"></i> <?php _e('Back', 'appointzilla'); ?></a></td>
                        </tr>
                </table> <!--end of view appointment -->
                <?php
                } else { ?>
                    <table width="100%" class="detail-view table table-striped table-condensed">
                        <tr>
                            <th width="10%"><?php _e('No.','appointzilla'); ?> </th>
                            <th width="13%"><?php _e('Name','appointzilla'); ?> </th>
                            <th width="15%"><?php _e('Staff','appointzilla'); ?> </th>
                            <th width="19%"><?php _e('Date','appointzilla'); ?> </th>
                            <th width="21%"><?php _e('Time','appointzilla'); ?> </th>
                            <th width="15%"><?php _e('Service','appointzilla'); ?> </th>
                            <th width="7%"><?php _e('Action','appointzilla'); ?> </th>
                        </tr>
                        <?php $findapp = $ClientDetails->email;
                          $appointment_table_name= $wpdb->prefix . "ap_appointments";
                          $toat_app_query = "SELECT * FROM `$appointment_table_name` WHERE `email` = '$findapp';";
                          $AllAppointments = $wpdb->get_results($toat_app_query);
                        if($AllAppointments) {
                            $i=1;
                            foreach($AllAppointments as $appointment) { ?>
                                <tr>
                                    <td>
                                        <em>
                                            <?php echo $i."."; ?></em></td>	<td><em><?php echo ucfirst($appointment->name); ?>
                                        </em>
                                    </td>
                                    <td>
                                        <em>
                                        <?php $staffid = $appointment->staff_id;
                                            $staff_table_name = $wpdb->prefix . "ap_staff";
                                            $staff_details= $wpdb->get_row("SELECT * FROM $staff_table_name WHERE `id` ='$staffid'");
                                            echo ucfirst($staff_details->name);
                                        ?>
                                        </em>
                                    </td>
                                    <td>
                                        <em>
                                            <?php
                                            if($appointment->recurring == 'yes') {
                                                echo date($DateFormat, strtotime($appointment->recurring_st_date))."-".date($DateFormat, strtotime($appointment->recurring_ed_date));
                                            } else {
                                                echo date($DateFormat, strtotime($appointment->date));
                                            }?>
                                        </em>
                                    </td>

                                    <td>
                                        <em><?php echo date("g:ia", strtotime($appointment->start_time))."-".date("g:ia", strtotime($appointment->end_time)); ?></em>
                                    </td>
                                    <td>
                                        <em>
                                            <?php $apppid=$appointment->service_id;
                                                $table_name = $wpdb->prefix . "ap_services";
                                                $ServiceDetails = $wpdb->get_row("SELECT * FROM $table_name WHERE `id` ='$apppid'");
                                                echo ucfirst($ServiceDetails->name); ?>
                                        </em>
                                    </td>
                                    <td>
                                        <a title="<?php _e('View','appointzilla'); ?>" rel="tooltip" href="?page=client-manage&appviewid=<?php echo $appointment->id; ?>&viewid=<?php echo $clientid; ?>"><i class="icon-eye-open"></i></a>
                                    </td>
                                </tr>
                                <?php $i++;
                            } //end of foreach appointment
                        } else { ?>
                                <tr>
                                    <td colspan="7"><?php _e('Sorry! No appointment(s) available for this client.','appointzilla'); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7"></td>
                                </tr><?php
                        } ?><!--end of appointment if -->
                    </table><?php
                } ?>
            </div>
			
			<div style="float:left; width:98%; height:auto; border:0px solid #000000;" id="left_client_box">
                <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"> <h3> <?php _e('Client Questions','appointzilla'); ?> </h3></div>
                <table width="100%" class="detail-view table table-striped table-condensed">
                    <?php
					$table_question 	 = $wpdb->prefix . "ap_clients_questions";
					$table_user 		 = $wpdb->prefix . "ap_clients";
					$table_question_user = $wpdb->prefix . "ap_clients_questions_relationship";
					$groupList = $wpdb->get_results("
					SELECT $table_question.question, $table_question.type, $table_question.group,question_id, $table_question_user.value FROM $table_user
						LEFT JOIN $table_question_user on $table_question_user.client_id = $table_user.id
						LEFT JOIN $table_question on $table_question_user.question_id = $table_question.id
					WHERE
						$table_user.id = $clientid
					order by `group`",ARRAY_A);
					$currentGroup = '';
					foreach($groupList as $singleQuestion):?>
					<?php if($currentGroup!=$singleQuestion['group']):?>
					<tr>
						<td colspan="2" style="background:#8291FF;"><h4><?php echo $singleQuestion['group'];?></h4></td>
					</tr>
					<?php endif;?>
					<tr>
						<th width="15%"><?php echo $singleQuestion['question'];?></th>
						<td><?php 
							if ($singleQuestion['type']!='bit')
								echo $singleQuestion['value'];
							else
								$singleQuestion['value']=='on'?_e('Yes','appointzilla'):_e('No','appointzilla');
							?>
						</td>
					</tr>
					<?php 
					$currentGroup = $singleQuestion['group'];
					endforeach;?>
                </table>
            </div>
			<?php
    }
	if(isset($_GET['quesionary'])) {
        $updatequestion=$_GET['quesionary'];
		$table_name = $wpdb->prefix . "ap_clients_questions";
		
		$groupList = $wpdb->get_results("SELECT * FROM `$table_name` where `group` not like '' group by `group`",ARRAY_A);
		
		if (is_numeric($updatequestion) && $_GET['action']=='delete'){
			$delete_app_query="DELETE FROM `$table_name` WHERE `id` = '$updatequestion';";
			if($wpdb->query($delete_app_query)) {
				echo "<script>alert('".__('Question successfully deleted.','appointzilla')."');</script>";
			}
		}
		else if (is_numeric($updatequestion) && $_GET['action']=='update'){
			$UpdateQuestionDetail = $wpdb->get_row("SELECT * FROM `$table_name` WHERE `id` = '$updatequestion'",ARRAY_A);
		}
		
		?>
		<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <h3><i class="fa fa-group"></i> <?php _e('New Question','appointzilla'); ?></h3>
        </div>
		<form action="" method="post" name="client-manage"><!--clint left box -->
            <table width="100%" class="detail-view table table-striped table-condensed">
                <tr>
					<th width="15%"><?php _e('Question','appointzilla'); ?> </th>
					<th width="4%"><?php _e('Type','appointzilla'); ?></th>
					<th width="4%"><?php _e('Group','appointzilla'); ?></th>
				</tr>
				<tr><td><input type="text" name="question" value="<?php echo $UpdateQuestionDetail['question'];?>"/></td>
					<td><select name="type">
						<option value="text" <?php echo ($UpdateQuestionDetail['type']=='text')?'selected="selected"':'';?>><?php _e('Text','appointzilla');?></option>
						<option value="int" <?php echo ($UpdateQuestionDetail['type']=='int')?'selected="selected"':'';?>><?php _e('Numeric','appointzilla');?></option>
						<option value="date" <?php echo ($UpdateQuestionDetail['type']=='date')?'selected="selected"':'';?>><?php _e('Date','appointzilla');?></option>
						<option value="bit" <?php echo ($UpdateQuestionDetail['type']=='bit')?'selected="selected"':'';?>><?php _e('Yes/No','appointzilla');?></option>
					</select></td>
					<td><select name="group" class="question_group">
						<option <?php echo ($UpdateQuestionDetail['type']=='' || !isset($UpdateQuestionDetail['type']))?'selected="selected"':'';?> value=""><?php _e('Select One','appointzilla');?></option>
						<?php foreach($groupList as $key=>$singleQuestionGroup):?>
						<option value="<?php echo $singleQuestionGroup['group'];?>" <?php echo ($UpdateQuestionDetail['type']==$singleQuestionGroup['group'])?'selected="selected"':'';?>><?php echo $singleQuestionGroup['group'];?></option>
						<?php endforeach;?>
						<option value="new"><?php _e('New','appointzilla');?></option>
					</select></td>
				</tr>
				<tr>
					<td colspan="2">
						<?php if(is_numeric($updatequestion) && $_GET['action']=='update'):?>
						<button name="questionupdate" class="btn" type="submit" value="<?php echo $updatequestion;?>" id="questionupdate"><i class="icon-pencil"></i> <?php _e('Update','appointzilla'); ?></button>
						<?php else:?>
						<button name="questioncreate" class="btn" type="submit" id="questioncreate" value="1"><i class="icon-ok"></i> <?php _e('Create','appointzilla'); ?> </button>
						<?php endif;?>
						<a href="?page=client" class="btn"><i class="icon-remove"></i> <?php _e('Cancel','appointzilla'); ?></a>
					</td>
				</tr>
			</table>
        </form>
        <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <h3><i class="fa fa-group"></i> <?php _e('List of Questions','appointzilla'); ?></h3>
        </div>
		<?php $questionList= $wpdb->get_results("SELECT * FROM `$table_name` order by `group`",ARRAY_A);?>
		<table width="100%" class="detail-view table table-striped table-condensed">
			<tr>
				<th width="15%"><?php _e('Question','appointzilla'); ?> </th>
				<th width="4%"><?php _e('Question Type','appointzilla'); ?></th>
				<th width="4%"><?php _e('Question Group','appointzilla'); ?></th>
				<th width="4%"><?php _e('Action','appointzilla'); ?></th>
			</tr>
			<?php foreach($questionList as $key=>$singleQuestion):?>
			<tr>
				<td><?php echo $singleQuestion['question']; ?></td>
				<td><?php echo $singleQuestion['type']; ?></td>
				<td><?php echo $singleQuestion['group']; ?></td>
				<td><a data-original-title="Update" rel="tooltip" href="?page=client-manage&quesionary=<?php echo $singleQuestion['id']; ?>&action=update"><i class="icon-pencil"></i></a><a data-original-title="Delete" rel="tooltip" href="?page=client-manage&quesionary=<?php echo $singleQuestion['id']; ?>&action=delete" onclick="return confirm('Do you want to delete this Question?')"><i class="icon-remove"></i></a>
								
				</td>
			</tr>
			<?php endforeach;?>
		</table>
		<?php
     }

    // Add new client
    if(isset($_POST['clientcreate'])) {
		global $wpdb;

        $client_table = $wpdb->prefix."ap_clients";
		
        $ExitsClientDetails = $wpdb->get_row("SELECT * FROM `$client_table` WHERE `email` = '$client_email' ");
        if($ExitsClientDetails) {
            echo "<script>alert('$client_email ".__('is already in the database.','appointzilla')."')</script>";
        } else {
            // insert new client deatils
            $wpdb->insert( 
				$client_table, 
				array(
					'name' => strip_tags($_POST['client_name']), 
					'email' => $_POST['client_email'], 
					'phone' => $_POST['client_phone'], 
					'address' => $_POST['client_address'], 
					'occupation' => $_POST['client_occupation'], 
					'note' => strip_tags($_POST['client_desc'])
				)
			);
			
			$new_user_id = $wpdb->insert_id;
			if($new_user_id!==false) {
				foreach($_POST['question_id'] as $key=>$value){
					$table_question 	 = $wpdb->prefix . "ap_clients_questions";
					$table_user 		 = $wpdb->prefix . "ap_clients";
					$table_question_user = $wpdb->prefix . "ap_clients_questions_relationship";
					$wpdb->insert( 
						$table_question_user, 
						array(
							'client_id' => $new_user_id, 
							'question_id' => $key, 
							'value' => $value
						)
					);
				}
                //$clientmessage = "New Client $client_name:($client_email) successfully added.";
                echo "<script>alert('".__('New client successfully added.','appointzilla')."')</script>";
                echo "<script>location.href='?page=client';</script>";
            }
        }
    }


    //update client
    if(isset($_POST['clientupdate'])) {
        global $wpdb;
        $client_up_id = $_POST['clientupdate'];
        $table_client = $wpdb->prefix . "ap_clients";
		$result = $wpdb->update( 
			$table_client, 
			array(
				'name' => strip_tags($_POST['client_name']),
				'email' => $_POST['client_email'],
				'phone' => $_POST['client_phone'],
				'address' => $_POST['address'],
				'occupation' => $_POST['occupation'],
				'note' => strip_tags($_POST['client_desc'])
			),
			array(
				'id' => $client_up_id
			)
		);
		//echo '<br/>'.$wpdb->last_query;
		if($result!==false) {
            print_r($_POST['question_id']);
			foreach($_POST['question_id'] as $key=>$value){
				$table_question_user = $wpdb->prefix . "ap_clients_questions_relationship";
                $wpdb->get_row("SELECT * FROM $table_question_user WHERE client_id= $client_up_id and question_id =$key ");
                if ($wpdb->num_rows>0){
                    $wpdb->update(
                        $table_question_user,
                        array(
                            'value' => $value
                        ),
                        array(
                            'client_id' => $client_up_id,
                            'question_id' => $key,
                        )
                    );
                }
                else{
                    $wpdb->insert(
                        $table_question_user,
                        array(
                            'client_id' => $client_up_id,
                            'question_id' => $key,
                            'value' => $value
                        )
                    );
                }
				
			}
			//echo "<script>alert('".__('Client details successfully updated.','appointzilla')."');</script>";
			//echo "<script>location.href='?page=client-manage&viewid=$client_up_id';</script>";
		} else {
			//echo "<script>alert('".__('Client details was not updated.','appointzilla')."');</script>";
			//echo "<script>location.href='?page=client-manage&viewid=$client_up_id';</script>";
		}
    } 
	// Add new question
    if(isset($_POST['questioncreate']) || isset($_POST['questionupdate'])) {
        global $wpdb;
		echo '1';
        $question = strip_tags($_POST['question']);
        $group = strip_tags($_POST['group']);
        $type = $_POST['type'];
		
        $question_table = $wpdb->prefix."ap_clients_questions";
        if (is_numeric($_POST['questionupdate']) && isset($_POST['questionupdate'])){
			$ExitsQuestion = $wpdb->get_row("UPDATE `$question_table` set `group` = '$group', `type` = '$type', `question` = '$question' WHERE `id` = '".$_POST['questionupdate']."'");
			echo "<script>alert('".__('question successfully updated.','appointzilla')."')</script>";
			echo "<script>location.href='?page=client-manage&quesionary=edit';</script>";
		}
		else{
			$ExitsQuestion = $wpdb->get_row("SELECT * FROM `$question_table` WHERE `question` = '$question' AND `group`='$group' ");
			if($ExitsQuestion) {
				echo "<script>alert('$question ".__('is already in the database.','appointzilla')."')</script>";
			} else {
				// insert new client deatils
				$insert_client = "INSERT INTO `$question_table` (`question` ,`type`, `group`) VALUES ('$question', '$type', '$group');";
				if($wpdb->query($insert_client)) {
					echo "<script>alert('".__('New question successfully added.','appointzilla')."')</script>";
					echo "<script>location.href='?page=client-manage&quesionary=edit';</script>";
				}
			}
		}
    }
	?>
    <script type="text/javascript">
    jQuery(document).ready(function () {
		$.datepicker.setDefaults( {dateFormat:"dd/mm/yy"} );
        $( ".datepicker" ).datepicker({ changeYear: true,changeMonth: true,maxDate: "-1y",yearRange: '1930:2013'},$.datepicker.regional[ "ru" ]);
		// form submit validation js
        jQuery('select[name=group]').change(function(){
			if ($(this).val()=='new'){
				$(this).after('<input type="text" class="question_group" name="group"/>');
				$(this).remove();
			}
		});
		jQuery('form').submit(function() {
			if ($('#questioncreate').length<=0){
				jQuery('.error').hide();
				var client_name = jQuery("input#client_name").val();
				if (client_name== "") {
					jQuery("#client_name").after('<span class="error">&nbsp;<br><strong><?php _e('Client name cannot be blank.','appointzilla'); ?></strong></span>');
					return false;
				} else {
					var client_name = isNaN(client_name);
					if(client_name== false) {
						jQuery("#client_name").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid value.','appointzilla'); ?></strong></span>');
						return false;
					}
				}

				var client_email = jQuery("input#client_email").val();
				var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if (client_email== "") {
					jQuery("#client_email").after('<span class="error">&nbsp;<br><strong> <?php _e('Email cannot be blank.','appointzilla'); ?></strong></span>');
					return false;
				} else {
					if(regex.test(client_email) == false ) {
						jQuery("#client_email").after('<span class="error">&nbsp;<br><strong><?php _e('invalid  value.','appointzilla'); ?></strong></span>');
						return false;
					}
				}

				var client_phone = jQuery("input#client_phone").val();
				if (client_phone== "") {
					jQuery("#client_phone").after('<span class="error">&nbsp;<br><strong> <?php _e('Phone Number cannot be blank.','appointzilla'); ?></strong></span>');
					return false;
				}
			}
			else if ($('.question_group').val()==''){
				jQuery(".question_group").after('<span class="error">&nbsp;<br><strong> <?php _e('Group cannot be blank.','appointzilla'); ?></strong></span>');
				return false;
			}
        });
    });
    </script>
</div>