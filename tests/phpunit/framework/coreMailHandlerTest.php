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
require_once CORE_BASE_PATH . 'modules/common/userFunctions.php';
class coreMailHandlerTest extends PHPUnit_Framework_TestCase {
  protected $mailHandler;
  protected $userFunctions;
  
  protected function setUp() {
    $this->mailHandler = new CoreMailHandler();
    $this->userFunctions = new userFunctions();
    $this->profileUrl = BASE_PATH . 'profile';
    $this->loginUrl = BASE_PATH . 'login';
    $this->recoveryUrl = BASE_PATH . 'passwordrecovery';
  }

  /**
   * @dataProvider registrationData
   */
  public function testRegistrationMail($registrationUsername, $registrationPassword) {
    $mail = $this->userFunctions->sendRegistrationEmail(array('registrationUsername' => $registrationUsername, 'registrationPassword' => $registrationPassword));
    $mail['text'] = $this->mailHandler->word_wrap($mail['text']);
    $mailSubject = USER_EMAIL_SUBJECT_PREFIX.' - Your '.APPLICATION_NAME.' Registration';
    $mailText  = "Congratulations and welcome to the ".APPLICATION_URL_TEXT." community.\r\n\r\n";
    $mailText .= "Please keep this e-mail for your records. Your account information is as follows:\r\n";
    $mailText .= "Username: ".$registrationUsername."\r\n";
    $mailText .= "Password: ".$registrationPassword."\r\n\r\n";
    $mailText .= "You can use your nickname and your password at any time to access the ".APPLICATION_NAME." community.\r\n\r\n";
    $mailText .= "To do so, just visit the following page:\r\n";
    $mailText .= "{unwrap}".$this->loginUrl."{/unwrap}\r\n\r\n";
    $mailText .= "To edit your profile details, go to this page:\r\n";
    $mailText .= "{unwrap}".$this->profileUrl."{/unwrap}\r\n\r\n";
    $mailText .= "In the event that you forgot your password, you will be able to reset it using the email address or your username associated with your account.\r\n";
    $mailText .= "{unwrap}".$this->recoveryUrl."{/unwrap}\r\n\r\n";
    $mailText .= "Best regards,\r\n";
    $mailText .= "Your ".APPLICATION_NAME." Team";
    
    $this->assertEquals($mailSubject, $mail['subject']);
    $this->assertEquals($this->mailHandler->word_wrap($mailText), $mail['text']);
  }
  
  /**
   * @dataProvider registrationData
   */
  public function testWordwrap($registrationUsername, $registrationPassword) {
    $mailText  = "Congratulations and welcome to the ".APPLICATION_URL_TEXT." community.\r\n\r\n";
    $mailText .= "Please keep this e-mail for your records. Your account information is as follows:\r\n";
    $mailText .= "Username: ".$registrationUsername."\r\n";
    $mailText .= "Password: ".$registrationPassword."\r\n\r\n";
    $mailText .= "You can use your nickname and your password at any time to access the\r\n".APPLICATION_NAME." community.\r\n\r\n";
    $mailText .= "To do so, just visit the following page:\r\n";
    $mailText .= $this->loginUrl."\r\n\r\n";
    $mailText .= "To edit your profile details, go to this page:\r\n";
    $mailText .= $this->profileUrl."\r\n\r\n";
    $mailText .= "In the event that you forgot your password, you will be able to reset it\r\nusing the email address or your username associated with your account.\r\n";
    $mailText .= $this->recoveryUrl."\r\n\r\n";
    $mailText .= "Best regards,\r\n";
    $mailText .= "Your ".APPLICATION_NAME." Team";
    $expectedOutput = "Congratulations and welcome to the ".APPLICATION_URL_TEXT." community.\r\n\r\nPlease keep this e-mail for your records. Your account information is as\r\nfollows:\r\nUsername: ".$registrationUsername."\r\nPassword: ".$registrationPassword."\r\n\r\nYou can use your nickname and your password at any time to access the\r\n".APPLICATION_NAME." community.\r\n\r\nTo do so, just visit the following page:\r\n".$this->loginUrl."\r\n\r\nTo edit your profile details, go to this page:\r\n".$this->profileUrl."\r\n\r\nIn the event that you forgot your password, you will be able to reset it\r\nusing the email address or your username associated with your account.\r\n".$this->recoveryUrl."\r\n\r\nBest regards,\r\nYour ".APPLICATION_NAME." Team\r\n";
    $this->assertEquals($expectedOutput, $this->mailHandler->word_wrap($mailText));
  }
  
  /* *** DATA PROVIDERS *** */
  public function registrationData() {
    $dataArray = array(
    array('registrationUsername' => "phpUnitCoreMailHandlerTest", 
    'registrationPassword' => "WhyIsThisPlaintext")
    );
    return $dataArray;
    
  }
}
?>
