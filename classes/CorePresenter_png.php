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

class CorePresenter_png extends CorePresenterCommon {
  private $data;

  public function __construct(CoreModule $module) {
    parent::__construct($module);
    $this->data = $this->module->getData();
  }

  public function display() {
    header("Content-Type: image/png");
    if(is_array($this->data) && !empty($this->data)) {
      foreach ($this->data as $key => $val) {
        $img = imagecreatefromstring($val);
        imagepng($img, null, 9, PNG_NO_FILTER);
        imagedestroy($img);
      }
    }
    return true;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
