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
          $sql = 'select lmondo_rules.id, lmondo_rules.nom from lmondo_rules left join lmondo_triggers' .
            ' on lmondo_rules.id = lmondo_triggers.args where lmondo_triggers.scenario_id IS NULL OR lmondo_triggers.scenario_id <> ' . $ligneOriginale['scenario_id'];
        } else {
          $select = '';
          $sql = 'select lmondo_rules.id, lmondo_rules.nom from lmondo_rules left join lmondo_triggers' .
            ' on lmondo_rules.id = lmondo_triggers.args where lmondo_triggers.scenario_id IS NULL OR lmondo_triggers.id = ' . $ligneOriginale['id'];
        }
        switch ($ligneOriginale['type']) {
          case 'reco':
            $rule = new rule();
            $res = $rule->fetchAll($sql);
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
          $rule = new rule();
          $res = $rule->getFromID($value);
          if ($res !== FALSE) {
            $return = $res['nom'];
          }
          break;
      }
    }
    return $return;
  }

}
