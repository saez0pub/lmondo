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
    $this->assertNotEquals(FALSE, $result);
  }

  public function testProblemeDeConnexionMySQL() {
    global $config;
    $oldUser = $config['db']['user'];
    $config['db']['user'] = 'nePeutPasExisterSinonLeTestSeraPlanté';
    $db = new dbLmondo();
    $result = $db->getConnexion();
    $this->assertFalse($result);
    $config['db']['user'] = $oldUser;
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
    $oldPort = $config['db']['port'];
    $config['db']['port'] = '42';
    $oldHost = $config['db']['host'];
    //Je mets 127.0.0.1 pour éviter de passer par le socket et ainsi, 
    //je peux provoquer une erreur en mettant un port bidon.
    $config['db']['host'] = '127.0.0.1';
    $db = new dbLmondo;
    $result = $db->getErrorCode();
    $config['db']['port'] = $oldPort;
    $config['db']['host'] = $oldHost;
    $db = NULL;
    $this->assertEquals(LMONDO_DB_ERR_CONN, $result['code']);
  }

  public function testUserMysqlNePeutPasSeConnecter() {
    global $config;
    $oldUser = $config['db']['user'];
    $config['db']['user'] = 'nePeutPasExisterSinonLeTestSeraPlanté';
    $db = new dbLmondo;
    $result = $db->getErrorCode();
    $this->assertEquals(LMONDO_DB_ERR_USER, $result['code']);
    $config['db']['user'] = $oldUser;
  }

  public function testSiJeFaisUnSelectWhereAndOr_AlorsLeResultatEstBon() {
    $db = new dbLmondo('requete_test');
    initTestTable();
    $db->select('id,a1,a2,a3')
      ->addWhere('a1')
      ->addWhere('a2')
      ->addWhere('a2', '=', 'or', 'a22')
      ->addWhere('a4', '<');
    $db->prepare();
    $db->bindParam('a1', '1');
    $db->bindParam('a2', '1');
    $db->bindParam('a22', '1');
    $db->bindParam('a4', '6');
    $result = $db->executeAndFetchAll();
    $expected = Array(
        0 => Array('id' => 1, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        1 => Array('id' => 6, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        2 => Array('id' => 7, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        3 => Array('id' => 8, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        4 => Array('id' => 9, 'a1' => 1, 'a2' => 1, 'a3' => 2,),
        5 => Array('id' => 11, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        6 => Array('id' => 13, 'a1' => 1, 'a2' => 1, 'a3' => 3,),
        7 => Array('id' => 14, 'a1' => 1, 'a2' => 1, 'a3' => 1,),
        8 => Array('id' => 15, 'a1' => 1, 'a2' => 1, 'a3' => 1,)
    );
    $this->assertEquals($expected, $result);
  }

  public function testJePeuxRecupererLeNomDesColonnes() {
    $db = new dbLmondo('reco');
    $db->select('id,nom');
    $db->showColumn('id');
    $result = $db->getColumns();
    $expected = Array(0 => 'id', 1 => 'nom');
    $this->assertEquals($expected, $result);
  }

  public function testLaFonctionGetTablesEstCoherente() {
    $db = new dbLmondo('requete_test');
    $db->setEdit(false);
    $result = $db->getTable();
    $expected = file_get_contents(dirname(__FILE__) . '/templates/table_test.html');
    $this->assertEquals($expected, $result);
  }

  public function testJePeuxModifierUnAlias() {
    $db = new dbLmondo('requete_test');
    $result = $db->setColumnAlias('a1', 'Salut les copains !');
    $this->assertEquals('Salut les copains !', $result['a1']);
  }

  public function testJePeuxFaireUnBindPuisDemanderLaTable() {
    $db = new dbLmondo('requete_test');
    initTestTable();
    $db->select('id,a1,a2')
      ->addWhere('a3');
    $db->prepare();
    $db->bindParam('a3', '1');
    $expected = file_get_contents(dirname(__FILE__) . '/templates/table_test_1.html');
    $db->setEdit(false);
    $this->assertEquals($expected, $db->getTable());
  }
  
  public function testJePeuxBloquerLaModificationDEntrees() {
    $db = new testTable;
    initTestTable();
    $expected = file_get_contents(dirname(__FILE__) . '/templates/table_test_locked.html');
    $this->assertEquals($expected, $db->getTable());
  }

}

class testTable extends dbLmondo {

  function __construct() {
    parent::__construct('requete_test');
    $this->setChampId('id');
    $this->readOnlyKey = array('1',15);
  }

}