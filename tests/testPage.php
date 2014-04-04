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
class pageTest extends PHPUnit_Framework_TestCase {

  /**
   * @todo tester un session correcte
    public function testAuthAveclogin() {
    $page = new page();
    $result=$page->testAuth();
    $this->assertEquals(true, $result);
    }
   * 
   */
  public function testHeaderSansParametre() {
    // Arrange
    $page = new page(TRUE);
    $template = file_get_contents(dirname(__FILE__) . '/templates/header.html');

    // Assert
    $this->assertEquals($template, $page->prepareHeader());
  }

  public function testFooterSansParametre() {
    // Arrange
    $page = new page(TRUE);

    $template = file_get_contents(dirname(__FILE__) . '/templates/footer.html');

    // Assert
    $this->assertEquals($template, $page->prepareFooter());
  }

  public function testRetourIndex() {
    global $config;
    $ch = curl_init($config['serverUrl']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);

    //Le code retour curl OK est 0
    $this->assertEquals(curl_errno($ch), 0);
    $info = curl_getinfo($ch);

    //Il vaut mieux vérifier que le code est 200, un 301 ou autre n'est pas normal
    $this->assertEquals(200, $info['http_code']);
    curl_close($ch);
  }

  public function testLaconnexionBddestKO_laPageDeMaintenanceEstAffichée() {
    global $config, $db;
    $oldUser = $config['db']['user'];
    $config['db']['user'] = 'nePeutPasExisterSinonLeTestSeraPlanté';
    $db = new dbLmondo;
    $page = new page(TRUE);
    $result = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/maintenance.html');
    $config['db']['user'] = $oldUser;
    $db = new dbLmondo;
    $this->assertEquals($template, $result);
  }

  public function testLaVersionEstMauvaise_UnUpgradeEstNecessaire() {
    global $config;
    $oldVersion = $config['version'];
    $config['version'] = '4242424242';
    $page = new page(TRUE);
    $page->prepareHeader(FALSE);
    $result = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/upgradeplz.html');
    $config['version'] = $oldVersion;
    $this->assertEquals($template, $result);
  }

  public function testJAiLIndexParDefaut() {
    $page = new page(TRUE);
    $page->prepareHeader(FALSE);
    $result = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/index_vide_sans_menu.html');
    $this->assertEquals($template, $result);
  }

  public function testJAiLIndexParDefautViaCurlApresLeLogin() {
    global $config;
    $ch = curl_init($config['serverUrl']);
    $post_array = array('login' => $_GET["login"], 'password' => $_GET["password"], 'remember-me' => 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    $template = file_get_contents(dirname(__FILE__) . '/templates/index.html');
    $this->assertEquals($template, $result);
  }

  public function testSimaSessionRenseigneunMenu_AlorsJaiUnMenu() {
    global $config;
    $page = new page(TRUE);
    $_SESSION[$config['sessionName']]['menu'] = array('test' => 'toto');
    $result = $page->prepareHeader(TRUE);
    $template = file_get_contents(dirname(__FILE__) . '/templates/header_sans_dropdown.html');
    $this->assertEquals($template, $result);
    $_SESSION[$config['sessionName']]['menu'] = array('test' => array('tata' => 'tutu'));
    $result = $page->prepareHeader(TRUE);
    $template = file_get_contents(dirname(__FILE__) . '/templates/header_avec_dropdown.html');
    $this->assertEquals($template, $result);
  }
}