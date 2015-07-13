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
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class dbInstallTest extends PHPUnit_Framework_TestCase {

  public function testInstallationDB() {
    global $db, $config, $adminPassword;
    dropDB();
    $resInit = initDB();
    $this->assertNotEquals(FALSE, $resInit);
    $resPass = $db->query("UPDATE ".$config['db']['prefix']."users SET password='$adminPassword' where login = 'adminlmondo';");
    $this->assertNotEquals(FALSE, $resPass);
    $result = $db->checkDB();
    reinitDB();
    $this->assertEquals(TRUE, $result);
  }

  public function testSiUneInsertionOuUneDeletionSePasseBien_AlorsLeRetourDeQueryNEstPasFalse() {
    global $config, $db;
    $prefix = $config['db']['prefix'];
    $resInsert = $db->query("INSERT INTO `" . $prefix . "config` VALUES ('testDbQuery', '0.1',0);");
    $this->assertNotEquals(FALSE, $resInsert);
    $resDelete = $db->query("DELETE FROM `" . $prefix . "config` where cle = 'testDbQuery';");
    $this->assertNotEquals(FALSE, $resDelete);
  }

  public function testIlNYAPasDeTablesRestantesApresUnDropDB() {
    global $config, $db;
    dropDB();
    $this->assertEquals(array(), preg_grep('/^' . str_replace('/', '\\/', $config['db']['prefix']) . '/', $db->fetchAll("show tables;", PDO::FETCH_COLUMN)));
    reinitDB();
  }

}
