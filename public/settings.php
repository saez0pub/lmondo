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


include_once dirname(__FILE__).'/../lib/common.php';

$page = new page();

$content="
      <div class=row>
        <div class=\"highlight col-md-3\">
          <h1>Paramètres</h1>
        </div>
        <div class=\"col-md-9\">
";

$settings = new setting();
$settings->prepare();
$content.=$settings->getTable();
$content.="
        </div>
      </div>
";
$page->addcontent($content);
$page->showPage();