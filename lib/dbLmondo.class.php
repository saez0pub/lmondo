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

  protected $dbh;
  protected $stmnt;
  protected $dbError;
  protected $table;
  protected $select;
  protected $where;
  protected $sql;
  protected $champId;
  protected $column;
  protected $canEdit;

  function __construct($table = NULL) {
    global $config;
    $this->dbError['code'] = 0;
    $this->dbConnect();
    //Placer la table après la connexion car la connexion initialise le champs table à NULL
    $this->table = $config['db']['prefix'] . $table;
    $this->select = '*';
    $this->champId = 'id';
    $this->where = NULL;
    $this->sql = 'SELECT ' . $this->select . ' FROM ' . $this->table;
    if ($this->dbh !== FALSE) {
      foreach ($this->getColumns() as $value) {
        $this->column[$value] = $value;
      }
    }
    $this->canEdit = true;
    $this->modalTarget = '../ajax/default.php?table='.get_class($this).'&champs='.$this->champId.'&id=';
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
    $this->table = NULL;
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
   * @return $this|false retourne l'objet ou false en cas d'erreur
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
   * @param string $sql Requête à exécuter, si NULL (défaut, récupère la requête SQL construite.
   * @return $this|false retourne l'objet  
   * ou false en cas d'erreur
   */
  public function prepare($sql = NULL) {
    $return = FALSE;
    if ($sql === NULL) {
      $sql = $this->sql;
    }
    if ($this->dbh !== false) {
      try {
        $this->stmnt = $this->dbh->prepare($sql);
        $return = $this;
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
   * @return $this|boolean
   */
  public function execute() {
    $return = FALSE;
    if ($this->dbh !== false) {
      try {
        $this->stmnt->execute();
        $return = $this;
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
      return FALSE;
    }

    $sql = "select password from `" . $config['db']['prefix'] . "users` where login = 'adminlmondo';";
    $res = $this->fetch($sql);
    if ($res === FALSE || empty($res['password'])) {
      return FALSE;
    }
    return $return;
  }

  /**
   * Permet définir le champs ID qui est id pardéfaut.
   * @param string $nomColonneId
   */
  public function setChampId($nomColonneId) {
    $this->champId = $nomColonneId;
  }

  /**
   * Récupère une ligne à partir de l'id de la table donnée en paramètre de la construction
   * @param string $id id de la ligne a récupérer
   * @param string $colonneId nom de la colonne a chercher
   * @param string $champs champs positionnés après le select, '*' par défaut
   * @return mixed false si l'on n'a pas la table ou si on a eu un problème dans 
   * la requête, sinon la ligne trouvée.
   */
  public function getFromID($id) {
    $return = FALSE;
    if ($this->table !== NULL) {
      $this->prepare("SELECT " . $this->select . " from " . $this->table . " where " . $this->champId . " = :id");
      $this->bindParam('id', $id);
      $return = $this->executeAndFetch();
    }
    return $return;
  }

  /**
   * Prépare la clause select d'un requete
   * @param Array $champs Tableau des champs du SELECT
   */
  public function select($champs) {
    if (!is_array($champs)) {
      $champs = array($champs);
    }
    $this->select = join(', ', $champs);
    $this->sql = "SELECT " . $this->select . " FROM " . $this->table;
    return $this;
  }

  /**
   * Ajoute une clause Where a la requête en cours de préparation
   * @param string $column partie gauche de la clause where
   * @param string $operator opérator (défaut =)
   * @param string $and jointure avec la précédente clause where (s'il y en a une)
   * @param string $paramName nom du paramètre qui sera a spécifier dans le bind (défaut la valeur de $column)
   */
  public function addWhere($column, $operator = '=', $and = "AND", $paramName = NULL) {
    if ($paramName === NULL) {
      $paramName = $column;
    }
    if ($this->where === NULL) {
      $this->sql .= " WHERE ($column $operator :$paramName) ";
      $this->where = 0;
    } else {
      $this->sql.= " $and ($column $operator :$paramName) ";
    }
    return $this;
  }

  /**
   * Ajoute une clause Where a la requête en cours de préparation précédée de AND si besoin
   * @param string $column partie gauche de la clause where
   * @param string $operator opérator (défaut =)
   * @param string $paramName nom du paramètre qui sera a spécifier dans le bind (défaut la valeur de $column)
   */
  public function _and($column, $operator = '=', $paramName = NULL) {
    $this->addWhere($column, $operator, "AND", $paramName);
    return $this;
  }

  /**
   * Ajoute une clause Where a la requête en cours de préparation précédée de OR si besoin
   * @param string $column partie gauche de la clause where
   * @param string $operator opérator (défaut =)
   * @param string $paramName nom du paramètre qui sera a spécifier dans le bind (défaut la valeur de $column)
   */
  public function _or($column, $operator = '=', $paramName = NULL) {
    $this->addWhere($column, $operator, "OR", $paramName);
    return $this;
  }

  /**
   * Retourne le nombre de lignes affectées par le dernier appel à une exécution SQL
   * @return int nombre de lignes
   */
  public function numRows() {
    if ($this->table !== NULL) {
      return $this->stmnt->rowCount;
    } else {
      return 0;
    }
  }

  /**
   * Permet d'obtenir les colonnes de la requête
   * @return array les noms de colonnes
   */
  public function getColumns() {
    $db = $this->getConnexion();
    $columns = array();
    $stmnt = $db->prepare($this->sql . ' LIMIT 0');
    $stmnt->execute();
    for ($i = 0; $i < $stmnt->columnCount(); $i++) {
      $col = $stmnt->getColumnMeta($i);
      $columns[] = $col['name'];
    }
    return $columns;
    ;
  }

  /**
   * fonction de vérification si la table peut être éditée
   * @return boolean
   * @todo ajouter des droits utilisateurs un jour
   */
  public function canEdit() {
    return $this->canEdit;
  }

  
  /**
   * permet de forcer le fait de pouvoir editer les entrées
   */
  public function setEdit($canEdit) {
    $this->canEdit = $canEdit;
  }
  
  public function getColumnName($colonne) {
      if (isset($this->column[$colonne])) {
        return $this->column[$colonne];
      }  else {
        return $colonne;
      }
  }
  /**
   * Fonction de préformatage de table
   * @param type $return
   * @return string
   */
  public function getTable($return = TRUE) {
    $columns = $this->getColumns();
    $res = '<table class="table table-striped table-hover">' . "\n";
    $res.= '<thead>' . "\n";
    $res.= '<tr>' . "\n";
    if ($this->canEdit()) {
      $res.= "<th></th>\n";
    }
    foreach ($columns as $value) {
      $res.= '<th>' . $this->getColumnName($value) . '</th>' . "\n";
    }
    $res.= '</tr>' . "\n";
    $res.= '</thead>' . "\n";
    $this->prepare($this->sql);
    foreach ($this->executeAndFetchAll() as $value) {
      $res.='<tr>';
      if ($this->canEdit()) {
        $res.= '<td><button type="button" class="btn btn-default" data-toggle="modal" data-remote="'.$this->modalTarget.$value[$this->champId].'" data-target="#myModal"><span class="glyphicon glyphicon-edit"></span></button>
</td>'."\n";
      }
      foreach ($columns as $col) {
        $res.= '<td>' . $value[$col] . '</td>';
      }
      $res.='</tr>' . "\n";
    }
    $res.= '</table>' . "\n";

    if ($return === TRUE) {
      return $res;
    } else {
      echo $res;
    }
  }

  /**
   * Permet d'afficher un alias différent sur un colonne donnée
   * @param type $column
   * @param type $alias
   */
  public function setColumnAlias($column, $alias) {
    if (isset($this->column[$column])) {
      $this->column[$column] = $alias;
    }
    return $this->column;
  }

}
