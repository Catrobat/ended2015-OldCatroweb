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

class projects extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('projectList.css');
    $this->addCss('projects.css');
    //$this->addJs('commonFunctions.js'); 
    $this->addJs('projectLoader.js');
    $this->addJs('projectContentFiller.js');
    $this->addJs('projectObject.js');
    $this->addJs('projects.js');
    $this->htmlHeaderFile = 'htmlProjectsHeaderTemplate.php';    
  }

  public function __default() {
    $this->numberOfPages = ceil($this->getNumberOfVisibleProjects() / PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE);
    
    if(!$this->session->pageNr) {
      $this->session->pageNr = 1;
    }

    if(isset($_REQUEST['method']) || isset($_REQUEST['p'])) {
      if(isset($_REQUEST['method'])) {
        $this->session->pageNr = intval($_REQUEST['method']);
      }
      if(isset($_REQUEST['p'])) {
        $this->session->pageNr = intval($_REQUEST['p']);
      }
      if($this->session->pageNr < 1) {
        $this->session->pageNr = 1;
      }
      if($this->session->pageNr > $this->numberOfPages) {
        $this->session->pageNr = $this->numberOfPages;
      }
    }
    
    //TODO not sure if necessary => no functionality
    if(isset($_SERVER['HTTP_REFERER']) && !$this->session->referer) {
      $this->session->referer = $_SERVER['HTTP_REFERER'];
    }
    if(isset($_SERVER['HTTP_REFERER']) && $this->session->referer != $_SERVER['HTTP_REFERER']) {
      $this->session->referer = $_SERVER['HTTP_REFERER'];
      $this->session->task = "newestProjects"; //TODO unused?
    }
    
    if($_REQUEST['sort']) {
      switch($_REQUEST['sort']) {
        case PROJECT_SORTBY_AGE:
        case PROJECT_SORTBY_DOWNLOADS:
        case PROJECT_SORTBY_VIEWS:
        case PROJECT_SORTBY_RANDOM:
            $this->session->sort = $_REQUEST['sort'];
            break;
        default:
          $this->session->sort = PROJECT_SORTBY_DEFAULT;
      }
    }
    else if (!$this->session->sort) {
      $this->session->sort = PROJECT_SORTBY_DEFAULT;
    }
    
    $params = array();
    $params['numProjectsPerPage'] = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;    
    $params['pageNr'] = intVal($this->session->pageNr);
    $params['pageNrMax'] = $this->numberOfPages;
    $params['layout'] = PROJECT_LAYOUT_ROW;
    $params['container'] = '#projectContainer';
    
    $params['sort'] = $this->session->sort;
    $params['filter'] = array('searchQuery' => $_REQUEST['search'],
                              'author'        => $_REQUEST['author']);
                              
    $params['page'] = array('number'             => intVal($this->session->pageNr),
                            'numProjectsPerPage' => PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
                            'pageNrMax'          => ceil($this->getNumberOfVisibleProjects() / PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE)
                           );
    
    $params['config'] = array('PROJECT_LAYOUT_ROW' => PROJECT_LAYOUT_ROW,
                              'PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE' => PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,  //TODO unused?
                              'PROJECT_LAYOUT_COLUMN' => 2,  //TODO unused?
                              'PROJECT_LAYOUT_COLUMN_PROJECTS_PER_ROW' => 5,  //TODO unused?
                              'sortby' => array('age' => PROJECT_SORTBY_AGE,
                                                'downloads' => PROJECT_SORTBY_DOWNLOADS,
                                                'views' => PROJECT_SORTBY_VIEWS,
                                                'random' => PROJECT_SORTBY_RANDOM)
                             );
    $params['userNickname'] = $this->session->userLogin_userNickname; //TODO unused?
    $filter =  pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($_REQUEST['search']))); //TODO unused?
    $filter = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $filter); //TODO unused?
    
    $this->links = array(
        array('title' => 'downloads', 'url' => BASE_PATH.'catroid/projects/?sort=downloads', 'image' => BASE_PATH.'images/symbols/arrow_down1.png', 'style' => ($this->session->sort == PROJECT_SORTBY_DOWNLOADS)? 'sortLinkActive' : ''),
        array('title' => 'newest', 'url' => BASE_PATH.'catroid/projects/?sort=newest', 'image' => BASE_PATH.'images/symbols/clock2.png', 'style' => ($params['sort'] == PROJECT_SORTBY_AGE)? 'sortLinkActive' : ''),
        array('title' => 'views', 'url' => BASE_PATH.'catroid/projects/?sort=views', 'image' => BASE_PATH.'images/symbols/view7.png', 'style' => ($params['sort'] == PROJECT_SORTBY_VIEWS)? 'sortLinkActive' : ''),
        array('title' => 'random', 'url' => BASE_PATH.'catroid/projects/?sort=random', 'image' => BASE_PATH.'', 'style' => ($params['sort'] == PROJECTS_SORT_RANDOM)? 'sortLinkActive' : ''),
    );
    $this->jsParams = "'".json_encode($params)."'";

    //dummy for languageTest: request error ('viewer', 'ajax_request_page_not_found')
    $error = array();
    $error['type'] = 'viewer';
    $error['code'] = 'ajax_request_page_not_found';
    $error['extra'] = '';

    $this->error = $error;
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
