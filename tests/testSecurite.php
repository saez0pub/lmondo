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
class testSecurite extends PHPUnit_Framework_TestCase {

  public function testSijePOSTUneVariableNonReferencee_AlorsMaSessionEstDetruite() {
    global $config;
    $oldSession = $_SESSION[$config['sessionName']];
    $oldConfig = $config;
    $config['stopOnExec'] = FALSE;
    $_GET['nePeutExister'] = 1;
    //Il faut désactiver le header de redirection car il n'est pas testé ici et
    //provoquerait une erreur sur les headers qui ne peuvent pas être envoyés
    doSecurityCheck(FALSE, FALSE);
    $this->assertEquals(array(), $_SESSION);
    $_SESSION[$config['sessionName']] = $oldSession;
    $oldConfig = $config;
  }

  public function testSijePOSTUneVariableNonRefencee_AlorsJeSuisRedirigeSurLeLogin() {
    global $config;
    $ch = curl_init($config['serverUrl']);
    $post_array = array('login' => $_GET["login"], 'password' => $_GET["password"], 'remember-me' => 1, 'ExistePas' => 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $result = curl_exec($ch);
    $template = file_get_contents(dirname(__FILE__) . '/templates/login.html');
    $this->assertEquals($template, $result);
  }
  
  public function testSijePOSTUneVariableQuiNeRespectePasleMasqueDefinit_AlorsJeSuisRedirigeSurLeLogin() {
    global $config;
    $ch = curl_init($config['serverUrl']);
    $post_array = array('login' => $_GET["login"], 'password' => $_GET["password"], 'remember-me' => 'a');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $result = curl_exec($ch);
    $template = file_get_contents(dirname(__FILE__) . '/templates/login.html');
    $this->assertEquals($template, $result);
  }

}
