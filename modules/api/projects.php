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

class projects extends CoreAuthenticationNone {
  protected $maxId = 2147483647; // (2^8)^4 / 2 - 1  (4 bytes signed int)
  protected $mask = array(PROJECT_MASK_DEFAULT => array('ProjectId', 'ProjectName'),
      PROJECT_MASK_GRID_ROW_AGE => array('ProjectId', 'ProjectName', 'ProjectNameShort', 'ScreenshotSmall', 'UploadedString', 'FileSize'),
      PROJECT_MASK_GRID_ROW_DOWNLOADS => array('ProjectId', 'ProjectName', 'ProjectNameShort', 'ScreenshotSmall', 'Downloads', 'FileSize'),
      PROJECT_MASK_GRID_ROW_VIEWS => array('ProjectId', 'ProjectName', 'ProjectNameShort', 'ScreenshotSmall', 'Views', 'FileSize'),
      PROJECT_MASK_FEATURED => array('ProjectId', 'ProjectName', 'FeaturedImage', 'Author', 'FileSize'),
      PROJECT_MASK_ALL => array('ProjectId', 'ProjectName', 'ProjectNameShort', 'ScreenshotBig', 'ScreenshotSmall', 'Author',
          'Description', 'Uploaded', 'UploadedString', 'Version', 'Views', 'Downloads', 'ProjectUrl', 'DownloadUrl', 'FileSize'));

  public function __construct() {
    parent::__construct();

    $this->xmlSerializerOptions = array(
        'cdata' => true,
        'rootName' => 'Catrobat',
        'defaultTagName' => 'CatrobatProject'
    );
  }

  public function __default() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
    
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
    
    $mask = PROJECT_MASK_DEFAULT;
    if(isset($_REQUEST['mask'])) {
      $mask = $_REQUEST['mask'];
    }
    
    $order = PROJECT_SORTBY_AGE;
    if(isset($_REQUEST['order'])) {
      $order = $_REQUEST['order'];
    }
    
    $query = '';
    if(isset($_REQUEST['query'])) {
      $query = $_REQUEST['query'];
    }
    
    $user = '';
    if(isset($_REQUEST['user'])) {
      $user = $_REQUEST['user'];
    }
    
    $this->retrieve($offset, $limit, $mask, $order, $query, $user);
  }
  
  public function recent() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
    
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }

    $this->retrieve($offset, $limit, PROJECT_MASK_ALL, PROJECT_SORTBY_AGE);
  }
  
  public function recentIDs() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
    
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
    
    $this->retrieve($offset, $limit, PROJECT_MASK_DEFAULT, PROJECT_SORTBY_AGE);
  }
  
  public function mostDownloaded() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
  
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
     
    $this->retrieve($offset, $limit, PROJECT_MASK_ALL, PROJECT_SORTBY_DOWNLOADS);
  }
  
  public function  mostDownloadedIDs() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
  
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
  
    $this->retrieve($offset, $limit, PROJECT_MASK_DEFAULT, PROJECT_SORTBY_DOWNLOADS);
  }
  
  public function mostViewed() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
  
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
  
    $this->retrieve($offset, $limit, PROJECT_MASK_ALL, PROJECT_SORTBY_VIEWS);
  }
  
  public function mostViewedIDs() {
    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
  
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
  
    $this->retrieve($offset, $limit, PROJECT_MASK_DEFAULT, PROJECT_SORTBY_VIEWS);
  }
  
  public function getInfoById() {
    $offset = 0;
    $limit = 1;
    $projects = array();
    if(isset($_REQUEST['id'])) {
      $id = $_REQUEST['id'];
    }else{
      $this->Error = 'no id given';
      return;
    }
    if(!is_numeric($id)){
      $this->Error = 'given id is not an integer';
      return;
    }
    
    $result = pg_execute($this->dbConnection, "get_visible_project_by_id", array($id)) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    if($projects == null){
      $this->Error = 'Project not found (uploaded)';
      return;
    }
    $this->generateOutput($projects, PROJECT_MASK_ALL, 0);
    
    return array('CatrobatInformation' => $this->CatrobatInformation,'CatrobatProjects' => $this->CatrobatProjects);
  }
  
  public function getInfoByTitle() {
    $offset = 0;
    $limit = 1;
    $projects = array();
    if(isset($_REQUEST['title'])) {
      $title = $_REQUEST['title'];
    }else{
      $this->Error = 'no title given';
      return;
    }
  
    $result = pg_execute($this->dbConnection, "get_project_by_title", array($title)) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    if($projects == null){
      $this->Error = 'Project not found (uploaded)';
      return;
    }
    $this->generateOutput($projects, PROJECT_MASK_ALL, 0);
  
    return array('CatrobatInformation' => $this->CatrobatInformation,'CatrobatProjects' => $this->CatrobatProjects);
  }
  
  public function search() {
    if(!isset($_REQUEST['query']) || strlen(trim($_REQUEST['query'])) == 0) {
      $this->Error = 'no search query';
      return;
    }

    $offset = 0;
    if(isset($_REQUEST['offset'])) {
      $offset = $_REQUEST['offset'];
    }
    
    $limit = 20;
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
    
    $this->retrieve($offset, $limit, PROJECT_MASK_ALL, PROJECT_SORTBY_AGE, $_REQUEST['query']);
  }
  
  public function featured($limit = 20, $visible = "t") {
    if(isset($_REQUEST['limit'])) {
      $limit = $_REQUEST['limit'];
    }
    
    $projects = array();
    $result = pg_execute($this->dbConnection, "get_featured_projects_ordered_by_update_time_limited", array($limit, $visible)) or
    $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(pg_num_rows($result) > 0 ) {
      $projects = pg_fetch_all($result);
    }
    pg_free_result($result);
    $this->generateOutput($projects, PROJECT_MASK_FEATURED, min($limit, count($projects)));
    if($this->Error != "") {
      return $this->Error;
    }
    return array('CatrobatInformation' => $this->CatrobatInformation, 'CatrobatProjects' => $this->CatrobatProjects);
  }
  
  public function get($offset=0, $limit=20, $mask=PROJECT_MASK_DEFAULT, $order=PROJECT_SORTBY_AGE, $query='', $user='') {
    $this->retrieve($offset, $limit, $mask, $order, $query, $user);
    if($this->Error != "") {
      return $this->Error;
    }
    return array('CatrobatInformation' => $this->CatrobatInformation, 'CatrobatProjects' => $this->CatrobatProjects);
  }

  private function retrieve($offset=0, $limit=20, $mask=PROJECT_MASK_DEFAULT, $order=PROJECT_SORTBY_AGE, $query='', $user='') {
    $limit = min(abs(intval($limit)), 100);
    $offset = max(intval($offset), 0);
    $query = trim(strval($query));
    $user = trim(strval($user));
  
  
    $queryParameter = array($limit, $offset);
    $keywordsCount = 3;
    $userQuery = "";
    $searchQuery = "";
    $orderQuery = "";


    switch($order) {
      case PROJECT_SORTBY_AGE:
        $orderQuery = "ORDER BY last_activity DESC, projects.id DESC";
        break;
      case PROJECT_SORTBY_DOWNLOADS:
        $orderQuery = "ORDER BY projects.download_count DESC, projects.id DESC";
        break;
      case PROJECT_SORTBY_VIEWS:
        $orderQuery = "ORDER BY projects.view_count DESC, projects.id DESC";
        break;
      case PROJECT_SORTBY_RANDOM:
        $orderQuery = "ORDER BY random()";
        break;
      default:
        $order = PROJECT_SORTBY_AGE;
        $orderQuery = "ORDER BY last_activity DESC, projects.id DESC";
        break;
    }
  
  
    if(strlen($user) > 0) {
      $userQuery = " AND (cusers.username = \$" . $keywordsCount;
      $userQuery .= " OR cusers.username_clean = \$" . $keywordsCount . ") ";
  
      $username = pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($user)));
      $username = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $username);
      array_push($queryParameter, $username);
  
      $keywordsCount++;
    }
   
    
    
    $searchTerms = explode(" ", $query);
    $this->completeTerm = $query;
    for($i = 0; $i < sizeof($searchTerms); $i++) {
      if (strlen($searchTerms[$i]) < 4) {
        unset($searchTerms[$i]);
        $searchTerms = array_values($searchTerms);     
        $i = $i - 1;
      }
    }

    array_unshift($searchTerms,$query);
    
    foreach($searchTerms as $term) {
      if(strlen($term) > 0) {
        $searchQuery .= (($searchQuery == "") ? " AND (" : " OR " );
        $searchQuery .= "title ILIKE \$" . $keywordsCount;
        $searchQuery .= " OR description ILIKE \$" . $keywordsCount;
        $searchQuery .= " OR username ILIKE \$" . $keywordsCount;
  
        $searchTerm = pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($term)));
        $searchTerm = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $searchTerm);
        array_push($queryParameter, "%" . $searchTerm . "%");
  
        $keywordsCount++;
      }
    }
    $searchQuery .= (($searchQuery != "") ? ") " : "" );
  
  
  
    $statementName = "query_u" . ((strlen($user) > 0) ? '1' : '0') .
        "_q" . ((strlen($query) > 0) ? count($searchTerms) : '0') .
        "_" . $order;
    $sqlQuery = "SELECT
        projects.id, projects.title, projects.description, projects.view_count, projects.user_id, projects.download_count, projects.version_name, projects.filesize_bytes,
        coalesce(extract(epoch from \"timestamp\"(projects.update_time)), extract(epoch from \"timestamp\"(projects.upload_time))) AS last_activity,
        cusers.username AS uploaded_by
    FROM projects, cusers
    WHERE visible = true AND cusers.id=projects.user_id" . $userQuery . $searchQuery . "
    " . $orderQuery . "
    LIMIT \$1 OFFSET \$2";
    
    
    if($this->prepareStatement($statementName, $sqlQuery)) {
      $result = pg_execute($this->dbConnection, $statementName, $queryParameter);
      if($result) {
        if(pg_num_rows($result) > 0) {
          $projects = pg_fetch_all($result);
          if(strlen($query) != 0)
            usort($projects, array($this, 'cmp')); 
          $this->generateOutput($projects, $mask, $this->getNumberOfVisibleProjects($statementName, $queryParameter));
        } else {
          $this->generateOutput(array(), $mask, $this->getNumberOfVisibleProjects($statementName, $queryParameter));
        }
        pg_free_result($result);
      } else {
        $this->Error = 'query failed ' . pg_last_error();
      }
    }
    
  }
  
  private function cmp($value_1, $value_2)  {
    $title1 = strtolower($value_1['title']);
    $title2 = strtolower($value_2['title']);
    $description1 = strtolower($value_1['description']);
    $description2 = strtolower($value_2['description']);
    $uploader1 = strtolower($value_1['uploaded_by']);
    $uploader2 = strtolower($value_2['uploaded_by']);
    $count1 = 0;
    $count2 = 0;
  
    if ($this->completeTerm === $title1)
      $count1 += 1;
    if ($this->completeTerm === $description1)
      $count1 += 1;
    if ($this->completeTerm === $uploader1)
      $count1 += 1;
    
    if ($this->completeTerm === $title2)
      $count2 += 1;
    if ($this->completeTerm === $description2)
      $count2 += 1;
    if ($this->completeTerm === $uploader2)
      $count2 += 1;
    
    $count1 += substr_count($title1, $this->completeTerm);
    $count1 += substr_count($description1, $this->completeTerm);
    $count1 += substr_count($uploader1, $this->completeTerm);
    
    $count2 += substr_count($title2, $this->completeTerm);
    $count2 += substr_count($description2, $this->completeTerm);
    $count2 += substr_count($uploader2, $this->completeTerm);

    if ($count1 === $count2)
      return 0;
  
    return ($count1 > $count2) ? -1 : 1;
  }
  
  private function prepareStatement($statementName, $sqlQuery) {
    $result = pg_query_params($this->dbConnection, 'SELECT name FROM pg_prepared_statements WHERE name=$1',
        array($statementName));
  
    $exists = false;
    if($result) {
      $exists = (pg_num_rows($result) > 0);
      pg_free_result($result);
    }
  
    if(!$exists) {
      $prepared = pg_prepare($this->dbConnection, $statementName, $sqlQuery);
      if(!$prepared) {
        $this->Error = "couldn't prepare statement " . pg_last_error();
        return false;
      }
    }
    return true;
  }

  private function getNumberOfVisibleProjects($statementName, $queryParameter) {
    $queryParameter[0] = $this->maxId;
    $queryParameter[1] = 0;
    $result = pg_execute($this->dbConnection, $statementName, $queryParameter);
    
    $numberOfRows = -1;
    if($result) {
      $numberOfRows = pg_num_rows($result);
      pg_free_result($result);
    }
    return $numberOfRows;
  }
  
  private function generateOutput($projects, $mask=PROJECT_MASK_DEFAULT, $total=0) {
    $tempProjectList = array();
    foreach($projects as $project) {
      $currentProject = array();
      
      $selectedFields = $this->mask[PROJECT_MASK_DEFAULT];
      if(array_key_exists($mask, $this->mask)) {
        $selectedFields = $this->mask[$mask];
      }

      if(in_array('ProjectId', $selectedFields)) {
        $currentProject['ProjectId'] = $project['id'];
      }
      if(in_array('ProjectName', $selectedFields)) {
        $currentProject['ProjectName'] = $project['title'];
      }
      if(in_array('ProjectNameShort', $selectedFields)) {
        $currentProject['ProjectNameShort'] = $project['title'];//makeShortString($project['title'], 9, '…');
      }
      if(in_array('ScreenshotBig', $selectedFields)) {
        $currentProject['ScreenshotBig'] = str_replace(BASE_PATH, "", getProjectImageUrl($project['id']));
      }
      if(in_array('ScreenshotSmall', $selectedFields)) {
        $currentProject['ScreenshotSmall'] = str_replace(BASE_PATH, "", getProjectThumbnailUrl($project['id']));
      }
      if(in_array('FeaturedImage', $selectedFields)) {
        $currentProject['FeaturedImage'] = str_replace(BASE_PATH, "", getFeaturedProjectImageUrl($project['id']));
      }
      if(in_array('Author', $selectedFields)) {
        $currentProject['Author'] = $project['uploaded_by'];
      }
      if(in_array('Description', $selectedFields)) {
        $currentProject['Description'] = $project['description'];
      }
      if(in_array('Uploaded', $selectedFields)) {
        $currentProject['Uploaded'] = $project['last_activity'];
      }
      if(in_array('UploadedString', $selectedFields)) {
        $currentProject['UploadedString'] = getTimeInWords(intval($project['last_activity']), $this->languageHandler, time());
      }
      if(in_array('Version', $selectedFields)) {
        $currentProject['Version'] = $project['version_name'];
      }
      if(in_array('Views', $selectedFields)) {
        $currentProject['Views'] = $project['view_count'];
      }
      if(in_array('Downloads', $selectedFields)) {
        $currentProject['Downloads'] = $project['download_count'];
      }
      if(in_array('ProjectUrl', $selectedFields)) {
        $currentProject['ProjectUrl'] = 'details/' . $project['id'];
      }
      if(in_array('DownloadUrl', $selectedFields)) {
        $currentProject['DownloadUrl'] = 'download/' . $project['id'] . PROJECTS_EXTENSION;
      }
      if(in_array('FileSize', $selectedFields)) {
        $currentProject['FileSize'] = convertBytesToMegabytes($project['filesize_bytes']);
      }
      array_push($tempProjectList, $currentProject);
    }
  
    $this->CatrobatInformation = array(
        "BaseUrl" => BASE_PATH,
        "TotalProjects" => $total,
        "ProjectsExtension" => PROJECTS_EXTENSION
    );
    $this->CatrobatProjects = $tempProjectList;
  }
}

?>
