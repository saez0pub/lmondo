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
class dbTest extends PHPUnit_Framework_TestCase {

  public function testLaConnexionMySQLEstOK() {
    //Il vaut mieux valider que les conditions de tests soient bonnes
    global $db;
    $result = $db->getConnexion();
    $this->assertNotEquals(false,$result);
  }

  public function testProblemeDeConnexionMySQL() {
    global $config;
    $config['db']['user'] = 'nePeutPasExisterSinonLeTestSeraPlanté';
    $db = new dbLmondo;
    $result = $db->getConnexion();
    $this->assertFalse($result);
  }

  public function testExtractionSQLState() {
    global $db;
    $code = 4242424242;
    $msgTestNormal = "SQLSTATE[$code] [824894] lorem ipsum";
    $this->assertEquals($code, $db->getErrorFromConnexionErrorMessage($msgTestNormal));
    $msgTestKO = "lorem ipsum";
    $this->assertEquals(NULL, $db->getErrorFromConnexionErrorMessage($msgTestKO));
  }

  public function testMySQLNonDemarre() {
    global $config;
    $config['db']['port'] = 'nepeutxister.local.mamachine42';
    $db = new dbLmondo;
    $result = $db->getErrorCode();
    $this->assertEquals($result['code'], LMONDO_DB_ERR_CONNECT);
  }

  public function testUserMysqlNePeutPasSeConnecter() {
    global $config;
    $config['db']['user'] = 'nePeutPasExisterSinonLeTestSeraPlanté';
    $db = new dbLmondo;
    $result = $db->getErrorCode();
    $this->assertEquals($result['code'], LMONDO_DB_ERR_CONNECT);
  }

}
