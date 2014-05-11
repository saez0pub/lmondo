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

  public function getColumnInput($colonne, $valeur) {
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
          $return .='<option value="' . $key . '" ' . $select . '>' . htmlentities($value) . '</option> ';
        }
        $return .="</select>";
        break;
      case 'argsa':
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
          $return .='<option value="' . $key . '" ' . $select . '>' . htmlentities($value) . '</option> ';
        }
        $return .="</select>";
        break;
      default:
        break;
    }
    return $return;
  }

  public function getAdditionalColumn($name, $id, $value = '') {
    global $config;
    if (isset($config['triggers'][$value])) {
      $value = $config['triggers'][$value];
    }
    return parent::getAdditionalColumn($name, $id, $value);
  }

}
