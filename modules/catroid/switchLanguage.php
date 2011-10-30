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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class switchLanguage extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
  }

  public function __default() {
  }

  public function switchIt() {
    $this->statusCode = 500;
    if(isset($_POST['language'])) {
      if($this->session->userLogin_userId > 0) {
        $this->doSaveLanguageToProfile($_POST['language'], $this->session->userLogin_userId);
      }

      $this->languageHandler->setLanguageCookie($_POST['language']);
      $this->statusCode = 200;
    }    
  }
  
  private function doSaveLanguageToProfile($language, $id) {
    try {
      $query = "EXECUTE update_user_language_by_id('$language','$id')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        $return_value = false;
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      $return_value = true;
    } catch(Exception $e) {
      $return_value = false;
      $this->answer .= $this->errorHandler->getError('profile', 'language_update_failed', $e->getMessage());
    }
    return $return_value;

  } 

  public function __destruct() {
    parent::__destruct();
  }
}
?>
