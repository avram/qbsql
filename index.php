<?php
/*
 * qbsql - a program for quiz bowl stats keeping
 * Copyright 2008  Avram Lyon <ajlyon+qbsql@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301  USA
 */
/* index.php
 *
 * Central command for qbsql 
 *
 * Author: Avram Lyon 
 * Created: 25 February 2004
 */
 require "init.php";			// set up (connect to DB, etc)
 $title="Name of Tournament";
 require "head.php";			// Generate header as appropriate
?>
<? if(!$auth) { ?>
 <form method="post" action="?login">
 <p><input type="text" name="login_u" size="10" /><input type="password" name="login_p" size="10" /> <input type="submit" value="Log in" /></p>
 </form>
<? } else {?>
 <p><a href="?kill=now">[Log out]</a></p>     
 <?
}
 require "foot.php";			// finish off page

 ?>
