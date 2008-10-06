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
/* 
 * head.php
 *
 * Takes:
 * $title -- the page title
 *
 * Author: Avram Lyon
 * Created: 21 February 2004
 */
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
    echo "\n"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 <meta http-equiv="Content-type" content='text/html; charset="UTF-8"' />
 <title><?php echo $title ?></title>
 <link rel="stylesheet" type="text/css" href="qbsql.css" />
</head>
<body>
 <h1><?php echo $title ?></h1>
