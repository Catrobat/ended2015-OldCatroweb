<?php
/**
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

class CorePresenter_wav extends CorePresenterCommon {
  public function __construct(CoreModule $module) {
    parent::__construct($module);
  }

  public function display() {
    header('Content-Type: audio/x-wav');
    header('Content-Disposition: inline;');
    header('Cache-Control: no-cache');
    header("Content-Transfer-Encoding: binary"); 
    $this->data = $this->module->getData();
    if(is_array($this->data) && !empty($this->data)) {
      foreach ($this->data as $key => $val) {
        echo $val;
      }
    }
    return true;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
