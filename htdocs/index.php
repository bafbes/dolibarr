<?php
/* Copyright (C) 2001-2004	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2020	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2017	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2011-2012	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2015		Marcos García			<marcosgdf@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/index.php
 *	\brief      Dolibarr home page
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
        function hide_all(){
            for(i=1;i<7;i++){
                $("#div"+i).html("");
            }
        }
        $(document).ready(function(){
            $("#UR").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=$obj->fk_object&type=UR",2)?>", success: function(result){
                        $("#div1").html(result);
                    }});
            });
        });
        $(document).ready(function(){
            $("#UD").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=$obj->fk_object&type=UD",2)?>", success: function(result){
                        $("#div2").html(result);
                    }});
            });
        });
        $(document).ready(function(){
            $("#UL").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=$obj->fk_object&type=UL",2)?>", success: function(result){
                        $("#div3").html(result);
                    }});
            });
        });
        $(document).ready(function(){
            $("#ASS").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/structures.php?id=$obj->fk_object&type=ASS",2)?>", success: function(result){
                        $("#div4").html(result);
                    }});
            });
        });
        $(document).ready(function(){
            $("#IN").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/adherents.php?id=$obj->fk_object&type=IN",2)?>", success: function(result){
                        $("#div5").html(result);
                    }});
            });
        });
        $(document).ready(function(){
            $("#PM").click(function(){
                hide_all();
                $.ajax({url: "<?= dol_buildpath("/cgl/tabs/adherents.php?id=$obj->fk_object&type=PM",2)?>", success: function(result){
                        $("#div6").html(result);
                    }});
            });
        });

    </script>

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
            <h2>ASSOCIATION</h2>
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
    <div style="height:80px;width:10px;float:left;"></div>
    <div id="div1" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:30px;float:left;"></div>
    <div id="div2" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:40px;float:left;"></div>
    <div id="div3" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:40px;float:left;"></div>
    <div id="div4" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:40px;float:left;"></div>
    <div id="div5" style="height:80px;width:150px;float:left;"></div>
    <div style="height:80px;width:40px;float:left;"></div>
    <div id="div6" style="height:80px;width:150px;float:left;"></div>
<?php


function print_arbo($parent, $id, $h)
{
    global $db;
    $s=new Societe($db);
    $s->fetch($parent);
    if($parent==$id) print '<b>';
    print "$h ".str_replace('<a ','<a target=_blank ',$s->getNomUrl(1));
    if($parent!=$id) print ' <a href="'.dol_buildpath("/cgl/tabs/niveau1.php?id=$parent",1)
        .'" target=_blank><span class="fa fa-plus-circle valignmiddle paddingleft" title="Niveau 1"></span></a>';
    print '<br>';
    if($parent==$id) print '</b>';

    $sql = "SELECT se.fk_object  FROM ".MAIN_DB_PREFIX."societe s JOIN ".MAIN_DB_PREFIX."societe_extrafields se ON s.rowid=se.fk_object";
    $sql.=" WHERE affiliation=$parent ORDER BY se.type_structure desc,s.nom asc";
    $resql = $db->query($sql);
    if ($resql) {
        $num = $db->num_rows($resql);
        for ($i = 0; $i < $num; $i++) {
            $obj = $db->fetch_object($resql);
            print '  ';
            if($parent==$id)print_arbo($obj->fk_object,$id,$h.'|__');
        }
    }
}
