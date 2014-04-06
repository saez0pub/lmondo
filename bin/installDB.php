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


include dirname(__FILE__) . '/../lib/common.php';
include dirname(__FILE__) . '/../lib/dbInstall.function.php';
echo "Assurez vous d'avoir créé l'utilisateur de connexion, le base de donnéescf etc/config.php\n"
. "pour le définir et le script var/DB/createLmondoUser.sql puis appuyez sur Entrée.";
$password = trim(fgets(STDIN));

$resInit = initDB();
if ($resInit !== FALSE) {
  echo "Entrez le mot de passe de l'utilisateur adminlmondo :\n";
  system('stty -echo');
  $password = trim(fgets(STDIN));
  system('stty echo');
  if (!empty($password)) {
    echo "Entrez le mot de passe à nouveau :\n";
    system('stty -echo');
    $password2 = trim(fgets(STDIN));
    system('stty echo');
    if ($password === $password2) {
      $newPassword = md5($password);
      $sql = "UPDATE ".$config['db']['prefix']."users SET password='$newPassword' where login = 'adminlmondo';";
      $res = $db->query($sql);
      if ($res === FALSE) {
        echo "Problème dans la mise à jour de mot de passe, vérifier que l'utilisateur de connexion à la base de données est valide\n";
      } else {
        echo "mot de passe mis à jour avec succès";
      }
    }
  } else {
    echo "Les mots de passe ne correspondent pas, abandon.";
  }
}  else {
  echo "Problème dans l'initialisation de la base de données, abandon\n";  
}