<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or License, or (at your option) any later version.
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
  <input type="hidden" id="basePath" value="<?php echo BASE_PATH?>">
  <script type="text/javascript">
  	$(document).ready(function() {
  		new ProjectDetails($("#basePath").attr("value"));
  	});
  </script>

  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
  		   		<div class="webMainContentTitle">
  		   			<div class="detailsProjectTitle">
  		   				<?php echo $this->project['title']?>
  		   			</div>
  		   		</div>
            	<div class="detailsDiv">
        			<div class="whiteBoxMain">
        				<div class="detailsFlexDiv">
            				<div class="detailsLeft">
                				<div class="detailsMainImage">
                					<a href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']?>.zip?fname=<?php echo urlencode($this->project['title'])?>">
                						<img class="projectDetailsImage" src="<?php echo $this->project['image']?>" alt="project thumbnail">
                					</a>
            					<div class="detailsLicenseLink">
            					  <a class="licenseLink" href="<?php echo BASE_PATH?>catroid/projectlicense">Some rights reserved</a>
                					</div>
                				</div>
                				<?php if(!$this->isMobile) {?>
                        			<div class="detailsMainStats">
                        			    <div class="detailsStatistics">
                    					<p class="detailsStats"><img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view7.png" alt="view count image">                    					
                    					<!--   <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view8.png" alt="view count image">
                    					-->
                        					<b><?php echo $this->project['view_count']?></b> views</p>
                        					<div style="height:10px;"></div>
                        					<p class="detailsStats"><img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down1.png" alt="download count image">
                    					<!-- <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down_2.png" alt="download count image"> -->
                        					<b><?php echo $this->project['download_count']?></b> downloads</p>
                    					</div>
                        			</div>
                    			<?php }?>
                			</div>
                			<div class="detailsRight">
                				<div class="detailsMainDescription">
      		   						<div class="detailsPublishTime">
                    					<p class="detailsTime"><img class="projectDetailsTimeSymbol" src="<?php echo BASE_PATH?>images/symbols/clock2.png" alt="publish time image">
                					<!-- <img class="projectDetailsTimeSymbol" src="<?php echo BASE_PATH?>images/symbols/clock4.png" alt="publish time image"> -->
    									uploaded<br>
    									<b><?php echo $this->project['publish_time_in_words']?></b> ago.</p>
                    				</div>
                    				<div class="detailsDownloadButton">
                            			<a class="button blue middle" style="white-space:nowrap;" href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']?>.zip?fname=<?php echo urlencode($this->project['title'])?>">
                            				<img class="projectDetailsDownloadSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down5.png" alt="download project button">
                            				<span class="detailsDownloadButtonText">Download</span>
                            			</a>
                    				</div>
                    				<?php if(!$this->isMobile && $this->project['qr_code_image']) {?>
                            			<div class="detailsQRCode">
        									<img class="projectDetailsQRImage" src="<?php echo $this->project['qr_code_image']?>" alt="qr code image">
                            			</div>
                    			    <?php }?>
                    				<div class="detailsProjectDescription">
                    					<?php if($this->project['description_short']) {?>
                    						<p class="detailsDescription" id="detailsDescription">
                    						    <?php echo $this->project['description_short'];?>
                    						</p>
                    						<input type="hidden" id="fullDescriptionText" value="<?php echo htmlspecialchars($this->project['description'])?>">
                    						<input type="hidden" id="shortDescriptionText" value="<?php echo htmlspecialchars($this->project['description_short'])?>">
                    						<button type="button" id="showFullDescriptionButton" class="button green compact showFullDescriptionButton">
                        					  	<span class="detailsMoreButtonText">more</span>
                        					</button>
                        					<button type="button" id="showShortDescriptionButton" class="button green compact showShortDescriptionButton">
                        					  	<span class="detailsLessButtonText">less</span>
                        					</button>
                    					<?php } else {?>
                    						<p class="detailsDescription">
                    						    <?php echo $this->project['description'];?>
                    						</p>
                    					<?php }?>
                    					<div style="clear:both;"></div>
                    				</div>
                    			</div>
                    			<?php if($this->isMobile) {?>
                        			<div class="detailsMainStats">
                        			    <div class="detailsStatistics">
                        					<p class="detailsStats"><img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view7.png" alt="view count image">
                    					<!-- <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view8.png" alt="view count image"> -->
                        					<b><?php echo $this->project['view_count']?></b> views</p>
                        					<div style="height:10px;"></div>
                        					<p class="detailsStats"><img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down1.png" alt="download count image">
                    					<!-- <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down_2.png" alt="download count image"> -->
                        					<b><?php echo $this->project['download_count']?></b> downloads</p>
                    					</div>
                        			</div>
                    			<?php }?>
                    			
                        		<div class="detailsMainStats">
                        			<div class="detailsFlagButton" id="detailsFlagButton">
                      		   			<button type="button" class="button white medium" id="reportAsInappropriateButton">
                      		   				<span class="detailsFlagButtonText">report as inappropriate</span>                            			
                                    	</button>
                            		</div>
                            		<div class="reportAsInappropriateDialog" id="reportAsInappropriateDialog">
                            			<form method="POST" class="reportInappropriateForm">
                            				<span class="reportInappropriateLabel">Why do you think this project is inappropriate?</span><br>
                            				<input type="hidden" id="reportInappropriateProjectId" value="<?php echo $this->project['id']?>">
      										<input type="text" class="reportInappropriateReason" id="reportInappropriateReason" name="flagReason" placeholder="enter a reason" required>
      										<input type="button" class="button white compact reportInappropriateButton" id="reportInappropriateReportButton" value="Report">
      										<input type="button" class="button white compact reportInappropriateButton" id="reportInappropriateCancelButton" value="Cancel">
    									</form>
                            		</div>
                            		<div class="reportAsInappropriateAnswer" id="reportAsInappropriateAnswer"></div>
                        		</div>
                    			
                			</div>
            				<div style="clear:both;"></div>
        				</div>    	
        			</div>
            	</div>
  		  	</div>
  		</div>
  	</div>