<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
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

class loadNewestProjects extends CoreAuthenticationNone {
  protected $pageNr = 0;

  public function __construct() {
    parent::__construct();
    
    $labels = array();
    $labels['websitetitle'] = "Catroid Website";
    $labels['title'] = "Newest Projects";
    $labels['prevButton'] = "&laquo; Newer";
    $labels['nextButton'] = "Older &raquo;";
    $labels['loadingButton'] = "<img src='".BASE_PATH."images/symbols/ajax-loader.gif' /> loading...";
    $this->labels = $labels;
  }

  public function __default() {
    if(isset($_REQUEST['method'])) {
      $this->pageNr = intval($_REQUEST['method'])-1;
    }    
    
    $this->content = $this->retrievePageNrFromDatabase($this->pageNr);
  }

  public function retrievePageNrFromDatabase($pageNr) {
  	if($pageNr < 0) {
      return "NIL";
  	}
  	 
    $query = 'EXECUTE get_visible_projects_ordered_by_uploadtime_limited_and_offset('.PROJECT_PAGE_LOAD_MAX_PROJECTS.', '.(PROJECT_PAGE_LOAD_MAX_PROJECTS * $pageNr).');';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    if($projects[0]['id']) {
      $i=0;
      foreach($projects as $project) {
        $projects[$i]['title'] = $projects[$i]['title'];
        $projects[$i]['title_short'] = $this->shortenTitle($project['title']);
        $projects[$i]['upload_time'] =  $this->getTimeInWords(strtotime($project['upload_time']), time());
        $projects[$i]['thumbnail'] = $this->getThumbnail($project['id']);
        $i++;
      }
      return($projects);
    } else {
      return "NIL";
    }
  }

  public function shortenTitle($string) {
    if(strlen($string) > PROJECT_TITLE_MAX_DISPLAY_LENGTH) {
      return mb_substr($string, 0, PROJECT_TITLE_MAX_DISPLAY_LENGTH, 'UTF-8');
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

  public function __destruct() {
    parent::__destruct();
  }
}
?>
