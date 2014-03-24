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
 * classe de gestion de la BDD
 */
class dbLmondo {

  private $dbh;
  private $stmnt;
  private $dbError;
  private $table;
  
  function __construct($table=NULL) {
    $this->dbError['code'] = 0;
    $this->dbConnect();
    $this->table = $table;
  }

  /*
   * Connection à la BDD configurée
   */
  private function dbConnect() {
    global $config;
    try {
      $this->dbh = new PDO(
        'mysql:host=' . $config['db']['host'] .
        ';port=' . $config['db']['port'] .
        ';dbname=' . $config['db']['name'] . '', $config['db']['user'], $config['db']['pass'], array(
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        )
      );
    } catch (PDOException $e) {
      $this->dbh = false;
      $this->dbError['code'] = $e->getCode();
      $this->dbError['message'] = $e->getMessage();
      $this->dbError['sqlstate'] = $this->getErrorFromConnexionErrorMessage();
    }
  }

  /**
   * Récupération du dernier message d'erreur à partir du dernier message d'erreur
   * @param sring $message message dont le code d'erreur est à extraire. 
   *                       La dernière erreur si NULL
   * @return string
   */
  public function getErrorFromConnexionErrorMessage($message = NULL) {
    if (is_null($message)) {
      $message = $this->dbError['message'];
    }
    preg_match('/\[\d+\]/', $message, $matches);

    if (is_array($matches) && sizeof($matches) > 0) {
      return preg_replace('/(\[|\])/', NULL, $matches[0]);
    } else {
      return null;
    }
  }

  /**
   * Récupération de la connexion
   * @return PDOObject|false Retourne un objet PDO ou false s'il ya eu une 
   * erreur de connexion
   */
  public function getConnexion() {
    return $this->dbh;
  }

  /**
   * Récupération du dernier code d'erreur
   * @return int code d'erreur de la BDD
   */
  public function getErrorCode() {
    return $this->dbError;
  }

  /**
   * Exécute une requête et récupère le premier résultat
   * @param string $sql Requête à exécuter
   * @param int $fetchStyle Contrôle comment la prochaine ligne sera retournée 
   * à l'appelant. Cette valeur doit être une des constantes PDO::FETCH_*, et 
   * par défaut, vaut la valeur de PDO::FETCH_ASSOC
   * @return array|false retourne le résultat ou false en cas d'erreur
   */
  public function fetch($sql, $fetchStyle = PDO::FETCH_ASSOC) {
    $return = array();
    if ($this->dbh !== false) {
      try {
        $stmt = $this->dbh->prepare($sql);

        // call the stored procedure
        $stmt->execute();

        $return = $stmt->fetch($fetchStyle);
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }

  /**
   * Exécute une requête et récupère tout le résultat
   * @param string $sql Requête à exécuter
   * @param int $fetchStyle Contrôle comment la prochaine ligne sera retournée 
   * à l'appelant. Cette valeur doit être une des constantes PDO::FETCH_*, et 
   * par défaut, vaut la valeur de PDO::FETCH_ASSOC
   * @return array|false retourne le résultat ou false en cas d'erreur
   */
  public function fetchAll($sql, $fetchStyle = PDO::FETCH_ASSOC) {
    $return = array();
    if ($this->dbh !== false) {
      try {
        $stmt = $this->dbh->prepare($sql);
        // call the stored procedure
        $stmt->execute();
        while ($rs = $stmt->fetch($fetchStyle)) {
          $return[] = $rs;
        }
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }
  
  /**
   * Exécute une requête et récupère tout le résultat
   * @param string $sql Requête à exécuter
   * @return array|false retourne le résultat ou false en cas d'erreur
   */
  public function query($sql) {
    try {
      $return = $this->dbh->query($sql);
    } catch (PDOException $e) {
      $return = FALSE;
    }
    return $return;
  }

  /**
   * Prépare une instruction SQL
   * @param string $sql Requête à exécuter
   * @return PDOStatement|false retourne l'objet PDOStatement 
   * ou false en cas d'erreur
   */
  public function prepare($sql) {
    $return = FALSE;
    if ($this->dbh !== false) {
      try {
        $this->stmnt = $this->dbh->prepare($sql);
        $return = $this->stmnt;
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }

  /*
   * Exécute un bind des paramètres d'une instruction SQL préparée
   * @param string $sql Requête à exécuter
   * @param int $fetchStyle Contrôle comment la prochaine ligne sera retournée 
   *                        à l'appelant. Cette valeur doit être une des 
   *                        constantes PDO::FETCH_*, et par défaut, vaut la 
   *                        valeur de PDO::FETCH_ASSOC
   * http://www.php.net/manual/fr/pdostatement.bindparam.php
   */
  /**
   * Exécute un bind des paramètres d'une instruction SQL préparée
   * @param mixed $parameter Identifiant. Pour une requête préparée utilisant 
   * des marqueurs nommés, ce sera le nom du paramètre sous la forme :name. 
   * Pour une requête préparée utilisant les marqueurs interrogatifs, 
   * ce sera la position indexé -1 du paramètre. 
   * @param mixed $value valeur de la variable bindée
   * @param int $type Type explicite de données pour le paramètre utilisant la 
   * constante PDO::PARAM_* constants. Pour retourner un paramètre INOUT depuis 
   * une procédure stockée, utilisez l'opérateur OR pour définir l'octet 
   * PDO::PARAM_INPUT_OUTPUT pour le paramètre data_type. 
   * @link http://www.php.net/manual/fr/pdostatement.bindparam.php lien sur l'utilisation
   */
  public function bindParam($parameter, $value, $type = PDO::PARAM_STR) {
    if ($this->dbh !== false) {
      $this->stmnt->bindParam($parameter, $value, $type);
    }
  }

  /**
   * Execute l'instruction préparée
   * @return boolean
   */
  public function execute() {
    $return = FALSE;
    if ($this->dbh !== false) {
      try {
        $return = $this->stmnt->execute();
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }

  /**
   * Exécute l'instruction préparée et récupère le premier résultat
   * @param int $fetchStyle Contrôle comment la prochaine ligne sera retournée 
   * à l'appelant. Cette valeur doit être une des constantes PDO::FETCH_*, 
   * et par défaut, vaut la valeur de PDO::FETCH_ASSOC. 
   * @return array|false retourne le résultat ou false en cas d'erreur
   */
  public function executeAndFetch($fetchStyle = PDO::FETCH_ASSOC) {
    $return = array();
    if ($this->dbh !== false) {
      try {
        $this->stmnt->execute();
        $return = $this->stmnt->fetch($fetchStyle);
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }

  /**
   * 
   * Exécute l'instruction préparée et récupère tout le résultat
   * @param int $fetchStyle Contrôle comment la prochaine ligne sera retournée 
   * à l'appelant. Cette valeur doit être une des constantes PDO::FETCH_*, 
   * et par défaut, vaut la valeur de PDO::FETCH_ASSOC. 
   * @return array|false retourne tout le résultat ou false en cas d'erreur
   */
  public function executeAndFetchAll($fetchStyle = PDO::FETCH_ASSOC) {
    $return = array();
    if ($this->dbh !== false) {
      try {
        $this->stmnt->execute();
        while ($rs = $this->stmnt->fetch($fetchStyle)) {
          $return[] = $rs;
        }
      } catch (PDOException $e) {
        $return = FALSE;
      }
    }
    return $return;
  }

  /**
   * Vérifie si la BDD est utilisable
   * @return boolean true pour la BDD OK et false si une étape n'est pas passée
   */
  public function checkDB() {
    global $config;
    $return = TRUE;
    $sql = "select valeur from `" . $config['db']['prefix'] . "config` where cle = 'version';";
    $res = $this->fetch($sql);
    $version = $res['valeur'];
    if ($config['version'] !== $version) {
      $return = FALSE;
    }

    $sql = "select password from `" . $config['db']['prefix'] . "users` where login = 'adminlmondo';";
    $res = $this->fetch($sql);
    if ($res === FALSE || empty($res['password'])) {
      $return = FALSE;
    }
    return $return;
  }
  
  /**
   * Récupère une ligne à partir de l'id de la table donnée en paramètre de la construction
   * @param string $id id de la ligne a récupérer
   * @param string $colonneId nom de la colonne a chercher
   * @param string $champs champs positionnés après le select, '*' par défaut
   * @return mixed false si l'on n'a pas la table ou si on a eu un problème dans 
   * la requête, sinon la ligne trouvée.
   */
  public function getFromDB($id, $colonneId ='id', $champs = "*"){
    global $config;
    $return = FALSE;
    if ($this->table !== NULL){
      $this->prepare("SELECT $champs from ".$config['db']['prefix'].$this->table." where $colonneId = :id");
      $this->bindParam('id', $id);
      $return = $this->executeAndFetch();
    }
    return $return;
  }

}
