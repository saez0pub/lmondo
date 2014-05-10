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


if ($ligne = $target->getFromID($id)) {
  $colonnes = $target->getColumns();
  echo '
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form">
        ';
  foreach ($colonnes as $colonne) {
    if ($colonne != $champsId){
      echo '
          <div class="form-group">
            <label for="input'.$colonne.'">'.$target->getColumnName($colonne).'</label>
            <input type="text" class="form-control" id="input'.$colonne.'" value="'.$ligne[$colonne].'">
          </div>';
    }
  }
  echo '
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>';
}