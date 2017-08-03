<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Admin's Homepage
 */

global $config, $user;

?>

        <?php
        openBox(ttReport, 'normal-70.1%', 'participantsdetails');

        ?>

    <form name="participantdetails" method="post">
    <h3><?= ttReportSelectDetail ?></h3>

    <table id ="table-partecipant" class="filter">
        <tr>
            <td>
                <select size="10" id="detail" class="select-partecipant">
                </select>
            </td>
            <td>
                <a class="normal button right rSpace" id="add" onclick="addStudentDetail(detail.value)"><?=ttAdd?></a>
            </td>
        </tr>
    </table>


    </form>
    <br/>
    <hr/>
    <div id="tabsbutton">
        <a class="normal button rSpace" id="next" onclick="closePartecipantDetails()"><?=ttClose?></a>
    </div>
        <div class="clearer"></div>
        <?php
        closeBox();
        ?>