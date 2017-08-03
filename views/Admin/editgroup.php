<?php
/**
 * File: editstudent.php
 * User: tomma
 * Date: 23/10/16
 * Desc: Shows form for edit user user
 */

global $user, $tt;

?>
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="loader" class="loader"></div>
    <?php
    openBox(ttSelectGroup, 'left', 'groupsList');
        echo "<div id='contenitoreGruppi' style='height:350px;text-align:center; visibility:hidden'>";
    $db = new sqlDB();
    if ($db->qListOnlyGroup()){
        echo '<div class="list"><ul>';
        while($group = $db->nextRowAssoc()){
            echo '<b><li><a 
                    class="showGroupInfo" 
                    name="group" 
                    value="'.$group['idGroup'].'" 
                    onclick="showGroupInfo(this);">
                    '.$group['NameGroup'].'
                </a>
            </li></b>';
            $db2 = new sqlDB();
            if ($db2->qListSpecificSubgroup($group['idGroup'])) {
                while ($subgroup = $db2->nextRowAssoc()) {
                    echo '<li style="text-indent:20px">
                    <a class="showGroupInfo" 
                        name="subgroup" 
                        value="'.$subgroup['idSubGroup'].'" 
                        onclick="showSubgroupInfo(this);">'
                            .$subgroup['NameSubGroup'].'
                    </a>
                    </li>';
                }
            }
            echo "<br>&nbsp;";
        }
        echo '</ul></div>';
    }else{
        echo ttEDatabase;
    }
    echo "</div>";
    closeBox();
    openBox(ttInfo, 'right', 'groupInfo'); ?>

    <form class="infoEdit" onsubmit="return false;"></form>


    <?php closeBox(); ?>

    <div class="clearer"></div>
</div>
<script>
$(document).ready(function() {
    $('#contenitoreGruppi').css("visibility","visible");
    $('#loader').hide();
});
</script>