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

/**
 * Class Utilisateur
 */
class scenario extends dbLmondo {

  function __construct() {
    parent::__construct('scenarios');
    $this->column['nom'] = 'Nom du scenario';
    $this->additionalColumns = array(
        'triggers',
    );
  }

  public function getColumnName($colonne) {
    $colonne = parent::getColumnName($colonne);
    switch ($colonne) {
      case 'triggers':
        $colonne = 'Déclencheurs';
        break;
    }
    return $colonne;
  }

  public function getAdditionalColumn($name, $id, $value = '') {
    global $config, $db;
    $return = parent::getAdditionalColumn($name, $id, $value);

    switch ($name) {
      case 'triggers':
        //$modalTarget = '../ajax/modal.php?table=trigger&champs=scenario_id&scenario_id=';
        //$return = '<a type="button" class="btn btn-default" data-toggle="modal" href="' . $modalTarget . $id . '" data-target="#myModal"><span class="glyphicon glyphicon-link"></span></a>';
        $trigger = new trigger();
        $trigger->updateModalTarget('../ajax/modal.trigger.php?scenario_id=' . $id);
        $trigger->hideColumn('scenario_id');
        $trigger->select('id,type,args')
          ->addWhere('scenario_id');
        $trigger->prepare();
        $trigger->bindParam('scenario_id', $id);
        $return .= $trigger->getTable();
        break;
      case 'action':
        $action = new action();
        $res = $action->getFromID($value);
        $return = $res['nom'];
        break;
    }


    return $return;
  }

  public function deleteHook($id) {
    global $config;
    $return = FALSE;
    $triggers = new trigger();
    foreach ($triggers->getFromScenarioID($id) as $value) {
      if ($value['type'] == 'reco') {
        if ($triggers->delete($value['args']) === FALSE) {
          $return = FALSE;
        }
      }
    }
    return $return;
  }

  public function getColumnInput($colonne, $valeur = '') {
    global $config;
    $return = parent::getColumnInput($colonne, $valeur);
    switch ($colonne) {
      case 'action':
        $action = new action();
        $res = $action->fetchAll();
        if (empty($valeur)) {
          $select = 'selected';
        } else {
          $select = '';
        }
        $return = '<select class="form-control" id="' . $colonne . '" name="' . $colonne . '"><option value="" ' . $select . '>Veuillez sélectionner</option>';
        foreach ($res as $key => $value) {
          if ($valeur == $value['id']) {
            $select = 'selected';
          } else {
            $select = '';
          }
          $return .='<option value="' . $value['id'] . '" ' . $select . '>' . htmlentities($value['nom']) . '</option> ';
        }
        $return .="</select>";
        break;
      default:
        break;
    }
    return $return;
  }

}
