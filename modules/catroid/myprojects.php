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

class myprojects extends CoreAuthenticationUser {

  public function __construct() {
    parent::__construct();
    $this->addCss('myprojects.css');
    $this->addJs("myprojects.js");
  }

  public function __authenticationFailed() {

  }

  public function __default() {
    $this->projects = $this->retrieveMyProjects($this->session->userLogin_userId);
  }

  public function delete() {
    if(isset($_REQUEST['id'])) {
      pg_execute($this->dbConnection, "hide_project", array($_REQUEST['id'])) or
        $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }
    $this->projects = $this->retrieveMyProjects($this->session->userLogin_userId);
  }
  
  public function retrieveMyProjects($userId) {
    $result = pg_execute($this->dbConnection, "get_my_projects", array($userId)) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    if(pg_num_rows($result) > 0) {
      return pg_fetch_all($result); 
    }
    return NULL;
  }
  
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
