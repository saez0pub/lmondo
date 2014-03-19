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

    $result = "<!DOCTYPE html>
<html lang=\"fr\">
  <head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <meta name=\"description\" content=\"\">
    <meta name=\"author\" content=\"\">
    <link rel=\"shortcut icon\" href=\"../assets/ico/favicon.ico\">

    <title>Lmondo</title>

    <!-- Bootstrap core CSS -->
    <link href=\"../css/bootstrap.min.css\" rel=\"stylesheet\">

    <!-- Custom styles for this template -->
    <link href=\"../css/main.css\" rel=\"stylesheet\">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>
      <script src=\"https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js\"></script>
    <![endif]-->
    
    <!-- Css added -->

  </head>

  <body>

    <div class=\"navbar navbar-inverse navbar-fixed-top\" role=\"navigation\">
      <div class=\"container\">
        <div class=\"navbar-header\">
          <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
            <span class=\"sr-only\">Toggle navigation</span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
          <a class=\"navbar-brand\" href=\"#\">Lmondo</a>
        </div>
        <div class=\"collapse navbar-collapse\">
          <ul class=\"nav navbar-nav\">
            <li class=\"active\"><a href=\"#\">Accueil</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class=\"container\">
";
    // Assert
    $this->assertEquals($result, $page->prepareHeader());
  }

  public function testFooterSansParametre() {
    // Arrange
    $page = new page(TRUE);

    $result = "
    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>
    <script src=\"../../js/bootstrap.min.js\"></script>
    <script src=\"../../js/main.js\"></script>
  </body>
</html>
";

    // Assert
    $this->assertEquals($result, $page->prepareFooter());
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
    $template = file_get_contents(dirname(__FILE__).'/templates/maintenance.html');
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
    $template = file_get_contents(dirname(__FILE__).'/templates/upgradeplz.html');
    $config['version'] = $oldVersion;
    $this->assertEquals($template, $result);
  }
}