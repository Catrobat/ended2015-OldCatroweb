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
?>

<!DOCTYPE HTML>
<html lang="<?php echo $this->languageHandler->getLanguage()?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <!-- <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, minimum-scale=1.0, maximum-scale=1.3, initial-scale=1.0, user-scalable=yes" /> -->
    
  <?php if(isset($_SESSION["errorArgs"][0]) && $_SESSION["errorArgs"][0] == "doAReload") { $_SESSION["errorArgs"][0] = null; ?>
	<meta http-equiv="refresh" content="30;url=<?php echo BASE_PATH?>" />
  <?php }?>
    
  <title><?php echo $this->getWebsiteTitle() ?></title>
  <link href="<?php echo BASE_PATH?>include/css/baseStyle.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/header.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/buttons.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_PATH?>include/css/login.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
<?php while($css = $this->getCss()) {?>
  <link href="<?php echo BASE_PATH?>include/css/<?php echo $css.'?'.VERSION?>" media="screen" rel="stylesheet" type="text/css" />
<?php }?>
<?php if(!$this->isMobile) {?>
  <link href="<?php echo BASE_PATH?>include/css/baseStyleDesktop.css?<?php echo VERSION; ?>" media="screen" rel="stylesheet" type="text/css" />
<?php }?>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/baseClassVars.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/commonFunctions.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript">
    __baseClassVars.basePath = <?php echo "'".BASE_PATH."'"; ?>;
    __baseClassVars.corePath = <?php echo "'".CORE_BASE_PATH."'"; ?>;
  </script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/classy.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/jquery.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/headerMenu.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/login.js?<?php echo VERSION; ?>" ></script>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/languageHandler.js?<?php echo VERSION; ?>" ></script>    
  
<?php while($js = $this->getJs()) {?>
  <script type="text/javascript" src="<?php echo BASE_PATH?>include/script/<?php echo $js.'?'.VERSION?>"></script>
<?php }?>
  <link rel="icon" href="<?php echo BASE_PATH?>images/logo/favicon.png<?php echo '?'.VERSION?>" type="image/png" />
</head>

<body>
  <div class="webMainContainer">
<?php include($this->header);?>

<?php include($this->viewer);?>
  
<?php include($this->footer);?>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      bindAjaxLoader("<?php echo BASE_PATH?>");
    });
  </script>
<?php echo '  <img src="' . googleAnalyticsGetImageUrl() . '" />'; ?> 
</body>
</html>
