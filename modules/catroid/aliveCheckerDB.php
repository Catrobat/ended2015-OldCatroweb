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

class aliveCheckerDB extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
  }

  public function __default() {
    echo $this->checkAlive();
    exit();
	}

	public function checkAlive() {
    $query = 'SELECT 1';
    $result = @pg_query($this->dbConnection, $query) or exit();
    if($result) {
      return 200;
    } else {
      return 500;
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
