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

function startSession() {
  global $config;
  if (!isset($_SESSION) || $_SESSION === NULL) {
    session_start();
  }
  if (isset($_GET['login']) && (isset($_GET['password']) || isset($_GET['passwordmd5']))) {
    $user = new user();
    $res = $user->getFromDB();
    if ($res !== FALSE) {
      $_SESSION[$config['sessionName']]['user'] = $res;
      if (isset($_GET['remember-me']) && $res !== FALSE) {
        $params = session_get_cookie_params();
        setcookie('login', $_GET['login'], $config['cookieTime'], $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        setcookie('passwordmd5', md5($_GET['password']), $config['cookieTime'], $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
      }
    } else {
      $_SESSION[$config['sessionName']] = FALSE;
    }
  }
}

function stopSession($rediRectToIndex = TRUE, $stopExec = FALSE) {
  global $config;
  if (isset($_SESSION[$config['sessionName']])) {

    session_unset();
    session_destroy();
    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      foreach ($_COOKIE as $key => $value) {
        setcookie($key, '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
      }
    }
  }
  if ($rediRectToIndex == TRUE) {
    /* Redirection vers une page différente du même dossier 
     * le @ permet de pouvoir appeler la fonction ans générer d'erreur
     */
    $host = @$_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $curUri = rtrim(basename($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("Location: http://$host$uri/$extra");
  } else {
    echo 'true';
  }

  if ($stopExec) {
    exit;
  }
}

function addMessageAfterRedirect($message, $level = 'info') {
  global $config;
  switch ($level) {
    case 'success':
      $res = '<div class="alert alert-success">' . $message . '<div>';
      break;
    case 'info':
      $res = '<div class="alert alert-info">' . $message . '<div>';
      break;
    case 'warning':
      $res = '<div class="alert alert-warning">' . $message . '<div>';
      break;
    case 'danger':
      $res = '<div class="alert alert-danger">' . $message . '<div>';
      break;

    default:
      $res = '<div class="' . $class . '">' . $message . '<div>';
      break;
  }
  $_SESSION[$config['sessionName']]['messageAfterRedirect'] = $res;
}