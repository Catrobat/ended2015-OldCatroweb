<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
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

$this->module->addGlobalCss('adminLayout.css');

$this->module->addGlobalJs('baseClassVars.js');
$this->module->addGlobalJs('classy.js');

?>

<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Administration - Catroid Website</title>
  <?php echo $this->getGlobalCss(true); ?>
  <?php echo $this->getCss(); ?>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo JQUERY_VERSION; ?>/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo BASE_PATH . CACHE_PATH; ?>jquery<?php echo JQUERY_VERSION; ?>.min.js"><\/script>')</script>
  <?php echo $this->getGlobalJs(true); ?>
  <?php echo $this->getJs(); ?>
</head>
  <?php include($this->header);?>
  <?php include($this->viewer);?>
  <?php include($this->footer);?>

</html>
