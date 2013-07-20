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

?>
<article>
  <header>
    <div>
      <span id="projectDetailsProjectTitle"><?php echo $this->project['title']?></span>
      <div class="projectDetailsAuthorTop">
        <ul>
          <li>
            <div class="img-author"></div>
            <div style="padding-left: 0.5em;">
              <a href="<?php echo BASE_PATH; ?>profile/<?php echo $this->project['uploaded_by']; ?>"><?php echo $this->project['uploaded_by']; ?></a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </header>
  <div id="projectDetailsContainer">
    <div class="projectDetailsThumbnail">
      <a href="<?php echo BASE_PATH?>download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
        <img id="projectDetailsThumbnailImage" src="<?php echo $this->project['image']?>" alt="<?php $this->project['title']?>" />
      </a>
    </div>

    <div class="projectDetailsDescription">
      <div>
        <span class="projectDetailsDescriptionHeading">
          <?php echo $this->languageHandler->getString('description'); ?>
        </span>
      </div>
      <div class="projectDetailsDescriptionText">
        <span>
          <?php echo ($this->project['description'])? $this->project['description'] : $this->languageHandler->getString('no_description_available');?>
        </span>
      </div>
    </div>

    <div class="projectDetailsDownload">
      <div class="projectDetailsDownloadButton">
        <span id="projectDetailsDownloadVersion"><?php echo $this->languageHandler->getString('version_info_text') . " " . $this->project['version_name'];?></span>
        <a style="text-decoration: none;" href="<?php echo BASE_PATH?>download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
          <div class="blue">
            <span class="projectDetailsDownloadText shrinkTextToFit"><?php echo $this->languageHandler->getString('download_button');?></span>
          </div>
        </a>
        <a id="projectDetailsDownloadLicense" href="<?php echo BASE_PATH?>licenseToPlay"><?php echo $this->languageHandler->getString('some_rights_reserved');?></a>
      </div>
    </div>

    <div class="projectDetailsSeperator">
    </div>

    <div class="projectDetailsInformation">
      <ul>
        <li>
          <div class="img-author-blue"></div>
          <div class="projectDetailsInformationText">
            <a href="<?php echo BASE_PATH; ?>profile/<?php echo $this->project['uploaded_by']; ?>"><?php echo $this->project['uploaded_by_string']; ?></a>
          </div>
        </li>
        <li>
          <div class="img-age"></div>
          <div class="projectDetailsInformationText">
            <?php echo $this->project['publish_time_in_words']; ?>
          </div>
        </li>
        <li>
          <div class="img-size"></div>
          <div class="projectDetailsInformationText">
            <?php echo $this->project['fileSize'] . " MB " . $this->languageHandler->getString('filesize'); ?>
          </div>
        </li>
        <li>
          <div class="img-downloads"></div>
          <div class="projectDetailsInformationText">
            <span><?php echo $this->project['download_count'] . " " . $this->languageHandler->getString('downloads'); ?></span>
          </div>
        </li>
        <li>
          <div class="img-views"></div>
          <div class="projectDetailsInformationText">
            <?php echo $this->project['view_count'] . " " . $this->languageHandler->getString('views'); ?>
          </div>
        </li>
        <?php if($this->project['remixof'] != 0) { ?>
        <li>
          <div class="img-views"></div>
          <div class="projectDetailsInformationText">
            <?php echo $this->languageHandler->getString('remix_of'); ?> <a href="<?php echo BASE_PATH; ?>details/<?php echo $this->remixedProject['id']; ?>"><?php echo $this->remixedProject['title']; ?></a>
          </div>
        </li>
        <?php } ?>
        <?php if($this->numberOfRemixes != 0) { ?>
        <li>
          <div class="img-views"></div>
          <div class="projectDetailsInformationText">
            <?php echo $this->numberOfRemixes . " " . $this->languageHandler->getString('remixes') ?>
          </div>
        </li>
        <?php } ?>
      </ul>
    </div>

    <div class="projectDetailsSeperator">
    </div>

    <div id="projectDetailsReportContainer">
      <div id="detailsFlagButton">
        <button type="button" class="buttonBlue" id="reportAsInappropriateButton">
          <span id="detailsFlagButtonText"><?php echo $this->languageHandler->getString('report_as_inappropriate')?></span>
        </button>
      </div>
      <?php if($this->project['showReportAsInappropriateButton']['show']) : ?>
      <div class="reportAsInappropriateDialog" id="reportAsInappropriateDialog">
        <form method="POST" class="reportInappropriateForm">
          <p><?php echo $this->languageHandler->getString('report_as_inappropriate_label')?></p>
          <input type="hidden" id="reportInappropriateProjectId" value="<?php echo $this->project['id']?>"/>
          <p>
          <textarea class="reportInappropriateReason" id="reportInappropriateReason" name="flagReason" placeholder="<?php echo $this->languageHandler->getString('flag_reason_placeholder')?>" required="required"></textarea>
          </p>
          <p class="reportAsInappropriateDialogButtons">
            <input type="button" class="buttonWhite buttonSmall" id="reportInappropriateCancelButton" value="<?php echo $this->languageHandler->getString('cancel')?>"/>
            <input type="button" class="buttonGreen buttonSmall" id="reportInappropriateReportButton" value="<?php echo $this->languageHandler->getString('report')?>"/>
          </p>
        </form>
      </div>
      <div id="reportAsInappropriateAnswer"></div>
      <?php else : ?>
      <div class="reportAsInappropriateDialog" id="reportAsInappropriateDialog">
        <?php echo $this->project['showReportAsInappropriateButton']['message']; ?>
      </div>
    <?php endif; ?>
    </div>
  </div>
</article>
<script type="text/javascript">
  $(document).ready(function() {
    Details = new ProjectDetails();
  });
</script>
