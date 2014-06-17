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

}
