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
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));


include_once '../lib/common.php';
include_once '../lib/dbInstall.function.php';

$config['serverUrl'] = 'http://localhost:8000/';
$config['db']['prefix'] = 'tests_todelete_' . $config['db']['prefix'];
//Nettoyage des précedents tests en cas d'interuption
dropDB();
initDB();
foreach (scandir('.') as $file) {
  if (preg_match('/^test.*.php$/', $file)) {
    echo "Include $file\n";
    include $file;
  }
}
/**
 * @todo faire un drop des tables tests
 */
dropDB();