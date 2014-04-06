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
class user extends dbLmondo {

  /**
   * Permet de récupérer l'utilisateur à partir des données d'identifiant
   * @param string $login nom de l'utilisateur, $_GET['login'] si NULL
   * @param string $password mot de passe de l'utilisateur, $_GET['password'] 
   * si NULL et $_GET['passwordmd5'] si $_GET['password'] est NULL
   * @return array données de l'utilisateur
   */
  function getFromLogin($login = NULL, $password = NULL) {
    global $config;
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

    $this->prepare("SELECT * from " . $config['db']['prefix'] . "users where login = :login and password = :password and enabled = 1");
    $this->bindParam(":login", $login);
    $this->bindParam(":password", $password);
    $res = $this->executeAndFetch();
    return $res;
  }

  function getUserMenu() {
    global $config;

    $res = '';
    $this->prepare("SELECT id,nom,link from " . $config['db']['prefix'] . "menu where level = 0 order by ordre" );
    $topMenu = $this->executeAndFetchAll();
    foreach ($topMenu as $value) {
      $tmp = Array();
      $id = $value['id'];
      $this->prepare("SELECT id,nom,link from " . $config['db']['prefix'] . "menu where parent = :id and level = 1 order by ordre");
      $this->bindParam(":id", $id);
      $subMenu = $this->executeAndFetchAll();
      if ($subMenu === false || sizeof($subMenu) <= 0) {
        $res[$value['nom']] = $value['link'];
      } else {
        foreach ($subMenu as $value2) {
          $tmp[$value2['nom']] = $value2['link'];
        }
        $res[$value['nom']] = $tmp;
      }
    }

    return $res;
  }

}
