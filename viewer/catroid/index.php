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
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
            	<div class="webMainContentTitle">Newest Projects</div>
  		   		<div class="projectListRow">
  		   			<div class="whiteBoxMain">
    					<div class="projectListElementRow">
    				    <?php
    					  $i=1;
    					  if($this->projects) {
                            foreach($this->projects as $project) {
                        ?>
                        <?php if($i%(PROJECT_ROW_MAX_PROJECTS+1) == 0) {?>
                        <div style="clear:both;"></div>
                        </div> <!-- projectListElementRow close //-->
        			</div> <!-- whiteBoxMain close //-->
              		<div class="projectListSpacer"></div>
              	</div> <!-- projectListRow close //-->
              	<div class="projectListRow">
            		<div class="whiteBoxMain">
            			<div class="projectListElementRow">
            			<?php $i=1;}?>
                      		<div class="projectListElement">
                        		<div class="projectListThumbnail" title="<?php echo $project['title']?>">
          							<div><a class="projectListDetailsLink" href="<?php echo BASE_PATH?>catroid/details/<?php echo $project['id']?>">
          								<img class="projectListPreview" src="<?php echo $project['thumbnail']?>" alt="pro" />
          							</a></div>
          						</div>
          						<div class="projectListDetails">
          							<a class="projectListDetailsLink" href="<?php echo BASE_PATH?>catroid/details/<?php echo $project['id']?>">
                                        <?php echo $project['title_short']?>
                                    </a>
                        		</div>
                      		</div>
                        <?php $i++;}}?>
                        <div style="clear:both;"></div>
                  		</div> <!-- projectListElementRow close //-->
  					</div> <!-- whiteBoxMain close //-->
  					<div class="projectListSpacer"></div>
  				</div> <!-- projectListRow close //-->
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
