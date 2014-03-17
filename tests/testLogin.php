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
class testLogin extends PHPUnit_Framework_TestCase {

  public function testApresUneInstallationJAfficheUnlogin() {
    global $config;
    $oldSession = $_SESSION[$config['sessionName']];
    unset($_SESSION[$config['sessionName']]);
    $ch = curl_init($config['serverUrl']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $retour = curl_exec($ch);
    $template = file_get_contents(dirname(__FILE__) . '/templates/login.html');
    $this->assertEquals($retour, $template);
    curl_close($ch);
    $_SESSION[$config['sessionName']] = $oldSession;
  }

  public function AtestSiJePosteUnUtilisateurValide_AlorsJaiLaPageDIndex() {
    global $config, $cookieTest;
    $oldSession = $_SESSION[$config['sessionName']];
    unset($_SESSION[$config['sessionName']]);
    $ch = curl_init($config['serverUrl']);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieTest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $retour = curl_exec($ch);
    $template = file_get_contents(dirname(__FILE__) . '/templates/index.html');
    //Je mets la réinitialisaition de session avant le test pour s'assurer que 
    //l'on la conserve
    $_SESSION[$config['sessionName']] = $oldSession;
    $this->assertEquals($template, $retour);
    curl_close($ch);
  }

}
