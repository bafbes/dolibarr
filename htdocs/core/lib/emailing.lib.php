<?php
/* Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see https://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/emailing.lib.php
 *		\brief      Library file with function for emailing module
 */

/**
 * Prepare array with list of tabs
 *
 * @param   Mailing	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function emailing_prepare_head(Mailing $object)
{
	global $user, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/comm/mailing/card.php?id=".$object->id;
	$head[$h][1] = $langs->trans("MailCard");
	$head[$h][2] = 'card';
	$h++;

	if (empty($conf->global->MAIN_USE_ADVANCED_PERMS) || (!empty($conf->global->MAIN_USE_ADVANCED_PERMS) && $user->rights->mailing->mailing_advance->recipient)) {
		$head[$h][0] = DOL_URL_ROOT."/comm/mailing/cibles.php?id=".$object->id;
		$head[$h][1] = $langs->trans("MailRecipients");
		if ($object->nbemail > 0) {
			$head[$h][1] .= '<span class="badge marginleftonlyshort">'.$object->nbemail.'</span>';
		}
		$head[$h][2] = 'targets';
		$h++;
	}

	if (!empty($conf->global->EMAILING_USE_ADVANCED_SELECTOR)) {
		$head[$h][0] = DOL_URL_ROOT."/comm/mailing/advtargetemailing.php?id=".$object->id;
		$head[$h][1] = $langs->trans("MailAdvTargetRecipients");
		$head[$h][2] = 'advtargets';
		$h++;
	}

	$head[$h][0] = DOL_URL_ROOT."/comm/mailing/info.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'emailing');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'emailing', 'remove');

	return $head;
}

/**
 * @param $subject Mail subject
 * @param $message Mail message
 * @param $lang Mail language
 * @return array [$subject,$message] parsed with $lang
 */
function mail2lang($subject, $message, $lang)
{
    global $db;
    $origsubject=$subject;
    $origmessage=$message;
    if (empty(strstr($subject, 'LANG_'))) return [$subject, $message];//Retour si symbole inexistant dans sujet du mail
    $lang=substr($lang,0,2);
    if($lang=='fr'){
        $subject=strstr($subject, 'LANG_FR');//Chaine depuis position LANG_FR
        if(!empty($subject)) {
            $subject = substr($subject, 7, 500000);//Chaine sans ...LANG_FR
            $pos = strpos($subject, 'LANG_');//deuxième occurence de LANG_XX
            if (is_numeric($pos)) $subject=substr($subject,0,$pos);

            $message=strstr($message, 'LANG_FR');//Chaine depuis position LANG_FR
            $message = substr($message, 7, 500000);//Chaine sans LANG_FR
            $pos = strpos($message, 'LANG_');
            if (is_numeric($pos)) $message=substr($message,0,$pos);
            return [$subject,$message];
        }
        else $lang='en';
    }
    elseif($lang=='es'){
        $subject=strstr($subject, 'LANG_ES');//Chaine depuis position LANG_XX
        if(!empty($subject)) {
            $subject = substr($subject, 7, 500000);//Chaine sans LANG_XX
            $pos = strpos($subject, 'LANG_');//deuxième occurence de LANG_XX
            if (is_numeric($pos)) $subject=substr($subject,0,$pos);

            $message=strstr($message, 'LANG_ES');//Chaine depuis position LANG_XX
            $message = substr($message, 7, 500000);//Chaine sans LANG_XX
            $pos = strpos($message, 'LANG_');//deuxième occurence de LANG_XX
            if (is_numeric($pos)) $message=substr($message,0,$pos);
            return [$subject,$message];
        }
        else $lang='en';
    }
    else $lang='en';
    if($lang=='en'){
        $subject=strstr($origsubject, 'LANG_EN');//Chaine depuis position LANG_XX
        if(!empty($subject)) {
            $subject = substr($subject, 7, 500000);//Chaine sans LANG_XX
            $pos = strpos($subject, 'LANG_');
            if (is_numeric($pos)) $subject=substr($subject,0,$pos);

            $message=strstr($origmessage, 'LANG_EN');//Chaine depuis position LANG_XX
            $message = substr($message, 7, 500000);//Chaine sans LANG_XX
            $pos = strpos($message, 'LANG_');
            if (is_numeric($pos)) $message=substr($message,0,$pos);
            return [$subject,$message];
        }
    }
    return [$origsubject,$origmessage];
}
