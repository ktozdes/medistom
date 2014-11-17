<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.07.14
 * Time: 13:24
 */

class CabinetController {
    function __construct()
    {

    }
    public function helloworld()
    {
        echo 'hello world from cabinet controller';
    }

    public function getCabinetList()
    {
        global $wpdb;
        //get all category list
        $cabinetsTable  = $wpdb->prefix . "ap_cabinets";
        $staffTable 	= $wpdb->prefix . "ap_staff";
        $cabinetsStaffTable 	= $wpdb->prefix . "ap_cabinets_staff";
        $cabinets = $wpdb->get_results("select `$cabinetsTable`.`cabinet_id`, cabinet_name, cabinet_note,`$staffTable`.`name` as 'staff_name'  from `$cabinetsTable`
		LEFT JOIN `$cabinetsStaffTable` on `$cabinetsTable`.`cabinet_id` = `$cabinetsStaffTable`.`cabinet_id`
		LEFT JOIN `$staffTable` on `$cabinetsStaffTable`.`staff_id` = `$staffTable`.`id`
		ORDER BY `$cabinetsTable`.`cabinet_id`
	", ARRAY_A);
        ?>
        <table width="100%" class="table table-hover">
            <thead>
            <tr style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
                <td><strong><?php _e('Name','appointzilla');?></strong></td>
                <td><strong><?php _e('Note','appointzilla');?></strong></td>
                <td><strong> <?php _e('Assigned Staff','appointzilla');?></strong></td>
                <td><strong> <?php _e('Action','appointzilla');?></strong></td>
            </tr>
            </thead>
            <?php
            $previous_cabinet = array();
            $assigned_staff = '';
            foreach($cabinets as $single_cabinet):
                if ($single_cabinet[cabinet_id]!=$previous_cabinet[cabinet_id] && count($previous_cabinet)>0):?>
                    <tr class="odd" style="border-bottom:1px;">
                        <td><em><?php echo $previous_cabinet['cabinet_name']; ?></em></td>
                        <td><em><?php echo $previous_cabinet['cabinet_note']; ?></em> </td>
                        <td><em><?php echo $assigned_staff; ?></em></td>
                        <td class="button-column">
                            <a rel="tooltip" href="?page=appzilla_cabinets&action=update&cabinet_id=<?php echo $previous_cabinet[cabinet_id]; ?>" title="<?php _e('Update','appointzilla');?>"><i class="icon-pencil"></i></a> &nbsp;
                            <a rel="tooltip" href="?page=appzilla_cabinets&action=delete&cabinet_id=<?php echo $previous_cabinet['cabinet_id']; ?>" onclick="return confirm('<?php _e('Do you want to delete this cabinet?','appointzilla');?>')" title="<?php _e('Delete','appointzilla');?>" ><i class="icon-remove"></i></td>
                    </tr>
                    <?php
                    $assigned_staff = '';
                endif;
                $previous_cabinet = $single_cabinet;
                $assigned_staff = (strlen($assigned_staff)>3)?$assigned_staff .' , '. $single_cabinet['staff_name']:$single_cabinet['staff_name'];

            endforeach; ?>
            <tr class="odd" style="border-bottom:1px;">
                <td><em><?php echo ucwords($previous_cabinet['cabinet_name']); ?></em></td>
                <td><em><?php echo ucfirst($previous_cabinet['cabinet_note']); ?></em> </td>
                <td><em><?php echo ucfirst($assigned_staff); ?></em> </td>
                <td class="button-column">
                    <a rel="tooltip" href="?page=appzilla_cabinets&action=update&cabinet_id=<?php echo $previous_cabinet['cabinet_id']; ?>" title="<?php _e('Update','appointzilla');?>"><i class="icon-pencil"></i></a> &nbsp;
                    <a rel="tooltip" href="?page=appzilla_cabinets&action=delete&cabinet_id=<?php echo $previous_cabinet['cabinet_id']; ?>" onclick="return confirm('<?php _e('Do you want to delete this cabinet?','appointzilla');?>')" title="<?php _e('Delete','appointzilla');?>" ><i class="icon-remove"></i>
                </td>
            </tr>
            </tbody>
        </table>
        <a href="?page=appzilla_cabinets&action=new" class="btn btn-primary"><i class="icon-ok"></i> <?php _e('New Cabinet','appointzilla');?></a>
    <?php
    }

    public function getNewCabinet()
    {
        global $wpdb;
        ?>
        <div id="new_cabinet" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" style="background-color:transparent!important;">
                    <thead>
                    <tr>
                        <td><strong><?php _e('Name','appointzilla');?></strong></td>
                        <td><strong><?php _e('Note','appointzilla');?></strong></td>
                        <td><strong> <?php _e('Assigned Staff','appointzilla');?></strong></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php _e('Cabinet Name','appointzilla');?> : <input type="text" id="cabinet_name" name="cabinet_name" class="inputheight" /></td>
                        <td><?php _e('Cabinet Note','appointzilla');?> : <input type="text" id="cabinet_note" name="cabinet_note" class="inputheight" /></td>
                        <td><?php _e('Assign Staff(s)','appointzilla');?> : <?php _e('Use CTRL to Select Multiple Staff(s)','appointzilla');?>
                            <select id="staff" name="staff[]" multiple="multiple" size="7" style="width:300px;">
                                <?php
                                $staffTable 	= $wpdb->prefix . "ap_staff";
                                $staffs = $wpdb->get_results("select *  from `$staffTable`",ARRAY_A);
                                foreach($staffs as $single_staff):
                                    ?>
                                    <option value="<?php echo $single_staff['id'];?>"><?php echo $single_staff['name'];?></option>
                                <?php endforeach;?>
                            </select></td>
                    </tr>
                    </tbody>
                </table>
                <button style="margin-bottom:10px;" id="CreateCabinet" type="submit" class="btn" name="CreateCabinet"><i class="icon-ok"></i> <?php _e('Create Cabinet','appointzilla');?></button>
                <a rel="tooltip" href="?page=appzilla_cabinets" class="btn"><?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }



    public function getEditableCabinetTable($params)
    {
        global $wpdb;

        $cabinetsTable  = $wpdb->prefix . "ap_cabinets";
        $cabinetsStaffTable 	= $wpdb->prefix . "ap_cabinets_staff";

        $cabinet_table  = $wpdb->prefix . "ap_cabinets";
        $cabinetData    = $wpdb->get_row("select $cabinet_table.cabinet_id, cabinet_name, cabinet_note, GROUP_CONCAT(staff_id SEPARATOR ',') as staff_ids from `$cabinet_table`
        LEFT JOIN `$cabinetsStaffTable` on `$cabinetsTable`.`cabinet_id` = `$cabinetsStaffTable`.`cabinet_id`
        WHERE `$cabinetsTable`.cabinet_id = $params[cabinet_id]",ARRAY_A);
        $cabinetData[staff_ids] = explode(',',$cabinetData[staff_ids]);
        ?>
        <div id="update_cabinet" class="bs-docs-example tooltip-demo jquery-tab">
            <form method="post">
                <table width="100%" style="background-color:transparent!important;">
                    <thead>
                    <tr>
                        <td><strong><?php _e('Name','appointzilla');?></strong></td>
                        <td><strong><?php _e('Note','appointzilla');?></strong></td>
                        <td><strong> <?php _e('Assigned Staff','appointzilla');?></strong></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" id="cabinet_id" name="cabinet_id" class="inputheight" value="<?php echo $cabinetData[cabinet_id]?>"/>
                            <?php _e('Cabinet Name','appointzilla');?> : <input type="text" id="cabinet_name" name="cabinet_name" class="inputheight" value="<?php echo $cabinetData[cabinet_name]?>"/></td>
                        <td><?php _e('Cabinet Note','appointzilla');?> : <input type="text" id="cabinet_note" name="cabinet_note" class="inputheight" value="<?php echo $cabinetData[cabinet_note]?>"/></td>
                        <td><?php _e('Assign Staff(s)','appointzilla');?> : <?php _e('Use CTRL to Select Multiple Staff(s)','appointzilla');?>
                            <select id="staff" name="staff[]" multiple="multiple" size="7" style="width:300px;">
                                <?php
                                $staffTable 	= $wpdb->prefix . "ap_staff";
                                $staffs = $wpdb->get_results("select *  from `$staffTable`",ARRAY_A);
                                foreach($staffs as $single_staff):?>
                                    <option <?php echo in_array($single_staff[id],$cabinetData[staff_ids])?'selected':'';?> value="<?php echo $single_staff['id'];?>"><?php echo $single_staff['name'];?></option>
                                <?php endforeach;?>
                            </select></td>
                    </tr>
                    </tbody>
                </table>
                <button style="margin-bottom:10px;" id="UpdateCabinet" type="submit" class="btn" name="update_cabinet"><i class="icon-ok"></i> <?php _e('Update Cabinet','appointzilla');?></button>
                <a rel="tooltip" href="?page=appzilla_cabinets" class="btn"><?php _e('Back','appointzilla');?></a>
            </form>
        </div>
    <?php
    }

    function createNewCabinet($params)
    {

        if ($params[cabinet_name]=='')
            return -1;
        global $wpdb;
        $cabinet_table = $wpdb->prefix . "ap_cabinets";
        $cabinetList = $wpdb->get_results("SELECT * FROM $cabinet_table WHERE cabinet_name = '$params[cabinet_name]'",ARRAY_A);
        if (count($cabinetList)>0)
            return -2;
        $result = $wpdb->insert($cabinet_table,
            array(
                cabinet_name=>$params[cabinet_name],
                cabinet_note=>$params[cabinet_note]
            )
        );
        if (!$result)
            return -1;
        $newCabinetID = $wpdb->insert_id;
        if($newCabinetID !== false){
            $table_name = $wpdb->prefix . "ap_cabinets_staff";
            if ($_POST['staff']!=''){
                foreach($_POST['staff'] as $singleStaffID){
                    $wpdb->insert(
                        $table_name,
                        array(
                            'cabinet_id' => $newCabinetID,
                            'staff_id' => $singleStaffID
                        )
                    );
                }

            }
        }
        return $newCabinetID;
    }

    function updateCabinet($params)
    {
        if ($params[cabinet_name]=='')
            return -1;
        global $wpdb;
        $cabinet_table = $wpdb->prefix . "ap_cabinets";
        $result = $wpdb->update($cabinet_table,
            array(
                cabinet_name=>$params[cabinet_name],
                cabinet_note=>$params[cabinet_note]

            ),
            array(
                cabinet_id=>$params[cabinet_id]
            )
        );
        $cabinets_staff_table = $wpdb->prefix . "ap_cabinets_staff";
        $wpdb->delete( $cabinets_staff_table,
            array(cabinet_id=>$params[cabinet_id]));
        foreach($_POST['staff'] as $singleStaffID){
            $wpdb->insert(
                $cabinets_staff_table,
                array(
                    'cabinet_id' => $params[cabinet_id],
                    'staff_id' => $singleStaffID
                )
            );
        }
        return $params[cabinet_id];
    }

    function deleteCabinet($params)
    {
        if ($params[cabinet_id]=='')
            return -1;
        global $wpdb;
        $cabinet_table = $wpdb->prefix . "ap_cabinets";
        $cabinets_staff_table = $wpdb->prefix . "ap_cabinets_staff";
        $wpdb->delete($cabinet_table,
            array(
                cabinet_id=>$params[cabinet_id]
            )
        );
        $wpdb->delete( $cabinets_staff_table,
            array(cabinet_id=>$params[cabinet_id]));
        return 1;
    }
}