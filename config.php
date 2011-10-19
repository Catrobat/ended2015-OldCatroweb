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

define('VERSION','0.6.2_I18N');
define('BASE_PATH',((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.str_replace('//', '/', $_SERVER['SERVER_NAME'].str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']).'/')));
define('CORE_BASE_PATH',dirname(__FILE__).'/');
define('XML_PATH','include/xml/');
define('LANGUAGE_PATH','include/xml/lang/');
define('CSS_PATH','include/css/');
define('SCRIPT_PATH','include/script/');
define('CLASS_PATH','classes/');
define('MODULE_PATH','modules/');
define('VIEWER_PATH','viewer/');
define('PROJECTS_APP_BUILDING_SRC','app-building/catroid-source/');
define('PROJECTS_DIRECTORY','resources/projects/');
define('PROJECTS_UNZIPPED_DIRECTORY','resources/catroid/');
define('PROJECTS_QR_DIRECTORY','resources/qrcodes/');
define('PROJECTS_QR_EXTENSION','_qr.png');
// define('PROJECTS_QR_SERVICE_URL','http://catroidwebtest.ist.tugraz.at/api/qrCodeGenerator/generate.png?url=');
define('PROJECTS_QR_SERVICE_URL','http://localhost/catroweb/api/qrCodeGenerator/generate.png?url=');
define('PROJECTS_THUMBNAIL_DIRECTORY','resources/thumbnails/');
define('PROJECTS_THUMBNAIL_DEFAULT','thumbnail');
define('PROJECTS_THUMBNAIL_EXTENSION_ORIG','_original.png');
define('PROJECTS_THUMBNAIL_EXTENSION_SMALL','_small.png');
define('PROJECTS_THUMBNAIL_EXTENSION_LARGE','_large.png');
define('PROJECTS_EXTENSION','.catroid');
define('PROJECTS_MAX_SIZE',104857600);
define('PROJECT_TITLE_MAX_DISPLAY_LENGTH',20);
define('PROJECT_SHORT_DESCRIPTION_MAX_LENGTH',178);
define('PROJECT_PAGE_LOAD_MAX_PROJECTS', 5);
define('PROJECT_PAGE_SHOW_MAX_PAGES', 5);
define('PROJECT_ROW_MAX_PROJECTS', 3);
define('PROJECT_FLAG_NOTIFICATION_THRESHOLD', 1);
define('APP_EXTENSION','.apk');
define('APP_QR_EXTENSION','_app_qr.png');
define('DEVELOPMENT_MODE',true);
define('SEND_NOTIFICATION_EMAIL',false);
define('SEND_NOTIFICATION_USER_EMAIL',false);
define('DEVELOPMENT_STATUS','[beta]');
define('DEFAULT_HTML_TEMPLATE_NAME', 'htmlTemplate.php');
define('DEFAULT_HTML_HEADER_TEMPLATE_NAME', 'htmlHeaderTemplate.php');
define('DEFAULT_HTML_FOOTER_TEMPLATE_NAME', 'htmlFooterTemplate.php');
define('DEFAULT_DEV_ERRORS_FILE', 'errors_dev.xml');
define('DEFAULT_PUB_ERRORS_FILE', 'errors_pub.xml');
define('DEFAULT_TEMPLATE_LANGUAGE_FILE', 'template.xml');
define('SITE_DEFAULT_LANGUAGE', 'en');
define('SITE_DEFAULT_TITLE', 'Catroid Website');
define('MVC_DEFAULT_MODULE', 'catroid');
define('MVC_DEFAULT_CLASS', 'index');
define('MVC_DEFAULT_METHOD', '__default');
define('MVC_DEFAULT_AUTH_FAILED_METHOD', '__authenticationFailed');
define('MVC_DEFAULT_VIEW', 'html');
define('USER_EMAIL_NOREPLY','noreply@catroid.org');
define('USER_EMAIL_SUBJECT_PREFIX','CATROID.ORG');
define('ADMIN_EMAIL_WEBMASTER','webmaster@catroid.org');
define('ADMIN_EMAIL_NOREPLY','noreply@catroid.org');
define('ADMIN_EMAIL_SUBJECT_PREFIX','CATROID.ORG');
define('ADMIN_POOTLE_ROOT_URL','http://catroidwebtest.ist.tugraz.at:8080/');
define('CONTACT_EMAIL','webmaster@catroid.org');
define('USER_STATUS_STRING_ACTIVE','active');
define('USER_STATUS_STRING_INACTIVE','inactive');
define('USER_STATUS_STRING_DELETED','deleted');
define('USER_STATUS_STRING_WAITFORCONFIRMATION','wait_for_confirmation');
define('USER_MIN_USERNAME_LENGTH',4);
define('USER_MAX_USERNAME_LENGTH',32);
define('USER_MIN_PASSWORD_LENGTH',6);
define('USER_MAX_PASSWORD_LENGTH',32);
define('DATABASE_CONNECTION_PERSISTENT',true);
?>
