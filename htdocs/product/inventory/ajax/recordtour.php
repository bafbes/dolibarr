<?php

/*
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
 *    \file       /htdocs/product/inventory/ajax/searchfrombarcode.php
 *    \brief      File to make Ajax action on product and stock
 */

if (!defined('NOTOKENRENEWAL')) {
    define('NOTOKENRENEWAL', 1); // Disables token renewal
}
if (!defined('NOREQUIREMENU')) {
    define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREHTML')) {
    define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
    define('NOREQUIREAJAX', '1');
}
if (!defined('NOREQUIRESOC')) {
    define('NOREQUIRESOC', '1');
}
if (!defined('NOCSRFCHECK')) {
    define('NOCSRFCHECK', '1');
}
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . "/product/class/product.class.php";
require_once DOL_DOCUMENT_ROOT . "/product/inventory/class/inventory.class.php";
require_once DOL_DOCUMENT_ROOT . "/product/stock/class/mouvementstock.class.php";

$action = GETPOST("action", "alpha");
$lineid = GETPOST("inventorylineid", "int");
$qty = GETPOST("qty", "int");
$inventory = new Inventory($db);
$response = "";

$langs->loadLangs(array("stocks", "other", "productbatch"));

$objectreturn = array();

if ($action == "recordtour") {
    if (!empty($lineid)) {
        $line = new InventoryLine($db);
        $line->fetch($lineid);
        $nbtours = $line->nb_tours + 1;
        $error = 0;
        $inventory->fetch($line->fk_inventory);
        $stockmovment = new MouvementStock($db);
        $stockmovment->setOrigin($inventory->element, $inventory->id);
        $product_static = new Product($db);
        $result = $product_static->fetch($line->fk_product, '', '', '', 1, 1, 1);
        $product_static->load_stock(); // Load stock_reel + stock_warehouse.
        $realqtynow = $product_static->stock_warehouse[$line->fk_warehouse]->real;
        if (!is_null($qty)) {
            $stock_movement_qty = price2num($qty - $realqtynow, 'MS');
            if ($stock_movement_qty != 0) {
                if ($stock_movement_qty < 0) {
                    $movement_type = 1;
                }
                else {
                    $movement_type = 0;
                }

                $datemovement = '';
                //$inventorycode = 'INV'.$inventory->id;
                $inventorycode = 'INV-' . $inventory->ref;

                $idstockmove = $stockmovment->_create($user, $line->fk_product, $line->fk_warehouse, $stock_movement_qty, $movement_type, 0, $langs->trans('LabelOfInventoryMovemement', $inventory->ref), $inventorycode);
                if ($idstockmove < 0) {
                    $error++;
                    setEventMessages($stockmovment->error, $stockmovment->errors, 'errors');
                }

                // Update line with id of stock movement (and the start quantity if it has changed this last recording)
                $sql = "UPDATE " . MAIN_DB_PREFIX . "inventorydet";
                $sql .= " SET fk_movement = $idstockmove,";
                $sql .= " qty_stock=$qty,qty_view=$qty,nb_tours=$nbtours";
                $sql .= " WHERE rowid = " . ((int)$lineid);
                $result = $db->query($sql);
                if (!$result) {
                    $error++;
                    setEventMessages($db->lasterror(), null, 'errors');
                }
            }
        }
        if ($result > 0 && !$error) {
            $response = array('status' => 'success', 'message' => 'Success on updating line', 'id_line' => $result);
        }
        else {
            $response = array('status' => 'error', 'errorcode' => 'ErrorUpdate', 'message' => "Error on line update");
        }
    }
    else {
        $response = array('status' => 'error', 'errorcode' => 'NoIdForInventory', 'message' => "No id for inventory");
    }
}

$response = json_encode($response);
echo $response;
