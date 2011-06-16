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
class registration extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('registration.css');
    $this->addJs('registration.js');
    $this->initRegistration();
  }

  public function __default() {
  }

  public function initRegistration() {
    $answer = '';
    try {
      $this->months = getMonthsArray();
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->initCountryCodes();
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    $this->answer .= $answer;
  }

  private function initCountryCodes() {
    $query = "EXECUTE get_country_from_countries";
    $result = @pg_query($this->dbConnection, $query);

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }

    if(pg_num_rows($result) > 0) {
      $countryCodeList = array();
      $countryNameList = array();

      $x = 1;
      while($country = pg_fetch_assoc($result)) {
        $countryCodeList[$x] = $country['code'];
        $countryNameList[$x] = $country['name'];
        $x++;
      }
      // if user country is not in list
      $countryCodeList[$x] = "undef";
      $countryNameList[$x] = "undefined";
      pg_free_result($result);
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'country_codes_not_available'));
    }
    $this->countryCodeList = $countryCodeList;
    $this->countryNameList = $countryNameList;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
