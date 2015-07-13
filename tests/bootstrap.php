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
global $pid;

//Sur une installation fraiche, il faut un mot de passe pour l'admin
//Pour que les tests fonctionnent, il faut l'applicatif qui tourne et 
//l'utilisateur adminlmondo ainsi que son mot de passe valide
//$_GET["password"] = time() + rand(0, 2000);
$_GET["password"] = 'adminlmondo';
$adminPassword = md5($_GET["password"]);
$_GET["login"] = "adminlmondo";

include dirname(__FILE__) . '/../lib/common.php';
include_once dirname(__FILE__) . '/../lib/dbInstall.function.php';
$host = "localhost";
$port = 8000;
$docRoot = "../public/";
$config['serverUrl'] = "http://$host:$port/";
$config['db']['prefix'] = 'tests_todelete_' . $config['db']['prefix'];

$command = sprintf(
  'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!', $host, $port, $docRoot
);

$output = array();
exec($command, $output);
$pid = (int) $output[0];

echo sprintf(
  '%s - Web server started on %s:%d with PID %d', date('r'), $host, $port, $pid
) . PHP_EOL;

//On va éviter d'écraser ou supprimer la conf en place
$config['input']['config'] = tempnam("/tmp", "listenerLmondo");
$config['input']['grammar'] = tempnam("/tmp", "grammar");

//Les tests ne doivent pas être interompus
//$config['stopOnExec'] = FALSE;
//Utilisé pour redirections ou autres
//Ne doit pas être positionné avant car il planterait l'installation de base 
//de données
$_SERVER['HTTP_HOST'] = 'localhost';

reinitDB();
initLogin();
startSession();
foreach (scandir('.') as $file) {
  if (preg_match('/^test.*.php$/', $file)) {
    include dirname(__FILE__) . '/' . $file;
  }
}

function reinitDB() {
  global $db, $config, $adminPassword;
  //Nettoyage des précedents tests en cas d'interuption
  dropDB();
  initDB();
  $ret = $db->query("UPDATE " . $config['db']['prefix'] . "users SET password='$adminPassword' where login = 'adminlmondo';");
  return $ret;
}

function initLogin() {
  global $config, $cookieTest;
  $post_array = array('login' => $_GET["login"], 'password' => $_GET["password"], 'remember-me' => 1);
  $cookieTest = tempnam("/tmp", "COOKIE");
  $ch = curl_init($config['serverUrl']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieTest);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
  $retour = curl_exec($ch);
  curl_close($ch);
  $user = new user();
  //Initialisation de la session
  $_SESSION[$config['sessionName']]['user'] = $user->getFromLogin($_GET["login"], $_GET["password"]);
}

function initTestTable() {
  global $db, $config;
  $db->query("DROP TABLE IF EXISTS " . $config['db']['prefix'] . "requete_test ");
  $db->query("CREATE TABLE " . $config['db']['prefix'] . "requete_test (
      `id` int(11) NOT NULL AUTO_INCREMENT,      
      `a1` varchar(100) NOT NULL,
      `a2` varchar(100) NOT NULL,
      `a3` varchar(100) NOT NULL,
      `a4` varchar(100) NOT NULL,
      PRIMARY KEY (`id`))");
  $db->query("INSERT INTO " . $config['db']['prefix'] . "requete_test VALUES
      (NULL,1,1,1,1),
      (NULL,2,2,2,2),
      (NULL,3,3,3,3),
      (NULL,4,4,4,4),
      (NULL,5,5,5,5),
      (NULL,1,1,1,2),
      (NULL,1,1,1,3),
      (NULL,1,1,1,4),
      (NULL,1,1,2,1),
      (NULL,1,2,1,1),
      (NULL,1,1,1,6),
      (NULL,1,3,1,1),
      (NULL,1,1,3,1),
      (NULL,1,1,1,3),
      (NULL,1,1,1,1)
    ");
}

register_shutdown_function(function() {
  global $cookieTest, $config, $pid;
  dropDB();
  unlink($cookieTest);
  unlink($config['input']['config']);
  unlink($config['input']['grammar']);
  echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
  exec('kill ' . $pid);
});
