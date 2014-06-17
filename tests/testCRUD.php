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
class testCRUD extends PHPUnit_Framework_TestCase {

  public function testJePeuxFaireUnInsertSelectUpdateDelete() {
    $reco = new reco();
    $columns=array(
        'nom' => 'testJePeuxFaireUnInsert',
        'content' => 'testJePeuxFaireUnInsert'
    );
    $template = $columns;
    $id = $reco->insert($columns);
    $this->assertNotEquals(FALSE, $id);
    $this->assertGreaterThan(0, $id);
    $ligne = $reco->getFromID($id);
    $template['id'] = $id;
    $this->assertEquals($template, $ligne);
    $columns['nom'] = 'testJePeuxFaireUnUpdate';
    $reco->update($id, $columns);
    $ligne = $reco->getFromID($id);
    $this->assertNotEquals($template, $ligne);
    $template['nom'] = $columns['nom'];
    $this->assertEquals($template, $ligne);
    $reco->delete($id);
    $ligne = $reco->getFromID($id);
    $this->assertEquals(FALSE, $ligne);
    }
}
