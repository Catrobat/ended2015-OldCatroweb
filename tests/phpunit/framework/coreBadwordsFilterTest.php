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

require_once('frameworkTestsBootstrap.php');

class coreBadwordsFilterTest extends PHPUnit_Framework_TestCase
{
  protected $testModel;
  
  protected function setUp() {
    require_once('frameworkTestModel.php');
    $this->testModel = new frameworkTestModel();
  }

  /**
   * @dataProvider badWords
   */
  public function testBadwordsFilterBad($badWord) {
    $this->assertEquals(1, $this->testModel->badWordsFilter->areThereInsultingWords($badWord));
  }
  
  /**
   * @dataProvider goodWords
   */
  public function testBadwordsFilterGood($goodWord) {
    $this->assertEquals(0, $this->testModel->badWordsFilter->areThereInsultingWords($goodWord));
  }

  /* DATA PROVIDERS */
  public function badWords() {
    $badWords = array(
          array("fuck"), 
          array("shit"),
          array("ass"),
          array("this is a sucking text with some really bad words in it. so go home asshole!"),
          array("f*uck"));
    return $badWords;
  }
  
  public function goodWords() {
    $goodWords = array(
          array("test"), 
          array("catroid"),
          array("here comes some text which does not have any insulting word inside."),
          array("project"));
    return $goodWords;
  }
}
?>
