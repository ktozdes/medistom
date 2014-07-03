<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.07.14
 * Time: 13:24
 */

class DiagnosisController {
    function __construct()
    {

    }
    public function helloworld()
    {
        echo 'hello world from diagnosis controller';
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
                    <td><em><?php echo $singleDiagnosis[diagnosis_note]; ?></em></td>
                    <td><em><a rel="tooltip" href="?page=diagnosis&action=update&diagnosis_id=<?php echo $singleDiagnosis[diagnosis_id]; ?>" title="<?php _e('Update','appointzilla'); ?>"><i class="icon-pencil"></i></a>&nbsp;
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
                    <tr>
                        <td><?php _e('Diagnosis Name','appointzilla');?> : <input type="text" id="diagnosis_name" name="diagnosis_name" class="inputheight" /> <input type="hidden" name="new_diagnosis" value="1" /></td>
                        <td><?php _e('Diagnosis Note','appointzilla');?> : <textarea type="text" id="diagnosis_note" name="diagnosis_note" ></textarea></td>
                    </tr>
                </table>
                <button id="create_diagnosis_button" type="button" class="btn" name="create_diagnosis_button"><i class="icon-ok"></i> <?php _e('Create','appointzilla');?></button>
                <a href="?page=diagnosis" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }



    public function getEditableDiagnosisTable($params)
    {
        global $wpdb;
        $diagnosis_table  = $wpdb->prefix . "ap_diagnosis";
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
                <button id="create_diagnosis_button" type="button" class="btn" name="create_diagnosis_button"><i class="icon-ok"></i> <?php _e('Update','appointzilla');?></button>
                <a href="?page=diagnosis" class="btn" ><i class="icon-remove"></i> <?php _e('Cancel','appointzilla');?></a>
            </form>
        </div>
    <?php
    }

    function createNewDiagnosis($params)
    {
        if ($params[diagnosis_name]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
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
        return $newDiagnosisID;
    }

    function updateDiagnosis($params)
    {
        if ($params[diagnosis_name]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
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
        return $params[diagnosis_id];
    }

    function deleteDiagnosis($params)
    {
        if ($params[diagnosis_id]=='')
            return -1;
        global $wpdb;
        $diagnosis_table = $wpdb->prefix . "ap_diagnosis";
        $wpdb->delete($diagnosis_table,
            array(
                diagnosis_id=>$params[diagnosis_id]
            )
        );
        return 1;
    }
}