<?php

/*
 * Copyright (C) 2014 saez0pub
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

include_once dirname(__FILE__) . '/../../lib/common.php';

$table = $_GET['inputTable'];
$id = $_GET['inputId'];
eval("\$target = new $table();");
$update = array();
$ligne = $target->getFromID($id);
if ($ligne !== FALSE && $target->canEdit()) {
  $colonnes = $target->getColumns();
  foreach ($colonnes as $col) {
    echo " $col\n";
    if (isset($_GET[$col]) && $ligne[$col] !== $_GET[$col]) {
      $update[$col] = $_GET[$col];
    }
  }
  if (sizeof($update) > 0) {
    if ($target->update($id, $update) !== FALSE) {
      addMessageAfterRedirect("ID $id Modifié");
    }  else {
      addMessageAfterRedirect("ID $id non Modifié", 'warning');
    }
  }
}
