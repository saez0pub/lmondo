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

/**
 * Page d'abstration d'affichage de la page
 */
class page {

  protected $css;
  protected $js;
  protected $header;
  protected $content;
  protected $footer;
  protected $canShowPage;
  protected $returnPage;
  protected $overrideContent;

  /**
   * Construction de la page
   * @param boolean $returnPage TRUE : retourne la page, FALSE (valeur par défaut) affiche la page
   */
  function __construct($returnPage = FALSE) {
    global $db, $config;
    $this->css = array();
    $this->js = $config['js'];
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
    $this->checkRecoReload();
    $this->prepareHeader();
    $this->prepareFooter();
  }

  /**
   * Vérifie si un reload de la reco est necessaire et ajoute un message
   */
  public function checkRecoReload() {
    global $db, $config;
    if (isset($_SESSION[$config['sessionName']]['user']) && $this->canShowPage === TRUE) {
      $res = $db->fetchAll("SELECT * FROM " . $config['db']['prefix'] . "config WHERE cle = 'reco_settings_db' OR cle = 'reco_settings_disk'");
      foreach ($res as $value) {
        if($value['cle'] === 'reco_settings_db'){
          $recoDb = $value['valeur'];
        }elseif($value['cle'] === 'reco_settings_disk'){
          $recoDisk = $value['valeur'];
        }
      }
      if(isset($recoDb) && isset($recoDisk) && $recoDb > $recoDisk){
        addMessageAfterRedirect('<a class="btn btn-default" data-target="#myModal" href="../ajax/recoReload.php" data-toggle="modal" type="button">'
          . 'Une mise à jour de la configuration de reconnaissance vocale est necessaire.'
          . '</a>');
      }
    }
  }
  /**
   * Ajoute dans la page du contenu
   * @param string $content Texte a ajouter dans la page
   */
  public function addcontent($content) {
    $this->content.="$content\n";
  }

  /**
   * Ajoute une classe CSS dans le header de la page
   * @param string $css
   */
  public function addCSS($css) {
    $this->css[] = $css;
    $this->prepareHeader();
  }

  /**
   * Ajoute une reference javascript dans le footer de la page
   * @param string $css
   */
  public function addJS($js) {
    $this->js[] = $js;
    $this->prepareFooter();
  }

  /**
   * Permet de produire le contenu final de la page
   * @param boolean $return TRUE : retourne le contenu, FALSE affiche le contenu (Par défaut, il s'agit du paramètre donné à la construction).
   * @return string si $return est TRUE, affiche la page
   */
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

  /**
   * Permet de produire une page de maintenance
   * @param boolean $return TRUE : retourne le contenu, FALSE affiche le contenu (Par défaut, il s'agit du paramètre donné à la construction).
   * @return string si $return est TRUE, affiche la page
   */
  public function showMaintenancePage($return = NULL) {
    $this->prepareHeader(false);
    if ($return === NULL) {
      $return = $this->returnPage;
    }
    $this->content = $this->overrideContent;

    $this->canShowPage = FALSE;
    if ($return) {
      return $this->prepareHeader(FALSE) . $this->content . $this->prepareFooter();
    } else {
      echo $this->prepareHeader(FALSE) . $this->content . $this->prepareFooter();
    }
  }

  /**
   * Permet de préparer le header de la page
   * @param boolean $showMenu Est ce que je peux afficher les menu
   * @return string le contenu html du header
   */
  public function prepareHeader($showMenu = TRUE) {
    $this->header = file_get_contents(dirname(__FILE__) . '/../var/templates/header_1.html');
    foreach ($this->css as $css) {
      $this->header.="    <link href=\"" . $css . "\" rel=\"stylesheet\">\n";
    }
    $this->header.=file_get_contents(dirname(__FILE__) . '/../var/templates/header_2.html');
    if ($showMenu && $this->canShowPage) {
      $this->header = str_replace('$menu$', $this->getMenu(), $this->header);
    } else {
      $this->header = str_replace('$menu$', '', $this->header);
    }
    return $this->header;
  }

  /**
   * Donne le message de redirection s'il existe
   * @return string le message de redirection
   */
  public function addRedirectMessage() {
    global $config;
    $return = '';
    if (isset($_SESSION[$config['sessionName']]['messageAfterRedirect'])) {
      $return =  $_SESSION[$config['sessionName']]['messageAfterRedirect'];
      unset($_SESSION[$config['sessionName']]['messageAfterRedirect']);
    }
    return $return;
  }

  /**
   * construit et retourne le menu html 
   * @return string le menu
   */
  public function getMenu() {
    global $config;
    $class = '';
    $return = "\n          <ul class=\"nav navbar-nav\">
            <li class=\"$class\"><a href=\"index.php\">Accueil</a></li>" . $this->getUserMenu() . "
          </ul>";
    if (isset($_SESSION[$config['sessionName']]['user']['login'])) {
      $return.="
          <ul class=\"nav navbar-nav navbar-right\" id=\"usernavigation\">
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">" . $_SESSION[$config['sessionName']]['user']['login'] . " <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"#\" id=\"logout\">Déconnexion</a></li>
              </ul>
            </li>
          </ul>";
    }
    return $return;
  }

  /**
   * Retourne le menu suivant la session utilisateur
   * @return string menu au format HTML ou vide si il n'ya pas d'accès
   */
  public function getUserMenu() {
    global $config;
    $class = '';
    $return = '';
    if (isset($_SESSION[$config['sessionName']]['menu'])) {
      foreach ($_SESSION[$config['sessionName']]['menu'] as $nom => $menu) {
        if (!is_array($menu)) {
          $return.="
            <li class=\"$class\"><a href=\"$menu\">$nom</a>";
        } else {
          $return.="
            <li class=\"dropdown $class\"><a data-toggle=\"dropdown\" class=\"dropdown-toggle\" href=\"#\">$nom <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">";
          foreach ($menu as $submenu => $link) {
            $return.="
                <li><a href=\"$link\">$submenu</a></li>";
          }
          $return.="
              </ul>
            </li>";
        }
      }
    }
    return $return;
  }

  /**
   * Construit et retourne le footer
   * @return string le footer en html
   */
  public function prepareFooter() {
    global $config;
    $js = "";
    foreach ($this->js as $jsAdd) {
      $js.='    <script src="'.$jsAdd.'"></script>'."\n";
    }
    $this->footer = file_get_contents(dirname(__FILE__) . '/../var/templates/footer.html');
    $this->footer = str_replace('$jsToLoad$', $js, $this->footer);
    return $this->footer;
  }

  /**
   * Donne le contenu de la page
   * @return string le contenu de la page.
   */
  public function getContent() {
    global $config;
    //Je ne teste pas $_SESSION[$config['sessionName']]['user']['login'] pour pouvoir afficher la page de maintenance
    if (isset($_SESSION[$config['sessionName']]['user']) && $this->canShowPage === TRUE) {
      $return = $this->content;
    } else {
      $this->addCSS('../css/login.css');
      $this->prepareHeader(false);
      $return = file_get_contents(dirname(__FILE__) . '/../var/templates/login.html');
    }
    return $this->addRedirectMessage() . $return;
  }

}
