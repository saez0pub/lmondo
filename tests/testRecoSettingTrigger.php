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
class testRecoSettingTrigger extends PHPUnit_Framework_TestCase {

  public function testSiJAiUnMessageEnAttenteAlorsUnMessageApparaitSurLaPage() {
    global $db, $config;
    $db->query("UPDATE " . $config['db']['prefix'] . "config SET valeur=1 where cle = 'reco_settings_db';");
    $page = new page(TRUE);
    $message = $page->addRedirectMessage();
    $template = '<div class="alert alert-info">Une mise à jour de la configuration de reconnaissance vocale est necessaire.</div>';
    $db->query("UPDATE " . $config['db']['prefix'] . "config SET valeur=0, where cle = reco_settings_db';");
    $this->assertEquals($template, $message);
  }

  public function testSijeModifieUnParametreAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $old = $settings->getFromID('reco_name');
    $settings->update('reco_name', array('valeur' => $old['valeur'].'test'));
    $newVers = $settings->getFromID('reco_settings_db');
    $template = $vers['valeur'] + 1;
    $settings->update('reco_name', array('valeur' => $old['valeur']));
    $settings->update('reco_settings_db', array('valeur' => $vers['valeur']));
    $this->assertEquals($template, $newVers['valeur']);
  }

  
  public function testSilMaSessionEstFalseEtQueJeModifiUnParameter_AlorsJeNAiPasDeMessageSurLaPageDeLogin() {
    global $config, $db;

    $settings = new setting();
    $old = $settings->getFromID('reco_name');
    $oldSession = $_SESSION[$config['sessionName']];
    $_SESSION[$config['sessionName']] = FALSE;
    $settings->update('reco_name', array('valeur' => $old['valeur'].'test'));
    $page = new page(TRUE);
    $result = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/login.html');
    $_SESSION[$config['sessionName']] = $oldSession;
    $settings->update('reco_name', array('valeur' => $old['valeur']));
    $this->assertEquals($template, $result);
  }
}
