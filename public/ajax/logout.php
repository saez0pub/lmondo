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

include_once dirname(__FILE__) . '/../../lib/common.php';

if (isset($_SESSION[$config['sessionName']])) {

  session_unset();
  session_destroy();
  // Unset all of the session variables.
  $_SESSION = array();

  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    $cookiesSet = array_keys($_COOKIE);
    for ($x = 0; $x < count($cookiesSet); $x++)
      setcookie($cookiesSet[$x], "", time() - 1);
    foreach ($_COOKIE as $key => $value) {
      setcookie($key, '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
  }
  echo 'true';
}