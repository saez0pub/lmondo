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
 * Class Utilisateur
 */
class rule extends dbLmondo {

  function __construct() {
    parent::__construct('rules');
    $this->column['nom'] = 'Nom de règle';
    $this->column['content'] = 'Contenu de la règle';
  }

  public function deleteHook($id) {
    global $config;
    $sql = 'DELETE FROM ' . $config['db']['prefix'] . 'triggers WHERE type=\'reco\' and args=:id';
    $this->prepare($sql);
    $this->bindParam('id', $id);
    return $this->execute();
  }

  public function run($content) {
    global $config;
    $res = FALSE;
    $return = FALSE;
    $this->sql = 'SELECT scenarios.action FROM ' . $this->table . ' rules INNER JOIN ' .
      $config['db']['prefix'] . 'triggers triggers on triggers.args = rules.id' .
      ' INNER JOIN ' . $config['db']['prefix'] . 'scenarios scenarios ON scenarios.id = triggers.scenario_id' .
      ' WHERE triggers.type = :type AND rules.content = :content';
    $this->prepare();
    $this->bindParam('type', 'reco');
    $this->bindParam('content', $content);
    $res = $this->executeAndFetch();
    if ($res !== FALSE) {
      $action = new action();
      $return = $action->run($res['action'], Array(1 => $content));
    }
    return $return;
  }

}
