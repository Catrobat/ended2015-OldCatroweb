<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
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
    $mailText .= "http://catroid.org/catroid/login\r\n\r\n";
    $mailText .= "To edit your profile details, go to this page:\r\n";
    $mailText .= "http://catroid.org/catroid/profile\r\n\r\n";
    $mailText .= "In the event that you forgot your password, you will be able to reset it using the email address or your username associated with your account.\r\n";
    $mailText .= "{unwrap}http://catroid.org/catroid/passwordrecovery{/unwrap}\r\n\r\n";
    $mailText .= "Best regards,\r\n";
    $mailText .= "Your Catroid Team\r\n";
    $mailText .= "{unwrap}.......................................................................................{/unwrap}";
    
    $expectedOutput = "Congratulations and welcome to the Catroid.org community.

Please keep this e-mail for your records. Your account information is as
follows:
Username: username
Password: password

You can use your nickname and your password at any time to access the
catroid community.

To do so, just visit the following page:
http://catroid.org/catroid/login

To edit your profile details, go to this page:
http://catroid.org/catroid/profile

In the event that you forgot your password, you will be able to reset it
using the email address or your username associated with your account.
http://catroid.org/catroid/passwordrecovery


Best regards,
Your Catroid Team
.......................................................................................

";
    
    $this->assertEquals($expectedOutput, $this->mailHandler->wordwrap($mailText));
  }
}
?>
