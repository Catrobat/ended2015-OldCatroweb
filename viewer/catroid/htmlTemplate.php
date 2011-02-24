<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
  <!-- <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, minimum-scale=1.0, maximum-scale=1.3, initial-scale=1.0, user-scalable=yes" /> -->
  <title>Catroid Website</title>
  <link href="<?php echo BASE_PATH?>include/css/baseStyle.css" media="screen" rel="stylesheet" type="text/css" />
  <?php while($css = $this->getCss()) {?>
  	<link href="<?php echo BASE_PATH?>include/css/<?php echo $css?>" media="screen" rel="stylesheet" type="text/css" />
  <?php }?>
  <?php if(!$this->isMobile) {?>
  	<link href="<?php echo BASE_PATH?>include/css/baseStyleDesktop.css" media="screen" rel="stylesheet" type="text/css" />
  <?php }?>
  <?php while($js = $this->getJs()) {?>
  	<script type="text/javascript" src="<?php echo BASE_PATH?>include/script/<?php echo $js?>"></script>
  <?php }?>
  <link rel="icon" href="<?php echo BASE_PATH?>images/layout/favicon.png" type="image/png" />
</head>

<body>
  <div class="webMainContainer">
  <?php include($this->header);?>

  <?php include($this->viewer);?>
  
  <?php include($this->footer);?>
  </div>
</body>
</html>
