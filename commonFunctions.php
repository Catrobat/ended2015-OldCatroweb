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

function getUserBlockClassWhitelistArray() {
  $whitelistClasses = array(
  	"privacypolicy",
    "terms",
    "copyrightpolicy",
    "imprint",
    "contactus",
    "errorPage",
    "login",
    "logout"
    );
    return $whitelistClasses;
}

function convertBytesToMegabytes($numOfBytes) {
  $mb = ceil($numOfBytes/1048576 * 10) / 10;
  if(($numOfBytes/1048576) < 0.1) {
    $mb = "&lt; 0.1";
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

function getCatroidProjectQRCodeUrl($projectId, $projectTitle) {
  $qr = BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
  $qrFile = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
  if(!is_file($qrFile)) {
    $urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.PROJECTS_EXTENTION.'?fname='.urlencode($projectTitle));
    $destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
    if(!generateQRCode($urlToEncode, $destinationPath)) {
      return false;
    }
  }
  return $qr;
}

function getAppProjectQRCodeUrl($projectId, $projectTitle) {
  $qr = BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.APP_QR_EXTENTION;
  $qrFile = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.APP_QR_EXTENTION;
  if(!is_file($qrFile)) {
    $urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.APP_EXTENTION.'?fname='.urlencode($projectTitle));
    $destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.APP_QR_EXTENTION;
    if(!generateQRCode($urlToEncode, $destinationPath)) {
      return false;
    }
  }
  return $qr;
}

function generateQRCode($urlToEncode, $destinationPath) {
  $serviceUrl = PROJECTS_QR_SERVICE_URL.$urlToEncode;
  $qrImageHandle = @imagecreatefrompng($serviceUrl);
  if(!$qrImageHandle) {
    return false;
  }
  if(!@imagepng($qrImageHandle, $destinationPath, 9)) {
    return false;
  }
  @imagedestroy($qrImageHandle);
  chmod($destinationPath, 0666);
  return true;
}

function getTimeInWords($fromTime, $languageHandler, $toTime = 0) {
  if($toTime == 0) {
    $toTime = time();
  }
  $seconds = round(abs($toTime - $fromTime));
  $minutes = round($seconds/60);
  if($minutes <= 1) {
    return ($minutes == 0) ? $languageHandler->getString('template_common_less_than_a_minute_ago') : $languageHandler->getString('template_common_one_minute_ago');
  }
  if($minutes < 45) {
    return $languageHandler->getString('template_common_minutes_ago', $minutes);
  }
  if($minutes < 90) {
    return $languageHandler->getString('template_common_one_hour_ago');
  }
  if($minutes < 1440) {
    return $languageHandler->getString('template_common_hours_ago', round(floatval($minutes)/60.0));
  }
  if($minutes < 2880) {
    return $languageHandler->getString('template_common_one_day_ago');
  }
  if($minutes < 43200) {
    return $languageHandler->getString('template_common_days_ago', round(floatval($minutes)/1440));
  }
  if($minutes < 86400) {
    return $languageHandler->getString('template_common_one_month_ago');
  }
  if($minutes < 525600) {
    return $languageHandler->getString('template_common_months_ago', round(floatval($minutes)/43200));
  }
  if($minutes < 1051199) {
    return $languageHandler->getString('template_common_one_year_ago');
  }
  return $languageHandler->getString('template_common_over_years_ago', round(floatval($minutes)/525600));
}

function getSupportedLanguagesArray($languageHandler) {
  $supportedLanguages = array(
    'ar'=>array('name'=>$languageHandler->getString('template_common_arabic'), 'nameNative'=>'‫العربية‬', 'supported'=>false),
    'bg'=>array('name'=>$languageHandler->getString('template_common_bulgarian'), 'nameNative'=>'‪български‬', 'supported'=>false),
    'ca'=>array('name'=>$languageHandler->getString('template_common_catalan'), 'nameNative'=>'‪català‬', 'supported'=>false),
    'zh-CN'=>array('name'=>$languageHandler->getString('template_common_chinese_simplified_han'), 'nameNative'=>'‪中文（简体中文）‬', 'supported'=>true),
    'zh-TW'=>array('name'=>$languageHandler->getString('template_common_chinese_traditional_han'), 'nameNative'=>'‪中文 (繁體中文)‬', 'supported'=>true),
    'hr'=>array('name'=>$languageHandler->getString('template_common_croatian'), 'nameNative'=>'‪hrvatski‬', 'supported'=>false),
    'cs'=>array('name'=>$languageHandler->getString('template_common_czech'), 'nameNative'=>'‪čeština‬', 'supported'=>false),
    'da'=>array('name'=>$languageHandler->getString('template_common_danish'), 'nameNative'=>'‪dansk‬', 'supported'=>false),
    'nl'=>array('name'=>$languageHandler->getString('template_common_dutch'), 'nameNative'=>'‪Nederlands‬', 'supported'=>false),
    'en-GB'=>array('name'=>$languageHandler->getString('template_common_english_united_kingdom'), 'nameNative'=>'‪English (United Kingdom)‬', 'supported'=>false),
    'en'=>array('name'=>$languageHandler->getString('template_common_english_united_states'), 'nameNative'=>'‪English (United States)‬', 'supported'=>true),
    'et'=>array('name'=>$languageHandler->getString('template_common_estonian'), 'nameNative'=>'‪eesti‬', 'supported'=>false),
    'fil'=>array('name'=>$languageHandler->getString('template_common_filipino'), 'nameNative'=>'‪Filipino‬', 'supported'=>false),
    'fi'=>array('name'=>$languageHandler->getString('template_common_finnish'), 'nameNative'=>'‪suomi‬', 'supported'=>false),
    'fr'=>array('name'=>$languageHandler->getString('template_common_french'), 'nameNative'=>'‪français‬', 'supported'=>false),
    'de'=>array('name'=>$languageHandler->getString('template_common_german'), 'nameNative'=>'‪Deutsch‬', 'supported'=>true),
    'el'=>array('name'=>$languageHandler->getString('template_common_greek'), 'nameNative'=>'‪Ελληνικά‬', 'supported'=>false),
    'iw'=>array('name'=>$languageHandler->getString('template_common_hebrew'), 'nameNative'=>'‫עברית‬', 'supported'=>false),
    'hi'=>array('name'=>$languageHandler->getString('template_common_hindi'), 'nameNative'=>'‪हिन्दी‬', 'supported'=>false),
    'hu'=>array('name'=>$languageHandler->getString('template_common_hungarian'), 'nameNative'=>'‪magyar‬', 'supported'=>false),
    'id'=>array('name'=>$languageHandler->getString('template_common_indonesian'), 'nameNative'=>'‪Bahasa Indonesia‬', 'supported'=>false),
    'it'=>array('name'=>$languageHandler->getString('template_common_italian'), 'nameNative'=>'‪italiano‬', 'supported'=>false),
    'ja'=>array('name'=>$languageHandler->getString('template_common_japanese'), 'nameNative'=>'‪日本語‬', 'supported'=>false),
    'ko'=>array('name'=>$languageHandler->getString('template_common_korean'), 'nameNative'=>'‪한국어‬', 'supported'=>false),
    'lv'=>array('name'=>$languageHandler->getString('template_common_latvian'), 'nameNative'=>'‪latviešu‬', 'supported'=>false),
    'lt'=>array('name'=>$languageHandler->getString('template_common_lithuanian'), 'nameNative'=>'‪lietuvių‬', 'supported'=>false),
    'ms'=>array('name'=>$languageHandler->getString('template_common_malay'), 'nameNative'=>'‪Bahasa Melayu‬', 'supported'=>true),
    'no'=>array('name'=>$languageHandler->getString('template_common_norwegian'), 'nameNative'=>'‪norsk‬', 'supported'=>false),
    'fa'=>array('name'=>$languageHandler->getString('template_common_persian'), 'nameNative'=>'‫فارسی‬', 'supported'=>false),
    'pl'=>array('name'=>$languageHandler->getString('template_common_polish'), 'nameNative'=>'‪polski‬', 'supported'=>false),
    'pt-BR'=>array('name'=>$languageHandler->getString('template_common_portuguese_brazil'), 'nameNative'=>'‪português (Brasil)‬', 'supported'=>false),
    'pt-PT'=>array('name'=>$languageHandler->getString('template_common_portuguese_portugal'), 'nameNative'=>'‪português (Portugal)‬', 'supported'=>false),
    'ro'=>array('name'=>$languageHandler->getString('template_common_romanian'), 'nameNative'=>'‪română‬', 'supported'=>true),
    'ru'=>array('name'=>$languageHandler->getString('template_common_russian'), 'nameNative'=>'‪русский‬', 'supported'=>true),
    'sr'=>array('name'=>$languageHandler->getString('template_common_serbian'), 'nameNative'=>'‪Српски‬', 'supported'=>false),
    'sk'=>array('name'=>$languageHandler->getString('template_common_slovak'), 'nameNative'=>'‪slovenčina‬', 'supported'=>false),
    'sl'=>array('name'=>$languageHandler->getString('template_common_slovenian'), 'nameNative'=>'‪slovenščina‬', 'supported'=>false),
    'es-419'=>array('name'=>$languageHandler->getString('template_common_spanish_latin_america'), 'nameNative'=>'‪español (Latinoamérica)‬', 'supported'=>false),
    'es'=>array('name'=>$languageHandler->getString('template_common_spanish_spain'), 'nameNative'=>'‪español (España)‬', 'supported'=>false),
    'sv'=>array('name'=>$languageHandler->getString('template_common_swedish'), 'nameNative'=>'‪svenska‬', 'supported'=>false),
    'th'=>array('name'=>$languageHandler->getString('template_common_thai'), 'nameNative'=>'‪ไทย‬', 'supported'=>false),
    'tr'=>array('name'=>$languageHandler->getString('template_common_turkish'), 'nameNative'=>'‪Türkçe‬', 'supported'=>false),
    'uk'=>array('name'=>$languageHandler->getString('template_common_ukrainian'), 'nameNative'=>'‪українська‬', 'supported'=>false),
    'vi'=>array('name'=>$languageHandler->getString('template_common_vietnamese'), 'nameNative'=>'‪Tiếng Việt‬', 'supported'=>false)
  );
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

function unzipFile($zipFile, $destDir) {
  $zip = new ZipArchive();
  if($zip->open($zipFile) === TRUE) {
    if($zip->extractTo($destDir)) {
      $zip->close();
      return true;
    }
  }
  return false;
}
?>
