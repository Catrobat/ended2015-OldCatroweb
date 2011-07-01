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
		'administrator',
    'catroweb',
  	'kittyroid'
  	);
  	return $usernameBlacklist;
}

function getMonthsArray($languageHandler) {
  $months = array(
  1=>$languageHandler->getString('template_common_january'),
  2=>$languageHandler->getString('template_common_february'),
  3=>$languageHandler->getString('template_common_march'),
  4=>$languageHandler->getString('template_common_april'),
  5=>$languageHandler->getString('template_common_may'),
  6=>$languageHandler->getString('template_common_june'),
  7=>$languageHandler->getString('template_common_july'),
  8=>$languageHandler->getString('template_common_august'),
  9=>$languageHandler->getString('template_common_september'),
  10=>$languageHandler->getString('template_common_october'),
  11=>$languageHandler->getString('template_common_november'),
  12=>$languageHandler->getString('template_common_december')
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

function getTimeInWords($fromTime, $languageHandler, $toTime = 0) {
  if($toTime == 0) {
    $toTime = time();
  }
  $seconds = round(abs($toTime - $fromTime));
  $minutes = round($seconds/60);
  if ($minutes <= 1) {
    return ($minutes == 0) ? $languageHandler->getString('template_common_less_than_a_minute_ago') : $languageHandler->getString('one_minute_ago');
  }
  if ($minutes < 45) {
    return $languageHandler->getString('template_common_minutes_ago', $minutes);
  }
  if ($minutes < 90) {
    return $languageHandler->getString('template_common_one_hour_ago');
  }
  if ($minutes < 1440) {
    return $languageHandler->getString('template_common_hours_ago', round(floatval($minutes)/60.0));
  }
  if ($minutes < 2880) {
    return $languageHandler->getString('template_common_one_day_ago');
  }
  if ($minutes < 43200) {
    return $languageHandler->getString('template_common_days_ago', round(floatval($minutes)/1440));
  }
  if ($minutes < 86400) {
    return $languageHandler->getString('template_common_one_month_ago');
  }
  if ($minutes < 525600) {
    return $languageHandler->getString('template_common_months_ago', round(floatval($minutes)/43200));
  }
  if ($minutes < 1051199) {
    return $languageHandler->getString('template_common_one_year_ago');
  }
  return $languageHandler->getString('template_common_over_years_ago', round(floatval($minutes)/525600));
}

function getSupportedLanguagesArray() {
  $supportedLanguages = array(
  	'de',
  	'en',
  	'ms',
  	'cn');
  return $supportedLanguages;
}

function copyDir($src, $dst) {
  if(file_exists($dst)) {
    removeDir($dst);
  }
  if(is_dir($src)) {
    mkdir($dst, 0777, true);
    $files = scandir($src);
    foreach($files as $file) {
      if($file != "." && $file != "..") {
        copyDir("$src/$file", "$dst/$file");
      }
    }
  }
  else if(file_exists($src)) {
    copy($src, $dst);
  }
}

function removeDir($dir) {
  if(is_dir($dir)) {
    $files = scandir($dir);
    foreach($files as $file) {
      if($file != "." && $file != "..") {
        removeDir("$dir/$file");
      }
    }
    rmdir($dir);
  }
  else if(file_exists($dir)) {
    unlink($dir);
  }
}

?>
