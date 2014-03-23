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

global $config;

//Hack POST overrides GET
foreach ($_COOKIE as $key => $value) {
  $_POST[$key] = $value;
}
foreach ($_POST as $key => $value) {
  $_GET[$key] = $value;
}
//Versionapplicative
$config['version'] = '0.1';

//DB Connexion
$config['db']['user'] = 'lmondo';
$config['db']['pass'] = 'IlVaudraitMieuxLeChanger';
$config['db']['host'] = 'localhost';
$config['db']['port'] = '3306';
$config['db']['name'] = 'lmondo';
$config['db']['prefix'] = 'lmondo_';

//Nom de la session
$config['sessionName'] = 'lmondo';

//Expiration des cookies
//Par défaut : 1 mois
$config['cookieTime'] = time() + 60 * 60 * 24;

//Est ce que l'on arrête le chargepment de la page en cas de problème dur les 
//variables postées ? recommendé : TRUE 
//FALSE est surtout utilisé pour les tests Unitaires
$config['stopOnExec'] = TRUE;

//Configuration des vérifications de sécurité
$config['securite']['login'] = 'alphanum';
$config['securite']['password'] = 'mysqlChecked';
$config['securite']['passwordmd5'] = 'alphanum';
$config['securite']['PHPSESSID'] = 'alphanum';
$config['securite']['remember-me'] = 'digit';
$config['securite']['XDEBUG_SESSION_START'] = 'alphanum';
$config['securite']['XDEBUG_SESSION_STOP_NO_EXEC'] = 'alphanum';
