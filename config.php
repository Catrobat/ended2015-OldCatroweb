<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('VERSION','0.6.1');
define('BASE_PATH','http://'.str_replace('//', '/', $_SERVER['SERVER_NAME'].str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']).'/')));
define('CORE_BASE_PATH',dirname(__FILE__).'/');
define('XML_PATH','include/xml/');
define('CSS_PATH','include/css/');
define('SCRIPT_PATH','include/script/');
define('PROJECTS_DIRECTORY','resources/projects/');
define('PROJECTS_QR_DIRECTORY','resources/qrcodes/');
define('PROJECTS_QR_EXTENTION','_qr.png');
define('PROJECTS_THUMBNAIL_DIRECTORY','resources/thumbnails/');
define('PROJECTS_THUMBNAIL_DEFAULT','thumbnail');
define('PROJECTS_THUMBNAIL_EXTENTION_SMALL','_small.jpg');
define('PROJECTS_THUMBNAIL_EXTENTION_LARGE','_large.jpg');
define('PROJECTS_EXTENTION','.zip');
define('PROJECTS_MAX_SIZE',104857600);
define('PROJECT_TITLE_MAX_DISPLAY_LENGTH',20);
define('PROJECT_SHORT_DESCRIPTION_MAX_LENGTH',178);
define('PROJECT_PAGE_MAX_PROJECTS', 5);
define('PROJECT_ROW_MAX_PROJECTS', 3);
define('PROJECT_FLAG_NOTIFICATION_THRESHOLD', 1);
define('DEVELOPMENT_MODE',true);
define('SEND_NOTIFICATION_EMAIL',false);
define('DEVELOPMENT_STATUS','[beta]');
define('DEFAULT_HTML_TEMPLATE_NAME', 'htmlTemplate.php');
define('DEFAULT_HTML_HEADER_TEMPLATE_NAME', 'htmlHeaderTemplate.php');
define('DEFAULT_HTML_FOOTER_TEMPLATE_NAME', 'htmlFooterTemplate.php');
define('MVC_DEFAULT_MODULE', 'catroid');
define('MVC_DEFAULT_CLASS', 'index');
define('MVC_DEFAULT_METHOD', '__default');
define('MVC_DEFAULT_VIEW', 'html');
define('ADMIN_EMAIL_WEBMASTER','webmaster@catroid.org');
define('ADMIN_EMAIL_NOREPLY','noreply@catroid.org');
define('ADMIN_EMAIL_SUBJECT_PREFIX','CATROID.ORG');
define('USER_STATUS_STRING_ACTIVE','active');
define('USER_STATUS_STRING_INACTIVE','inactive');
define('USER_STATUS_STRING_DELETED','deleted');
define('USER_STATUS_STRING_WAITFORCONFIRMATION','wait_for_confirmation');
define('USER_MIN_USERNAME_LENGTH',4);
define('USER_MAX_USERNAME_LENGTH',32);
define('USER_MIN_PASSWORD_LENGTH',6);
define('USER_MAX_PASSWORD_LENGTH',32);
?>
