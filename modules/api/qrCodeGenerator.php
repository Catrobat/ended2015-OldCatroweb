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

class qrCodeGenerator extends CoreAuthenticationTUGraz {
 
	public function __construct() {
    parent::__construct();
  }

  public function __default() {
    echo 'ok';
    exit();
  }

  public function generate() {
    $this->generateQrCode($_REQUEST['url']);
  }
  
  public function generateQrCode($url) {
    if($url) {
      if(strlen($url) > 14) {
        $image = "/tmp/" . time() . ".png";
        shell_exec('qrencode -o ' . $image . ' ' . $url . ' -s 5 -l H -m 1');
        $this->qr = shell_exec('composite -gravity center ' . BASE_PATH . 'images/logo/qr-logo.png ' . $image . ' -');
      } else {
        $this->qr = shell_exec("qrencode \"".$url."\" -s 5 -m 1 -o -");
      }
    } else {
      return false;
    }
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
