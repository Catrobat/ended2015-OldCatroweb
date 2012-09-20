<?php
/**
 *    Catroid: An on-device graphical programming language for Android devices
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

class projects extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    
    $this->xmlSerializerOptions = array(
        'cdata' => true,
        'rootName' => 'Catrobat',
        'defaultTagName' => 'CatrobatProject'
    );
  }

  public function __default() {
  }

  public function recent() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = max(intVal($_REQUEST['offset']), 0);
    }

    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = min(abs(intVal($_REQUEST['limit'])), 100);
    }
    
    $result = pg_execute($this->dbConnection, 'get_visible_projects_ordered_by_uploadtime_limited_offset_api', array($limit, $offset));
    if($result) {
      if(pg_num_rows($result) > 0) {
        $projects = pg_fetch_all($result);
        $this->generateOutput($projects);
      } else {
        $this->Error = 'no projects found';
      }
      pg_free_result($result);
    }
  }
  
  public function search() {
    if(!isset($_REQUEST['query']) || strlen(trim($_REQUEST['query'])) == 0) {
      $this->Error = 'no search query';
      return;
    }
    
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = max(intVal($_REQUEST['offset']), 0);
    }
  
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = min(abs(intVal($_REQUEST['limit'])), 100);
    }
    
    $searchTerms = explode(" ", $_REQUEST['query']);
    $keywordsCount = 3;
    $searchQuery = "";
    $searchRequest = array();
    
    foreach($searchTerms as $term) {
      if ($term != "") {
        $searchQuery .= (($searchQuery=="")?"":" OR " )."title ILIKE \$".$keywordsCount;
        $searchQuery .= " OR description ILIKE \$".$keywordsCount;
        $searchTerm = pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($term)));
        $searchTerm = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $searchTerm);
        array_push($searchRequest, "%".$searchTerm."%");
        $keywordsCount++;
      }
    }
    pg_prepare($this->dbConnection, "get_search_results", "SELECT projects.id, projects.title, projects.description, projects.view_count, projects.download_count, projects.version_name, coalesce(extract(epoch from \"timestamp\"(projects.update_time)), extract(epoch from \"timestamp\"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by FROM projects, cusers WHERE ($searchQuery) AND visible = true AND cusers.id=projects.user_id ORDER BY last_activity DESC  LIMIT \$1 OFFSET \$2") or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $result = pg_execute($this->dbConnection, "get_search_results", array_merge(array($limit, $offset), $searchRequest)) or
                  $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      if(pg_num_rows($result) > 0) {
        $projects = pg_fetch_all($result);
        $this->generateOutput($projects);
      } else {
        $this->Error = 'no projects found';
      }
      pg_free_result($result);
    }
    pg_query($this->dbConnection, 'DEALLOCATE get_search_results');
  }
  
  private function generateOutput($projects) {
    $tempProjectList = array();
    foreach($projects as $project) {
      $currentProject = array(
          'ProjectName' => $project['title'],
          'ScreenshotBig' => BASE_PATH . 'resources/thumbnails/' . $project['id'] . PROJECTS_THUMBNAIL_EXTENSION_LARGE,
          'ScreenshotSmall' => BASE_PATH . 'resources/thumbnails/' . $project['id'] . PROJECTS_THUMBNAIL_EXTENSION_SMALL,
          'Author' => $project['uploaded_by'],
          'Description' => $project['description'],
          'Uploaded' => $project['last_activity'],
          'Version' => $project['version_name'],
          'Views' => $project['view_count'],
          'Downloads' => $project['download_count'],
          'ProjectUrl' => BASE_PATH . 'catroid/details/' . $project['id'],
          'DownloadUrl' => BASE_PATH . 'catroid/download/' . $project['id'] . PROJECTS_EXTENSION
      );
      array_push($tempProjectList, $currentProject);
    }
    
    $this->CatrobatInformation = array(
        "ApiVersion" => VERSION,
        "BaseUrl" => BASE_PATH,
        "TotalProjects" => $this->getNumberOfVisibleProjects(),
        "ProjectsExtension" => PROJECTS_EXTENSION
    );
    $this->CatrobatProjects = $tempProjectList;
  }

  private function getNumberOfVisibleProjects() {
    $result = pg_execute($this->dbConnection, "get_number_of_visible_projects", array());
    if($result) {
      $number = pg_fetch_all($result);
      pg_free_result($result);

      if($number[0]['count']) {
        return $number[0]['count'];
      }
    }
    return 0;
  }

  public function doUpload($formData, $fileData) {
  }
}
?>
