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
class action extends dbLmondo {

  function __construct() {
    parent::__construct('actions');
    $this->column['nom'] = 'Nom de l\'action';
    $this->column['command'] = 'Commande';
    $this->column['type'] = 'Type';
    $this->column['args'] = 'Paramètres';
  }

  public function getAdditionalColumn($name, $id, $value = '') {
    global $config;
    $return = parent::getAdditionalColumn($name, $id, $value);

    switch ($name) {
      case 'type':
        $action = new action();
        $res = $action->getFromID($id);
        if (isset($config['actions'][$res['type']])) {
          $return = $config['actions'][$res['type']];
        }
    }
    return $return;
  }

  public function getColumnInput($colonne, $valeur) {
    global $config;
    $return = parent::getColumnInput($colonne, $valeur);
    switch ($colonne) {
      case 'type':
        $return = '<select class="form-control" id="' . $colonne . '" name="' . $colonne . '">';
        foreach ($config['actions'] as $key => $value) {
          $return .='<option value="'.$key.'">'.htmlentities($value).'</option> ';
        }
        $return .="</select>";
        break;
      default:
        break;
    }
    return $return;
  }

}
