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

class dbLmondo {

  private $dbh;
  private $stmnt;
  private $dbError;

  function __construct() {
    $this->dbError['code'] = 0;
    $this->dbConnect();
  }

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

  public function getConnexion() {
    return $this->dbh;
  }

  public function getErrorCode() {
    return $this->dbError;
  }

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

  public function query($sql) {
    try {
      $return = $this->dbh->query($sql);
    } catch (PDOException $e) {
      $return = FALSE;
    }
    return $return;
  }

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

  public function bindParam($variable, $value, $type = PDO::PARAM_STR) {
    if ($this->dbh !== false) {
      $this->stmnt->bindParam($variable, $value, $type);
    }
  }

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

}
