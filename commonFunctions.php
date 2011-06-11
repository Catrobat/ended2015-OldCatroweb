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
 * - as soon as the same method-code is used in more than one class, consider putting the method in here
 * - keep them as short as possible
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

function convertBytesToMegabytes($numOfBytes) {
  $mb = round($numOfBytes/1048576, 1);
  if($mb < 0.1) {
    $mb = html_entity_decode("< 0.1");
  }
  return $mb;
}

function makeShortString($string, $maxLength, $suffix = '') {
  if(strlen($string) > $maxLength) {
    return mb_substr($string, 0, $maxLength-mb_strlen($suffix, 'UTF-8'), 'UTF-8').$suffix;
  }
  return $string;
}

function getProjectThumbnailUrl($projectId) {
  $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
  $thumbFile = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
  if(!is_file($thumbFile)) {
    $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENTION_SMALL;
  }
  return $thumb;
}

function getProjectImageUrl($projectId) {
  $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
  $imgFile = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
  if(!is_file($imgFile)) {
    $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENTION_LARGE;
  }
  return $img;
}

function getProjectQRCodeUrl($projectId) {
  $qr = BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
  $qrFile = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
  if(!is_file($qrFile)) {
    return false;
  }
  return $qr;
}

function getTimeInWords($fromTime, $toTime = 0) {
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

function getSupportedLanguagesArray() {
  $supportedLanguages = array(
  	'de',
  	'en');
  return $supportedLanguages;
}

?>
