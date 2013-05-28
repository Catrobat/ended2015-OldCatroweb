<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

$this->module->addGlobalCss('normalize.css');
$this->module->addGlobalCss('base.css');
$this->module->addGlobalCss('header.css');
$this->module->addGlobalCss('footer.css');


$this->module->addGlobalJs('baseClassVars.js');
$this->module->addGlobalJs('classy.js');
$this->module->addGlobalJs('header.js');
$this->module->addGlobalJs('footer.js');
$this->module->addGlobalJs('languageHandler.js');
$this->module->addGlobalJs('searchBar.js');



?>
<!DOCTYPE html>
<html lang="<?php echo $this->languageHandler->getLanguage()?>">
  <head>
    <meta charset="utf-8" />
    <title><?php echo $this->getWebsiteTitle() ?></title>
    <meta name="description" content="Catroid is a visual programming language for Android devices that is inspired by the Scratch programming language for PCs, developed by the Lifelong Kindergarten Group at the MIT Media Lab. It is the aim of the Catroid project to facilitate the learning of programming skills among children and users of all ages. No desktop or notebook computer is needed.">
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <?php echo $this->getGlobalCss(); ?>
    <?php echo $this->getCss(); ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo JQUERY_VERSION; ?>/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo BASE_PATH . CACHE_PATH; ?>jquery<?php echo JQUERY_VERSION; ?>.min.js"><\/script>')</script>
    <script src="<?php echo BASE_PATH . CACHE_PATH; ?>jquery.history.js"></script>

    <link rel="icon" href="<?php echo BASE_PATH?>images/logo/favicon.png<?php echo '?'.VERSION?>" type="image/png" />
  </head>
  <body>
    <div id="wrapper">
      <?php include($this->header);?>
      <?php include($this->viewer);?>
      <div id="noscript">
        <script>$('#noscript').hide(); $('#wrapper article').show();</script>
        <img src="<?php echo BASE_PATH; ?>images/symbols/warning.png" alt="" />
        <p><?php echo $this->languageHandler->getString('template_body_nojs'); ?></p>
      </div>
    </div>
    
    <footer>
      <?php include($this->footer);?>
    </footer>

    <?php echo $this->getGlobalJs(); ?>
    <?php echo $this->getJs(); ?>

    <?php echo '  <img id="ga" src="' . googleAnalyticsGetImageUrl() . '" />'; ?>
  </body>
</html>
