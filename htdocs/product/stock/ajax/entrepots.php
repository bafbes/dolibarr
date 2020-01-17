<?php
/* Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2005-2013 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file receptions/product/ajax/products.php
 * \brief File to return Ajax response on product list request
 */

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';

$langs->load("products");
$langs->load("main");

$htmlname = GETPOST('htmlname', 'alpha');
top_httphead();

if(empty($htmlname) || !GETPOST($htmlname)) return;
$searchkey = GETPOST($htmlname);

if($htmlname == 'idprod') {
    $sql = "SELECT e.rowid,e.ref,e.ref label,ee.contentref as idcontentref,p.ref as contentref, ee.contentqty,1 as type";
    $sql .= " FROM " . MAIN_DB_PREFIX . "equipement as e";
    $sql .= " JOIN " . MAIN_DB_PREFIX . "equipement_extrafields as ee ON ee.fk_object=e.rowid";
    $sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "product p ON ee.contentref=p.rowid";
    $sql .= ' WHERE e.entity IN ('.getEntity('product', 1).')';
    $sql .= " AND (e.ref LIKE '%".$searchkey."%' OR e.numversion LIKE '%".$searchkey."%')";
    $result = $db->query($sql);
    if($result) {
        $num = $db->num_rows($result);
        $i = 0;
        while($num && $i < $num) {
            $objp = $db->fetch_object($result);
            $optJson = array(
                'rowid'=>$objp->rowid,
                'value' => $objp->ref,
                'label' => $objp->ref.':'.$objp->label,
                'contentref' => $objp->contentref,
                'idcontentref' => $objp->idcontentref,
                'contentqty' => $objp->contentqty,
                'type' => $objp->type
            );
            $outarray[] = $optJson;
            $i++;
        }
    }
    else {
        dol_print_error($db);
    }
}
elseif(strpos($htmlname,'entrepot')) {
    $sql = "SELECT rowid,label,lieu";
    $sql .= " FROM " . MAIN_DB_PREFIX . "entrepot as p";
    $sql.= ' WHERE p.entity IN ('.getEntity('product', 1).')';
    $sql .= " AND (p.description LIKE '%".$searchkey."%' OR p.label LIKE '%".$searchkey."%' OR p.lieu LIKE '%".$searchkey."%')";
    $result = $db->query($sql);
    if($result) {
        $num = $db->num_rows($result);
        $i = 0;
        while($num && $i < $num) {
            $objp = $db->fetch_object($result);
            $optJson = array(
                'rowid'=>$objp->rowid,
                'value' => $objp->label,
                'label' => $objp->lieu,
            );
            $outarray[] = $optJson;
            $i++;
        }
    }
}
else {
    dol_print_error($db);
}

$db->close();

print json_encode($outarray);


