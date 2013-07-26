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

define('VERSION','0.7.0');
define('MIN_CATROBAT_VERSION', '0.7.3beta');
define('MIN_CATROBAT_LANGUAGE_VERSION', '0.8');
define('APPLICATION_NAME', 'Pocket Code');
define('APPLICATION_URL_TEXT', 'PocketCode.org');
// define('BASE_PATH',((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.str_replace('//', '/', $_SERVER['SERVER_NAME'].str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']).'/')));
define('BASE_PATH',((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.str_replace('//', '/', $_SERVER['SERVER_NAME'].'/'));
define('CORE_BASE_PATH',dirname(__FILE__).'/');
define('XML_PATH','include/xml/');
define('LANGUAGE_PATH','include/xml/lang/');
define('CSS_PATH','include/css/');
define('SCRIPT_PATH','include/script/');
define('CLASS_PATH','classes/');
define('MODULE_PATH','modules/');
define('VIEWER_PATH','viewer/');
define('CACHE_PATH','cache/');
define('PROJECTS_FTP_UPLOAD_DIRECTORY','/tmp/');
define('PROJECTS_APP_BUILDING_SRC','app-building/catroid-source/');
define('PROJECTS_DIRECTORY','resources/projects/');
define('PROJECTS_UNZIPPED_DIRECTORY','resources/catroid/');
define('PROJECTS_QR_SERVICE_URL', BASE_PATH . 'api/qrCodeGenerator/generate.png?url=');
define('PROJECTS_FEATURED_DIRECTORY','resources/featured/');
define('PROJECTS_FEATURED_EXTENSION','.gif');
define('PROJECTS_THUMBNAIL_DIRECTORY','resources/thumbnails/');
define('PROJECTS_THUMBNAIL_DEFAULT','thumbnail');
define('PROJECTS_THUMBNAIL_EXTENSION_ORIG','_original.png');
define('PROJECTS_THUMBNAIL_EXTENSION_SMALL','_small.png');
define('PROJECTS_THUMBNAIL_EXTENSION_LARGE','_large.png');
define('PROJECTS_EXTENSION','.catrobat');
define('PROJECTS_MAX_SIZE',104857600);
define('PROJECT_TITLE_MAX_DISPLAY_LENGTH',20);
define('PROJECT_SHORT_DESCRIPTION_MAX_LENGTH',178);
define('PROJECT_PAGE_LOAD_MAX_PROJECTS', 5);
define('PROJECT_PAGE_SHOW_MAX_PAGES', 5);
define('PROJECT_ROW_MAX_PROJECTS', 3);
define('PROJECT_FLAG_NOTIFICATION_THRESHOLD', 1);
define('PROJECT_URL_TEXT', 'http://pocketcode.org/details/');

define('PROJECT_LAYOUT_GRID_ROW', 1);

define('PROJECT_MASK_DEFAULT', 'min');
define('PROJECT_MASK_GRID_ROW_AGE', 'listAge');
define('PROJECT_MASK_GRID_ROW_DOWNLOADS', 'listDownloads');
define('PROJECT_MASK_GRID_ROW_VIEWS', 'listViews');
define('PROJECT_MASK_FEATURED', 'featured');
define('PROJECT_MASK_ALL', 'all');

define('PROJECT_SORTBY_AGE', 'age');
define('PROJECT_SORTBY_DOWNLOADS', 'downloads');
define('PROJECT_SORTBY_VIEWS', 'views');
define('PROJECT_SORTBY_RANDOM', 'random');
define('PROJECT_SORTBY_DEFAULT', PROJECT_SORTBY_AGE);

define('PROJECT_MEDIA_LICENSE', 'http://developer.catrobat.org/ccbysa_v3');
define('PROJECT_PROGRAM_LICENSE', 'http://developer.catrobat.org/agpl_v3');

define('APP_EXTENSION','.apk');

define('DEVELOPMENT_MODE',true);
if (DEVELOPMENT_MODE) {
	define('SEND_NOTIFICATION_EMAIL',false);
	define('SEND_NOTIFICATION_USER_EMAIL',false);
	define('DATABASE_CONNECTION_PERSISTENT',false);
  define('UPDATE_AUTH_TOKEN',false);
} else {
	define('SEND_NOTIFICATION_EMAIL',true);
	define('SEND_NOTIFICATION_USER_EMAIL',true);
	define('DATABASE_CONNECTION_PERSISTENT',true);
  define('UPDATE_AUTH_TOKEN',true);
}
	
define('DEVELOPMENT_STATUS','[beta]');
define('DEFAULT_HTML_TEMPLATE_NAME', 'htmlTemplate.php');
define('DEFAULT_HTML_HEADER_TEMPLATE_NAME', 'htmlHeaderTemplate.php');
define('DEFAULT_HTML_FOOTER_TEMPLATE_NAME', 'htmlFooterTemplate.php');
define('DEFAULT_DEV_ERRORS_FILE', 'errors_dev.xml');
define('DEFAULT_PUB_ERRORS_FILE', 'errors_pub.xml');
define('DEFAULT_TEMPLATE_LANGUAGE_FILE', 'template.xml');
define('SITE_DEFAULT_LANGUAGE', 'en');
define('SITE_DEFAULT_TITLE', APPLICATION_NAME.' Website');
define('MVC_DEFAULT_MODULE', 'catroid');
define('MVC_DEFAULT_CLASS', 'index');
define('MVC_DEFAULT_METHOD', '__default');
define('MVC_DEFAULT_AUTH_FAILED_METHOD', '__authenticationFailed');
define('MVC_DEFAULT_VIEW', 'html');
define('USER_EMAIL_NOREPLY','noreply@pocketcode.org');
define('USER_EMAIL_SUBJECT_PREFIX','POCKETCODE.ORG');
define('ADMIN_EMAIL_WEBMASTER','webmaster@pocketcode.org');
define('ADMIN_EMAIL_NOREPLY','noreply@pocketcode.org');
define('ADMIN_EMAIL_SUBJECT_PREFIX','POCKETCODE.ORG');
define('ADMIN_POOTLE_ROOT_URL','http://translate.catroid.org/');
define('CONTACT_EMAIL','webmaster@catrobat.org');
define('USER_STATUS_STRING_ACTIVE','active');
define('USER_STATUS_STRING_INACTIVE','inactive');
define('USER_STATUS_STRING_DELETED','deleted');
define('USER_STATUS_STRING_WAITFORCONFIRMATION','wait_for_confirmation');
define('USER_MIN_USERNAME_LENGTH',4);
define('USER_MAX_USERNAME_LENGTH',32);
define('USER_MIN_PASSWORD_LENGTH',6);
define('USER_MAX_PASSWORD_LENGTH',32);
define('USER_HASH_ITERATIONS',11);
define('GA_PIXEL','ga.php');
define('SESSION_LIFETIME', 60*60*24*365);
define('JQUERY_VERSION', '2.0.0');
define('MOBILE_BROWSERDETECTION_URL_FOR_UPDATE', 'http://detectmobilebrowsers.com/download/php');

?>
