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
    $id_sorted = array();
    $unique_title = array();
    $unique_description = "";
    $newest_project = null;
    
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $num_projects_before = pg_num_rows($result);
    
    mt_srand ((double) microtime() * 1000000);
    $unique_description = $projectDescription.'_'.mt_rand();
    
    $query = 'SELECT projects.id, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY last_activity DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $newest_project = pg_fetch_assoc($result);
    pg_free_result($result);
    
    for($i = 0; $i < PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE; $i++) {
      $testFile =  $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
      
      $unique_title[$i] = $projectTitle.'_'.mt_rand();
      $formData = array(
          'projectTitle' => $unique_title[$i],
          'projectDescription' => $unique_description,
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
      $time= mt_rand($newest_project['last_activity'] + 1,time());
      $date_string = date("Y-m-d H:i:s.u",$time);
      array_push($date_sorted, $date_string);
      array_push($id_sorted, $id);
      $upload_time = "TIMESTAMP WITH TIME ZONE '".date($date_string)."'";
      $update_query = 'UPDATE projects SET upload_time='.$upload_time.', update_time=NULL WHERE projects.id='.$id;
      $result = pg_query($this->dbConnection, $update_query) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
    
    array_multisort($date_sorted, SORT_DESC, SORT_STRING, $id_sorted, SORT_DESC, SORT_NUMERIC);
    
    $sort = $sortby;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $offset = 0;
  
    $pg_projects = $this->getResults($sortby, $limit, $offset, "", "");  
    
    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_AGE, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pg_projects));
    $i = 0;
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($project['ProjectId']));
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
    $projects_all_info = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
    
    $this->assertEquals(3, count($projects_all_info['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects_all_info['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects_all_info['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects_all_info['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects_all_info['CatrobatProjects']), count($pg_projects));
    
    $i = 0;
    foreach ($projects_all_info['CatrobatProjects'] as $project) {
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals(current($id_sorted), intval($project['ProjectId']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['Author'], $pg_projects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pg_projects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['Views'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pg_projects[$i]['id'].PROJECTS_EXTENSION);      
      $this->assertEquals($project['Version'], $pg_projects[$i]['version_name']);      
      $this->assertEquals($project['Uploaded'], $pg_projects[$i]['last_activity']);      
      $i++;
      next($id_sorted);
    }
    
    // search for unique project 
    for($i = 0; $i < count($unique_title); $i++) {
      $offset = 0;
      $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
      $search_term = $unique_title[$i];
      $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_AGE, $sortby, $search_term);
      $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");
  
      $this->assertEquals(1, count($search_projects['CatrobatProjects']));
      $this->assertEquals(1, count($pg_projects));
      $this->assertEquals(1, intval($search_projects['CatrobatInformation']['TotalProjects']));
      
      $this->assertEquals($search_projects['CatrobatProjects'][0]['ProjectName'], $pg_projects[0]['title']);
      $this->assertEquals($search_projects['CatrobatInformation']['BaseUrl'].$search_projects['CatrobatProjects'][0]['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[0]['id']));
      $this->assertEquals($search_projects['CatrobatProjects'][0]['ProjectUrl'],  'details/' . $pg_projects[0]['id']);
      $this->assertEquals($search_projects['CatrobatProjects'][0]['UploadedString'], getTimeInWords($pg_projects[0]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['ProjectId']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['ScreenshotBig']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Author']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Description']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Uploaded']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Version']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Views']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['Downloads']));
      $this->assertFalse(isset($search_projects['CatrobatProjects'][0]['DownloadUrl']));
    }

    // search for description
    $i = 0;
    $offset = 0;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_AGE, $sortby, $search_term);
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");
    
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
    
    $i = 0;
    reset($id_sorted);
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($search_projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($search_project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($search_project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($search_project['ProjectId']));
      $this->assertFalse(isset($search_project['ScreenshotBig']));
      $this->assertFalse(isset($search_project['Author']));
      $this->assertFalse(isset($search_project['Description']));
      $this->assertFalse(isset($search_project['Uploaded']));
      $this->assertFalse(isset($search_project['Version']));
      $this->assertFalse(isset($search_project['Views']));
      $this->assertFalse(isset($search_project['Downloads']));
      $this->assertFalse(isset($search_project['DownloadUrl']));
      $i++;
    }
    
    // search for author
    $i = 0;
    $offset = 0;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_user = "anonymous";
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_AGE, $sortby, $search_term, $search_user);
    
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, $search_user);
    
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
    
    $i = 0;
    reset($id_sorted);
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($search_projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($search_project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($search_project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertFalse(isset($search_project['ProjectId']));
      $this->assertFalse(isset($search_project['ScreenshotBig']));
      $this->assertFalse(isset($search_project['Author']));
      $this->assertFalse(isset($search_project['Description']));
      $this->assertFalse(isset($search_project['Uploaded']));
      $this->assertFalse(isset($search_project['Version']));
      $this->assertFalse(isset($search_project['Views']));
      $this->assertFalse(isset($search_project['Downloads']));
      $this->assertFalse(isset($search_project['DownloadUrl']));
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
    $dl_sorted = array();
    $id_sorted = array();
    $unique_title = array();
    $unique_description = "";
    $most_dl_project = null;
  
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $num_projects_before = pg_num_rows($result);
  
    mt_srand ((double) microtime() * 1000000);
    $unique_description = $projectDescription.'_'.mt_rand();
  
    $query = 'SELECT projects.id, projects.download_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY projects.download_count DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $most_dl_project = pg_fetch_assoc($result);
    pg_free_result($result);
  
    for($i = 0; $i < PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE; $i++) {
      $testFile =  $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
  
      $unique_title[$i] = $projectTitle.'_'.mt_rand();
      $formData = array(
          'projectTitle' => $unique_title[$i],
          'projectDescription' => $unique_description,
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
  
    array_push($dl_sorted, $most_dl_project['download_count']);
    array_push($id_sorted, $most_dl_project['id']);
    $insert_same_dl_count = false;
    
    foreach ($insertIds as $id) {
      if(!$insert_same_dl_count) {
        $download_count = $most_dl_project['download_count'];
        $insert_same_dl_count = true;
      }
      else {
        $download_count = mt_rand($most_dl_project['download_count'] + 1, mt_getrandmax() - 1);
      }
      $view_count = $id;
      array_push($dl_sorted, $download_count);
      array_push($id_sorted, $id);
      $update_query = 'UPDATE projects SET download_count = '.$download_count.', view_count = '.$view_count.' WHERE projects.id = '.$id;
      $result = pg_query($this->dbConnection, $update_query) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
  
    array_multisort($dl_sorted, SORT_DESC, SORT_NUMERIC, $id_sorted, SORT_DESC, SORT_NUMERIC);
    
    $limit = count($id_sorted);
    $offset = 0;
  
    $pg_projects = $this->getResults($sortby, $limit, $offset, "", "");

    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_DOWNLOADS, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pg_projects));
    $i = 0;
    
    $last_download_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertGreaterThanOrEqual($project['Downloads'], $last_download_count);
      $this->assertEquals($project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals(current($dl_sorted), $project['Downloads']);
      $this->assertEquals(current($id_sorted), $project['ProjectId']);
      
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
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
      
      $last_download_count = $project['Downloads'];
      $last_id =   $project['id'];
      next($id_sorted);
      next($dl_sorted);
      $i++;
    }
    
    // test all mask
    $projects_all_info = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
  
    $this->assertEquals(3, count($projects_all_info['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects_all_info['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects_all_info['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects_all_info['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects_all_info['CatrobatProjects']), count($pg_projects));
  
    reset($id_sorted);
    reset($dl_sorted);
    $i = 0;
    foreach ($projects_all_info['CatrobatProjects'] as $project) {
      $this->assertEquals($project['Author'], $pg_projects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pg_projects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pg_projects[$i]['id'].PROJECTS_EXTENSION);
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pg_projects[$i]['id']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['Uploaded'], $pg_projects[$i]['last_activity']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['Views'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['Version'], $pg_projects[$i]['version_name']);
      $this->assertEquals(current($dl_sorted), $project['Downloads']);
      $this->assertEquals(current($id_sorted), intval($project['ProjectId']));
      $i++;
      next($id_sorted);
      next($dl_sorted);
    }

    // search for unique project
    for($i = 0; $i < count($unique_title); $i++) {
      $offset = 0;
      $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
      $search_term = $unique_title[$i];
      $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_DOWNLOADS, $sortby, $search_term);
      $project = $search_projects['CatrobatProjects'][0];
      $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");

      $this->assertEquals(1, count($search_projects['CatrobatProjects']));
      $this->assertEquals(1, intval($search_projects['CatrobatInformation']['TotalProjects']));
      $this->assertEquals(1, count($pg_projects));

      $this->assertEquals($search_projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[0]['id']));
      $this->assertEquals($project['Downloads'], $pg_projects[0]['download_count']);
      $this->assertEquals($project['ProjectId'], $pg_projects[0]['id']);
      $this->assertEquals($project['ProjectName'], $pg_projects[0]['title']);

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
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_DOWNLOADS, $sortby, $unique_description);
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");
  
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
  
    reset($id_sorted);
    reset($dl_sorted);
    $i = 0;
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($search_project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));

      $this->assertEquals(current($dl_sorted), $search_project['Downloads']);
      $this->assertEquals(current($id_sorted), intval($search_project['ProjectId']));

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
      next($id_sorted);
      next($dl_sorted);
    }
    
    // search for author
    $offset = 0;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_user = "anonymous";
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_DOWNLOADS, $sortby, $search_term, $search_user);
  
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, $search_user);
  
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
  
    $i = 0;
    reset($id_sorted);
    reset($dl_sorted);
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($search_project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));

      $this->assertEquals(current($dl_sorted), $search_project['Downloads']);
      $this->assertEquals(current($id_sorted), intval($search_project['ProjectId']));

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
      next($id_sorted);
      next($dl_sorted);
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
    $viewed_sorted = array();
    $id_sorted = array();
    $unique_title = array();
    $unique_description = "";
    $most_viewed_project = null;
  
    $query = 'SELECT projects.id FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND cusers.username ILIKE \'anonymous\'';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $num_projects_before = pg_num_rows($result);
  
    mt_srand ((double) microtime() * 1000000);
    $unique_description = $projectDescription.'_'.mt_rand();
  
    $query = 'SELECT projects.id, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id  ORDER BY projects.view_count DESC, projects.id DESC LIMIT 1';
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $most_viewed_project = pg_fetch_assoc($result);
    pg_free_result($result);
  
    for($i = 0; $i < PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE; $i++) {
      $testFile =  $fileName;
      $fileChecksum = md5_file($testFile);
      $fileSize = filesize($testFile);
  
      $unique_title[$i] = $projectTitle.'_'.mt_rand();
      $formData = array(
          'projectTitle' => $unique_title[$i],
          'projectDescription' => $unique_description,
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
  
    array_push($viewed_sorted, $most_viewed_project['view_count']);
    array_push($id_sorted, $most_viewed_project['id']);
    $insert_same_view_count = false;
  
    foreach ($insertIds as $id) {
      if(!$insert_same_view_count) {
        $view_count = $most_viewed_project['view_count'];
        $insert_same_view_count = true;
      }
      else {
        $view_count = mt_rand($most_viewed_project['view_count'] + 1, mt_getrandmax() - 1);
      }
      $download_count = $id;
      array_push($viewed_sorted, $view_count);
      array_push($id_sorted, $id);
      $update_query = 'UPDATE projects SET download_count = '.$download_count.', view_count = '.$view_count.' WHERE projects.id = '.$id;
      $result = pg_query($this->dbConnection, $update_query) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(pg_affected_rows($result), 1);
      pg_free_result($result);
    }
  
    array_multisort($viewed_sorted, SORT_DESC, SORT_NUMERIC, $id_sorted, SORT_DESC, SORT_NUMERIC);
  
  
    $limit = count($id_sorted);
    $offset = 0;
  
    $pg_projects = $this->getResults($sortby, $limit, $offset, "", "");
  
    $projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_VIEWS, $sortby);
    $this->assertEquals(3, count($projects['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects['CatrobatProjects']), count($pg_projects));
    $i = 0;
  
    $last_view_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects['CatrobatProjects'] as $project) {
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertGreaterThanOrEqual($project['Views'], $last_view_count);
      $this->assertEquals($project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($project['Views'], $pg_projects[$i]['view_count']);
      $this->assertEquals(current($viewed_sorted), $project['Views']);
      $this->assertEquals(current($id_sorted), $project['ProjectId']);
  
      $this->assertFalse(isset($project['Author']));
      $this->assertFalse(isset($project['Description']));
      $this->assertFalse(isset($project['Downloads']));
      $this->assertFalse(isset($project['DownloadUrl']));
      $this->assertFalse(isset($project['ProjectUrl']));
      $this->assertFalse(isset($project['ScreenshotBig']));
      $this->assertFalse(isset($project['Uploaded']));
      $this->assertFalse(isset($project['UploadedString']));
      $this->assertFalse(isset($project['Version']));
      if($last_view_count == $project['Views']){
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
  
      $last_view_count = $project['Views'];
      $last_id =   $project['id'];
      next($id_sorted);
      next($viewed_sorted);
      $i++;
    }
  
    // test all mask
    $projects_all_info = $this->obj->get($offset, $limit, PROJECT_MASK_ALL, $sortby);
  
    $this->assertEquals(3, count($projects_all_info['CatrobatInformation']));
    $this->assertNotEquals(-1, $projects_all_info['CatrobatInformation']['TotalProjects']);
    $this->assertEquals(BASE_PATH, $projects_all_info['CatrobatInformation']['BaseUrl']);
    $this->assertEquals(PROJECTS_EXTENSION, $projects_all_info['CatrobatInformation']['ProjectsExtension']);
    $this->assertEquals('', $this->obj->Error);
    $this->assertEquals(count($projects_all_info['CatrobatProjects']), count($pg_projects));
  
    reset($id_sorted);
    reset($viewed_sorted);
    $i = 0;
    foreach ($projects_all_info['CatrobatProjects'] as $project) {
      $this->assertEquals($project['Author'], $pg_projects[$i]['uploaded_by']);
      $this->assertEquals($project['Description'], $pg_projects[$i]['description']);
      $this->assertEquals($project['Downloads'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['DownloadUrl'], 'download/' . $pg_projects[$i]['id'].PROJECTS_EXTENSION);
      $this->assertEquals($project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($project['ProjectUrl'],  'details/' . $pg_projects[$i]['id']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotBig'], getProjectImageUrl($pg_projects[$i]['id']));
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['Uploaded'], $pg_projects[$i]['last_activity']);
      $this->assertEquals($project['UploadedString'], getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['Views'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['Version'], $pg_projects[$i]['version_name']);
      $this->assertEquals(current($viewed_sorted), $project['Views']);
      $this->assertEquals(current($id_sorted), intval($project['ProjectId']));
      $i++;
      next($id_sorted);
      next($viewed_sorted);
    }
    
    // search for unique project
    for($i = 0; $i < count($unique_title); $i++) {
      $offset = 0;
      $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
      $search_term = $unique_title[$i];
      $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_VIEWS, $sortby, $search_term);
      $project = $search_projects['CatrobatProjects'][0];
      $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");
  
      $this->assertEquals(1, count($search_projects['CatrobatProjects']));
      $this->assertEquals(1, intval($search_projects['CatrobatInformation']['TotalProjects']));
      $this->assertEquals(1, count($pg_projects));
  
      $this->assertEquals($search_projects['CatrobatInformation']['BaseUrl'].$project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[0]['id']));
      $this->assertEquals($project['ProjectId'], $pg_projects[0]['id']);
      $this->assertEquals($project['ProjectName'], $pg_projects[0]['title']);
      $this->assertEquals($project['Views'], $pg_projects[0]['view_count']);
  
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
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_VIEWS, $sortby, $unique_description);
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, "");
  
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
  
    reset($id_sorted);
    reset($viewed_sorted);
    $i = 0;
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($search_project['Views'], $pg_projects[$i]['view_count']);
  
      $this->assertEquals(current($viewed_sorted), $search_project['Views']);
      $this->assertEquals(current($id_sorted), intval($search_project['ProjectId']));
  
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
      next($id_sorted);
      next($viewed_sorted);
    }
  
    // search for author
    $offset = 0;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $search_term = $unique_description;
    $search_user = "anonymous";
    $search_projects = $this->obj->get($offset, $limit, PROJECT_MASK_LIST_VIEWS, $sortby, $search_term, $search_user);
  
    $pg_projects = $this->getResults($sortby, $limit, $offset, $search_term, $search_user);
  
    $this->assertEquals(count($insertIds), count($search_projects['CatrobatProjects']));
    $this->assertEquals(count($insertIds), intval($search_projects['CatrobatInformation']['TotalProjects']));
    $this->assertEquals(count($insertIds), count($pg_projects));
  
    $i = 0;
    reset($id_sorted);
    reset($viewed_sorted);
    foreach($search_projects['CatrobatProjects'] as $search_project) {
      $this->assertEquals($search_project['ProjectId'], $pg_projects[$i]['id']);
      $this->assertEquals($search_project['ProjectName'], $pg_projects[$i]['title']);
      $this->assertEquals($projects['CatrobatInformation']['BaseUrl'].$search_project['ScreenshotSmall'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($search_project['Views'], $pg_projects[$i]['view_count']);
  
      $this->assertEquals(current($viewed_sorted), $search_project['Views']);
      $this->assertEquals(current($id_sorted), intval($search_project['ProjectId']));
  
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
      next($id_sorted);
      next($viewed_sorted);
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
