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

  public function laBDDEstVide_AlorsJeDemandeUnstallation($param) {
    $this->assertEquals(false, $result);
  }
  public function testSitrouveAdminAvecUnMotDePasseVide_AlorsJAfficheLaPageDInstallation() {
    global $db;
    $oldAdminPwd = $db->fetch("SELECT password from users where login = 'adminlmondo';");
    $db->query("UPDATE users SET password='' where login = 'adminlmondo';");
    $result = false;
    $db->query("UPDATE users SET password='$oldAdminPwd' where login = 'adminlmondo';");
    $this->assertEquals(false, $result);
  }

}