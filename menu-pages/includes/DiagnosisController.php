<?php class DiagnosisController{
    function __construct()
    {

    }

    public function getDiagnosisListTable()
    {
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
        $diagnosisList = $wpdb->get_results("SELECT * FROM $diagnosis_table",ARRAY_A);
        ?>
        <table width="100%" class="table table-hover">
        <thead>
            <tr style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
            <th><?php _e('No.','appointzilla'); ?></th>
            <th><?php _e('Name','appointzilla'); ?></th>
            <th><?php _e('Note','appointzilla'); ?></th>
            <th><?php _e('Action','appointzilla'); ?> </th>
        </tr>
        </thead>
        <?php
        foreach($diagnosisList as $singleDiagnosis) : ?>
        <tr>
            <td><em><?php echo $singleDiagnosis[diagnosis_id]; ?></em></td>
            <td><em><?php echo $singleDiagnosis[diagnosis_name]; ?></em></td>
            <td><em><?php echo $singleDiagnosis[diagnosis_title]; ?></em></td>
            <td><em><a rel="tooltip" href="?page=diagnosis&action=view&diagnosis_id=<?php echo $singleDiagnosis[diagnosis_id]; ?>" title="<?php _e('View','appointzilla'); ?>"><i class="icon-eye-open"></i></a>&nbsp;
                    <a rel="tooltip" href="?page=diagnosis&action=update&diagnosis_id=<?php echo $singleDiagnosis[diagnosis_id]; ?>" title="<?php _e('Update','appointzilla'); ?>"><i class="icon-pencil"></i></a>&nbsp;
                    <a rel="tooltip" href="?page=diagnosis&action=delete&diagnosis_id=<?php echo $singleDiagnosis[diagnosis_id]; ?>" onclick="return confirm('<?php _e('Do you want to delete this diagnosis','appointzilla'); ?>')" title="<?php _e('Delete','appointzilla'); ?>"><i class="icon-remove"></i></a></em></td>
        </tr>
    <?php endforeach;?>
        </table>
        <a href="?page=diagnosis&action=new" class="btn btn-primary"><i class="icon-ok"></i> <?php _e('New Diagnosis','appointzilla');?></a>
        <?php
    }

    public function getNewDiagnosisTable()
    {?>
        <div id="new_diagnosis" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" class="table table-hover">
                    <tbody>
                    <tr>
                        <td><?php _e('Diagnosis Name','appointzilla');?> : <input type="text" id="diagnosis_name" name="diagnosis_name" class="inputheight" /> <input type="hidden" name="new_diagnosis" value="1" /></td>
                        <td><?php _e('Diagnosis Note','appointzilla');?> : <textarea type="text" id="diagnosis_note" name="diagnosis_note" ></textarea></td>
                    </tr>
                    </tbody>
                </table>
                <?php
                global $wpdb;
                $service_table              = $wpdb->prefix . "ap_services";
                $service_category_table       = $wpdb->prefix . "ap_service_category";
                $service_category = $wpdb->get_results("select * from `$service_category_table` order by name asc");
                if (count($service_category)>0){?>
                <ul>
                    <?php foreach($service_category as $gruopname) { ?>
                        <li><a href="#tab_cat_id-<?php echo $gruopname->id; ?>"><?php echo $gruopname->name; ?></a></li>
                    <?php }?>
                </ul>
                    <?php foreach($service_category as $gruopname) { ?>
                    <div id="tab_cat_id-<?php echo $gruopname->id; ?>">
                        <?php $this->getSelectableServiceTab(array('serviceCategoryID'=>$gruopname->id));?>
                    </div>
                    <?php }?>
                <?php }?>
                <button id="create_diagnosis_button" type="button" class="btn" name="create_diagnosis_button"><i class="icon-ok"></i> <?php _e('Create','appointzilla');?></button>
                <a href="?page=diagnosis" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }



    public function getEditableDiagnosisTable($params)
    {
        global $wpdb;
        $diagnosis_table                = $wpdb->prefix . "ap_diagnosis";
        $service_category_table         = $wpdb->prefix . "ap_service_category";

        $service_category = $wpdb->get_results("select * from `$service_category_table` order by name asc");
        $diagnosisData    = $wpdb->get_row("select * from `$diagnosis_table` WHERE diagnosis_id = $params[diagnosis_id]",ARRAY_A);
        ?>
        <div id="update_diagnosis" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" class="table table-hover">
                    <tbody>
                    <tr>
                        <td><?php _e('Diagnosis Name','appointzilla');?> : <input type="text" id="diagnosis_name" name="diagnosis_name" class="inputheight" value="<?php echo $diagnosisData['diagnosis_name'];?>"/>
                            <input type="hidden" name="update_diagnosis" value="1" />
                            <input type="hidden" name="diagnosis_id" value="<?php echo $params[diagnosis_id];?>" /></td>
                        <td><?php _e('Diagnosis Note','appointzilla');?> : <textarea type="text" id="diagnosis_note" name="diagnosis_note" ><?php echo $diagnosisData['diagnosis_note'];?></textarea></td>
                    </tr>
                    </tbody>
                </table>
                <?php

                if (count($service_category)>0){?>
                    <ul>
                        <?php foreach($service_category as $gruopname) { ?>
                            <li><a href="#tab_cat_id-<?php echo $gruopname->id; ?>"><?php echo $gruopname->name; ?></a></li>
                        <?php }?>
                    </ul>
                    <?php foreach($service_category as $gruopname) { ?>
                        <div id="tab_cat_id-<?php echo $gruopname->id; ?>">
                            <?php $this->getSelectableServiceTab(array('serviceCategoryID'=>$gruopname->id,diagnosis_id=>$params[diagnosis_id]));?>
                        </div>
                    <?php }?>
                <?php }?>
                <button id="create_diagnosis_button" type="button" class="btn" name="create_diagnosis_button"><i class="icon-ok"></i> <?php _e('Update','appointzilla');?></button>
                <a href="?page=diagnosis" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }

    public function viewDiagnosisTable($params)
    {
        global $wpdb;
        $diagnosis_table            = $wpdb->prefix . "ap_diagnosis";
        $service_table              = $wpdb->prefix . "ap_services";
        $diagnosis_service_table    = $wpdb->prefix . "ap_diagnosis_service";
        $diagnosisList = $wpdb->get_results("SELECT * FROM $diagnosis_table
            INNER JOIN $diagnosis_service_table on $diagnosis_table.diagnosis_id = $diagnosis_service_table.diagnosis_id
            INNER JOIN $service_table on $service_table.id = $diagnosis_service_table.service_id
            WHERE $diagnosis_table.diagnosis_id = $params[diagnosis_id]",ARRAY_A);
        if (count($diagnosisList)>0){?>
            <table width="100%" class="detail-view table table-striped table-condensed">
            <tr>
                <td><strong><?php _e('Name','appointzilla'); ?></strong></td>
                <td>:</td>
                <td><?php echo $diagnosisList[0][diagnosis_name];?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Note','appointzilla'); ?></strong></td>
                <td>:</td>
                <td><?php echo $diagnosisList[0][diagnosis_note];?></td>
            </tr>
            </table>
            <h3><i class="fa fa fa-wrench"></i> <?php _e('Services','appointzilla');?></h3>
            <table width="100%" class="detail-view table table-striped table-condensed">
            <thead>
            <tr style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
                <th><?php _e('Code','appointzilla'); ?></th>
                <th><?php _e('Name','appointzilla'); ?></th>
            </tr>
            </thead>
        <?php
            foreach($diagnosisList as $key=>$singleService){?>
                <tr>
                    <td><?php echo $singleService[code]; ?></td>
                    <td><?php echo $singleService[name]; ?></td>
                </tr>
            <?php
            }?>
            </table>
            <a class="btn" href="?page=diagnosis&action=update&diagnosis_id=<?php echo $params[diagnosis_id];?>"><i class="icon-pencil"></i> <?php _e('Update','appointzilla');?></a>
            <a class="btn" href="?page=diagnosis"><i class="icon-arrow-left"></i> <?php _e('Back','appointzilla');?></a>
        <?php
        }
    }

    function getSelectableServiceTab($params)
    {
        global $wpdb;
        $service_table              = $wpdb->prefix . "ap_services";
        $selectedServices = array();
        if (isset($params[diagnosis_id])){
            $diagnosis_service_table    = $wpdb->prefix . "ap_diagnosis_service";
            $diagnosisServiceList = $wpdb->get_results("SELECT service_id FROM $diagnosis_service_table WHERE diagnosis_id=$params[diagnosis_id]",ARRAY_A);

            foreach($diagnosisServiceList as $key=>$singleService){
                $selectedServices[] = $singleService[service_id];
            }
        }
        ?>
        <table class="table table-hover">
            <thead>
            <tr class="filter_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>">
                <td><input type="text" class="filter_code" style="max-width:100px;"/></td>
                <td><input type="text" class="filter_name" style="min-width:150px;"/></td>
                <td><select class="filter_action" style="max-width:100px;">
                        <option value="any" selected="selected"><?php _e('Any','appointzilla');?></option>
                        <option value="yes"><?php _e('Checked','appointzilla');?></option>
                        <option value="no"><?php _e('Unchecked','appointzilla');?></option>
                    </select></td>
            </tr>
            <tr class="title_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>">
                <td><strong><?php _e('Code','appointzilla');?></strong></td>
                <td><strong><?php _e('Name','appointzilla');?></strong></td>
                <td><strong> <?php _e('Action','appointzilla');?></strong></td>
            </tr>
            </thead>
            <tbody><?php // get service list group wise
            $ServiceDetails = $wpdb->get_results("SELECT * FROM $service_table WHERE `category_id` =$params[serviceCategoryID] AND availability = 'yes' ORDER BY implant");
            foreach($ServiceDetails as $service) {?>
                <tr class="odd value_row row_cat_id-<?php echo $params[serviceCategoryID]; ?>" style="border-bottom:1px;">
                    <td><?php echo ucwords($service->code); ?></td>
                    <td><?php echo ucwords($service->name); ?></td>
                    <td class="button-column">
                        <input type="checkbox" name="selected[<?php echo $service->id; ?>]" <?php echo (in_array($service->id,$selectedServices))?'checked="checked"':''?>/>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php
    }

    function createNewDiagnosis($params)
    {
        if ($params[diagnosis_name]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
        $diagnosis_service_table = $wpdb->prefix . "ap_diagnosis_service";
        $diagnosisList = $wpdb->get_results("SELECT * FROM $diagnosis_table WHERE diagnosis_name = '$params[diagnosis_name]'",ARRAY_A);
        if (count($diagnosisList)>0)
            return -2;
        $result = $wpdb->insert($diagnosis_table,
            array(
                diagnosis_name=>$params[diagnosis_name],
                diagnosis_note=>$params[diagnosis_note]
            )
        );
        if (!result)
            return -1;
        $newDiagnosisID = $wpdb->insert_id;
        foreach($params[selected] as $key=>$value){
            $wpdb->insert($diagnosis_service_table,
                array(
                    diagnosis_id=>$newDiagnosisID,
                    service_id=>$key
                )
            );
        }
        return $newDiagnosisID;
    }

    function updateDiagnosis($params)
    {
        if ($params[diagnosis_name]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
        $diagnosis_service_table = $wpdb->prefix . "ap_diagnosis_service";
        $result = $wpdb->update($diagnosis_table,
            array(
                diagnosis_name=>$params[diagnosis_name],
                diagnosis_note=>$params[diagnosis_note]

            ),
            array(
                diagnosis_id=>$params[diagnosis_id]
            )

        );
        if (!result)
            return -1;
        $wpdb->delete($diagnosis_service_table,
            array(
                diagnosis_id=>$params[diagnosis_id]
            )
        );
        foreach($params[selected] as $key=>$value){
            $wpdb->insert($diagnosis_service_table,
                array(
                    diagnosis_id=>$params[diagnosis_id],
                    service_id=>$key
                )
            );
        }
        return $params[diagnosis_id];
    }

    function deleteDiagnosis($params)
    {
        if ($params[diagnosis_id]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
        $diagnosis_service_table = $wpdb->prefix . "ap_diagnosis_service";
        $wpdb->delete($diagnosis_table,
            array(
                diagnosis_id=>$params[diagnosis_id]
            )
        );

        $wpdb->delete($diagnosis_service_table,
            array(
                diagnosis_id=>$params[diagnosis_id]
            )
        );
        return 1;
    }
}