<?php
 ob_start();
 $version = "v01a";
 $gitphp_appstring = "gitphp $version";
/*
 *  index.php
 *  gitphp: A PHP git repository browser
 *  Component: Index script
 *
 *  Copyright (C) 2006 Christopher Han <xiphux@gmail.com>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

 /*
  * Configuration
  */
 include_once('config.inc.php');

 /*
  * Instantiate Smarty
  */
 include_once($gitphp_conf['smarty_prefix'] . "Smarty.class.php");
 $tpl =& new Smarty;
 $tpl->load_filter('output','trimwhitespace');

 /*
  * Function library
  */
 include_once('gitphp.lib.php');

 $tpl->clear_all_assign();
 $tpl->assign("version",$version);
 $tpl->assign("title",$gitphp_conf['title']);
 $tpl->display("header.tpl");

 $tpl->display("footer.tpl");

 ob_end_flush();

?>
