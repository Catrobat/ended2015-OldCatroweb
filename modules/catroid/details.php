<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class details extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();

    $this->addCss('buttons.css');
    if($this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX) ||
    $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX_MOBILE) ||
    $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_SAFARI) ||
    $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_CHROME) ||
    $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_ANDROID)) {
      $this->addCss('projectDetails.css');
    } else {
      $this->addCss('projectDetails_nohtml5.css');
    }
    $this->addJs('projectDetails.js');

    $this->isMobile = $this->clientDetection->isMobile();
  }

  public function __default() {
    $projectId = $_REQUEST['method'];
    $this->project = $this->getProjectDetails($projectId);
  }

  public function getProjectDetails($projectId) {
    if(!$projectId || !is_numeric($projectId)) {
      $this->errorHandler->showErrorPage('db', 'no_entry_for_id', 'ID: '.$projectId);
      exit();
    }
    if($this->session->adminUser) {
      $query = "EXECUTE get_project_by_id('$projectId');";
    } else {
      $query = "EXECUTE get_visible_project_by_id('$projectId');";
    }
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $project = pg_fetch_assoc($result);
    pg_free_result($result);

    if(!$project) {
      $this->errorHandler->showErrorPage('db', 'no_entry_for_id', 'ID: '.$projectId);
      exit();
    }
    $project['image'] = $this->getProjectImage($project['id']);
    $project['publish_time_in_words'] = $this->getTimeInWords(strtotime($project['upload_time']), time());
    $project['publish_time_precice'] = date('Y-m-d H:i:s', strtotime($project['upload_time']));
    $project['uploaded_by'] = 'unknown';
    $project['title'] = utf8_decode($project['title']);
    if($project['description']) {
      $project['description'] = utf8_decode($project['description']);
    } else {
      $project['description'] = '';
    }
    $project['description_short'] = $this->shortenDescription($project['description']);
    $project['qr_code_image'] = $this->getQRCodeImage($projectId);
    $this->incrementViewCounter($projectId);
    return $project;
  }

  public function getProjectImage($projectId) {
    $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
    $img_file = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
    if(!is_file($img_file)) {
      $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
    }

    return $img;
  }

  public function getQRCodeImage($projectId) {
    $qrImg = BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
    $qrImgFile = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
    if(!is_file($qrImgFile)) {
      return false;
    }

    return $qrImg;
  }

  public function getTimeInWords($fromTime, $toTime = 0) {
    if($toTime == 0) {
      $toTime = time();
    }
    $seconds = round(abs($toTime - $fromTime));
    $minutes = round($seconds/60);
    if ($minutes <= 1) {
      return ($minutes == 0) ? 'less than a minute' : '1 minute';
    }
    if ($minutes < 45) {
      return $minutes.' minutes';
    }
    if ($minutes < 90) {
      return 'about 1 hour';
    }
    if ($minutes < 1440) {
      return 'about '.round(floatval($minutes)/60.0).' hours';
    }
    if ($minutes < 2880) {
      return '1 day';
    }
    if ($minutes < 43200) {
      return 'about '.round(floatval($minutes)/1440).' days';
    }
    if ($minutes < 86400) {
      return 'about 1 month';
    }
    if ($minutes < 525600) {
      return round(floatval($minutes)/43200).' months';
    }
    if ($minutes < 1051199) {
      return 'about 1 year';
    }
    return 'over '.round(floatval($minutes)/525600) . ' years';
  }

  public function shortenDescription($string) {
    if(strlen($string) > PROJECT_SHORT_DESCRIPTION_MAX_LENGTH) {
      return substr($string, 0, PROJECT_SHORT_DESCRIPTION_MAX_LENGTH-3).'...';
    }
    return false;
  }

  public function incrementViewCounter($projectId) {
    $query = "EXECUTE increment_view_counter('$projectId');";
    $result = pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    return;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
