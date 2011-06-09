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

require_once('frameworkTestsBootstrap.php');

class configTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  public function testConfig()
  {
    $this->assertEquals(VERSION, '0.6.2');
    $this->assertEquals(XML_PATH, 'include/xml/');
    $this->assertEquals(CSS_PATH, 'include/css/');
    $this->assertEquals(SCRIPT_PATH, 'include/script/');
    $this->assertEquals(PROJECTS_DIRECTORY, 'resources/projects/');
    $this->assertEquals(PROJECTS_UNZIPPED_DIRECTORY, 'resources/catroid/');
    $this->assertEquals(PROJECTS_QR_DIRECTORY, 'resources/qrcodes/');
    $this->assertEquals(PROJECTS_QR_EXTENTION, '_qr.png');
    $this->assertEquals(PROJECTS_QR_SERVICE_URL, 'http://qrcode.kaywa.com/img.php?s=5&d=');
    $this->assertEquals(PROJECTS_THUMBNAIL_DIRECTORY, 'resources/thumbnails/');
    $this->assertEquals(PROJECTS_THUMBNAIL_DEFAULT, 'thumbnail');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENTION_ORIG, '_original.jpg');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENTION_SMALL, '_small.jpg');
    $this->assertEquals(PROJECTS_THUMBNAIL_EXTENTION_LARGE, '_large.jpg');
    $this->assertEquals(PROJECTS_EXTENTION, '.zip');
    $this->assertEquals(PROJECTS_MAX_SIZE, 104857600);
    $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, 20);
    $this->assertEquals(PROJECT_SHORT_DESCRIPTION_MAX_LENGTH, 178);
    $this->assertEquals(PROJECT_PAGE_LOAD_MAX_PROJECTS, 5);
    $this->assertEquals(PROJECT_PAGE_SHOW_MAX_PAGES, 5);
    $this->assertEquals(PROJECT_ROW_MAX_PROJECTS, 3);
    $this->assertEquals(PROJECT_FLAG_NOTIFICATION_THRESHOLD, 1);
    $this->assertEquals(PROJECT_DEFAULT_SAVEFILE_NAME, 'defaultProject');
    $this->assertEquals(DEVELOPMENT_STATUS, '[beta]');
    $this->assertEquals(DEFAULT_HTML_TEMPLATE_NAME, 'htmlTemplate.php');
    $this->assertEquals(DEFAULT_HTML_HEADER_TEMPLATE_NAME, 'htmlHeaderTemplate.php');
    $this->assertEquals(DEFAULT_HTML_FOOTER_TEMPLATE_NAME, 'htmlFooterTemplate.php');
    $this->assertEquals(MVC_DEFAULT_MODULE, 'catroid');
    $this->assertEquals(MVC_DEFAULT_CLASS, 'index');
    $this->assertEquals(MVC_DEFAULT_METHOD, '__default');
    $this->assertEquals(MVC_DEFAULT_AUTH_FAILED_METHOD, '__authenticationFailed');
    $this->assertEquals(MVC_DEFAULT_VIEW, 'html');
    $this->assertEquals(USER_EMAIL_NOREPLY, 'noreply@catroid.org');
    $this->assertEquals(USER_EMAIL_SUBJECT_PREFIX, 'CATROID.ORG');
    $this->assertEquals(ADMIN_EMAIL_WEBMASTER, 'webmaster@catroid.org');
    $this->assertEquals(ADMIN_EMAIL_NOREPLY, 'noreply@catroid.org');
    $this->assertEquals(ADMIN_EMAIL_SUBJECT_PREFIX, 'CATROID.ORG');
    $this->assertEquals(USER_STATUS_STRING_ACTIVE, 'active');
    $this->assertEquals(USER_STATUS_STRING_INACTIVE, 'inactive');
    $this->assertEquals(USER_STATUS_STRING_DELETED, 'deleted');
    $this->assertEquals(USER_STATUS_STRING_WAITFORCONFIRMATION, 'wait_for_confirmation');
    $this->assertEquals(USER_MIN_USERNAME_LENGTH, 4);
    $this->assertEquals(USER_MAX_USERNAME_LENGTH, 32);
    $this->assertEquals(USER_MIN_PASSWORD_LENGTH, 6);
    $this->assertEquals(USER_MAX_PASSWORD_LENGTH, 32);
    $this->assertEquals(DATABASE_CONNECTION_PERSISTENT, true);
    
  }
}
?>
