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

class loadProjects extends CoreAuthenticationNone {
  protected $pageNr = 0;
  protected $ajax = false;

  public function __construct() {
    parent::__construct();

  }
  
  public function escapeUserInput($input) {
    $escapedString = $input;
    $escapedString =  pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($escapedString )));
    $escapedString  = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $escapedString);
    $escapedString  = "%".$escapedString."%";
    return $escapedString;
  }

  public function __default() {

    $this->statusCode = STATUS_CODE_OK;

    if(isset($_REQUEST)) {
      $this->ajax = true;

      if(isset($_REQUEST['method'])) {
        $this->pageNr = max(0, intval($_REQUEST['method']) - 1);
      }

      $limit = intval((isset($_REQUEST['numProjectsPerPage']))? $_REQUEST['numProjectsPerPage'] : PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE);
      $offset = 0;

      if(isset($_REQUEST['page'])) {
        $this->pageNr = max(0, intval($_REQUEST['page']) - 1);
        $offset = max(0,($this->pageNr)*$limit);
      }

      $this->session->pageNr = $this->pageNr;
      
      $sortby = PROJECT_SORTBY_DEFAULT;
      if($_REQUEST['sort']) {
        switch($_REQUEST['sort']) {
          case PROJECT_SORTBY_AGE:
          case PROJECT_SORTBY_DOWNLOADS:
          case PROJECT_SORTBY_VIEWS:
          case PROJECT_SORTBY_RANDOM:
            $sortby = $_REQUEST['sort'];
            break;
          default:
            $sortby = PROJECT_SORTBY_DEFAULT;
            break;
        }
      }
      
      $pageLabels = array();
      $pageLabels['title'] = $this->languageHandler->getString('title');
      
      $filter = array('searchQuery' => '%', 'author' => '%');
      if($_REQUEST['searchQuery']) {
        $filter['searchQuery'] = $this->escapeUserInput($_REQUEST['searchQuery']);
        $pageLabels['title'] = $this->languageHandler->getString('search_title');
      }
      
      if($_REQUEST['author']) {
        $filter['author'] = $this->escapeUserInput($_REQUEST['author']);
        $pageLabels['title'] = $this->languageHandler->getString('search_title');
      }        
      
      $this->content = $this->getProjects($sortby, $limit, $offset, $filter);
      $this->buttons = array("prevButton" => ($this->pageNr == 0)? false : true, "nextButton" => (count($this->content) == PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE)? true : false);

      $pageLabels['websitetitle'] = SITE_DEFAULT_TITLE;
      $pageLabels['pageNr'] = $this->languageHandler->getString('page_number',  intVal($this->session->pageNr + 1 ));
      $pageLabels['prevButton'] = $this->languageHandler->getString('prev_button', '&laquo;');
      $pageLabels['nextButton'] = $this->languageHandler->getString('next_button', '&raquo;');
      $pageLabels['loadingButton'] = $this->languageHandler->getString('loading_button');
      $this->pageLabels = $pageLabels;
    }
  }

  public function getProjects($sort, $limit = null, $offset = 0, $filter = "") {
    $projects = "NIL";
    if(!isset($sort) || $sort == "") {
      $sort = PROJECT_SORTBY_AGE;
    }

    if(($this->pageNr < 0) && ($this->ajax)) {
      return "NIL";
    }

    switch($sort) {
      case PROJECT_SORTBY_AGE:
        $projects = $this->retrieveProjectsFromDatabase("get_visible_projects_orderby_age_limited_offset", $limit, $offset, $filter);
        break;
      case PROJECT_SORTBY_DOWNLOADS:
        $projects = $this->retrieveProjectsFromDatabase("get_visible_projects_orderby_download_count_limited_and_offset", $limit, $offset, $filter);
        break;
      case PROJECT_SORTBY_VIEWS:
        $projects = $this->retrieveProjectsFromDatabase("get_visible_projects_orderby_view_count_limited_and_offset", $limit, $offset, $filter);
        break;
      case PROJECT_SORTBY_RANDOM:
        $projects = $this->retrieveProjectsFromDatabase("get_visible_projects_orderby_random_limited_and_offset", $limit, $offset, $filter);
        break;
      default:
        $projects = $this->retrieveProjectsFromDatabase("get_visible_projects_orderby_age_limited_offset", $limit, $offset, $filter);
        break;
    }
    return $projects;
  }

  private function retrieveProjectsFromDatabase($sql, $limit, $offset = 0, $filter = "") {
    if($filter != "") {
      $result = pg_execute($this->dbConnection, $sql,
          array($limit, $offset, $filter['searchQuery'], $filter['author'])) 
          or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      $projects = pg_fetch_all($result);
    }
    else {
      $result = pg_execute($this->dbConnection, $sql, array($limit, $offset)) 
                  or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      $projects = pg_fetch_all($result);
    }
    pg_free_result($result);
    if($projects[0]['id']) {
      $i=0;
      foreach($projects as $project) {
        $projects[$i]['title'] = $projects[$i]['title'];
        $projects[$i]['title_short'] = makeShortString($project['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH);
        $projects[$i]['last_activity'] =  $this->languageHandler->getString('uploaded', getTimeInWords(intval($project['last_activity']), $this->languageHandler, time()));
        $projects[$i]['thumbnail'] = getProjectThumbnailUrl($project['id']);
        $projects[$i]['download_count'] = isset($projects[$i]['download_count'])? $projects[$i]['download_count'] : '';
        $projects[$i]['view_count'] = isset($projects[$i]['view_count'])? $projects[$i]['view_count'] : '';
        $projects[$i]['uploaded_by_string'] = $this->languageHandler->getString('uploaded_by', $projects[$i]['uploaded_by']);
        $i++;
      }
      return($projects);
    } 
     elseif($this->pageNr == 0) {
      $projects[0]['id'] = 0;
      $projects[0]['title'] = $this->languageHandler->getString('no_results');
      $projects[0]['title_short'] = $this->languageHandler->getString('no_results');
      $projects[0]['upload_time'] =  "";
      $projects[0]['thumbnail'] = BASE_PATH."images/symbols/thumbnail_gray.jpg";
      return($projects);
    }    
    else {
      return "NIL";
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>

