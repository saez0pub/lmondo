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
class testFreshInstall extends PHPUnit_Framework_TestCase {

  public function testSitrouveAdminAvecUnMotDePasseVide_AlorsJAfficheLaPageDeMaintenance() {
    global $db, $config, $adminPassword;
    $resInit = reinitDB();
    $this->assertNotEquals(FALSE, $resInit);
    $db->query("UPDATE " . $config['db']['prefix'] . "users SET password='' where login = 'adminlmondo';");
    $resCheck = $db->checkDB();
    $this->assertEquals(FALSE, $resCheck);
    $page = new page(TRUE);
    $page->prepareHeader(FALSE);
    $resultPage = $page->showPage();
    $template = file_get_contents(dirname(__FILE__) . '/templates/upgradeplz.html');
    $this->assertEquals($template, $resultPage);
    $db->query("UPDATE " . $config['db']['prefix'] . "users SET password='$adminPassword' where login = 'adminlmondo';");
    $this->assertEquals(false, $resCheck);
  }

}
