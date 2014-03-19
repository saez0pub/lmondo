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

include_once dirname(__FILE__) . '/login.function.php';

class page {

  private $css;
  private $header;
  private $content;
  private $footer;
  private $canShowPage;
  private $returnPage;
  private $overrideContent;

  function __construct($returnPage = FALSE) {
    global $db;
    $this->css = array();
    $this->canShowPage = TRUE;
    $this->returnPage = $returnPage;
    $this->overrideContent = "<div class='alert alert-warning maintenance'>Le site est en maintenance, veuillez réessayer dans quelques minutes</div>";
    if ($db->getConnexion() === FALSE) {
      //Vu que l'on a un problème de connexion de Bdd, on affiche la page de maintenance
      $this->showMaintenancePage();
    } elseif ($db->checkDB() === FALSE) {
      $this->overrideContent = "<div class='alert alert-critical maintenance'>Une installation ou mise à jour doit être faite, veuillez consulter les notes de mise à jour</div>";
      $this->showMaintenancePage();
    }
    $this->prepareHeader();
    $this->prepareFooter();
  }

  public function addcontent($content) {
    $this->content.="$content\n";
  }

  public function addCSS($css) {
    $this->css[] = $css;
    $this->prepareHeader();
  }

  public function showPage($return = NULL) {
    if ($return === NULL) {
      $return = $this->returnPage;
    }
    if ($this->canShowPage === TRUE) {
      //Vérification des données de session
      $this->content = $this->getContent();
      if ($return) {
        return $this->header . $this->content . $this->footer;
      } else {
        echo $this->header . $this->content . $this->footer;
      }
    } else {
      return $this->showMaintenancePage(TRUE);
    }
  }

  public function showMaintenancePage($return = NULL) {
    $this->prepareHeader(false);
    if ($return === NULL) {
      $return = $this->returnPage;
    }
    $this->content = $this->overrideContent;

    if ($return) {
      return $this->prepareHeader() . $this->content . $this->prepareFooter();
    } else {
      echo $this->prepareHeader() . $this->content . $this->prepareFooter();
    }
    $this->canShowPage = FALSE;
  }

  public function prepareHeader($showMenu = true) {
    $this->header = file_get_contents(dirname(__FILE__) . '/../var/templates/header_1.html');
    foreach ($this->css as $css) {
      $this->header.="    <link href=\"" . $css . "\" rel=\"stylesheet\">\n";
    }
    $this->header.=file_get_contents(dirname(__FILE__) . '/../var/templates/header_2.html');
    if ($showMenu) {
      $this->header = str_replace('$menu$', $this->getMenu(), $this->header);
    } else {
      $this->header = str_replace('$menu$', '', $this->header);
    }
    return $this->header;
  }

  public function getMenu() {
    global $config;
    $return = "\n          <ul class=\"nav navbar-nav\">
            <li class=\"active\"><a href=\"#\">Accueil</a></li>
          </ul>";
    if (isset($_SESSION[$config['sessionName']]['user']['login'])) {
      $return.="
          <ul class=\"nav navbar-nav navbar-right\" id=\"usernavigation\">
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".$_SESSION[$config['sessionName']]['user']['login']." <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"#\" id=\"logout\">Déconnexion</a></li>
              </ul>
            </li>
          </ul>";
    }
    return $return;
  }

  public function prepareFooter() {
    $this->footer = file_get_contents(dirname(__FILE__) . '/../var/templates/footer.html');
    return $this->footer;
  }

  public function getContent() {
    global $config;
    if (isset($_SESSION[$config['sessionName']]['user'])) {
      return $this->content;
    } else {
      $this->addCSS('../css/login.css');
      $this->prepareHeader(false);
      return file_get_contents(dirname(__FILE__) . '/../var/templates/login.html');
    }
  }

}
