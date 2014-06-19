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
class setting extends dbLmondo {

  function __construct() {
    parent::__construct('config');
    $this->setChampId('cle');
    $this->readOnlyKey = array('version', 'reco_settings_db', 'reco_settings_disk');
    $this->hideColumns = array('dump_to_listener');
  }

  public function updateHook($id, $columns, $ligne) {
    $dump = FALSE;
    foreach ($columns as $key => $value) {
      if ($ligne['dump_to_listener'] == 1 && $ligne[$key] != $value) {
        $dump = TRUE;
      }
    }
    if ($dump) {
      $cur = $this->getFromID('reco_settings_db');
      if ($cur !== FALSE) {
        $cur['valeur'] ++;
        if ($this->update('reco_settings_db', array('valeur' => $cur['valeur'])) !== FALSE) {
          $return = TRUE;
        }
      }
    } else {
      $return = TRUE;
    }

    return TRUE;
  }

}
