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
 * Vérifie les variables $_POST $_COOKIE et $_GET qui ont été regroupée
 * Chaque variable doit être connue et doit correspondre à un masque définit 
 * dans le fichier config.php
 * @param type $rediRectToIndex Est ce que je dois rediriger vers l'index
 */
function doSecurityCheck($rediRectToIndex = TRUE) {
  global $config;
  $stopExec = $config['stopOnExec'];
  foreach ($_GET as $key => $value) {
    if (isset($config['securite'][$key])) {
      switch ($config['securite'][$key]) {
        case 'int':
          $regexp = '^[0-9]+$';
          break;
        case 'alpha':
          $regexp = '^[[:alpha:]]+$';
          break;
        case 'ascii':
          $regexp = '^[[:ascii:]]+$';
          break;
        case 'digit':
          $regexp = '^[[:digit:]]+$';
          break;
        case 'alphanum':
          $regexp = '^[[:alnum:]]+$';
          break;
        case 'alphanum-_':
          $regexp = '^[[:alnum:]-_]+$';
          break;
        case 'mysqlChecked':
          $regexp = '.*';
          break;
        default:
          stopSession($rediRectToIndex, $stopExec, $extra = 'index.php?redirect=0&champs='.htmlentities($key));
          break;
      }
      if (!preg_match("/$regexp/", $value)) {
echo "$key-$value";
        stopSession($rediRectToIndex, $stopExec, $extra = 'index.php?redirect=0&champs='.htmlentities($key));
      }
    } else {
      stopSession($rediRectToIndex, $stopExec, $extra = 'index.php?redirect=0&champs='.htmlentities($key));
    }
  }
}
