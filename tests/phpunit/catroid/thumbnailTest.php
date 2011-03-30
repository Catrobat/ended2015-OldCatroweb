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

require_once('testsBootstrap.php');

class thumbnailTest extends PHPUnit_Framework_TestCase
{
	protected $obj;

	protected function setUp() {
		require_once CORE_BASE_PATH.'modules/catroid/thumbnail.php';
		$this->obj = new thumbnail();
		@unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_thumbnail.jpg');
	}

	public function testUploadThumbnail() {
		$thumbName1 = 'test_thumbnail.jpg';
		$fileData1 = array('upload'=>array('name'=>$thumbName1, 'type'=>'image/jpeg',
                        'tmp_name'=>dirname(__FILE__).'/testdata/'.$thumbName1, 'error'=>0, 'size'=>4482));
		$this->assertTrue($this->obj->uploadThumbnail($fileData1));
		$this->assertTrue(is_file(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbName1));

		$thumbName2 = 'test_thumbnail1.jpg';
		$fileData2 = array('upload'=>array('name'=>$thumbName1, 'type'=>'image/jpeg',
                        'tmp_name'=>dirname(__FILE__).'/testdata/'.$thumbName2, 'error'=>0, 'size'=>4482));
		$this->assertFalse($this->obj->uploadThumbnail($fileData2));
		$this->assertEquals(md5_file(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbName1),
		  md5_file(dirname(__FILE__).'/testdata/'.$thumbName1));
	}

	protected function tearDown() {
		@unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_thumbnail.jpg');
	}
}
?>
