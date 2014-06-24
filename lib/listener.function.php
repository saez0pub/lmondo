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

function writeToListenerFile() {
  global $config, $db;
  $setting = new setting();
  $setting->select(Array('cle', 'valeur'))
    ->where('dump_to_listener = 1')
    ->prepare();
  $configSection = "[config]\n";
  foreach ($setting->executeAndFetchAll() as $value) {
    if ($value['cle'] == 'reco_name') {
      $recoName = $value['valeur'];
    }
    $configSection .= $value['cle'] . " = " . $value['valeur'] . "\n";
  }
  file_put_contents($config['input']['config'], $configSection);
  $grammar = "#JSGF V1.0 latin-1;\n";
  $grammar .= "grammar robot;\n";
  $grammar .= "public <question> =";
  $sep = "";
  $grammar_end = "";
  $res = $db->fetchAll("select reco.content from lmondo_triggers triggers INNER JOIN lmondo_reco reco on triggers.args = reco.id where type='reco' ;");
  foreach ($res as $key => $value) {
    $grammar .= "$sep <gramm$key>";
    $grammar_end .= "<gramm$key> = " . $value['content'] . ";\n";
    $sep = " |";
  }
  $key++;
  $grammar .= "$sep <gramm$key> ;\n";
  $grammar_end .= "<gramm$key> = $recoName;\n";
  file_put_contents($config['input']['grammar'], $grammar . $grammar_end . "\n");
}

function updateRecoSettingDbIfNeeded() {
  $return = FALSE;
  $setting = new setting();
  $cur = $setting->getFromID('reco_settings_db');
  $disk = $setting->getFromID('reco_settings_disk');
  if ($cur !== FALSE && $disk !== FALSE) {
    if ($cur['valeur'] <= $disk['valeur']) {
      $disk['valeur'] ++;
      if ($setting->update('reco_settings_db', array('valeur' => $disk['valeur'])) !== FALSE) {
        $return = TRUE;
      }
    } else {
      $return = TRUE;
    }
  }
  return $return;
}
