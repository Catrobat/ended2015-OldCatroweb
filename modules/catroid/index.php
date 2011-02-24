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

class index extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    if($this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX_MOBILE) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_SAFARI) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_CHROME) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_ANDROID)) {
        $this->addCss('projectList.css');
    } else {
      $this->addCss('projectList_nohtml5.css');
    }
    $this->addCss('buttons.css');
    $this->htmlHeaderFile = 'htmlIndexHeaderTemplate.php';
  }

  public function __default() {
    $this->projects = $this->retrieveAllProjectsFromDatabase();
  }

  public function retrieveAllProjectsFromDatabase() {
    $query = 'EXECUTE get_visible_projects_ordered_by_uploadtime;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    if($projects[0]['id']) {
      $i=0;
      foreach($projects as $project) {
        $projects[$i]['title'] = utf8_decode($projects[$i]['title']);
        $projects[$i]['title_short'] = $this->shortenTitle(utf8_decode($project['title']));
        $projects[$i]['thumbnail'] = $this->getThumbnail($project['id']);
        $i++;
      }
      return($projects);
    } else {
      return ($projects[0]);
    }
  }

  public function shortenTitle($string) {
    if(strlen($string) > PROJECT_TITLE_MAX_DISPLAY_LENGTH) {
      return substr($string, 0, PROJECT_TITLE_MAX_DISPLAY_LENGTH);
    }
    return $string;
  }

  public function getThumbnail($projectId) {
    $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
    $thumb_file = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
    if(!is_file($thumb_file)) {
      $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
    }

    return $thumb;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
