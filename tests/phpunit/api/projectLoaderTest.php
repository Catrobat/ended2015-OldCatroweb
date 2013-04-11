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

class projectLoaderTests extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $upload;    
  protected $insertIDArray = array();
  protected $dbConnection;
  
  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/loadProjects.php';
    $this->obj = new loadProjects();
    require_once CORE_BASE_PATH.'modules/api/upload.php';
    $this->upload = new upload();
    
    $this->dbConnection = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
    or die('Connection to Database failed: ' . pg_last_error());  
  } 
  

  public function testCheckLabels() {
    return true;
//      $this->assertEquals($this->obj->labels['websitetitle'], "Catroid Website");
//      $this->assertEquals($this->obj->labels['title'], "Newest Projects");
//      $this->assertEquals($this->obj->labels['prevButton'], "&laquo; Newer");
//      $this->assertEquals($this->obj->labels['nextButton'], "Older &raquo;");
//      $this->assertEquals($this->obj->labels['loadingButton'], "loading...");
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByAge($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
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
    
    $sort = PROJECT_SORTBY_AGE;
    $limit = PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE;
    $offset = 0;
    $filter = array();
    $filter['author'] = $this->obj->escapeUserInput("");
    $filter['searchQuery'] = $this->obj->escapeUserInput("");
  
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY last_activity DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    
    $pg_projects = pg_fetch_all($result);  
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    
    $this->assertEquals(count($projects), pg_num_rows($result));
  
    $i = 0;
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertEquals(intval($project['id']), current($id_sorted));
      
      $i++;
      next($id_sorted);
    }
    
    // search
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_title[0]);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY last_activity DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
    
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(1, count($projects));
    $this->assertEquals(1, pg_num_rows($result));
    $this->assertEquals($unique_title[0], $pg_projects[0]['title']);
    $this->assertEquals($unique_title[0], $projects[0]['title']);
    
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_description);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY last_activity DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    
    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));
    
    pg_free_result($result);
    $i = 0;
    reset($id_sorted);
    
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertEquals(intval($project['id']), current($id_sorted));
    
      $i++;
      next($id_sorted);
    }
    
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY last_activity DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
    
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));
    
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput("");
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY last_activity DESC, projects.id DESC LIMIT ALL OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    
    $pg_projects = pg_fetch_all($result);    
    $projects = $this->obj->getProjects($sort, null, $offset, $filter);
    
    $this->assertEquals(count($insertIds) + $num_projects_before, count($projects));
    $this->assertEquals(count($insertIds) + $num_projects_before, pg_num_rows($result));
    
    pg_free_result($result);
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByDownloads($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
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
  
    $sort = PROJECT_SORTBY_DOWNLOADS;
    $limit = count($id_sorted);
    $offset = 0;
    $filter = array();
    $filter['author'] = $this->obj->escapeUserInput("");
    $filter['searchQuery'] = $this->obj->escapeUserInput("");

    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.download_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
  
    $this->assertEquals(count($projects), pg_num_rows($result));
    $this->assertEquals($limit, count($projects));

    $i = 0;
    $last_download_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertGreaterThanOrEqual($project['download_count'], $last_download_count);

      if($last_download_count == $project['download_count']){
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
      
      $last_download_count = $project['download_count'];
      $last_id =   $project['id'];
      $i++;
    }
  
    // search
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_title[0]);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.download_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
  
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(1, count($projects));
    $this->assertEquals(1, pg_num_rows($result));
    $this->assertEquals($unique_title[0], $pg_projects[0]['title']);
    $this->assertEquals($unique_title[0], $projects[0]['title']);
  
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_description);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.download_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);

    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));

    pg_free_result($result);
    $i = 0;
    $last_download_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertGreaterThanOrEqual($project['download_count'], $last_download_count);
  
      if($last_download_count == $project['download_count']){
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
      
      $i++;
      $last_download_count = $project['download_count'];
      $last_id = $project['id'];
    }
    
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.download_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
  
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));
  
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput("");
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.download_count DESC, projects.id DESC LIMIT ALL OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, null, $offset, $filter);
  
    $this->assertEquals(count($insertIds) + $num_projects_before, count($projects));
    $this->assertEquals(count($insertIds) + $num_projects_before, pg_num_rows($result));
  
    pg_free_result($result);
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctPostData
   */
  public function testRetrieveProjectsOrderedByViews($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    require_once('apiTestModel.php');
    $testModel = new apiTestModel();
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
  
    $sort = PROJECT_SORTBY_VIEWS;
    $limit = count($id_sorted);
    $offset = 0;
    $filter = array();
    $filter['author'] = $this->obj->escapeUserInput("");
    $filter['searchQuery'] = $this->obj->escapeUserInput("");
  
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.view_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
  
    $this->assertEquals(count($projects), pg_num_rows($result));
    $this->assertEquals($limit, count($projects));
  
    $i = 0;
    $last_view_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertGreaterThanOrEqual($project['view_count'], $last_view_count);
  
      if($last_view_count == $project['view_count']){
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
  
      $last_download_count = $project['view_count'];
      $last_id =   $project['id'];
      $i++;
    }
  
    // search
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_title[0]);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.view_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
  
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(1, count($projects));
    $this->assertEquals(1, pg_num_rows($result));
    $this->assertEquals($unique_title[0], $pg_projects[0]['title']);
    $this->assertEquals($unique_title[0], $projects[0]['title']);
  
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput($unique_description);
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.view_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
  
    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));
  
    pg_free_result($result);
    $i = 0;
    $last_download_count = mt_getrandmax();
    $last_id = mt_getrandmax();
    foreach ($projects as $project) {
      $this->assertEquals($project['title'], $pg_projects[$i]['title']);
      $this->assertEquals($project['title_short'], makeShortString($pg_projects[$i]['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH));
      $this->assertEquals($project['last_activity'], 'uploaded '.getTimeInWords($pg_projects[$i]['last_activity'], $testModel->languageHandler, time()));
      $this->assertEquals($project['thumbnail'], getProjectThumbnailUrl($pg_projects[$i]['id']));
      $this->assertEquals($project['download_count'], $pg_projects[$i]['download_count']);
      $this->assertEquals($project['view_count'], $pg_projects[$i]['view_count']);
      $this->assertEquals($project['uploaded_by_string'], 'by '.$pg_projects[$i]['uploaded_by']);
      $this->assertGreaterThanOrEqual($project['download_count'], $last_download_count);
  
      if($last_view_count == $project['view_count']){
        $this->assertGreaterThanOrEqual($project['id'], $last_id);
      }
  
      $i++;
      $last_view_count = $project['view_count'];
      $last_id = $project['id'];
    }
  
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.view_count DESC, projects.id DESC LIMIT '.$limit.'OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $pg_projects = pg_fetch_all($result);
  
    $projects = $this->obj->getProjects($sort, $limit, $offset, $filter);
    $this->assertEquals(count($insertIds), count($projects));
    $this->assertEquals(count($insertIds), pg_num_rows($result));
  
    pg_free_result($result);
    $filter['searchQuery'] = $this->obj->escapeUserInput("");
    $filter['author'] = $this->obj->escapeUserInput("anonymous");
    $query = 'SELECT projects.id, projects.title, coalesce(extract(epoch from "timestamp"(projects.update_time)), extract(epoch from "timestamp"(projects.upload_time))) AS last_activity, cusers.username AS uploaded_by, projects.download_count, projects.view_count FROM projects, cusers WHERE visible=true AND cusers.id=projects.user_id AND (projects.title ILIKE \''.$filter['searchQuery'].'\' OR projects.description ILIKE \''.$filter['searchQuery'].'\') AND cusers.username ILIKE \''.$filter['author'].'\' ORDER BY projects.view_count DESC, projects.id DESC LIMIT ALL OFFSET '.$offset;
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
  
    $pg_projects = pg_fetch_all($result);
    $projects = $this->obj->getProjects($sort, null, $offset, $filter);
  
    $this->assertEquals(count($insertIds) + $num_projects_before, count($projects));
    $this->assertEquals(count($insertIds) + $num_projects_before, pg_num_rows($result));
  
    pg_free_result($result);
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
        array('phpSortTest', 'projectLoaderTests', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
//         array('unitTest with empty description', '', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
//         array('unitTest with a very very very very long title and no description, hopefully not too long', 'description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
//         array("unitTest with special chars: ä, ü, ö ' ", "jüßt 4 spècia1 char **test** ' %&()[]{}_|~#", $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
//         array('unitTest with included Thumbnail', 'this project contains its thumbnail inside the zip file', $testFileWithThumbnail, $fileNameWithThumbnail, $fileChecksumWithThumbnail, $fileSizeWithThumbnail, $fileType),
//         array('unitTest with long description and uppercase fileChecksum', 'this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description.', $testFile, $fileName, strtoupper($fileChecksum), $fileSize, $fileType),
//         array('unitTest with Email and Language', 'description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType, 'en'),
//         array('unitTest', 'my project description with thumbnail in root folder.', $testFile, 'test2.zip', $fileChecksum, $fileSize, $fileType),
//         array('unitTest', 'my project description with thumbnail in images folder.', $testFile, 'test3.zip', $fileChecksum, $fileSize, $fileType),
//         array('unitTest', 'project with new extention "catroid".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
    );
    return $dataArray;
  }
  
  /**
   * @dataProvider randomLongStrings
   */
  public function testShortenTitle($string) {
    return true;
//     $short = makeShortString($string, PROJECT_TITLE_MAX_DISPLAY_LENGTH);

//     $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, strlen($short));
//     $this->assertEquals(0, strcmp(substr($string, 0, strlen($short)), $short));
  }   
  
 /* *** DATA PROVIDERS *** */
  public function randomLongStrings() {
    $returnArray = array();
    $strLen = 200;
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

    for($i=0;$i<5;$i++) {
      $str = '';
      for($j=0;$j<$strLen;$j++) {
        $str .= $chars[rand(0, count($chars)-1)];
      }
      $returnArray[$i] = array($str);
    }

    return $returnArray;
  }
  
  protected function tearDown() {
    pg_close($this->dbConnection);
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_small.jpg');
  }
}
?>
