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

abstract class CorePresenterCommon {
    protected $module;
    public $viewHelper;
    public $isMobile;

    public function __construct(CoreModule $module) {
        $this->module = $module;
        $this->module->getCoreRegistry()->setViewHelper(new CoreViewHelper());
        $this->viewHelper = $this->module->getCoreRegistry()->getViewHelper();
        $this->isMobile = $this->module->clientDetection->isMobile();
    }

    abstract public function display();
    
    public function getCss() {
      return $this->module->getCss();
    }
    
    public function getJs() {
      return $this->module->getJs();
    }

    public function __destruct() {

    }
}

?>