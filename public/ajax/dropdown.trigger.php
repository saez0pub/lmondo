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

$return = '<div id="args" name="args" class="danger">Nom implémenté</div>';

if (isset($_GET['type']) && isset($_GET['scenario_id'])) {
  $type = $_GET['type'];
  $scenario_id = $_GET['scenario_id'];
  if (!isset($_GET['valeur'])) {
    $valeur = $_GET['valeur'];
  } else {
    $valeur = '';
  }
  switch ($type) {
    case 'reco':
      $scenario = new scenario();
      if ($scenario->getFromID($scenario_id) !== FALSE) {
        $trigger = new trigger();
        $sql = 'select lmondo_rules.id, lmondo_rules.nom from lmondo_rules left join lmondo_triggers' .
          ' on lmondo_rules.id = lmondo_triggers.args where lmondo_triggers.scenario_id IS NULL OR lmondo_triggers.scenario_id <> ' . $scenario_id;
        $res = $trigger->fetchAll($sql);
        $return = '<select class="form-control" id="args" name="args"><option value="" ' . $select . '>Veuillez sélectionner</option>';
        foreach ($res as $key => $value) {
          if ($valeur == $value['id']) {
            $select = 'selected';
          } else {
            $select = '';
          }
          $return .='<option value="' . $value['id'] . '" ' . $select . '>' . htmlentities($value['nom']) . '</option> ';
        }
        $return .="</select>";
      } else {
        $return = '<div id="args" name="args" class="danger">Scénario non trouvé</div>';
      }
      break;

    default:

      break;
  }
}

echo $return;
