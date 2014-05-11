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

$champsId = @$_GET['champs'];
if (!isset($_GET[$champsId])) {
  $id = 0;
} else {
  $id = $_GET[$champsId];
}
include_once dirname(__FILE__) . '/../../lib/common.php';

$table = $_GET['table'];
$champsId = $_GET['champs'];
$id = $_GET[$champsId];
if (!in_array($table, $config['allowed_modals'])) {
  stopSession();
}
eval("\$target = new $table();");
$target->setChampId($champsId);
$ligne = $target->getFromID($id);
$colonnes = $target->getColumns();
if (empty($id)) {
  foreach ($colonnes as $value) {
    $ligne[$value] = '';
  }
  $titre = 'Nouvelle entrée';
  $action = '../ajax/insert.php';
} else {
  $titre = 'Modification ID ' . $id;
  $action = '../ajax/update.php';
}
if ($ligne !== FALSE && $target->canEdit()) {
  echo '
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">' . $titre . '</h4>
      </div>
      <div class="modal-body">
        <form id="modalForm" role="form">
        ';
  echo '<input type="hidden" id="inputTable" name="inputTable" value="' . $table . '" />';
  if (!empty($id)) {
    echo '<input type="hidden" id="inputChamps" name="inputChamps" value="' . $champsId . '" />'
    . '<input type="hidden" id="input' . $champsId . '" name="input' . $champsId . '" value="' . $id . '" />';
  }
  foreach ($colonnes as $colonne) {
    if ($colonne != $champsId) {
      echo '
          <div class="form-group">
            <label for="' . $colonne . '">' . $target->getColumnName($colonne) . '</label>
            ' . $target->getColumnInput($colonne, $ligne[$colonne]) . '
          </div>';
    }
  }
  echo '
        </form>
      </div>
      <div class="modal-footer">
        ';
  if (!empty($id)) {
    echo '<button type="button" class="btn btn-danger save" href="../ajax/delete.php">Supprimer</button>';
  }
  echo '<button type="button" class="btn btn-primary save" href="' . $action . '">Enregistrer</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>';
}