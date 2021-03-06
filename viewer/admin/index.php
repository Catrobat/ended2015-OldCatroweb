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

<body>
  <h2>Catroid Administration Site</h2>
  <a id="aAdministrationTools" href="<?php echo BASE_PATH;?>admin/tools">Administration Tools</a><br />
  <a id="aProjectUploader" href="<?php echo BASE_PATH;?>admin/projectUploader">Project Uploader</a><br />
  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>">&lt;- back to catroidweb</a>
  
  <br />
  <br />
  <?php if($this->numUnapprovedProjects > 0) : ?>
  
  <div class="unapprovedProjects">
    <a id="aAdminApprovedProjects" href="<?php echo BASE_PATH; ?>admin/tools/approveProjects">
      Unapproved projects: <span id="numberOfUnapprovedProjects"><?php echo $this->numUnapprovedProjects; ?></span>
    </a>
  </div>
  <?php else : ?>
  <div class="approvedProjects">All projects approved</div>
  <?php endif; ?>
</body>
