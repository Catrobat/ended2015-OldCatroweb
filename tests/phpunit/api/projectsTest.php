<?php
/*
 *Catroid: An on-device visual programming system for Android devices
 *Copyright (C) 2010-2013 The Catrobat Team
 *(<http://developer.catrobat.org/credits>)
 *
 *This program is free software: you can redistribute it and/or modify
 *it under the terms of the GNU Affero General Public License as
 *published by the Free Software Foundation, either version 3 of the
 *License, or (at your option) any later version.
 *
 *An additional term exception under section 7 of the GNU Affero
 *General Public License, version 3, is available at
 *http://developer.catrobat.org/license_additional_term
 *
 *This program is distributed in the hope that it will be useful,
 *but WITHOUT ANY WARRANTY; without even the implied warranty of
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *GNU Affero General Public License for more details.
 *
 *You should have received a copy of the GNU Affero General Public License
 *along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once('testsBootstrap.php');

class projectsTest extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $upload;    
  protected $insertIDArray = array();
  protected $dbConnection;
  
  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/api/projects.php';
    $this->obj = new projects();
    require_once CORE_BASE_PATH.'modules/api/upload.php';
    $this->upload = new upload();
    
    $this->dbConnection = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
    or die('Connection to Database failed: ' . pg_last_error());  
  }

  private function getResults($order, $limit, $offset, $query, $user) {
    $query = trim(strval($query));
    $user = trim(strval($user));
    $limit = min(abs(intval($limit)), 100);
    $offset = max(intval($offset), 0);
    $keywordsCount = 3;
    $userQuery = "";
    $searchQuery = "";
    $orderQuery = "";
    $queryParameter = array($limit, $offset);
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
      $userQuery = " AND (cusers.username ILIKE \$" . $keywordsCount;
      $userQuery .= " OR cusers.username_clean ILIKE \$" . $keywordsCount . ") ";
    
      $username = pg_escape_string(preg_replace("/\\\/", "\\\\\\", checkUserInput($user)));
      $username = preg_replace(array("/\%/", "/\_/"), array("\\\%", "\\\_"), $username);
      array_push($queryParameter, "%" . $username . "%");
    
      $keywordsCount++;
    }
    
    $searchTerms = explode(" ", $query);
    foreach($searchTerms as $term) {
      if(strlen($term) > 0) {
        $searchQuery .= (($searchQuery == "") ? " AND (" : " OR " );
        $searchQuery .= "title ILIKE \$" . $keywordsCount;
        $searchQuery .= " OR description ILIKE \$" . $keywordsCount;
    
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
    projects.id, projects.title, projects.description, projects.view_count, projects.download_count, projects.version_name,
    coalesce(extract(epoch from \"timestamp\"(projects.update_time)), extract(epoch from \"timestamp\"(projects.upload_time))) AS last_activity,
    cusers.username AS uploaded_by
    FROM projects, cusers
    WHERE visible = true AND cusers.id=projects.user_id" . $userQuery . $searchQuery . "
    " . $orderQuery . "
    LIMIT \$1 OFFSET \$2";
    
    $result = pg_query_params($this->dbConnection, $sqlQuery, $queryParameter) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    return $projects;
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByAge($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
    $sortby = PROJECT_SORTBY_AGE;
    $insertIds = array();
    $date_sorted = array();
    $idSorted = array();
    $uniqueTitle = array();
    $uniqueDescription = "";
    $newestProject = null;
    $numUploadProjects = 5;
    
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $numProjectsBefore = pg_num_rows($result);
    
    mt_srand ((double) microtime() * 1000000);
    $uniqueDescription = $projectDescription.'_'.mt_rand();
    
    $query = 'SELECT projects.id, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY last_activity DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $newestProject = pg_fetch_assoc($result);
    pg_free_result($result);
    
    for($i = 0; $i < $numUploadProjects; $i++) {
      $testFile =  $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
      
      $uniqueTitle[$i] = $projectTitle.'_'.mt_rand();
      $formData = array(
          'projectTitle' => $uniqueTitle[$i],
          'projectDescription' => $uniqueDescription,
          'fileChecksum' => $fileChecksum,
          'userLanguage'=>$uploadLanguage
      );
      $fileData = array(
          'upload' => array(
              'name' => $fileName,
              'type' => $fileType,
              'tmp_name' => $testFile,
              'error' => 0,
              'size'=>$fileSize
          )
      );
      $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
      $fileSize = filesize($testFile);
  
      $this->upload->doUpload($formData, $fileData, $serverData);
      array_push($insertIds, $this->upload->projectId);
    }
  
    foreach ($insertIds as $id) {
      $time= mt_rand($newestProject['last_activity'] + 1, time());
      $date_string = date("Y-m-d H:i:s.u",$time);
      array_push($date_sorted, $date_string);
      array_push($idSorted, $id);
      $uploadTime = "TIMESTAMP WITH TIME ZONE '".date($date_string)."'";
      $updateQuery = 'UPDATE projects SET upload_time='.$uploadTime.', update_time=NULL WHERE projects.id='.$id;
      $result = pg_query($this->dbConnection, $updateQuery) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
    
    array_multisort($date_sorted, SORT_DESC, SORT_STRING, $idSorted, SORT_DESC, SORT_NUMERIC);
    
    $sort = $sortby;
    $limit = $numUploadProjects;
    $offset = 0;
  
    $pgProjects = $this->getResults($sortby, $limit, $offset, "", "");  
    
    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_AGE, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pgProjects));
    $i = 0;
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($project['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['Version']));
      $this->assertFalse(isset($project['Views']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $i++;
    }
    
    // test all mask
    $projectsAllInfo = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
    
    $this->assertEquals(3, count($projectsAllInfo['CatrobatInformation']));
    $this->assertNotEquals(-1, $projectsAllInfo['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projectsAllInfo['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projectsAllInfo['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projectsAllInfo['CatrobatProjects']), count($pgProjects));
    
    $i = 0;
    foreach ($projectsAllInfo['CatrobatProjects'] as $project) {
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pgProjects[$i]['id']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals(current($idSorted), intval($project['ProjectId']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['Author'], $pgProjects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pgProjects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertEquals($project['Views'], $pgProjects[$i]['view_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pgProjects[$i]['id'].PROJECTS_EXTENSION);      
      $this->assertEquals($project['Version'], $pgProjects[$i]['version_name']);      
      $this->assertEquals($project['Uploaded'], $pgProjects[$i]['last_activity']);      
      $i++;
      next($idSorted);
    }
    
    // search for unique project 
    for($i = 0; $i < count($uniqueTitle); $i++) {
      $offset = 0;
      $limit = $numUploadProjects;
      $searchTerm = $uniqueTitle[$i];
      $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_AGE, $sortby, $searchTerm);
      $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");
  
      $this->assertEquals(1, count($searchProjects['CatrobatProjects']));
      $this->assertEquals(1, count($pgProjects));
      $this->assertEquals(1, intval($searchProjects['CatrobatInformation']['TotalProjects']));
      
      $this->assertEquals($searchProjects['CatrobatProjects'][0]['ProjectId'], $pgProjects[0]['id']);
      $this->assertEquals($searchProjects['CatrobatProjects'][0]['ProjectName'], $pgProjects[0]['title']);
      $this->assertEquals($searchProjects['CatrobatInformation']['BaseUrl'].$searchProjects['CatrobatProjects'][0]['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[0]['id']));
      $this->assertEquals($searchProjects['CatrobatProjects'][0]['UploadedString'], getTimeInWords($pgProjects[0]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['ProjectUrl']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['ScreenshotBig']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Author']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Description']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Uploaded']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Version']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Views']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['Downloads']));
      $this->assertFalse(isset($searchProjects['CatrobatProjects'][0]['DownloadUrl']));
    }

    // search for description
    $i = 0;
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_AGE, $sortby, $searchTerm);
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");
    
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
    
    $i = 0;
    reset($idSorted);
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($searchProjects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($searchProject['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($searchProject['ProjectUrl']));
      $this->assertFalse(isset($searchProject['ScreenshotBig']));
      $this->assertFalse(isset($searchProject['Author']));
      $this->assertFalse(isset($searchProject['Description']));
      $this->assertFalse(isset($searchProject['Uploaded']));
      $this->assertFalse(isset($searchProject['Version']));
      $this->assertFalse(isset($searchProject['Views']));
      $this->assertFalse(isset($searchProject['Downloads']));
      $this->assertFalse(isset($searchProject['DownloadUrl']));
      $i++;
    }
    
    // search for author
    $i = 0;
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchUser = "anonymous";
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_AGE, $sortby, $searchTerm, $searchUser);
    
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, $searchUser);
    
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
    
    $i = 0;
    reset($idSorted);
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($searchProjects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($searchProject['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($searchProject['ProjectUrl']));
      $this->assertFalse(isset($searchProject['ScreenshotBig']));
      $this->assertFalse(isset($searchProject['Author']));
      $this->assertFalse(isset($searchProject['Description']));
      $this->assertFalse(isset($searchProject['Uploaded']));
      $this->assertFalse(isset($searchProject['Version']));
      $this->assertFalse(isset($searchProject['Views']));
      $this->assertFalse(isset($searchProject['Downloads']));
      $this->assertFalse(isset($searchProject['DownloadUrl']));
      $i++;
    }
    
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByDownloads($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
    $sortby = PROJECT_SORTBY_DOWNLOADS;
    $insertIds = array();
    $dlSorted = array();
    $idSorted = array();
    $uniqueTitle = array();
    $uniqueDescription = "";
    $most_dl_project = null;
    $numUploadProjects = 5;
  
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $numProjectsBefore = pg_num_rows($result);
  
    mt_srand ((double) microtime() * 1000000);
    $uniqueDescription = $projectDescription.'_'.mt_rand();
  
    $query = 'SELECT projects.id, projects.download_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY projects.download_count DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $most_dl_project = pg_fetch_assoc($result);
    pg_free_result($result);
  
    for($i = 0; $i < $numUploadProjects; $i++) {
      $testFile = $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
  
      $uniqueTitle[$i] = $projectTitle . '_' . mt_rand();
      $formData = array(
          'projectTitle' => $uniqueTitle[$i],
          'projectDescription' => $uniqueDescription,
          'fileChecksum' => $fileChecksum,
          'userLanguage'=>$uploadLanguage
      );
      $fileData = array(
          'upload' => array(
              'name' => $fileName,
              'type' => $fileType,
              'tmp_name' => $testFile,
              'error' => 0,
              'size'=>$fileSize
          )
      );
      $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
      $fileSize = filesize($testFile);
  
      $this->upload->doUpload($formData, $fileData, $serverData);
      array_push($insertIds, $this->upload->projectId);
    }
  
    array_push($dlSorted, $most_dl_project['download_count']);
    array_push($idSorted, $most_dl_project['id']);
    $insertSameDlCount = false;
    
    foreach ($insertIds as $id) {
      if(!$insertSameDlCount) {
        $downloadCount = $most_dl_project['download_count'];
        $insertSameDlCount = true;
      }
      else {
        $downloadCount = mt_rand($most_dl_project['download_count'] + 1, mt_getrandmax() - 1);
      }
      $viewCount = $id;
      array_push($dlSorted, $downloadCount);
      array_push($idSorted, $id);
      $updateQuery = 'UPDATE projects SET download_count = '.$downloadCount.', view_count = '.$viewCount.' WHERE projects.id = '.$id;
      $result = pg_query($this->dbConnection, $updateQuery) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
  
    array_multisort($dlSorted, SORT_DESC, SORT_NUMERIC, $idSorted, SORT_DESC, SORT_NUMERIC);
    
    $limit = count($idSorted);
    $offset = 0;
  
    $pgProjects = $this->getResults($sortby, $limit, $offset, "", "");

    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_DOWNLOADS, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pgProjects));
    $i = 0;
    
    $last_download_count = mt_getrandmax();
    $lastId = mt_getrandmax();
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertGreaterThanOrEqual($project['Downloads'], $last_download_count);
      $this->assertEquals($project['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals(current($dlSorted), $project['Downloads']);
      $this->assertEquals(current($idSorted), $project['ProjectId']);
      
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $this->assertFalse(isset($project['Views']));
      if($last_download_count == $project['Downloads']){
        $this->assertGreaterThanOrEqual($project['id'], $lastId);
      }
      
      $last_download_count = $project['Downloads'];
      $lastId = $project['id'];
      next($idSorted);
      next($dlSorted);
      $i++;
    }
    
    // test all mask
    $projectsAllInfo = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
  
    $this->assertEquals(3, count($projectsAllInfo['CatrobatInformation']));
    $this->assertNotEquals(-1, $projectsAllInfo['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projectsAllInfo['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projectsAllInfo['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projectsAllInfo['CatrobatProjects']), count($pgProjects));
  
    reset($idSorted);
    reset($dlSorted);
    $i = 0;
    foreach ($projectsAllInfo['CatrobatProjects'] as $project) {
      $this->assertEquals($project['Author'], $pgProjects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pgProjects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pgProjects[$i]['id'].PROJECTS_EXTENSION);
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pgProjects[$i]['id']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pgProjects[$i]['id']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['Uploaded'], $pgProjects[$i]['last_activity']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['Views'], $pgProjects[$i]['view_count']);
      $this->assertEquals($project['Version'], $pgProjects[$i]['version_name']);
      $this->assertEquals(current($dlSorted), $project['Downloads']);
      $this->assertEquals(current($idSorted), intval($project['ProjectId']));
      $i++;
      next($idSorted);
      next($dlSorted);
    }

    // search for unique project
    for($i = 0; $i < count($uniqueTitle); $i++) {
      $offset = 0;
      $limit = $numUploadProjects;
      $searchTerm = $uniqueTitle[$i];
      $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_DOWNLOADS, $sortby, $searchTerm);
      $project = $searchProjects['CatrobatProjects'][0];
      $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");

      $this->assertEquals(1, count($searchProjects['CatrobatProjects']));
      $this->assertEquals(1, intval($searchProjects['CatrobatInformation']['TotalProjects']));
      $this->assertEquals(1, count($pgProjects));

      $this->assertEquals($searchProjects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[0]['id']));
      $this->assertEquals($project['Downloads'], $pgProjects[0]['download_count']);
      $this->assertEquals($project['ProjectId'], $pgProjects[0]['id']);
      $this->assertEquals($project['ProjectName'], $pgProjects[0]['title']);

      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $this->assertFalse(isset($project['Views']));
    }
    
    // search for description
    $i = 0;
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_DOWNLOADS, $sortby, $uniqueDescription);
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");
  
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
  
    reset($idSorted);
    reset($dlSorted);
    $i = 0;
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));

      $this->assertEquals(current($dlSorted), $searchProject['Downloads']);
      $this->assertEquals(current($idSorted), intval($searchProject['ProjectId']));

      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $this->assertFalse(isset($project['Views']));
      $i++;
      next($idSorted);
      next($dlSorted);
    }
    
    // search for author
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchUser = "anonymous";
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_DOWNLOADS, $sortby, $searchTerm, $searchUser);
  
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, $searchUser);
  
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
  
    $i = 0;
    reset($idSorted);
    reset($dlSorted);
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));

      $this->assertEquals(current($dlSorted), $searchProject['Downloads']);
      $this->assertEquals(current($idSorted), intval($searchProject['ProjectId']));

      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $this->assertFalse(isset($project['Views']));
      $i++;
      next($idSorted);
      next($dlSorted);
    }
  
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByViews($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
    $sortby = PROJECT_SORTBY_VIEWS;
    $insertIds = array();
    $viewedSorted = array();
    $idSorted = array();
    $uniqueTitle = array();
    $uniqueDescription = "";
    $mostViewedProject = null;
    $numUploadProjects = 5;
  
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $numProjectsBefore = pg_num_rows($result);
  
    mt_srand ((double) microtime() * 1000000);
    $uniqueDescription = $projectDescription.'_'.mt_rand();
  
    $query = 'SELECT projects.id, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY projects.view_count DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $mostViewedProject = pg_fetch_assoc($result);
    pg_free_result($result);
  
    for($i = 0; $i < $numUploadProjects; $i++) {
      $testFile =  $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
  
      $uniqueTitle[$i] = $projectTitle.'_'.mt_rand();
      $formData = array(
          'projectTitle' => $uniqueTitle[$i],
          'projectDescription' => $uniqueDescription,
          'fileChecksum' => $fileChecksum,
          'userLanguage'=>$uploadLanguage
      );
      $fileData = array(
          'upload' => array(
              'name' => $fileName,
              'type' => $fileType,
              'tmp_name' => $testFile,
              'error' => 0,
              'size'=>$fileSize
          )
      );
      $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
      $fileSize = filesize($testFile);
  
      $this->upload->doUpload($formData, $fileData, $serverData);
      array_push($insertIds, $this->upload->projectId);
    }
  
    array_push($viewedSorted, $mostViewedProject['view_count']);
    array_push($idSorted, $mostViewedProject['id']);
    $insertSameViewCount = false;
  
    foreach ($insertIds as $id) {
      if(!$insertSameViewCount) {
        $viewCount = $mostViewedProject['view_count'];
        $insertSameViewCount = true;
      }
      else {
        $viewCount = mt_rand($mostViewedProject['view_count'] + 1, mt_getrandmax() - 1);
      }
      $downloadCount = $id;
      array_push($viewedSorted, $viewCount);
      array_push($idSorted, $id);
      $updateQuery = 'UPDATE projects SET download_count = '.$downloadCount.', view_count = '.$viewCount.' WHERE projects.id = '.$id;
      $result = pg_query($this->dbConnection, $updateQuery) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
  
    array_multisort($viewedSorted, SORT_DESC, SORT_NUMERIC, $idSorted, SORT_DESC, SORT_NUMERIC);
  
  
    $limit = count($idSorted);
    $offset = 0;
  
    $pgProjects = $this->getResults($sortby, $limit, $offset, "", "");
  
    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_VIEWS, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pgProjects));
    $i = 0;
  
    $lastViewCount = mt_getrandmax();
    $lastId = mt_getrandmax();
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertGreaterThanOrEqual($project['Views'], $lastViewCount);
      $this->assertEquals($project['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($project['Views'], $pgProjects[$i]['view_count']);
      $this->assertEquals(current($viewedSorted), $project['Views']);
      $this->assertEquals(current($idSorted), $project['ProjectId']);
  
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      if($lastViewCount == $project['Views']){
        $this->assertGreaterThanOrEqual($project['id'], $lastId);
      }
  
      $lastViewCount = $project['Views'];
      $lastId = $project['id'];
      next($idSorted);
      next($viewedSorted);
      $i++;
    }
  
    // test all mask
    $projectsAllInfo = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
  
    $this->assertEquals(3, count($projectsAllInfo['CatrobatInformation']));
    $this->assertNotEquals(-1, $projectsAllInfo['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projectsAllInfo['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projectsAllInfo['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projectsAllInfo['CatrobatProjects']), count($pgProjects));
  
    reset($idSorted);
    reset($viewedSorted);
    $i = 0;
    foreach ($projectsAllInfo['CatrobatProjects'] as $project) {
      $this->assertEquals($project['Author'], $pgProjects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pgProjects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pgProjects[$i]['download_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pgProjects[$i]['id'].PROJECTS_EXTENSION);
      $this->assertEquals($project['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pgProjects[$i]['id']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pgProjects[$i]['id']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($project['Uploaded'], $pgProjects[$i]['last_activity']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pgProjects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['Views'], $pgProjects[$i]['view_count']);
      $this->assertEquals($project['Version'], $pgProjects[$i]['version_name']);
      $this->assertEquals(current($viewedSorted), $project['Views']);
      $this->assertEquals(current($idSorted), intval($project['ProjectId']));
      $i++;
      next($idSorted);
      next($viewedSorted);
    }
    
    // search for unique project
    for($i = 0; $i < count($uniqueTitle); $i++) {
      $offset = 0;
      $limit = $numUploadProjects;
      $searchTerm = $uniqueTitle[$i];
      $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_VIEWS, $sortby, $searchTerm);
      $project = $searchProjects['CatrobatProjects'][0];
      $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");
  
      $this->assertEquals(1, count($searchProjects['CatrobatProjects']));
      $this->assertEquals(1, intval($searchProjects['CatrobatInformation']['TotalProjects']));
      $this->assertEquals(1, count($pgProjects));
  
      $this->assertEquals($searchProjects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[0]['id']));
      $this->assertEquals($project['ProjectId'], $pgProjects[0]['id']);
      $this->assertEquals($project['ProjectName'], $pgProjects[0]['title']);
      $this->assertEquals($project['Views'], $pgProjects[0]['view_count']);
  
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
    }
  
    // search for description
    $i = 0;
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_VIEWS, $sortby, $uniqueDescription);
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, "");
  
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
  
    reset($idSorted);
    reset($viewedSorted);
    $i = 0;
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($searchProject['Views'], $pgProjects[$i]['view_count']);
  
      $this->assertEquals(current($viewedSorted), $searchProject['Views']);
      $this->assertEquals(current($idSorted), intval($searchProject['ProjectId']));
  
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $i++;
      next($idSorted);
      next($viewedSorted);
    }
  
    // search for author
    $offset = 0;
    $limit = $numUploadProjects;
    $searchTerm = $uniqueDescription;
    $searchUser = "anonymous";
    $searchProjects = $this->obj->get($offset, $limit, PROJECT_MASK_GRID_ROW_VIEWS, $sortby, $searchTerm, $searchUser);
  
    $pgProjects = $this->getResults($sortby, $limit, $offset, $searchTerm, $searchUser);
  
    $this->assertEquals(count($insertIds), count($searchProjects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($searchProjects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pgProjects));
  
    $i = 0;
    reset($idSorted);
    reset($viewedSorted);
    foreach($searchProjects['CatrobatProjects'] as $searchProject) {
      $this->assertEquals($searchProject['ProjectId'], $pgProjects[$i]['id']);
      $this->assertEquals($searchProject['ProjectName'], $pgProjects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$searchProject['ScreenshotSmall'], getProjectThumbnailUrl($pgProjects[$i]['id']));
      $this->assertEquals($searchProject['Views'], $pgProjects[$i]['view_count']);
  
      $this->assertEquals(current($viewedSorted), $searchProject['Views']);
      $this->assertEquals(current($idSorted), intval($searchProject['ProjectId']));
  
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      $i++;
      next($idSorted);
      next($viewedSorted);
    }
  
    $this->upload->cleanup();
  }
  
  /* *** DATA PROVIDERS *** */
  public function correctPostData() {
    $fileName = 'test-0.7.0beta.catrobat';
    $fileNameWithThumbnail = 'test2.zip';
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $testFileWithThumbnail = dirname(__FILE__) . '/testdata/' . $fileNameWithThumbnail;
    $fileChecksum = md5_file($testFile);
    $fileChecksumWithThumbnail = md5_file($testFileWithThumbnail);
  
    $testFileCatroid = dirname(__FILE__) . '/testdata/test.catrobat';
    $fileChecksumCatroid = md5_file($testFileCatroid);
    $fileSizeCatroid = filesize($testFileCatroid);
  
    $fileSize = filesize($testFile);
    $fileSizeWithThumbnail = filesize($testFileWithThumbnail);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('phpProjectApiSortTest', 'projectApiTests', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
    );
    return $dataArray;
  }
  
  protected function tearDown() {
    pg_close($this->dbConnection);
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_small.jpg');
  }
}
?>
