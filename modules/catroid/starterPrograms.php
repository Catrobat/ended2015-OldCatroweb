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

class starterPrograms extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->addCss('starterPrograms.css');
    $this->addJs('starterPrograms.js');
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
  }

  public function __default() {
    $starterProjects = $this->getStarterProjects();
    
    $starterProjectsIdGroup1 = pg_fetch_all($starterProjects[0]);
    $starterProjectsIdGroup2 = pg_fetch_all($starterProjects[1]);
    $starterProjectsIdGroup3 = pg_fetch_all($starterProjects[2]);
    $starterProjectsIdGroup4 = pg_fetch_all($starterProjects[3]);
    
    $this->projectsGrouped = $this->getGroupedProjects($starterProjectsIdGroup1, $starterProjectsIdGroup2, $starterProjectsIdGroup3, $starterProjectsIdGroup4);
    
  }
  
  public function getStarterProjects() {
    $result1 = pg_execute($this->dbConnection, "get_starterProjectIds_group1", array()) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $result2 = pg_execute($this->dbConnection, "get_starterProjectIds_group2", array()) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $result3 = pg_execute($this->dbConnection, "get_starterProjectIds_group3", array()) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $result4 = pg_execute($this->dbConnection, "get_starterProjectIds_group4", array()) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());

    $result = array();
    
    array_push($result, $result1);
    array_push($result, $result2);
    array_push($result, $result3);
    array_push($result, $result4);
    
    return $result;
  }
  
  public function getGroupedProjects($starterProjectsIdGroup1, $starterProjectsIdGroup2, $starterProjectsIdGroup3, $starterProjectsIdGroup4) {
       
    $group1 = array();
    $group2 = array();
    $group3 = array();
    $group4 = array();
    
    $result = array("group1" => array(),
                    "group2" => array(),
                    "group3" => array(),
                    "group4" => array());
   
    for($i=0;isset($starterProjectsIdGroup1[$i]);$i++) {
      $tmp = pg_execute($this->dbConnection, "get_starterProjects_group", array($starterProjectsIdGroup1[$i]['project_id'])) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());

      $tmp = pg_fetch_object($tmp);
      
      $file = CORE_BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";

      if(!file_exists($file))
        $tmp->thumb = BASE_PATH."resources/thumbnails/thumbnail_small.png";
      else
        $tmp->thumb = BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
      
      array_push($result["group1"], $tmp);
    }
    
    for($i=0;isset($starterProjectsIdGroup2[$i]);$i++) {
      $tmp = pg_execute($this->dbConnection, "get_starterProjects_group", array($starterProjectsIdGroup2[$i]['project_id'])) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
      $tmp = pg_fetch_object($tmp);
      
      $file = CORE_BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
      
      if(!file_exists($file))
        $tmp->thumb = BASE_PATH."resources/thumbnails/thumbnail_small.png";
      else
        $tmp->thumb = BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
      
      array_push($result["group2"], $tmp);
    }
    
    for($i=0;isset($starterProjectsIdGroup3[$i]);$i++) {
      $tmp = pg_execute($this->dbConnection, "get_starterProjects_group", array($starterProjectsIdGroup3[$i]['project_id'])) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
      $tmp = pg_fetch_object($tmp);
      
      $file = CORE_BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
      
      if(!file_exists($file))
        $tmp->thumb = BASE_PATH."resources/thumbnails/thumbnail_small.png";
      else
        $tmp->thumb = BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
      
      array_push($result["group3"], $tmp);
    }  
    
    for($i=0;isset($starterProjectsIdGroup4[$i]);$i++) {
      $tmp = pg_execute($this->dbConnection, "get_starterProjects_group", array($starterProjectsIdGroup4[$i]['project_id'])) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
      $tmp = pg_fetch_object($tmp);
    
      $file = CORE_BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
    
      if(!file_exists($file))
        $tmp->thumb = BASE_PATH."resources/thumbnails/thumbnail_small.png";
      else
        $tmp->thumb = BASE_PATH."resources/thumbnails/".$tmp->id."_small.png";
    
      array_push($result["group4"], $tmp);
    }
    
    return $result;
    
  }
  
 
  public function __destruct() {
    parent::__destruct();
  }
}
?>
