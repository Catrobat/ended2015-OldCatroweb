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

require_once('frameworkTestsBootstrap.php');

class configTest extends PHPUnit_Framework_TestCase
{

  public function testConfig()
  {
  	$this->assertEquals(VERSION, '0.7.0');
  	$this->assertEquals(MIN_CATROBAT_VERSION, '0.7.3');
  	$this->assertEquals(MIN_CATROBAT_LANGUAGE_VERSION, '0.8');
    $this->assertEquals(APPLICATION_NAME, 'Pocket Code');
    $this->assertEquals(APPLICATION_URL_TEXT, 'PocketCode.org');
    $this->assertEquals(XML_PATH, 'include/xml/');
    $this->assertEquals(LANGUAGE_PATH, 'include/xml/lang/');
    $this->assertEquals(CSS_PATH, 'include/css/');
    $this->assertEquals(SCRIPT_PATH, 'include/script/');
    $this->assertEquals(CLASS_PATH, 'classes/');
    $this->assertEquals(MODULE_PATH, 'modules/');
    $this->assertEquals(VIEWER_PATH, 'viewer/');
    $this->assertEquals(CACHE_PATH, 'cache/');
    $this->assertEquals(PROJECTS_FTP_UPLOAD_DIRECTORY, '/tmp/');
    $this->assertEquals(PROJECTS_APP_BUILDING_SRC, 'app-building/catroid-source/');
    $this->assertEquals(PROJECTS_DIRECTORY, 'resources/projects/');
    $this->assertEquals(PROJECTS_UNZIPPED_DIRECTORY, 'resources/catroid/');
    $this->assertEquals(PROJECTS_QR_SERVICE_URL, 'http://catroid.local/api/qrCodeGenerator/generate.png?url=');
    $this->assertEquals(PROJECTS_FEATURED_DIRECTORY,'resources/featured/');
    $this->assertEquals(PROJECTS_FEATURED_EXTENSION,'.gif');
    $this->assertEquals(PROJECTS_THUMBNAIL_DIRECTORY, 'resources/thumbnails/');
    $this->assertEquals(PROJECTS_THUMBNAIL_DEFAULT, 'thumbnail');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENSION_ORIG, '_original.png');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENSION_SMALL, '_small.png');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENSION_LARGE, '_large.png');
    $this->assertEquals(PROJECTS_EXTENSION, '.catrobat');
    $this->assertEquals(PROJECTS_MAX_SIZE, 104857600);
    $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, 20);
    $this->assertEquals(PROJECT_SHORT_DESCRIPTION_MAX_LENGTH, 178);
    $this->assertEquals(PROJECT_PAGE_LOAD_MAX_PROJECTS, 5);
    $this->assertEquals(PROJECT_PAGE_SHOW_MAX_PAGES, 5);
    $this->assertEquals(PROJECT_ROW_MAX_PROJECTS, 3);
    $this->assertEquals(PROJECT_FLAG_NOTIFICATION_THRESHOLD, 1);
    $this->assertEquals(PROJECT_LAYOUT_GRID_ROW, 1);
    
    $this->assertEquals(PROJECT_MASK_DEFAULT, 'min');
    $this->assertEquals(PROJECT_MASK_GRID_ROW_AGE, 'listAge');
    $this->assertEquals(PROJECT_MASK_GRID_ROW_DOWNLOADS, 'listDownloads');
    $this->assertEquals(PROJECT_MASK_GRID_ROW_VIEWS, 'listViews');
    $this->assertEquals(PROJECT_MASK_FEATURED, 'featured');
    $this->assertEquals(PROJECT_MASK_ALL, 'all');
    
    $this->assertEquals(PROJECT_SORTBY_AGE, 'age');
    $this->assertEquals(PROJECT_SORTBY_DOWNLOADS, 'downloads');
    $this->assertEquals(PROJECT_SORTBY_VIEWS, 'views');
    $this->assertEquals(PROJECT_SORTBY_RANDOM, 'random');
    $this->assertEquals(PROJECT_SORTBY_DEFAULT, PROJECT_SORTBY_AGE);
    
    $this->assertEquals(PROJECT_MEDIA_LICENSE, 'http://developer.catrobat.org/ccbysa_v3');
    $this->assertEquals(PROJECT_PROGRAM_LICENSE, 'http://developer.catrobat.org/agpl_v3');
    
    $this->assertEquals(APP_EXTENSION,'.apk');
    
    $this->assertEquals(defined('DEVELOPMENT_MODE'), true);
    if (DEVELOPMENT_MODE) {
      $this->assertEquals(SEND_NOTIFICATION_EMAIL,false);
      $this->assertEquals(SEND_NOTIFICATION_USER_EMAIL,false);
      $this->assertEquals(DATABASE_CONNECTION_PERSISTENT,false);
      $this->assertEquals(UPDATE_AUTH_TOKEN,false);
    } else {
      $this->assertEquals(SEND_NOTIFICATION_EMAIL,true);
      $this->assertEquals(SEND_NOTIFICATION_USER_EMAIL,true);
      $this->assertEquals(DATABASE_CONNECTION_PERSISTENT,true);
      $this->assertEquals(UPDATE_AUTH_TOKEN,true);
    }
    
    $this->assertEquals(DEVELOPMENT_STATUS, '[beta]');
    $this->assertEquals(DEFAULT_HTML_TEMPLATE_NAME, 'htmlTemplate.php');
    $this->assertEquals(DEFAULT_HTML_HEADER_TEMPLATE_NAME, 'htmlHeaderTemplate.php');
    $this->assertEquals(DEFAULT_HTML_FOOTER_TEMPLATE_NAME, 'htmlFooterTemplate.php');
    $this->assertEquals(DEFAULT_DEV_ERRORS_FILE, 'errors_dev.xml');
    $this->assertEquals(DEFAULT_PUB_ERRORS_FILE, 'errors_pub.xml');
    $this->assertEquals(DEFAULT_TEMPLATE_LANGUAGE_FILE, 'template.xml');
    $this->assertEquals(SITE_DEFAULT_LANGUAGE, 'en');
    $this->assertEquals(SITE_DEFAULT_TITLE, APPLICATION_NAME.' Website');
    $this->assertEquals(MVC_DEFAULT_MODULE, 'catroid');
    $this->assertEquals(MVC_DEFAULT_CLASS, 'index');
    $this->assertEquals(MVC_DEFAULT_METHOD, '__default');
    $this->assertEquals(MVC_DEFAULT_AUTH_FAILED_METHOD, '__authenticationFailed');
    $this->assertEquals(MVC_DEFAULT_VIEW, 'html');
    $this->assertEquals(USER_EMAIL_NOREPLY, 'noreply@pocketcode.org');
    $this->assertEquals(USER_EMAIL_SUBJECT_PREFIX, 'POCKETCODE.ORG');
    $this->assertEquals(ADMIN_EMAIL_WEBMASTER, 'webmaster@pocketcode.org');
    $this->assertEquals(ADMIN_EMAIL_NOREPLY, 'noreply@pocketcode.org');
    $this->assertEquals(ADMIN_EMAIL_SUBJECT_PREFIX, 'POCKETCODE.ORG');
    $this->assertEquals(ADMIN_POOTLE_ROOT_URL, 'http://translate.catroid.org/');
    $this->assertEquals(CONTACT_EMAIL, 'webmaster@catrobat.org');
    $this->assertEquals(USER_STATUS_STRING_ACTIVE, 'active');
    $this->assertEquals(USER_STATUS_STRING_INACTIVE, 'inactive');
    $this->assertEquals(USER_STATUS_STRING_DELETED, 'deleted');
    $this->assertEquals(USER_STATUS_STRING_WAITFORCONFIRMATION, 'wait_for_confirmation');
    $this->assertEquals(USER_MIN_USERNAME_LENGTH, 4);
    $this->assertEquals(USER_MAX_USERNAME_LENGTH, 32);
    $this->assertEquals(USER_MIN_PASSWORD_LENGTH, 6);
    $this->assertEquals(USER_MAX_PASSWORD_LENGTH, 32);
    $this->assertEquals(DATABASE_CONNECTION_PERSISTENT, false);
    $this->assertEquals(UPDATE_AUTH_TOKEN, false);
    $this->assertEquals(GA_PIXEL, 'ga.php');
    $this->assertEquals(SESSION_LIFETIME, 60*60*24*365);
    $this->assertEquals(JQUERY_VERSION, '2.0.0');
    $this->assertEquals(MOBILE_BROWSERDETECTION_URL_FOR_UPDATE, 'http://detectmobilebrowsers.com/download/php');
  }
}
?>
