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

function doSecurityCheck($rediRectToIndex = TRUE) {
  global $config;
  $stopExec = $config['stopOnExec'];
  foreach ($_GET as $key => $value) {
    if (isset($config['securite'][$key])) {
      switch ($config['securite'][$key]) {
        case 'alpha':
          $regexp = '[[:alpha:]]+';
          break;
        case 'ascii':
          $regexp = '[[:ascii:]]+';
          break;
        case 'digit':
          $regexp = '[[:digit:]]+';
          break;
        case 'alphanum':
          $regexp = '[[:alnum:]]+';
          break;
        case 'mysqlChecked':
          $regexp = '.*';
          break;
        default:
          stopSession();
          break;
      }
      if (!preg_match("/$regexp/", $value)) {
        stopSession();
      }
    } else {
      stopSession($rediRectToIndex, $stopExec);
    }
  }
}
