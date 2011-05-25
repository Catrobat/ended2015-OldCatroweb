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

/* Guideline for commonFunctions:
 * - keep them very short
 * - give them meaningful names
 * - only put them here if they are used in more than one class
 * - or if they provide a set of data (e.g. array)
 * - no interaction with framework (e.g. database, errorHandler, etc.)
 */

function getUsernameBlacklistArray() {
  $usernameBlacklist = array(
    'admin',
    'catroid',
    'kittyroid'
   );
   return $usernameBlacklist;
}

function getMonthsArray() {
  $months = array(
    1=>"Jan",
    2=>"Feb",
    3=>"Mar",
    4=>"Apr",
    5=>"May",
    6=>"Jun",
    7=>"Jul",
    8=>"Aug",
    9=>"Sep",
    10=>"Oct",
    11=>"Nov",
    12=>"Dec"
  );
  return $months;
}

function getIpBlockClassWhitelistArray() {
  $whitelistClasses = array(
  	"privacypolicy",
    "terms",
    "copyrightpolicy",
    "imprint",
    "contactus",
    "errorPage"
  );
  return $whitelistClasses;
}

?>
