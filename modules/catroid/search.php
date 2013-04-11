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

class search extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('search.css');
    $this->addJs('projectLoader.js');
    $this->addJs('projectContentFiller.js');
    $this->addJs('projectObject.js');
    $this->addJs('projects.js');
  }

  public function __default() {
    $this->setWebsiteTitle("search");

    $params = array();
    $params['numProjectsPerPage'] = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $params['pageNr'] = 1;
    $params['pageNrMax'] = $this->numberOfPages;
    $params['layout'] = PROJECT_LAYOUT_ROW;
    $params['container'] = '#projectContainer';
    
    $params['sort'] = $this->session->sort;
    $params['filter'] = array('searchQuery' => $_REQUEST['search'],
        'author'        => '');
    
    $params['page'] = array('number'             => intVal($this->session->pageNr),
        'numProjectsPerPage' => PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
        'pageNrMax'          => ceil($this->getNumberOfVisibleProjects() / PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE)
    );
    
    $params['config'] = array('PROJECT_LAYOUT_ROW' => PROJECT_LAYOUT_ROW,
        'PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE' => PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
        'PROJECT_LAYOUT_COLUMN' => 2,
        'PROJECT_LAYOUT_COLUMN_PROJECTS_PER_ROW' => 5,
        'sortby' => array('age' => PROJECT_SORTBY_AGE,
            'downloads' => PROJECT_SORTBY_DOWNLOADS,
            'views' => PROJECT_SORTBY_VIEWS,
            'random' => PROJECT_SORTBY_RANDOM)
    );
    $params['userNickname'] = $this->session->userLogin_userNickname;
    $filter =  pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($_REQUEST['search'])));
    $filter = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $filter);
    
    $this->jsParams = "'".json_encode($params)."'";
  }
  
  public function getNumberOfVisibleProjects() {
    $result = pg_execute($this->dbConnection, "get_number_of_visible_projects", array()) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $number = pg_fetch_all($result);
    pg_free_result($result);
  
    if($number[0]['count']) {
      return $number[0]['count'];
    }
    return 0;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
