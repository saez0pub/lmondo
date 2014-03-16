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

//Vérou supplémentaire,impossible d'appeler l'installation depuis un serveur Web
if (isset($_SERVER['HTTP_HOST'])) {
  die();
}

function initDB() {
  global $config, $db;
  $prefix = $config['db']['prefix'];
  $return = TRUE;
  $sql = str_replace('$prefix$', $prefix, file_get_contents('../var/DB/installDB.sql'));
  $res = $db->query($sql);
  if ($res === FALSE) {
    $return = FALSE;
  }
  /*
   * @todo mettre en oeuvre un upgrade quand ce sera necessaire
   */
  return $return;
}

function dropDB() {
  /*
   * @todo automatiser la suppression des tables avec un preg match de show tables
   */
  global $config, $db;
  $return = TRUE;
  $listeTables = $db->fetchAll("show tables;", PDO::FETCH_COLUMN);
  foreach ($listeTables as $table) {
    if (preg_match('/^' . str_replace('/', '\\/', $config['db']['prefix']) . '/', $table)) {
      $sql = "DROP TABLE IF EXISTS `" . $config['db']['name'] . '`.`' . $table . "`;";
      $db->prepare($sql);
      $res = $db->execute();
      if ($res === FALSE) {
        $return = FALSE;
      }
    }
  }
  return $return;
  ;
}
