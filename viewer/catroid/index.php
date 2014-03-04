<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2014 The Catrobat Team
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

?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="//code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css">
<script src="/include/script/jquery.mobile.custom.js"></script>

<script>
var actualElement = 0;
src = [<?php
    for($i=0; $i < count($this->featuredProjects); $i++)
      echo '"'.$this->featuredProjects[$i].'", ';
    ?>];
var maxNumberOfElements = <?php echo(count($this->featuredProjects));              
  ?>

var timeout;
  
function getProjectLink(num) {
  var x = src[num].replace("<?php echo BASE_PATH . 'resources/featured/'; ?>","");
  x = x.replace(".jpg","");
  var link = <?php echo '"' . BASE_PATH . 'details/"'; ?> + x;
  return link;
}
  
function fade() {
  document.getElementById("" + actualElement).setAttribute("style", "display: none");
  document.getElementById("span" + actualElement).setAttribute("class", "pagination");
  
  actualElement++;
  if (actualElement == maxNumberOfElements ) {
    actualElement = 0;
  }
  
  document.getElementById("" + actualElement).setAttribute("style", "display: visible");
  document.getElementById("span" + actualElement).setAttribute("class", "pagination_selected");  
  
  clearTimeout(timeout);
  timeout = setTimeout("fade()",5*1000);
};

function next(nextElement) {
  if (nextElement == maxNumberOfElements ) {
    nextElement = 0;
  }
  
  document.getElementById("" + actualElement).setAttribute("style", "display: none");
  document.getElementById("span" + actualElement).setAttribute("class", "pagination");
  
  actualElement++;
  if (actualElement == maxNumberOfElements ) {
    actualElement = 0;
  }
  document.getElementById("" + nextElement).setAttribute("style", "display: visible");
  document.getElementById("span" + nextElement).setAttribute("class", "pagination_selected");  
  
  actualElement = nextElement;
  clearTimeout(timeout);
  timeout = setTimeout("fade()",5*1000);
};

function prev() {
  document.getElementById("" + actualElement).setAttribute("style", "display: none");
  document.getElementById("span" + actualElement).setAttribute("class", "pagination");

  actualElement--;
  if (actualElement < 0 ) {
    actualElement = maxNumberOfElements - 1;
  }
  
  document.getElementById("" + actualElement).setAttribute("style", "display: visible");
  document.getElementById("span" + actualElement).setAttribute("class", "pagination_selected");  
  
  clearTimeout(timeout);
  timeout = setTimeout("fade()",5*1000);
};

$(function(){
  $( '#featuredProject' ).on( "swipeleft", swipeleftHandler );
  function swipeleftHandler( event ){
    fade();
  }
});

$(function(){
  $( '#featuredProject' ).on( "swiperight", swiperightHandler );
  function swiperightHandler( event ){
    document.getElementById("featuredProject").setAttribute("Reeesl", "nje " + actualElement );
    prev();
  }
});

onload = function(){
  document.getElementById("0").setAttribute("style", "display: visible");
  document.getElementById("span0").setAttribute("class", "pagination_selected");

  timeout = setTimeout("fade()",5*1000);
};

</script>

      <article>
        <div id="programmOfTheWeek">
          <header><?php echo $this->languageHandler->getString('recommended'); ?></header>
          <div id="featuredProject">
             <?php
              for($i=0; $i < count($this->featuredProjects); $i++)
                echo '<img id="' . $i . '" style="display:none;" src=' . $this->featuredProjects[$i] . ' 
                      onclick="javascript:location.href=getProjectLink(' . $i . ')">';              
            ?>
          </div>
        </div>
        <?php
          for($i=0; $i < count($this->featuredProjects); $i++)
            echo '<span class="pagination" onclick="javascript:next(' . $i . ')" id="span' . $i . '"></span>';              
        ?>
        <div class="projectSpacer"></div>

        <header><?php echo $this->languageHandler->getString('mostDownloaded'); ?></header>
        <div id="mostDownloadedProjects" class="projectContainer"></div>
        <div id="mostDownloadedProjectsLoader" class="projectFooter">
          <img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" />
          <p>&nbsp;</p>
        </div>
        <div id="mostDownloadedShowMore" class="projectFooter">
          <div class="img-load-more"></div>
        </div>
        <div class="projectSpacer"></div>

        <header><?php echo $this->languageHandler->getString('mostViewed'); ?></header>
        <div id="mostViewedProjects" class="projectContainer"></div>
        <div id="mostViewedProjectsLoader" class="projectFooter">
          <img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" />
          <p>&nbsp;</p>
        </div>
        <div id="mostViewedShowMore" class="projectFooter">
          <div class="img-load-more"></div>
        </div>
        <div class="projectSpacer"></div>
        
        <header><?php echo $this->languageHandler->getString('newestProjects'); ?></header>
        <div id="newestProjects" class="projectContainer">    
        </div>
        
        <div id="newestProjectsLoader" class="projectFooter">
          <img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" />
          <p>&nbsp;</p>
        </div>
        <div id="newestShowMore" class="projectFooter">
          <div class="img-load-more"></div>
        </div>
        <div class="projectSpacer"></div>
        
      </article>
      <script type="text/javascript">
        $(document).ready(function() {
          var pageLabels = { 'websiteTitle' : '<?php echo SITE_DEFAULT_TITLE; ?>'};
          var index = Index(pageLabels, <?php echo $this->featuredProject; ?>);

          var newest = ProjectObject(<?php echo $this->newestProjectsParams; ?>, {'history' : $.proxy(index.saveHistoryState, index) });
          var downloads = ProjectObject(<?php echo $this->mostDownloadedProjectsParams; ?>, {'history' : $.proxy(index.saveHistoryState, index) });
          var views = ProjectObject(<?php echo $this->mostViewedProjectsParams; ?>, {'history' : $.proxy(index.saveHistoryState, index) });
          index.setProjectObjects(newest, downloads, views);

          newest.init();
          downloads.init();
          views.init();
        });
      </script>
