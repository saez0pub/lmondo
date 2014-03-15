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

class page {

  private $css;
  private $header;
  private $content;
  private $footer;
  private $canShowPage;
  private $returnPage;

  function __construct($returnPage = TRUE) {
    global $db;
    $this->css = array();
    $this->canShowPage = TRUE;
    $this->returnPage = TRUE;

    if ($db->getConnexion() === FALSE) {
      //Vu que l'on a un problème de connexion de Bdd, on affiche la page de maintenance
      $this->showMaintenancePage();
    }
  }

  public function addcontent($content) {
    $this->content.="$content\n";
  }

  public function addCSS($css) {
    $this->css[] = $css;
  }

  public function showPage($return = NULL) {
    if($return === NULL){
      $return = $this->returnPage;
    }
    if ($this->canShowPage === TRUE) {
      if ($return) {
        return $this->header . $this->content . $this->footer;
      } else {
        echo $this->header . $this->content . $this->footer;
      }
    } else {
      return $this->showMaintenancePage();
    }
  }

  public function showMaintenancePage($return = NULL) {
    if($return === NULL){
      $return = $this->returnPage;
    }
    $this->content = "<div class='maintenance'>Le site est en maintenance, veuillez réessayer dans quelques minutes</div>";
    if ($return) {
      return $this->header() . $this->content . $this->footer();
    } else {
      echo $this->header() . $this->conten . $this->footer();
    }
    $this->canShowPage = FALSE;
  }

  public function header() {
    $this->header = file_get_contents('../var/templates/header_1.html');
    foreach ($this->css as $css) {
      $this->header.="
    <link href=\"" . $css . " rel=\"stylesheet\">
 ";
    }
    $this->header.=file_get_contents('../var/templates/header_2.html');
    return $this->header;
  }

  public function footer() {
    $this->footer = file_get_contents('../var/templates/footer.html');
    return $this->footer;
  }

  public function testAuth() {
    return false;
  }

}
?>

