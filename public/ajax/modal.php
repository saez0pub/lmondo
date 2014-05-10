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

$table = $_GET['table'];
$champsId = $_GET['champs'];
$id = $_GET['id'];
eval("\$target = new $table();");

$ligne = $target->getFromID($id);
if ($ligne !== FALSE && $target->canEdit()) {
  $colonnes = $target->getColumns();
  echo '
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modification ID '.$id.'</h4>
      </div>
      <div class="modal-body">
        <form id="modalForm" role="form">
        ';
  echo '<input type="hidden" id="inputTable" name="inputTable" value="'.$table.'" />'
    . '<input type="hidden" id="inputId" name="inputId" value="'.$id.'" />';
  foreach ($colonnes as $colonne) {
    if ($colonne != $champsId){
      echo '
          <div class="form-group">
            <label for="'.$colonne.'">'.$target->getColumnName($colonne).'</label>
            <input type="text" class="form-control" id="'.$colonne.'" name="'.$colonne.'" value="'.htmlentities($ligne[$colonne]).'" />
          </div>';
    }
  }
  echo '
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary save" href="../ajax/update.php">Enregistrer</button>
      </div>
    </div>';
}