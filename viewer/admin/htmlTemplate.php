<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Administration - Catroid Website</title>
  <link href="<?php echo BASE_PATH?>include/css/adminLayout.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <?php while($css = $this->getCss()) {?>
  	<link href="<?php echo BASE_PATH.CSS_PATH.$css?>" media="screen" rel="stylesheet" type="text/css" />
  <?php }?>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/baseClassVars.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript">
    __baseClassVars.basePath = <?php echo "'".BASE_PATH."'"; ?>;
    __baseClassVars.corePath = <?php echo "'".CORE_BASE_PATH."'"; ?>;
  </script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/classy.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/jquery.js?<?php echo VERSION; ?>" ></script>
  <?php while($js = $this->getJs()) {?>
  	<script type="text/javascript" src="<?php echo BASE_PATH.SCRIPT_PATH.$js?>"></script>
  <?php }?>
</head>
  <?php include($this->header);?>
  <?php include($this->viewer);?>
  <?php include($this->footer);?>

</html>
