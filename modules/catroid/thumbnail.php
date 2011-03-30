<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
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

class thumbnail extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->addCss('thumbnailUploader.css');
  }

  public function __default() {

	}

  public function thumbnailUploader() {
  	if(isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['error'] == 0) {
      if($this->uploadThumbnail($_FILES)) {
        $this->answer = "Thumbnail upload successful!";
      } else {
        if(!$this->answer) {
      	  $this->answer = "Error: Thumbnail upload NOT successful! Try again!";
      	}
      }
  	}

  	$this->htmlFile = "thumbnailUploader.php";
  }

  public function uploadThumbnail($fileData) {
    $upfile = $fileData['upload']['name'];
    $updir = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$upfile;
    if(!file_exists($updir)) {
      return(copy($fileData['upload']['tmp_name'], $updir));
    } else {
      $this->answer = "Error: A thumbnail for this project is already present!";
      return false;
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
