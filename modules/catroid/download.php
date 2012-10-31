<?php
/*    Catroid: An on-device graphical programming language for Android devices
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

class download extends CoreAuthenticationNone {
  protected $clientDownloadCounterBlacklist;

  public function __construct() {
    parent::__construct();

    // Blacklisted user agents:
    //   https://play.google.com/store/apps/details?id=com.google.zxing.client.android (UA: ZXing)
    
    $this->clientDownloadCounterBlacklist = array("ZXing");
  }

  public function __default() {
    $id = $_REQUEST['method'];
    $line = $this->retrieveProjectById($id);
    if(!$line || $line == -1) {
      return;
    }
    
    $this->id = $id;
    $this->source_file = $line['source'];
    $this->file_name = str_replace(' ', '_', $line['title']);
	}

	public function retrieveProjectById($id) {
	  if(!is_numeric($id) || intval($id) < 0) {
      return -1;
	  }
    $this->incrementDownloadCounter($id);
	  $result = pg_execute($this->dbConnection, "get_project_by_id", array($id)) or
	            $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $line = pg_fetch_assoc($result);
    pg_free_result($result);
    return $line;
  }

  private function incrementDownloadCounter($projectId) {
    if(isset($_SERVER['HTTP_USER_AGENT'])) {
      $userAgent = $_SERVER['HTTP_USER_AGENT'];
      foreach($this->clientDownloadCounterBlacklist as $client) {
        if(strpos($userAgent, $client) !== false) {
          return;
        }
      }
    }
  
    $currentDownloadState = array();
    if(is_array($this->session->projectsCurrentlyLoading)) {
      $currentDownloadState = $this->session->projectsCurrentlyLoading;
    }
  
    $lastAccess = isset($currentDownloadState[$projectId]) ? intval($currentDownloadState[$projectId]) : 0;
    if($lastAccess + 20 < time()) {
      pg_execute($this->dbConnection, "increment_download_counter", array($projectId)) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  
      $currentDownloadState[$projectId] = time();
      //$this->session->projectsCurrentlyLoading = $currentDownloadState;
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}

?>
