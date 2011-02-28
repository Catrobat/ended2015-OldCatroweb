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
  <script type="text/javascript">
      $(document).ready(function() {
        new NewestProjects(<?php echo "'".BASE_PATH."', '".PROJECT_PAGE_MAX_PROJECTS."', '".$this->module->session->pageNr."', '".$this->numberOfPages."'"; ?>);        
      });
    </script>
  	<div class="webMainTop">
  		<div class="blueBoxMain">
  		    <div class="webMainHead">
  		    	<div class="webHeadLogo">
  		    	  	<a id="aIndexWebLogoLeft" href="<?php echo BASE_PATH?>catroid/index">
  		      			<img class="catroidLogo" src="<?php echo BASE_PATH?>images/logo/logo_head.png" alt="head logo" />
  		      	  	</a>
  		      	</div>
  		      	<div class="webHeadTitle">
  		      		<div class="webHeadTitleName">
  		      			<a class="noLink" href="http://code.google.com/p/catroid/downloads/list" target="_blank">
  			      			<span class="webHeadTitleName">Catroid</span>
  			      			<span class="webHeadTitleBeta"><?php echo DEVELOPMENT_STATUS?></span>  			      			
  			      		</a>
  			      		
  		      		</div>
  		      		<div class="webHeadTitleDownload">
  			      		 <a class="button green small webHeadTitleDownload" href="http://code.google.com/p/catroid/downloads/list" target="_blank">
  			      		 	Download
  			      		 </a>
  		      		</div>
  		      	</div>
  		      	<div style="clear:both;"></div>
  		    </div>
  		</div>
  	</div>