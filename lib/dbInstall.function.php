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

function initDB() {
  global $config, $db;
  $prefix = $config['db']['prefix'];
  $return = TRUE;
  $res = $db->query("CREATE TABLE IF NOT EXISTS `" . $prefix . "users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `lastlogin` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL
  ) ;");
  if ($res === FALSE){
    $return = FALSE;
  }
  $res = $db->query("CREATE TABLE IF NOT EXISTS `" . $prefix . "config` (
  `cle` varchar(100) PRIMARY KEY,
  `valeur` varchar(100) NOT NULL
  ) ;");
  if ($res === FALSE){
    $return = FALSE;
  }
  $res = $db->query("INSERT INTO `" . $prefix . "config` VALUES ('version', '0.1');");
  return $return;
}

function dropDB() {
  global $config, $db;
  $prefix = $config['db']['prefix'];
  $sql = "DROP TABLE IF EXISTS `" . $prefix . "users`;";
  return $db->query($sql);
}
