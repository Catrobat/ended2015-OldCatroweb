<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or License, or (at your option) any later version.
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
    $this->assertEquals('include/xml/', XML_PATH);
    $this->assertEquals('resources/projects/', PROJECTS_DIRECTORY);
    $this->assertEquals('resources/thumbnails/', PROJECTS_THUMBNAIL_DIRECTORY);
    $this->assertEquals('thumbnail', PROJECTS_THUMBNAIL_DEFAULT);
    $this->assertEquals('_small.jpg', PROJECTS_THUMBNAIL_EXTENTION_SMALL);
    $this->assertEquals('_large.jpg', PROJECTS_THUMBNAIL_EXTENTION_LARGE);
    $this->assertEquals('.zip', PROJECTS_EXTENTION);
    $this->assertEquals(104857600, PROJECTS_MAX_SIZE);
    $this->assertEquals('htmlTemplate.php', DEFAULT_HTML_TEMPLATE_NAME);
    $this->assertEquals(20, PROJECT_TITLE_MAX_DISPLAY_LENGTH);
    $this->assertEquals(3, PROJECT_ROW_MAX_PROJECTS);
    $this->assertEquals('catroid', MVC_DEFAULT_MODULE);
    $this->assertEquals('index', MVC_DEFAULT_CLASS);
    $this->assertEquals('__default', MVC_DEFAULT_METHOD);
    $this->assertEquals('html', MVC_DEFAULT_VIEW);
  }
}
?>
