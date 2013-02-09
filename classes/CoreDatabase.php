<?php
/**
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

class CoreDatabase {
  private $dbConnection = null;

  private function __construct() {
     $connectionString = "host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS;
     if(DATABASE_CONNECTION_PERSISTENT) {
       $this->dbConnection = pg_pconnect($connectionString) or die('Persistent Connection to Database failed: ' . pg_last_error());
     } else { 
       $this->dbConnection = pg_connect($connectionString) or die('Connection to Database failed: ' . pg_last_error());
     }
     $this->prepare();
  }

  public static function singleton() {
    static $db = null;
    if($db === null) {
      $db = new CoreDatabase();
    }
    return $db;
  }

  public function getConnection() {
    return $this->dbConnection;
  }

  private function prepare() {
    $statementsXmlFile = CORE_BASE_PATH.XML_PATH.'prepared_statements.xml';
    if(CorePreparePreparedStatements::getInstance()->setStatements($statementsXmlFile)) {
      CorePreparePreparedStatements::getInstance()->prepare($this->dbConnection);
    }
  }

  public function __destruct() {
    pg_close($this->dbConnection);
  }
}
?>
