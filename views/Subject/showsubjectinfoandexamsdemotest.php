<?php
/**
 * File: showsubjectinfoandexams.php
 * User: Masterplan
 * Date: 10/07/14
 * Time: 12:48
 * Desc: Show informations abount requested subject with list of all available exams
 */

global $log, $config, $user;

if(isset($_POST['idSubject'])){ ?>

    <label class="b2Space" for="infoName"><?= ttName ?> : </label>
    <input class="readonly" type="text" id="infoName" name="subjectName" size="50" value="<?= $_POST['idSubject'] ?>">
    <div class="clearer"></div>

    <label for="infoDesc"><?= ttDescription ?> : </label>
    <textarea class="readonly b2Space rSpace left" id="infoDesc" name="subjectDesc"><?= $subject['description'] ?></textarea>
    <div class="clearer"></div>

   <div id="examsAvailableTableContainer">
   <div id="examsAvailableTable_wrapper" class="dataTables_wrapper no-footer">
      <div class="fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr">
         <div class="dataTables_info" id="examsAvailableTable_info" role="status" aria-live="polite">Visualizzati 1 Demo Test in corso</div>
         <div id="examsAvailableTable_filter" class="dataTables_filter" style="display: none;"><label>Search:<input type="search" class="" aria-controls="examsAvailableTable"></label></div>
      </div>
      <div class="dataTables_scroll">
         <div class="dataTables_scrollHead ui-state-default" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
            <div class="dataTables_scrollHeadInner" style="box-sizing: content-box; width: 630px; padding-right: 0px;">
               <table class="stripe order-column dataTable no-footer" role="grid" style="margin-left: 0px; width: 630px;">
                  <thead>
                     <tr role="row">
                        <th class="eStatus sorting ui-state-default" tabindex="0" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 10px;" aria-label=": activate to sort column ascending">
                           <div class="DataTables_sort_wrapper"><span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </th>
                        <th class="eDay sorting ui-state-default sorting_desc" tabindex="0" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 51px;" aria-label="Giorno: activate to sort column ascending" aria-sort="descending">
                           <div class="DataTables_sort_wrapper">Giorno<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s"></span></div>
                        </th>
                        <th class="eTime sorting ui-state-default sorting_desc" tabindex="0" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 32px;" aria-label="Ora: activate to sort column ascending">
                           <div class="DataTables_sort_wrapper">Ora<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s"></span></div>
                        </th>
                        <th class="eName sorting ui-state-default" tabindex="0" aria-controls="examsAvailableTable" rowspan="1" colspan="1" aria-label="Esame: activate to sort column ascending" style="width: 220px;">
                           <div class="DataTables_sort_wrapper">Esame<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </th>
                        <th class="eRegEnd sorting ui-state-default" tabindex="0" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 135px;" aria-label="Chiusura registrazione: activate to sort column ascending">
                           <div class="DataTables_sort_wrapper">Chiusura registrazione<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </th>
                        <th class="eManage sorting_disabled ui-state-default" rowspan="1" colspan="1" style="width: 57px;" aria-label="Gestisci">
                           <div class="DataTables_sort_wrapper">Gestisci<span class="DataTables_sort_icon"></span></div>
                        </th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
         <div class="dataTables_scrollBody" style="overflow: auto; height: 150px; width: 100%;">
            <table id="examsAvailableTable" class="stripe order-column dataTable no-footer" role="grid" aria-describedby="examsAvailableTable_info" style="width: 100%;">
               <thead>
                  <tr role="row" style="height: 0px;">
                     <th class="eStatus sorting ui-state-default" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 10px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label=": activate to sort column ascending">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper"><span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </div>
                     </th>
                     <th class="eDay sorting ui-state-default sorting_desc" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 51px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-sort="descending" aria-label="Giorno: activate to sort column ascending">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper">Giorno<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s"></span></div>
                        </div>
                     </th>
                     <th class="eTime sorting ui-state-default sorting_desc" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 32px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="Ora: activate to sort column ascending">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper">Ora<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s"></span></div>
                        </div>
                     </th>
                     <th class="eName sorting ui-state-default" aria-controls="examsAvailableTable" rowspan="1" colspan="1" aria-label="Esame: activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 220px;">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper">Esame<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </div>
                     </th>
                     <th class="eRegEnd sorting ui-state-default" aria-controls="examsAvailableTable" rowspan="1" colspan="1" style="width: 135px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="Chiusura registrazione: activate to sort column ascending">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper">Chiusura registrazione<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span></div>
                        </div>
                     </th>
                     <th class="eManage sorting_disabled ui-state-default" rowspan="1" colspan="1" style="width: 57px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="Gestisci">
                        <div class="dataTables_sizing" style="height:0;overflow:hidden;">
                           <div class="DataTables_sort_wrapper">Gestisci<span class="DataTables_sort_icon"></span></div>
                        </div>
                     </th>
                  </tr>
               </thead>
               <tbody>
                  <tr role="row" class="odd">
                     <td class=" eStatus"><img alt="Attivo" title="Attivo" src="themes/default/images/Active.png"></td>
                     <td class="eDay sorting_1"><?php echo date("d/m/Y"); ?></td>
                     <td class="eTime sorting_2"><?php echo date("h:i"); ?></td>
                     <td class=" eName">Demo Test</td>
                     <td class=" eRegEnd">&infin;</td>
                     <td class=" eManage">
                        <img name="action" src="themes/default/images/info.png" onclick="showExamInfo(this)" title="Informazioni">
                        <img name="action" src="themes/default/images/do.png" onclick="startTest('<?php echo $_POST['file']; ?>');" title="Esegui esame">                    
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      <div class="fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"></div>
   </div>
</div>
<?php
}
?>
