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
    <script type="text/javascript">
      $(document).ready(function() {
        new ProjectDetails(<?php echo $this->project['id']; ?>);
      });
    </script>
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div class="webMainContent">
          <div class="webMainContentTitle">
            <div class="detailsProjectTitle"><?php echo $this->project['title']?></div>
          </div>
          <div class="detailsDiv">
            <div class="whiteBoxMain">
              <div class="detailsFlexDiv">
                <div class="detailsLeft">
                  <div class="detailsMainImage">
                    <a id="downloadProjectThumb" href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
                      <img class="projectDetailsImage" src="<?php echo $this->project['image']?>" alt="project thumbnail">
                    </a>
                    <div class="detailsLicenseLink">
                      <a class="licenseLink" href="<?php echo BASE_PATH?>catroid/projectlicense"><?php echo $this->languageHandler->getString('some_rights_reserved')?></a>
                    </div>
                  </div>
<?php if(!$this->isMobile) {?>
                  <div class="detailsMainStats">
                    <div class="detailsStatistics">
                      <p class="detailsStats">
                        <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view7.png" alt="view count image">
                        <strong><?php echo $this->project['view_count']?></strong> <?php echo $this->languageHandler->getString('views'); ?> 
                      </p>
                      <div style="height:10px;"></div>
                      <p class="detailsStats">
                        <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down1.png" alt="download count image">
                        <strong><?php echo $this->project['download_count']?></strong> <?php echo $this->languageHandler->getString('downloads')?> 
                      </p>
                    </div>
                  </div>
<?php }?>
                </div>
                <div class="detailsRight">
                  <div class="detailsMainDescription">
                    <div class="detailsPublishTime">
                      <div style="float:left;"><img class="projectDetailsTimeSymbol" src="<?php echo BASE_PATH?>images/symbols/clock2.png" alt="publish time image"></div>
                      <div style="float:left;">
                        <p class="detailsTime">
                          <?php echo $this->languageHandler->getString('uploaded')?><br/>
                          <strong><?php echo $this->project['publish_time_in_words']?></strong><br/>
                          <strong><?php echo $this->project['uploaded_by_string']?></strong><br/>
                          <span class="versionInfo"><?php echo $this->languageHandler->getString('version_info_text').' '; ?> <?php echo $this->project['version_name']?></span>
                        </p>
                      </div>
                      <div style="clear:both;"></div>
                    </div>
<?php if($this->project['show_warning'] == true) {?>
                   <div class="oldVersionWarning">
                     <?php echo $this->languageHandler->getString('old_version'); ?>
                     <p><?php echo $this->languageHandler->getString('old_version_author'); ?></p>
                   </div>
<?php } else { ?>
<?php   if($this->project['is_app_present']) {?>
                    <div class="detailsDownloadButton">
                      <button type="button" id="downloadCatroidSwitch" class="button noborderradius blueSelected"><span class="detailsDownloadTypeSwitchText"><?php echo $this->languageHandler->getString('project'); ?></span></button><button type="button" id="downloadAppSwitch" class="button noborderradius blue"><span class="detailsDownloadTypeSwitchText"><?php echo $this->languageHandler->getString('app'); ?></span></button>
                      <button type="button" id="downloadInfoButton" class="button noborderradius green" style="width:17%;"><span class="detailsDownloadTypeSwitchText">?</span></button>
                      <div class="detailsDownloadInfoTextContainer">
                        <div id="downloadCatroidInfo" class="detailsDownloadInfoText"><?php echo $this->languageHandler->getString('download_info_catroid'); ?></div>
                        <div id="downloadAppInfo" class="detailsDownloadInfoText"><?php echo $this->languageHandler->getString('download_info_app'); ?></div>
                      </div>
                    </div>
                    <div id="downloadAppSection">
                      <div id="downloadAppButton" class="detailsDownloadButton">
                        <a id="downloadAppProjectLink" class="button blue middle" style="white-space:nowrap;" href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo APP_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
                          <img class="projectDetailsDownloadSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down5.png" alt="download project button" />
                          <span class="detailsDownloadButtonText"><?php echo $this->languageHandler->getString('download')?></span>
                        </a>
                        <div class="detailsFileSize"><?php echo $this->languageHandler->getString('filesize')?>: <?php echo $this->project['appFileSize']?> MB</div>
                      </div>
<?php     if(!$this->isMobile && $this->project['qr_code_catroid_image']) {?>
                      <div class="detailsQRCode">
                        <img class="projectDetailsQRImage" src="<?php echo $this->project['qr_code_app_image']?>" alt="qr code image"/>
                      </div>
<?php     }?>
                    </div>
<?php   }?>
                    <div id="downloadCatroidSection">
                      <div class="detailsDownloadButton">
                        <a id="downloadCatroidProjectLink" class="button blue middle" style="white-space:nowrap;" href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
                          <img class="projectDetailsDownloadSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down5.png" alt="download project button" />
                          <span class="detailsDownloadButtonText"><?php echo $this->languageHandler->getString('download')?></span>
                        </a>
                        <div class="detailsFileSize"><?php echo $this->languageHandler->getString('filesize')?>: <?php echo $this->project['fileSize']?> MB</div>
                      </div>
<?php   if(!$this->isMobile && $this->project['qr_code_catroid_image']) {?>
                      <div class="detailsQRCode">
                        <img class="projectDetailsQRImage" src="<?php echo $this->project['qr_code_catroid_image']?>" alt="qr code image"/>
                      </div>
<?php   }?>
                    </div>
<?php   if(!$this->isMobile && $this->project['qr_code_catroid_image']) {?>
                    <div class="detailsQRCodeText">
                      <div id="qrcodeInfo" class="qrcodeInfo"><?php echo $this->languageHandler->getString('qrcode_info')?></div>
                      <button type="button" id="showQrCodeInfoButton" class="button white medium"><span class="showQrCodeInfoButton"><?php echo $this->languageHandler->getString('show_qr_code_info_button')?></span></button>
                      <button type="button" id="hideQrCodeInfoButton" class="button white medium"><span class="hideQrCodeInfoButton"><?php echo $this->languageHandler->getString('hide_qr_code_info_button')?></span></button>
                    </div>
<?php   }?>
                    <div class="detailsProjectDescription">
<?php   if($this->project['description_short']) {?>
                      <p class="detailsDescription" id="detailsDescription"><?php echo $this->project['description_short'];?></p>
                      <input type="hidden" id="fullDescriptionText" value="<?php echo htmlspecialchars($this->project['description'])?>"/>
                      <input type="hidden" id="shortDescriptionText" value="<?php echo htmlspecialchars($this->project['description_short'])?>"/>
                      <button type="button" id="showFullDescriptionButton" class="button green compact showFullDescriptionButton">
                        <span class="detailsMoreButtonText"><?php echo $this->languageHandler->getString('text_more')?></span>
                      </button>
                      <button type="button" id="showShortDescriptionButton" class="button green compact showShortDescriptionButton">
                        <span class="detailsLessButtonText"><?php echo $this->languageHandler->getString('text_less')?></span>
                      </button>
<?php   } else {?>
                      <p class="detailsDescription"><?php echo $this->project['description'];?></p>
<?php   }?>
                      <div style="clear:both;"></div>
                    </div>
<?php }?>
                  </div>
<?php if($this->isMobile) {?>
                  <div class="detailsMainStats">
                    <div class="detailsStatistics">
                      <p class="detailsStats">
                        <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/view7.png" alt="view count image">
                        <strong><?php echo $this->project['view_count']?></strong> <?php echo $this->languageHandler->getString('views')?> 
                      </p>
                      <div style="height:10px;"></div>
                      <p class="detailsStats">
                        <img class="projectDetailsViewSymbol" src="<?php echo BASE_PATH?>images/symbols/arrow_down1.png" alt="download count image">
                        <strong><?php echo $this->project['download_count']?></strong> <?php echo $this->languageHandler->getString('downloads')?> 
                      </p>
                    </div>
                  </div>
<?php }?>

<?php if($this->project['showReportAsInappropriateButton']) { ?>
                  <div class="detailsMainStats">
                    <div class="detailsFlagButton" id="detailsFlagButton">
                      <button type="button" class="button white medium" id="reportAsInappropriateButton">
                        <span class="detailsFlagButtonText"><?php echo $this->languageHandler->getString('report_as_inappropriate')?></span>
                      </button>
                    </div>
                    <div class="reportAsInappropriateDialog" id="reportAsInappropriateDialog">
                      <form method="POST" class="reportInappropriateForm">
                        <span class="reportInappropriateLabel"><?php echo $this->languageHandler->getString('report_as_inappropriate_label')?></span><br/>
                        <input type="hidden" id="reportInappropriateProjectId" value="<?php echo $this->project['id']?>"/>
                        <input type="text" class="reportInappropriateReason" id="reportInappropriateReason" name="flagReason" placeholder="<?php echo $this->languageHandler->getString('flag_reason_placeholder')?>" required="required"/>
                        <input type="button" class="button white compact reportInappropriateButton" id="reportInappropriateReportButton" value="<?php echo $this->languageHandler->getString('report')?>"/>
                        <input type="button" class="button white compact reportInappropriateButton" id="reportInappropriateCancelButton" value="<?php echo $this->languageHandler->getString('cancel')?>"/>
                      </form>
                    </div>
                    <div class="reportAsInappropriateAnswer" id="reportAsInappropriateAnswer"></div>
                  </div>
<?php } else {?>
                  <div class="detailsMainStats">
                    <div class="detailsFlagButton" id="detailsFlagButton">
                      <button type="button" class="button white medium" id="reportAsInappropriateButton">
                        <span class="detailsFlagButtonText"><?php echo $this->languageHandler->getString('report_as_inappropriate')?></span>
                      </button>
                    </div>
                    <div class="reportAsInappropriateDialog" id="reportAsInappropriateDialog">
                        <br/><?php echo $this->languageHandler->getString('report_as_inappropriate_info')?><br/>
                    </div>
                  </div>
<?php }?>
                </div>
                <div style="clear:both;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>