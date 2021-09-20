<?php

/**
 *	\file       htdocs/index.php
 *	\brief      Affichage des éléments du hauts comme liens vers listes de structures du type choisi
 *              Contient les liens vers les scripts ajax pour afficher la première colonne sur la gauche
 */

define('NOCSRFCHECK', 1); // This is main home and login page. We must be able to go on it from another web site.

require 'main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

// If not defined, we select menu "home"
$_GET['mainmenu'] = GETPOST('mainmenu', 'aZ09') ?GETPOST('mainmenu', 'aZ09') : 'home';
$action = GETPOST('action', 'aZ09');

$hookmanager->initHooks(array('index'));


/*
 * Actions
 */

// Check if company name is defined (first install)
if (!isset($conf->global->MAIN_INFO_SOCIETE_NOM) || empty($conf->global->MAIN_INFO_SOCIETE_NOM))
{
    header("Location: ".DOL_URL_ROOT."/admin/index.php?mainmenu=home&leftmenu=setup&mesg=setupnotcomplete");
    exit;
}
if (count($conf->modules) <= (empty($conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING) ? 1 : $conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING))	// If only user module enabled
{
    header("Location: ".DOL_URL_ROOT."/admin/index.php?mainmenu=home&leftmenu=setup&mesg=setupnotcomplete");
    exit;
}
if (GETPOST('addbox'))	// Add box (when submit is done from a form when ajax disabled)
{
	require_once DOL_DOCUMENT_ROOT.'/core/class/infobox.class.php';
	$zone = GETPOST('areacode', 'aZ09');
	$userid = GETPOST('userid', 'int');
	$boxorder = GETPOST('boxorder', 'aZ09');
	$boxorder .= GETPOST('boxcombo', 'aZ09');

	$result = InfoBox::saveboxorder($db, $zone, $boxorder, $userid);
	if ($result > 0) setEventMessages($langs->trans("BoxAdded"), null);
}


/*
 * View
 */

if (!is_object($form)) $form = new Form($db);

// Title
$title = $langs->trans("HomeArea").' - Opensilog '.DOL_VERSION;
if (!empty($conf->global->MAIN_APPLICATION_TITLE)) $title = $langs->trans("HomeArea").' - '.$conf->global->MAIN_APPLICATION_TITLE;

llxHeader('', $title);
$sql = "SELECT fk_object  FROM " . MAIN_DB_PREFIX . "societe_extrafields WHERE type_structure='UN'";
$resql = $db->query($sql);
if ($resql) {
    $obj = $db->fetch_object($resql);
}

?>
    <script>
        var current_element='';
        function hide_all(){
            for(i=0;i<7;i++){
                $("#div"+i).html("");
            }
        }
        function hide_adherents(){
            for(i=5;i<7;i++){
                $("#div"+i).html("");
            }
        }
        $(document).ready(function(){
            $("#UN").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=0&type=UN",2)?>", success: function(result){
                    $("#div0").html("<h1>"+result.num+"</h1>"+result.out);
                    current_element='UN'
                }});
            });
            $("#UR").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=0&type=UR",2)?>", success: function(result){
                    $("#div1").html("<h1>"+result.num+"</h1>"+result.out);
                    current_element='UR'
                }});
            });
            $("#UD").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=0&type=UD",2)?>", success: function(result){
                    $("#div2").html("<h1>"+result.num+"</h1>"+result.out);
                    current_element='UD'
                }});
            });
            $("#UL").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=0&type=UL",2)?>", success: function(result){
                    $("#div3").html("<h1>"+result.num+"</h1>"+result.out);
                    current_element='UL'
                }});
            });
            $("#ASS").click(function () {
                hide_all();
                $.ajax({
                    url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=0&type=ASS", 2)?>",
                    success: function (result) {
                        $("#div4").html("<h1>" + result.num + "</h1>" + result.out);
                        current_element = 'ASS'
                    }
                });
            });
            $("#IN").click(function(){
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/adherents.php?type=IN",2)?>"+"&id="+current_element, success: function(result){
                   pos=result.indexOf(' ');
                   n=result.substring(0,pos);
                   result=result.substring(pos+1,50000);
                   $("#div5").html("<h1>"+n+"</h1>"+result);
                }});
            });
            $("#PM").click(function(){
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/adherents.php?type=PM",2)?>"+"&id="+current_element, success: function(result){
                   pos=result.indexOf(' ');
                   n=result.substring(0,pos);
                   result=result.substring(pos+1,50000);
                   $("#div6").html("<h1>"+n+"</h1>"+result);
                }});
            });
        });

    </script>

    <a id="UN" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h1>Siège</h1>
        </div>
    </a>

    <a id="UR" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h1>UR</h1>
        </div>
    </a>

    <a id="UD" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h1>UD</h1>
        </div>
    </a>

    <a id="UL" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h1>UL</h1>
        </div>
    </a>


    <a id="ASS" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h2>ASSOCIATIONS</h2>
        </div>
    </a>

    <a id="IN" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h1>INDIVIDUEL</h1>
        </div>
    </a>
    <a id="PM" class="boxstatsindicator thumbstat nobold nounderline">
        <div class="boxstats" style="height:80px;width:150px;background:rgb(51,113,255);color: white;">
            <h3>PERSONNE</br> MORALE</h3>
        </div>
    </a>

    </br>
    <div style="height:80px;width:20px;float:left;"></div>
    <div id="div0" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:30px;float:left;"></div>
    <div id="div1" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:30px;float:left;"></div>
    <div id="div2" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:30px;float:left;"></div>
    <div id="div3" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:30px;float:left;"></div>
    <div id="div4" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:40px;float:left;"></div>
    <div id="div5" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:35px;float:left;"></div>
    <div id="div6" style="height:80px;width:150px;float:left;"></div>
<?php
