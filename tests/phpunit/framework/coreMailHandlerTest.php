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

class coreMailHandlerTest extends PHPUnit_Framework_TestCase {
  protected $mailHandler;
  
  protected function setUp() {
    $this->mailHandler = new CoreMailHandler();
  }

  public function testWordwrap() {
    $mailText  = "Congratulations and welcome to the Catroid.org community.\r\n\r\n";
    $mailText .= "Please keep this e-mail for your records. Your account information is as follows:\r\n";
    $mailText .= "Username: username\r\n";
    $mailText .= "Password: password\r\n\r\n";
    $mailText .= "You can use your nickname and your password at any time to access the catroid community.\r\n\r\n";
    $mailText .= "To do so, just visit the following page:\r\n";
    $mailText .= "https://pocketcode.org/login\r\n\r\n";
    $mailText .= "To edit your profile details, go to this page:\r\n";
    $mailText .= "https://pocketcode.org/profile\r\n\r\n";
    $mailText .= "In the event that you forgot your password, you will be able to reset it using the email address or your username associated with your account.\r\n";
    $mailText .= "{unwrap}https://pocketcode.org/passwordrecovery{/unwrap}\r\n\r\n";
    $mailText .= "Best regards,\r\n";
    $mailText .= "Your Catroid Team\r\n";
    $mailText .= "{unwrap}.......................................................................................{/unwrap}";
    
   $expectedOutput = "Congratulations and welcome to the Catroid.org community.\r\n\r\nPlease keep this e-mail for your records. Your account information is as\r\nfollows:\r\nUsername: username\r\nPassword: password\r\n\r\nYou can use your nickname and your password at any time to access the\r\ncatroid community.\r\n\r\nTo do so, just visit the following page:\r\nhttp://catroid.org/catroid/login\r\n\r\nTo edit your profile details, go to this page:\r\nhttp://catroid.org/catroid/profile\r\n\r\nIn the event that you forgot your password, you will be able to reset it\r\nusing the email address or your username associated with your account.\r\nhttp://catroid.org/catroid/passwordrecovery\r\n\r\n\r\nBest regards,\r\nYour Catroid Team\r\n.......................................................................................\r\n\r\n";
    
    $this->assertEquals($expectedOutput, $this->mailHandler->word_wrap($mailText));
    
  }
}
?>
