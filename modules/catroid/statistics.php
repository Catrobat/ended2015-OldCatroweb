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

class statistics extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->addCss('statistics.css');
    $this->addJs('statistics.js');
  }

  public function __default() {
    echo "Hello from Statistics Module";
    exit();
  }
  
  public function userStats() {
    $this->title = $this->languageHandler->getString("title");

    
    $query = pg_execute($this->dbConnection, "number_of_users", array()) or
               $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $result = pg_fetch_assoc($query);
    $this->numberOfUsers = $result['count'];
    pg_free_result($query);
    
    $query = pg_execute($this->dbConnection, "number_of_projects", array()) or
               $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $result = pg_fetch_assoc($query);
    $this->numberOfProjects = $result['count'];
    pg_free_result($query);

    $limit = 5;
    $offset = 0;
    $query = pg_execute($this->dbConnection, "user_with_most_created_projects", array($limit, $offset)) or
               $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $rows = array();
    while($result = pg_fetch_assoc($query)) {
      array_push($rows, $result);
    }
    $this->usersWithMostProjects = $rows;
    
    pg_free_result($query);

    $this->projectsPerUser = $this->getAverageProjectsPerUser($this->numberOfProjects, $this->numberOfUsers);
  }
  
  public function getAverageProjectsPerUser($users, $projects) {
    if($users == 0) {
      return 0;
    }
    return round($projects / $users, 2);
  }
  
  
  
  public function __destruct() {
    parent::__destruct();
  }
}
