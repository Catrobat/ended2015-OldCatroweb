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

class CoreDatabase {
  private $dbConnection = null;

  private function __construct() {
     $connectionString = "host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS;
     $this->dbConnection = pg_pconnect($connectionString)
     or die('Connection to Database failed: ' . pg_last_error());
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
