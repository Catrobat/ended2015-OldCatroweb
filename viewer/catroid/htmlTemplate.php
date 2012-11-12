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

$this->module->addGlobalJs('baseClassVars.js');
$this->module->addGlobalJs('classy.js');
$this->module->addGlobalJs('commonFunctions.js');
$this->module->addGlobalJs('headerMenu.js');
$this->module->addGlobalJs('login.js');
$this->module->addGlobalJs('languageHandler.js');

?>
<!DOCTYPE HTML>
<html lang="<?php echo $this->languageHandler->getLanguage()?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <!-- <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, minimum-scale=1.0, maximum-scale=1.3, initial-scale=1.0, user-scalable=yes" /> -->
    
  <title><?php echo $this->getWebsiteTitle() ?></title>
  <link href="<?php echo BASE_PATH?>include/css/baseStyle.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/header.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/buttons.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/login.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
<?php while($css = $this->getCss()) {?>
  <link href="<?php echo BASE_PATH?>include/css/<?php echo $css.'?'.VERSION?>" media="screen" rel="stylesheet" type="text/css" />
<?php }?>

<?php if(!$this->isMobile)  {?>
  <link href="<?php echo BASE_PATH?>include/css/baseStyleDesktop.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
<?php }?>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo BASE_PATH?>include/script/jquery-1.8.2.min.js"><\/script>')</script>
  <?php echo $this->getGlobalJs(); ?>
  <?php echo $this->getJs(); ?>
  <script type="text/javascript">
    var languageStringsObject = { 
      "ajax_took_too_long" : "<?php echo $this->module->errorHandler->getError('viewer', 'ajax_took_too_long'); ?>",
      "ajax_timed_out" : "<?php echo $this->module->errorHandler->getError('viewer', 'ajax_timed_out'); ?>"
    };
    var common = new Common(languageStringsObject);
  </script>
  
  
  <link rel="icon" href="<?php echo BASE_PATH?>images/logo/favicon.png<?php echo '?'.VERSION?>" type="image/png" />
</head>

<body>
  <div class="webMainContainer">
<?php include($this->header);?>

  <div id="ajaxAnswerBoxContainer">
    <div id="ajaxAnswerBox" class="blueBoxMain">
      <div class="whiteBoxMain"></div>
    </div>
  </div>

<?php include($this->viewer);?>
  
<?php include($this->footer);?>
  </div>
<?php echo '  <img src="' . googleAnalyticsGetImageUrl() . '" />'; ?> 
</body>
</html>
