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
    $template = '<div class="alert alert-info"><a class="btn btn-default" data-target="#myModal" href="../ajax/recoReload.php" data-toggle="modal" type="button">'
      . 'Une mise à jour de la configuration de reconnaissance vocale est necessaire.</a></div>';
    $db->query("UPDATE " . $config['db']['prefix'] . "config SET valeur=0, where cle = reco_settings_db';");
    $this->assertEquals($template, $message);
  }

  public function testSijeModifieUnParametrePlusieursAlorsLaVersionEnBddEstIncrementeeUnseullefois() {
    $settings = new setting();
    reinitDB();
    $vers = $settings->getFromID('reco_settings_db');
    $old = $settings->getFromID('reco_name');
    $settings->update('reco_name', array('valeur' => $old['valeur'] . 'test'));
    $settings->update('reco_name', array('valeur' => $old['valeur'] . 'test2'));
    $newVers = $settings->getFromID('reco_settings_db');
    $template = $vers['valeur'] + 1;
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

  public function testSiJAjouteUnTriggerDeRecoAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    reinitDB();
    $vers = $settings->getFromID('reco_settings_db');
    $trigger = new trigger();
    $id = $trigger->insert(array('type' => 'reco', 'args' => '999999', 'scenario_id' => 1));
    $template = $vers['valeur'] + 1;
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

  public function testSiJeModifieUnTriggerDeRecoAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    reinitDB();
    $vers = $settings->getFromID('reco_settings_db');
    $trigger = new trigger();
    $template = $vers['valeur'] + 1;
    $trigger->update(1, array('type' => 'reco', 'args' => '9999999', 'scenario_id' => 1));
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

  public function testSiJeSupprimeUnTriggerDeRecoAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    reinitDB();
    $vers = $settings->getFromID('reco_settings_db');
    $trigger = new trigger();
    $template = $vers['valeur'] + 1;
    $trigger->delete(1);
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

  public function testSiJeSupprimeUnScenarioDeRecoAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $scenario = new scenario();
    $scenario->delete('1');
    $template = $vers['valeur'] + 1;
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

   public function testSiJeSupprimeUnMotifDeRecoActifAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $reco = new reco();
    $reco->delete(1);
    $template = $vers['valeur'] + 1;
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

   public function testSiJeModifieUnMotifDeRecoInactifAlorsLaVersionEnBddNEstPasIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $reco = new reco();
    $reco->insert(array('id'=>99999, 'nom'=>'test', 'content'=>'test'));
    $reco->update(99999, Array('content'=>"testUnit"));
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals( $vers['valeur'], $newVers['valeur']);
  }

   public function testSiJeModifieUnMotifDeRecoActifAlorsLaVersionEnBddEstIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $reco = new reco();
    $reco->update(1, Array('content'=>"testUnit"));
    $template = $vers['valeur'] + 1;
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals($template, $newVers['valeur']);
  }

   public function testSiJeSupprimeUnMotifDeRecoInactifAlorsLaVersionEnBddNEstPasIncrementee() {
    $settings = new setting();
    $vers = $settings->getFromID('reco_settings_db');
    $reco = new reco();
    $reco->insert(array('id'=>99999, 'nom'=>'test', 'content'=>'test'));
    $reco->delete(99999);
    $newVers = $settings->getFromID('reco_settings_db');
    reinitDB();
    $this->assertEquals( $vers['valeur'], $newVers['valeur']);
  }
  
  public function testSiMaSessionEstFalseEtQueJeModifiUnParametre_AlorsJeNAiPasDeMessageSurLaPageDeLogin() {
    global $config, $db;

    $settings = new setting();
    $old = $settings->getFromID('reco_name');
    $oldSession = $_SESSION[$config['sessionName']];
    $_SESSION[$config['sessionName']] = FALSE;
    $settings->update('reco_name', array('valeur' => $old['valeur'] . 'test'));
    $page = new page(TRUE);
    $result = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/login.html');
    $_SESSION[$config['sessionName']] = $oldSession;
    $settings->update('reco_name', array('valeur' => $old['valeur']));
    $this->assertEquals($template, $result);
  }

  public function testJeJeDemandeUneEcritureDeFichierDeConfigRecoEtDeGrammaireAlorsJAiDesFichiersCoherents() {
    global $config;
    writeToListenerFile();
    $template = file_get_contents(dirname(__FILE__) . '/templates/lmondoListener.cfg.sample');
    $this->assertEquals($template, file_get_contents($config['input']['config']));
    $template = file_get_contents(dirname(__FILE__) . '/templates/grammar.jsgf.sample');
    $this->assertEquals($template, file_get_contents($config['input']['grammar']));
  }

}
