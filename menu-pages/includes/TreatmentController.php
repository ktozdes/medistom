<?php class TreatmentController{
    function __construct()
    {

    }

    public function helloworld()
    {
        echo 'hello world from treatment controller';
    }

    public function getTreatmentListTable()
    {
        global $wpdb;
        $treatment_table = $wpdb->prefix . "ap_treatment";
        $treatmentList = $wpdb->get_results("SELECT * FROM $treatment_table",ARRAY_A);
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
        foreach($treatmentList as $singleTreatment) : ?>
        <tr>
            <td><em><?php echo $singleTreatment[treatment_id]; ?></em></td>
            <td><em><?php echo $singleTreatment[treatment_name]; ?></em></td>
            <td><em><?php echo $singleTreatment[treatment_note]; ?></em></td>
            <td><em><a rel="tooltip" href="?page=treatment&action=view&treatment_id=<?php echo $singleTreatment[treatment_id]; ?>" title="<?php _e('View','appointzilla'); ?>"><i class="icon-eye-open"></i></a>&nbsp;
                    <a rel="tooltip" href="?page=treatment&action=update&treatment_id=<?php echo $singleTreatment[treatment_id]; ?>" title="<?php _e('Update','appointzilla'); ?>"><i class="icon-pencil"></i></a>&nbsp;
                    <a rel="tooltip" href="?page=treatment&action=delete&treatment_id=<?php echo $singleTreatment[treatment_id]; ?>" onclick="return confirm('<?php _e('Do you want to delete this treatment','appointzilla'); ?>')" title="<?php _e('Delete','appointzilla'); ?>"><i class="icon-remove"></i></a></em></td>
        </tr>
    <?php endforeach;?>
        </table>
        <a href="?page=treatment&action=new" class="btn btn-primary"><i class="icon-ok"></i> <?php _e('New Treatment','appointzilla');?></a>
        <?php
    }

    public function getNewTreatmentTable()
    {?>
        <div id="new_treatment" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" class="table table-hover">
                    <tbody>
                    <tr>
                        <td><?php _e('Treatment Name','appointzilla');?> : <input type="text" id="treatment_name" name="treatment_name" class="inputheight" /> <input type="hidden" name="new_treatment" value="1" /></td>
                        <td><?php _e('Treatment Note','appointzilla');?> : <textarea type="text" id="treatment_note" name="treatment_note" ></textarea></td>
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
                <button id="create_treatment_button" type="button" class="btn" name="create_treatment_button"><i class="icon-ok"></i> <?php _e('Create','appointzilla');?></button>
                <a href="?page=treatment" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }



    public function getEditableTreatmentTable($params)
    {
        global $wpdb;
        $treatment_table                = $wpdb->prefix . "ap_treatment";
        $service_category_table         = $wpdb->prefix . "ap_service_category";

        $service_category = $wpdb->get_results("select * from `$service_category_table` order by name asc");
        $treatmentData    = $wpdb->get_row("select * from `$treatment_table` WHERE treatment_id = $params[treatment_id]",ARRAY_A);
        ?>
        <div id="update_treatment" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" class="table table-hover">
                    <tbody>
                    <tr>
                        <td><?php _e('Treatment Name','appointzilla');?> : <input type="text" id="treatment_name" name="treatment_name" class="inputheight" value="<?php echo $treatmentData['treatment_name'];?>"/>
                            <input type="hidden" name="update_treatment" value="1" />
                            <input type="hidden" name="treatment_id" value="<?php echo $params[treatment_id];?>" /></td>
                        <td><?php _e('Treatment Note','appointzilla');?> : <textarea type="text" id="treatment_note" name="treatment_note" ><?php echo $treatmentData['treatment_note'];?></textarea></td>
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
                            <?php $this->getSelectableServiceTab(array('serviceCategoryID'=>$gruopname->id,treatment_id=>$params[treatment_id]));?>
                        </div>
                    <?php }?>
                <?php }?>
                <button id="create_treatment_button" type="button" class="btn" name="create_treatment_button"><i class="icon-ok"></i> <?php _e('Update','appointzilla');?></button>
                <a href="?page=treatment" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }

    public function viewTreatmentTable($params)
    {
        global $wpdb;
        $treatment_table            = $wpdb->prefix . "ap_treatment";
        $service_table              = $wpdb->prefix . "ap_services";
        $treatment_service_table    = $wpdb->prefix . "ap_treatment_service";
        $treatmentList = $wpdb->get_results("SELECT * FROM $treatment_table
            INNER JOIN $treatment_service_table on $treatment_table.treatment_id = $treatment_service_table.treatment_id
            INNER JOIN $service_table on $service_table.id = $treatment_service_table.service_id
            WHERE $treatment_table.treatment_id = $params[treatment_id]",ARRAY_A);
        if (count($treatmentList)>0){?>
            <table width="100%" class="detail-view table table-striped table-condensed">
            <tr>
                <td><strong><?php _e('Name','appointzilla'); ?></strong></td>
                <td>:</td>
                <td><?php echo $treatmentList[0][treatment_name];?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Note','appointzilla'); ?></strong></td>
                <td>:</td>
                <td><?php echo $treatmentList[0][treatment_note];?></td>
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
            foreach($treatmentList as $key=>$singleService){?>
                <tr>
                    <td><?php echo $singleService[code]; ?></td>
                    <td><?php echo $singleService[name]; ?></td>
                </tr>
            <?php
            }?>
            </table>
            <a class="btn" href="?page=treatment&action=update&treatment_id=<?php echo $params[treatment_id];?>"><i class="icon-pencil"></i> <?php _e('Update','appointzilla');?></a>
            <a class="btn" href="?page=treatment"><i class="icon-arrow-left"></i> <?php _e('Back','appointzilla');?></a>
        <?php
        }
    }

    function getSelectableServiceTab($params)
    {
        global $wpdb;
        $service_table              = $wpdb->prefix . "ap_services";
        $selectedServices = array();
        if (isset($params[treatment_id])){
            $treatment_service_table    = $wpdb->prefix . "ap_treatment_service";
            $treatmentServiceList = $wpdb->get_results("SELECT service_id FROM $treatment_service_table WHERE treatment_id=$params[treatment_id]",ARRAY_A);

            foreach($treatmentServiceList as $key=>$singleService){
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

    function createNewTreatment($params)
    {
        if ($params[treatment_name]=='')
            return -1;
        global $wpdb;
        $treatment_table = $wpdb->prefix . "ap_treatment";
        $treatment_service_table = $wpdb->prefix . "ap_treatment_service";
        $treatmentList = $wpdb->get_results("SELECT * FROM $treatment_table WHERE treatment_name = '$params[treatment_name]'",ARRAY_A);
        if (count($treatmentList)>0)
            return -2;
        $result = $wpdb->insert($treatment_table,
            array(
                treatment_name=>$params[treatment_name],
                treatment_note=>$params[treatment_note]
            )
        );
        if (!result)
            return -1;
        $newTreatmentID = $wpdb->insert_id;
        foreach($params[selected] as $key=>$value){
            $wpdb->insert($treatment_service_table,
                array(
                    treatment_id=>$newTreatmentID,
                    service_id=>$key
                )
            );
        }
        return $newTreatmentID;
    }

    function updateTreatment($params)
    {
        if ($params[treatment_name]=='')
            return -1;
        global $wpdb;
        $treatment_table = $wpdb->prefix . "ap_treatment";
        $treatment_service_table = $wpdb->prefix . "ap_treatment_service";
        $result = $wpdb->update($treatment_table,
            array(
                treatment_name=>$params[treatment_name],
                treatment_note=>$params[treatment_note]

            ),
            array(
                treatment_id=>$params[treatment_id]
            )

        );
        if (!result)
            return -1;
        $wpdb->delete($treatment_service_table,
            array(
                treatment_id=>$params[treatment_id]
            )
        );
        foreach($params[selected] as $key=>$value){
            $wpdb->insert($treatment_service_table,
                array(
                    treatment_id=>$params[treatment_id],
                    service_id=>$key
                )
            );
        }
        return $params[treatment_id];
    }

    function deleteTreatment($params)
    {
        if ($params[treatment_id]=='')
            return -1;
        global $wpdb;
        $treatment_table = $wpdb->prefix . "ap_treatment";
        $treatment_service_table = $wpdb->prefix . "ap_treatment_service";
        $wpdb->delete($treatment_table,
            array(
                treatment_id=>$params[treatment_id]
            )
        );

        $wpdb->delete($treatment_service_table,
            array(
                treatment_id=>$params[treatment_id]
            )
        );
        return 1;
    }
}