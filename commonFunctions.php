<?php
/*
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

/* Guideline for commonFunctions:
 * - as soon as the same method-code is used in more than one class, consider putting the method in here
 * - keep them as short as possible
 * - give them meaningful names
 * - only put them here if they are used in more than one class
 * - or if they provide a set of data (e.g. array)
 * - no interaction with framework (e.g. database, errorHandler, etc.)
 */

function googleAnalyticsGetImageUrl() {
  $url = BASE_PATH;
  $url .= GA_PIXEL . "?";
  $url .= "utmac=" . GA_ACCOUNT;
  $url .= "&utmn=" . rand(0, 0x7fffffff);
  $referer = (isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:'');
  $query = $_SERVER["QUERY_STRING"];
  $path = $_SERVER["REQUEST_URI"];
  if(empty($referer)) {
    $referer = "-";
  }
  $url .= "&utmr=" . urlencode($referer);
  if(!empty($path)) {
    $url .= "&utmp=" . urlencode($path);
  }
  $url .= "&guid=ON";
  return str_replace("&", "&amp;", $url);
}

function impedeCrawling($text) {
  list($usec, $sec) = explode(' ', microtime());
  mt_srand((float) $sec + ((float) $usec * 100000));
  $encodedString = "";
  $chars = str_split(trim($text));

  foreach($chars as $char) {
    switch(mt_rand(0, 2)) {
      case 0:
        $encodedString .= "&#X" . dechex(ord($char)) . ";";
        break;
      case 1:
        $encodedString .= "&#" . ord($char) . ";";
        break;
      case 2:
        $encodedString .= $char;
        break;
    }
  }
  
  return $encodedString;
}

function checkUserInput($text) {
  $text = html_entity_decode($text);
  $text = preg_replace("/&#?[a-z0-9]{2,8}/i", "", $text);
  $text = strip_tags($text);
  $text = htmlspecialchars($text);
  return trim($text);
}

function getUsernameBlacklistArray() {
  $usernameBlacklist = array(
    'admin',
    'catroid',
    'paintroid',
    'administrator',
    'catroweb',
    'kittyroid'
  );
  return $usernameBlacklist;
}

function getPublicServerBlacklistArray() {
  if(DEVELOPMENT_MODE) {
    return array();
  }

  $publicServerBlacklist = array(
    'testuser'
  );
  return $publicServerBlacklist;
}

function isItChristmas() {
  date_default_timezone_set('UTC');
  if(date("n") == 12 && date("j") > 17) {
    return true;
  }
  return false;
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
    "",
    "index",
    "contactus",
    "loadNewestProjects",
    "switchLanguage",
    "termsofuse",
    "error"
  );
  return $whitelistClasses;
}

function getUserBlockClassWhitelistArray() {
  $whitelistClasses = array(
    "terms",
    "contactus",
    "error",
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
  $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENSION_SMALL;
  $thumbFile = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENSION_SMALL;
  if(!is_file($thumbFile)) {
    $thumb = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENSION_SMALL;
  }
  return $thumb;
}

function getProjectImageUrl($projectId) {
  $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENSION_LARGE;
  $imgFile = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENSION_LARGE;
  if(!is_file($imgFile)) {
    $img = BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENSION_LARGE;
  }
  return $img;
}

function getFeaturedProjectImageUrl($projectId) {
  $img = BASE_PATH.PROJECTS_FEATURED_DIRECTORY.$projectId.PROJECTS_FEATURED_EXTENSION;
  $imgFile = CORE_BASE_PATH.PROJECTS_FEATURED_DIRECTORY.$projectId.PROJECTS_FEATURED_EXTENSION;
  if(!is_file($imgFile)) {
    $img = "";
  }
  return $img;
}

function getTimeInWords($fromTime, $languageHandler, $toTime = -1) {
  if($toTime == -1) {
    $toTime = time();
  }
  $seconds = round(abs($toTime - $fromTime));
  $minutes = round($seconds/60);
  if($minutes < 1) {
    return $languageHandler->getString('template_common_less_than_a_minute_ago');
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

function getLanguageOptions($languageHandler, $selectedLanguageCode = '') {
  if($selectedLanguageCode == '') {
    $selectedLanguageCode = $languageHandler->getLanguage();
  }

  $supportedLanguages = getSupportedLanguagesArray($languageHandler);
  $optionList = '';
  $selectedLanguageName = '';
  foreach($supportedLanguages as $lang => $details) {
    if($details['supported']) {
      $selected = '';
      if(strcmp($lang, $selectedLanguageCode) == 0) {
        $selected = ' selected="selected"';
        $selectedLanguageName = $details['name'];
      }
      $optionList .= '<option' . $selected . ' value="' . $lang . '">' . $details['name'] . ' - ' . $details['nameNative'] . '</option>';
    }
  }
  return array('html' => $optionList, 'selected' => $selectedLanguageName);
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
    'ja'=>array('name'=>$languageHandler->getString('template_common_japanese'), 'nameNative'=>'‪日本語‬', 'supported'=>true),
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
        removeDir($dir . "/" . $file);
      }
    }
    rmdir($dir);
  }
  else if(file_exists($dir)) {
    unlink($dir);
  }
}

function chmodDir($dir, $filemode, $dirmode) {
  if(is_dir($dir)) {
    $files = scandir($dir);
    foreach($files as $file) {
      if($file != "." && $file != "..") {
        chmodDir($dir . "/" . $file, $filemode, $dirmode);
      }
    }
    chmod($dir, $dirmode);
  }
  else if(file_exists($dir)) {
    chmod($dir, $filemode);
  }
}

function unzipFile($zipFile, $destDir) {
  $maxFileSize = intval(PROJECTS_MAX_SIZE / 3);
  if(class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    $res = $zip->open($zipFile);
    if($res === TRUE) {
      for($i = 0, $amount = $zip->numFiles; $i < $amount; $i++) {
        $stats = $zip->statIndex($i);
        if(intval($stats['size']) > $maxFileSize) {
          $zip->close();
          return false;
        }
      }
      if($zip->extractTo($destDir)) {
        $zip->close();
        return true;
      }
      $zip->close();
    }
  }
  return false;
}

function getCountryArray($languageHandler) {
  $countries = array(
    'ad'=>$languageHandler->getString('template_common_country_andorra'),
    'ae'=>$languageHandler->getString('template_common_country_united_arab_emirates'),
    'af'=>$languageHandler->getString('template_common_country_afghanistan'),
    'ag'=>$languageHandler->getString('template_common_country_antigua_and_barbuda'),
    'ai'=>$languageHandler->getString('template_common_country_anguilla'),
    'al'=>$languageHandler->getString('template_common_country_albania'),
    'am'=>$languageHandler->getString('template_common_country_armenia'),
    'an'=>$languageHandler->getString('template_common_country_netherlands_antilles'),
    'ao'=>$languageHandler->getString('template_common_country_angola'),
    'aq'=>$languageHandler->getString('template_common_country_antarctica'),
    'ar'=>$languageHandler->getString('template_common_country_argentina'),
    'as'=>$languageHandler->getString('template_common_country_american_samoa'),
    'at'=>$languageHandler->getString('template_common_country_austria'),
    'au'=>$languageHandler->getString('template_common_country_australia'),
    'aw'=>$languageHandler->getString('template_common_country_aruba'),
    'ax'=>$languageHandler->getString('template_common_country_aland_islands'),
    'az'=>$languageHandler->getString('template_common_country_azerbaijan'),
    'ba'=>$languageHandler->getString('template_common_country_bosnia_and_herzegovina'),
    'bb'=>$languageHandler->getString('template_common_country_barbados'),
    'bd'=>$languageHandler->getString('template_common_country_bangladesh'),
    'be'=>$languageHandler->getString('template_common_country_belgium'),
    'bf'=>$languageHandler->getString('template_common_country_burkina_faso'),
    'bg'=>$languageHandler->getString('template_common_country_bulgaria'),
    'bh'=>$languageHandler->getString('template_common_country_bahrain'),
    'bi'=>$languageHandler->getString('template_common_country_burundi'),
    'bj'=>$languageHandler->getString('template_common_country_benin'),
    'bm'=>$languageHandler->getString('template_common_country_bermuda'),
    'bn'=>$languageHandler->getString('template_common_country_brunei'),
    'bo'=>$languageHandler->getString('template_common_country_bolivia'),
    'br'=>$languageHandler->getString('template_common_country_brazil'),
    'bs'=>$languageHandler->getString('template_common_country_bahamas'),
    'bt'=>$languageHandler->getString('template_common_country_bhutan'),
    'bv'=>$languageHandler->getString('template_common_country_bouvet_island'),
    'bw'=>$languageHandler->getString('template_common_country_botswana'),
    'by'=>$languageHandler->getString('template_common_country_belarus'),
    'bz'=>$languageHandler->getString('template_common_country_belize'),
    'ca'=>$languageHandler->getString('template_common_country_canada'),
    'cc'=>$languageHandler->getString('template_common_country_cocos_keeling_islands'),
    'cd'=>$languageHandler->getString('template_common_country_democratic_republic_of_the_congo'),
    'cf'=>$languageHandler->getString('template_common_country_central_african_republic'),
    'cg'=>$languageHandler->getString('template_common_country_congo'),
    'ch'=>$languageHandler->getString('template_common_country_switzerland'),
    'ci'=>$languageHandler->getString('template_common_country_ivory_coast'),
    'ck'=>$languageHandler->getString('template_common_country_cook_islands'),
    'cl'=>$languageHandler->getString('template_common_country_chile'),
    'cm'=>$languageHandler->getString('template_common_country_cameroon'),
    'cn'=>$languageHandler->getString('template_common_country_china'),
    'co'=>$languageHandler->getString('template_common_country_colombia'),
    'cr'=>$languageHandler->getString('template_common_country_costa_rica'),
    'cs'=>$languageHandler->getString('template_common_country_serbia_and_montenegro'),
    'cu'=>$languageHandler->getString('template_common_country_cuba'),
    'cv'=>$languageHandler->getString('template_common_country_cape_verde'),
    'cx'=>$languageHandler->getString('template_common_country_christmas_island'),
    'cy'=>$languageHandler->getString('template_common_country_cyprus'),
    'cz'=>$languageHandler->getString('template_common_country_czech_republic'),
    'de'=>$languageHandler->getString('template_common_country_germany'),
    'dj'=>$languageHandler->getString('template_common_country_djibouti'),
    'dk'=>$languageHandler->getString('template_common_country_denmark'),
    'dm'=>$languageHandler->getString('template_common_country_dominica'),
    'do'=>$languageHandler->getString('template_common_country_dominican_republic'),
    'dz'=>$languageHandler->getString('template_common_country_algeria'),
    'ec'=>$languageHandler->getString('template_common_country_ecuador'),
    'ee'=>$languageHandler->getString('template_common_country_estonia'),
    'eg'=>$languageHandler->getString('template_common_country_egypt'),
    'eh'=>$languageHandler->getString('template_common_country_western_sahara'),
    'er'=>$languageHandler->getString('template_common_country_eritrea'),
    'es'=>$languageHandler->getString('template_common_country_spain'),
    'et'=>$languageHandler->getString('template_common_country_ethiopia'),
    'fi'=>$languageHandler->getString('template_common_country_finland'),
    'fj'=>$languageHandler->getString('template_common_country_fiji'),
    'fk'=>$languageHandler->getString('template_common_country_falkland_islands'),
    'fm'=>$languageHandler->getString('template_common_country_micronesia'),
    'fo'=>$languageHandler->getString('template_common_country_faroe_islands'),
    'fr'=>$languageHandler->getString('template_common_country_france'),
    'ga'=>$languageHandler->getString('template_common_country_gabon'),
    'gb'=>$languageHandler->getString('template_common_country_united_kingdom'),
    'gd'=>$languageHandler->getString('template_common_country_grenada'),
    'ge'=>$languageHandler->getString('template_common_country_georgia'),
    'gf'=>$languageHandler->getString('template_common_country_french_guiana'),
    'gh'=>$languageHandler->getString('template_common_country_ghana'),
    'gi'=>$languageHandler->getString('template_common_country_gibraltar'),
    'gl'=>$languageHandler->getString('template_common_country_greenland'),
    'gm'=>$languageHandler->getString('template_common_country_gambia'),
    'gn'=>$languageHandler->getString('template_common_country_guinea'),
    'gp'=>$languageHandler->getString('template_common_country_guadeloupe'),
    'gq'=>$languageHandler->getString('template_common_country_equatorial_guinea'),
    'gr'=>$languageHandler->getString('template_common_country_greece'),
    'gs'=>$languageHandler->getString('template_common_country_south_georgia_and_the_south_sandwich_islands'),
    'gt'=>$languageHandler->getString('template_common_country_guatemala'),
    'gu'=>$languageHandler->getString('template_common_country_guam'),
    'gw'=>$languageHandler->getString('template_common_country_guinea_bissau'),
    'gy'=>$languageHandler->getString('template_common_country_guyana'),
    'hk'=>$languageHandler->getString('template_common_country_hong_kong'),
    'hm'=>$languageHandler->getString('template_common_country_heard_island_and_mcdonald_islands'),
    'hn'=>$languageHandler->getString('template_common_country_honduras'),
    'hr'=>$languageHandler->getString('template_common_country_croatia'),
    'ht'=>$languageHandler->getString('template_common_country_haiti'),
    'hu'=>$languageHandler->getString('template_common_country_hungary'),
    'id'=>$languageHandler->getString('template_common_country_indonesia'),
    'ie'=>$languageHandler->getString('template_common_country_ireland'),
    'il'=>$languageHandler->getString('template_common_country_israel'),
    'in'=>$languageHandler->getString('template_common_country_india'),
    'io'=>$languageHandler->getString('template_common_country_british_indian_ocean_territory'),
    'iq'=>$languageHandler->getString('template_common_country_iraq'),
    'ir'=>$languageHandler->getString('template_common_country_iran'),
    'is'=>$languageHandler->getString('template_common_country_iceland'),
    'it'=>$languageHandler->getString('template_common_country_italy'),
    'jm'=>$languageHandler->getString('template_common_country_jamaica'),
    'jo'=>$languageHandler->getString('template_common_country_jordan'),
    'jp'=>$languageHandler->getString('template_common_country_japan'),
    'ke'=>$languageHandler->getString('template_common_country_kenya'),
    'kg'=>$languageHandler->getString('template_common_country_kyrgyzstan'),
    'kh'=>$languageHandler->getString('template_common_country_cambodia'),
    'ki'=>$languageHandler->getString('template_common_country_kiribati'),
    'km'=>$languageHandler->getString('template_common_country_comoros'),
    'kn'=>$languageHandler->getString('template_common_country_saint_kitts_and_nevis'),
    'kp'=>$languageHandler->getString('template_common_country_north_korea'),
    'kr'=>$languageHandler->getString('template_common_country_south_korea'),
    'kw'=>$languageHandler->getString('template_common_country_kuwait'),
    'ky'=>$languageHandler->getString('template_common_country_cayman_islands'),
    'kz'=>$languageHandler->getString('template_common_country_kazakhstan'),
    'la'=>$languageHandler->getString('template_common_country_laos'),
    'lb'=>$languageHandler->getString('template_common_country_lebanon'),
    'lc'=>$languageHandler->getString('template_common_country_saint_lucia'),
    'li'=>$languageHandler->getString('template_common_country_liechtenstein'),
    'lk'=>$languageHandler->getString('template_common_country_sri_lanka'),
    'lr'=>$languageHandler->getString('template_common_country_liberia'),
    'ls'=>$languageHandler->getString('template_common_country_lesotho'),
    'lt'=>$languageHandler->getString('template_common_country_lithuania'),
    'lu'=>$languageHandler->getString('template_common_country_luxembourg'),
    'lv'=>$languageHandler->getString('template_common_country_latvia'),
    'ly'=>$languageHandler->getString('template_common_country_libya'),
    'ma'=>$languageHandler->getString('template_common_country_morocco'),
    'mc'=>$languageHandler->getString('template_common_country_monaco'),
    'md'=>$languageHandler->getString('template_common_country_moldova'),
    'mg'=>$languageHandler->getString('template_common_country_madagascar'),
    'mh'=>$languageHandler->getString('template_common_country_marshall_islands'),
    'mk'=>$languageHandler->getString('template_common_country_macedonia'),
    'ml'=>$languageHandler->getString('template_common_country_mali'),
    'mm'=>$languageHandler->getString('template_common_country_burma'),
    'mn'=>$languageHandler->getString('template_common_country_mongolia'),
    'mo'=>$languageHandler->getString('template_common_country_macau'),
    'mp'=>$languageHandler->getString('template_common_country_northern_mariana_islands'),
    'mq'=>$languageHandler->getString('template_common_country_martinique'),
    'mr'=>$languageHandler->getString('template_common_country_mauritania'),
    'ms'=>$languageHandler->getString('template_common_country_montserrat'),
    'mt'=>$languageHandler->getString('template_common_country_malta'),
    'mu'=>$languageHandler->getString('template_common_country_mauritius'),
    'mv'=>$languageHandler->getString('template_common_country_maldives'),
    'mw'=>$languageHandler->getString('template_common_country_malawi'),
    'mx'=>$languageHandler->getString('template_common_country_mexico'),
    'my'=>$languageHandler->getString('template_common_country_malaysia'),
    'mz'=>$languageHandler->getString('template_common_country_mozambique'),
    'na'=>$languageHandler->getString('template_common_country_namibia'),
    'nc'=>$languageHandler->getString('template_common_country_new_caledonia'),
    'ne'=>$languageHandler->getString('template_common_country_niger'),
    'nf'=>$languageHandler->getString('template_common_country_norfolk_island'),
    'ng'=>$languageHandler->getString('template_common_country_nigeria'),
    'ni'=>$languageHandler->getString('template_common_country_nicaragua'),
    'nl'=>$languageHandler->getString('template_common_country_netherlands'),
    'no'=>$languageHandler->getString('template_common_country_norway'),
    'np'=>$languageHandler->getString('template_common_country_nepal'),
    'nr'=>$languageHandler->getString('template_common_country_nauru'),
    'nu'=>$languageHandler->getString('template_common_country_niue'),
    'nz'=>$languageHandler->getString('template_common_country_new_zealand'),
    'om'=>$languageHandler->getString('template_common_country_oman'),
    'pa'=>$languageHandler->getString('template_common_country_panama'),
    'pe'=>$languageHandler->getString('template_common_country_peru'),
    'pf'=>$languageHandler->getString('template_common_country_french_polynesia'),
    'pg'=>$languageHandler->getString('template_common_country_papua_new_guinea'),
    'ph'=>$languageHandler->getString('template_common_country_philippines'),
    'pk'=>$languageHandler->getString('template_common_country_pakistan'),
    'pl'=>$languageHandler->getString('template_common_country_poland'),
    'pm'=>$languageHandler->getString('template_common_country_saint_pierre_and_miquelon'),
    'pn'=>$languageHandler->getString('template_common_country_pitcairn_islands'),
    'pr'=>$languageHandler->getString('template_common_country_puerto_rico'),
    'ps'=>$languageHandler->getString('template_common_country_palestinian_territories'),
    'pt'=>$languageHandler->getString('template_common_country_portugal'),
    'pw'=>$languageHandler->getString('template_common_country_palau'),
    'py'=>$languageHandler->getString('template_common_country_paraguay'),
    'qa'=>$languageHandler->getString('template_common_country_qatar'),
    're'=>$languageHandler->getString('template_common_country_reunion'),
    'ro'=>$languageHandler->getString('template_common_country_romania'),
    'ru'=>$languageHandler->getString('template_common_country_russia'),
    'rw'=>$languageHandler->getString('template_common_country_rwanda'),
    'sa'=>$languageHandler->getString('template_common_country_saudi_arabia'),
    'sb'=>$languageHandler->getString('template_common_country_solomon_islands'),
    'sc'=>$languageHandler->getString('template_common_country_seychelles'),
    'sd'=>$languageHandler->getString('template_common_country_sudan'),
    'se'=>$languageHandler->getString('template_common_country_sweden'),
    'sg'=>$languageHandler->getString('template_common_country_singapore'),
    'sh'=>$languageHandler->getString('template_common_country_saint_helena'),
    'si'=>$languageHandler->getString('template_common_country_slovenia'),
    'sj'=>$languageHandler->getString('template_common_country_svalbard_and_jan_mayen'),
    'sk'=>$languageHandler->getString('template_common_country_slovakia'),
    'sl'=>$languageHandler->getString('template_common_country_sierra_leone'),
    'sm'=>$languageHandler->getString('template_common_country_san_marino'),
    'sn'=>$languageHandler->getString('template_common_country_senegal'),
    'so'=>$languageHandler->getString('template_common_country_somalia'),
    'sr'=>$languageHandler->getString('template_common_country_suriname'),
    'st'=>$languageHandler->getString('template_common_country_sao_tome_and_principe'),
    'sv'=>$languageHandler->getString('template_common_country_el_salvador'),
    'sy'=>$languageHandler->getString('template_common_country_syria'),
    'sz'=>$languageHandler->getString('template_common_country_swaziland'),
    'tc'=>$languageHandler->getString('template_common_country_turks_and_caicos_islands'),
    'td'=>$languageHandler->getString('template_common_country_chad'),
    'tf'=>$languageHandler->getString('template_common_country_french_southern_and_antarctic_lands'),
    'tg'=>$languageHandler->getString('template_common_country_togo'),
    'th'=>$languageHandler->getString('template_common_country_thailand'),
    'tj'=>$languageHandler->getString('template_common_country_tajikistan'),
    'tk'=>$languageHandler->getString('template_common_country_tokelau'),
    'tl'=>$languageHandler->getString('template_common_country_east_timor'),
    'tm'=>$languageHandler->getString('template_common_country_turkmenistan'),
    'tn'=>$languageHandler->getString('template_common_country_tunisia'),
    'to'=>$languageHandler->getString('template_common_country_tonga'),
    'tr'=>$languageHandler->getString('template_common_country_turkey'),
    'tt'=>$languageHandler->getString('template_common_country_trinidad_and_tobago'),
    'tv'=>$languageHandler->getString('template_common_country_tuvalu'),
    'tw'=>$languageHandler->getString('template_common_country_taiwan'),
    'tz'=>$languageHandler->getString('template_common_country_tanzania'),
    'ua'=>$languageHandler->getString('template_common_country_ukraine'),
    'ug'=>$languageHandler->getString('template_common_country_uganda'),
    'um'=>$languageHandler->getString('template_common_country_united_states_minor_outlying_islands'),
    'us'=>$languageHandler->getString('template_common_country_united_states'),
    'uy'=>$languageHandler->getString('template_common_country_uruguay'),
    'uz'=>$languageHandler->getString('template_common_country_uzbekistan'),
    'va'=>$languageHandler->getString('template_common_country_vatican_city_state'),
    'vc'=>$languageHandler->getString('template_common_country_saint_vincent_and_the_grenadines'),
    've'=>$languageHandler->getString('template_common_country_venezuela'),
    'vg'=>$languageHandler->getString('template_common_country_british_virgin_islands'),
    'vi'=>$languageHandler->getString('template_common_country_united_states_virgin_islands'),
    'vn'=>$languageHandler->getString('template_common_country_vietnam'),
    'vu'=>$languageHandler->getString('template_common_country_vanuatu'),
    'wf'=>$languageHandler->getString('template_common_country_wallis_and_futuna'),
    'ws'=>$languageHandler->getString('template_common_country_samoa'),
    'ye'=>$languageHandler->getString('template_common_country_yemen'),
    'yt'=>$languageHandler->getString('template_common_country_mayotte'),
    'za'=>$languageHandler->getString('template_common_country_south_africa'),
    'zm'=>$languageHandler->getString('template_common_country_zambia'),
    'zw'=>$languageHandler->getString('template_common_country_zimbabwe'),
  );
  return $countries;
}


function addhttp($url) {
  if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "http://" . $url;
  }
  return $url;
}

function wrapUrlsWithAnchors($description){
  
  // http://daringfireball.net/2010/07/improved_regex_for_matching_urls
  
  /*
   (?xi)
\b
(                       # Capture 1: entire matched URL
  (?:
    https?://               # http or https protocol
    |                       #   or
    www\d{0,3}[.]           # "www.", "www1.", "www2." … "www999."
    |                           #   or
    [a-z0-9.\-]+[.][a-z]{2,4}/  # looks like domain name followed by a slash
  )
  (?:                       # One or more:
    [^\s()<>]+                  # Run of non-space, non-()<>
    |                           #   or
    \(([^\s()<>]+|(\([^\s()<>]+\)))*\)  # balanced parens, up to 2 levels
  )+
  (?:                       # End with:
    \(([^\s()<>]+|(\([^\s()<>]+\)))*\)  # balanced parens, up to 2 levels
    |                               #   or
    [^\s`!()\[\]{};:'".,<>?«»“”‘’]        # not a space or one of these punct chars
  )
)
 */
  $pattern = '#\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\"\.,<>\?«»“”‘’]))#ie';

  $replacement = "'<a href=\"'.addhttp('\\0').'\" target=\"_blank\">\\0</a>'";
  
  return preg_replace($pattern, $replacement, $description);  
}
?>
