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

class saveDataToSession extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
  }

  public function __default() {
    $this->data=$_REQUEST['content'];    
    if(isset($_POST['content'])) {
      foreach($_POST['content'] as $key => $value) {
        $this->saveData($key, $value);                
      }
    }
  }
  
  private function saveData($key,$value) {
    switch($key) {
      case 'pageNr': 
        $this->session->pageNr = $value;
        break;
      case 'searchQuery':
        $this->session->searchQuery = $value;
        break;
      case 'task':
        $this->session->task = $value;
        break;
      case 'errorType':
        $this->session->errorType = $value;
        break;
      case 'errorCode':
        $this->session->errorCode = $value;
        break;
      case 'errorExtraInfo':
        $this->session->errorExtraInfo = $value;
        break;        
      case 'showCatroidDescription':
        $this->session->showCatroidDescription = $value;
        break;
    }
  }
}
?>
