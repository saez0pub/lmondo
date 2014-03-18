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
 * @author saez0pub
 */
global $adminPassword;
global $cookieTest;

//Sur une installation fraiche, il faut un mot de passe pour l'admin
$_POST["password"] = time() + rand(0, 2000);
$_POST["password"]= 'adminlmondo';
$adminPassword = md5($_POST["password"]);
$_POST["login"] = "adminlmondo";

include_once dirname(__FILE__) . '/../lib/common.php';
include_once dirname(__FILE__) . '/../lib/dbInstall.function.php';
$config['serverUrl'] = 'http://localhost:8000/';
$config['db']['prefix'] = 'tests_todelete_' . $config['db']['prefix'];
reinitDB();
initLogin();
startSession();
/**
 * @todo faire un drop des tables tests
 */
foreach (scandir('.') as $file) {
  if (preg_match('/^test.*.php$/', $file)) {
    echo "Include $file\n";
    include dirname(__FILE__) . '/' . $file;
  }
}
register_shutdown_function(function() {
  global $cookieTest;
  dropDB();
  unlink($cookieTest);
});

function reinitDB() {
  global $db, $config, $adminPassword;
  //Nettoyage des précedents tests en cas d'interuption
  dropDB();
  initDB();
  return $db->query("UPDATE " . $config['db']['prefix'] . "users SET password='$adminPassword' where login = 'adminlmondo';");
}

function initLogin() {
  global $config, $cookieTest;
  $post_array = array('login' => $_POST["login"], 'password' => $_POST["password"]);
  $cookieTest = tempnam("/tmp", "COOKIE");
  $ch = curl_init($config['serverUrl']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieTest);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
  $retour = curl_exec($ch);
  curl_close($ch);
  $user = new user();

  //Initialisation de la session
  $_SESSION[$config['sessionName']]['user'] = $user->getFromDB($_POST["login"], $_POST["password"]);
}
