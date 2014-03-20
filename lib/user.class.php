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

class user {

  function __construct() {
    
  }

  function getFromDB($login = NULL, $password = NULL) {
    global $db, $config;
    if ($login == NULL) {
      if (isset($_GET['login'])) {
        $login = $_GET['login'];
      }
    }

    if ($password == NULL) {
      if (isset($_GET['password'])) {
        $password = md5($_GET['password']);
      } elseif (isset($_GET['passwordmd5'])) {
        $password = $_GET['passwordmd5'];
      }
    }

    $db->prepare("SELECT * from " . $config['db']['prefix'] . "users where login = :login and password = :password and enabled = 1");
    $db->bindParam(":login", $login);
    $db->bindParam(":password", $password);
    $res = $db->executeAndFetch();
    return $res;
  }

}
