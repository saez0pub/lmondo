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
class trigger extends dbLmondo {

  function __construct() {
    parent::__construct('triggers');
    $this->column['type'] = 'Type';
    $this->column['args'] = 'Paramètres';
  }

  public function getColumnInput($colonne, $valeur = '', $ligneOriginale = Array(), $but = 'new') {
    global $config;
    $return = parent::getColumnInput($colonne, $valeur);
    switch ($colonne) {
      case 'type':
        if (empty($valeur)) {
          $select = 'selected';
        } else {
          $select = '';
        }
        $return = '<select class="form-control" id="' . $colonne . '" name="' . $colonne . '"><option value="" ' . $select . '>Veuillez sélectionner</option>';
        foreach ($config['triggers'] as $key => $value) {
          if ($valeur == "$key") {
            $select = 'selected';
          } else {
            $select = '';
          }
          $return .='<option value="' . $key . '" ' . $select . '>' . htmlentities($value['name']) . '</option> ';
        }
        $return .="</select>";
        break;
      case 'args':
        if ($but == 'new') {
          $select = 'selected';
          $sql = 'select lmondo_reco.id, lmondo_reco.nom from lmondo_reco left join lmondo_triggers' .
            ' on lmondo_reco.id = lmondo_triggers.args where lmondo_triggers.scenario_id IS NULL OR lmondo_triggers.scenario_id <> ' . $ligneOriginale['scenario_id'];
        } else {
          $select = '';
          $sql = 'select lmondo_reco.id, lmondo_reco.nom from lmondo_reco left join lmondo_triggers' .
            ' on lmondo_reco.id = lmondo_triggers.args where lmondo_triggers.scenario_id IS NULL OR lmondo_triggers.id = ' . $ligneOriginale['id'];
        }
        switch ($ligneOriginale['type']) {
          case 'reco':
            $reco = new reco();
            $res = $reco->fetchAll($sql);
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
            break;
          default:
            break;
        }
        break;
      default:
        break;
    }
    return $return;
  }

  public function getAdditionalColumn($name, $id, $value = '') {
    global $config;
    if (isset($config['triggers'][$value]['name'])) {
      $value = $config['triggers'][$value]['name'];
    }
    $return = parent::getAdditionalColumn($name, $id, $value);
    if ($name == 'args') {
      $trigger = new trigger();
      $ligne = $trigger->getFromID($id);
      switch ($ligne['type']) {
        case 'reco':
          $reco = new reco();
          $res = $reco->getFromID($value);
          if ($res !== FALSE) {
            $return = $res['nom'];
          }
          break;
      }
    }
    return $return;
  }

  public function insertHook($columns) {
    $return = FALSE;
    if ($columns['type'] == 'reco') {
      $return = updateRecoSettingDbIfNeeded();
    } else {
      $return = TRUE;
    }
    return $return;
  }

  public function updateHook($id, $columns, $ligne) {
    $return = FALSE;
    if ($columns['type'] == 'reco') {
      foreach ($columns as $key => $value) {
        if ($ligne[$key] !== $value) {
          $dump = TRUE;
        }
      }
      if ($dump) {
        $return = updateRecoSettingDbIfNeeded();
      } else {
        $return = TRUE;
      }
    } else {
      $return = TRUE;
    }
    return $return;
  }

  public function deleteHook($id) {
    $return = FALSE;
    $setting = new setting();
    $toDelete = $this->getFromID($id);
    if ($toDelete['type'] == 'reco') {
      $return = updateRecoSettingDbIfNeeded();
    } else {
      $return = TRUE;
    }
    return $return;
  }

  public function getFromScenarioID($id) {
    $return = FALSE;
    if ($this->table !== NULL) {
      $this->prepare("SELECT " . $this->select . " from " . $this->table . " where scenario_id = :id");
      $this->bindParam('id', $id);
      $return = $this->executeAndFetchAll();
    }
    return $return;
  }

}
