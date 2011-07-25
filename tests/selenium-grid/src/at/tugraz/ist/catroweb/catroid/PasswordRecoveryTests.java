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

package at.tugraz.ist.catroweb.catroid;

import java.util.HashMap;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "PasswordRecoveryTests" })
public class PasswordRecoveryTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check password recovery intro")
  public void passwordRecoveryIntro() throws Throwable {
    try {
      openLocation("catroid/login");

      // check password recovery link
      assertTrue(selenium().isTextPresent("Login"));
      assertTrue(selenium().isTextPresent("click here if you forgot your password?"));
      selenium().isElementPresent("xpath=//div[@class='loginMain']");
      selenium().isElementPresent("xpath=//div[@class='loginFormContainer']");
      selenium().isElementPresent("xpath=//div[@class='loginHelper']");
      selenium().isElementPresent("xpath=//a[@id='forgotPassword']");
      selenium().click("xpath=//a[@id='forgotPassword']");

      // check password recovery form
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Change your password"));
    } catch(AssertionError e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryIntro");
      throw e;
    } catch(Exception e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryIntro");
      throw e;
    }
  }

  @Test(dataProvider = "passwordRecoveryResetUsernames", groups = { "functionality", "popupwindows" }, description = "check password recovery")
  public void passwordRecoveryReset(HashMap<String, String> dataset) throws Throwable {
    try {
      // do registration process first, to create a new user with known password
      openLocation("catroid/registration");

      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationUsername']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationPassword']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationEmail']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationMonth']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationYear']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationGender']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationCountry']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationCity']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationSubmit']"));

      selenium().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
      selenium().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
      selenium().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
      selenium().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
      selenium().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
      selenium().type("xpath=//select[@name='registrationCountry']", dataset.get("registrationCountry"));
      selenium().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));
      selenium().click("xpath=//input[@name='registrationSubmit']");
      ajaxWait();
      waitForTextPresent(dataset.get("registrationUsername"));

      // goto lost password page and test reset by email and nickname, at first
      // use some wrong nickname or email
      openLocation("catroid/passwordrecovery");
      assertTrue(selenium().isTextPresent("Enter your nickname or email address:"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordRecoverySendLink']"));
      selenium().type("xpath=//input[@name='passwordRecoveryUserdata']", dataset.get("registrationUsername") + " to test");
      selenium().click("xpath=//input[@name='passwordRecoverySendLink']");
      ajaxWait();

      // check error message
      assertTrue(selenium().isTextPresent("Enter your nickname or email address:"));
      assertTrue(selenium().isTextPresent("The nickname or email address was not found."));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordRecoverySendLink']"));

      // now use real name
      selenium().type("xpath=//input[@name='passwordRecoveryUserdata']", dataset.get("registrationUsername"));
      selenium().click("xpath=//input[@name='passwordRecoverySendLink']");
      ajaxWait();
      assertTrue(selenium().isTextPresent(Config.TESTS_BASE_PATH + "catroid/passwordrecovery?c="));
      assertTrue(selenium().isTextPresent("An email was sent to your email address. Please check your inbox."));
      selenium().click("xpath=//a[@id='forgotPassword']");

      // enter 2short password
      waitForPageToLoad();
      String recoveryUrl = selenium().getLocation();
      assertTrue(selenium().isTextPresent("Please enter your new password:"));
      selenium().type("xpath=//input[@name='passwordSavePassword']", "short");
      selenium().click("xpath=//input[@name='passwordSaveSubmit']");
      ajaxWait();
      assertTrue(selenium().isTextPresent("Please enter your new password:"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordSavePassword']"));
      assertTrue(selenium().isTextPresent("password must have at least"));

      // enter the new password correctly
      selenium().type("xpath=//input[@name='passwordSavePassword']", dataset.get("registrationPassword") + " new");
      selenium().click("xpath=//input[@name='passwordSaveSubmit']");
      ajaxWait();
      assertTrue(selenium().isTextPresent("Your new password is set."));
      assertFalse(selenium().isTextPresent("Please enter your new password:"));

      // and try to login with the old credentials to verify password recovery
      // worked
      openLocation();
      ajaxWait();
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("logoutSubmitButton"));
      selenium().click("logoutSubmitButton");
      waitForPageToLoad();
      ajaxWait();

      selenium().click("headerProfileButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      waitForElementPresent("xpath=//input[@id='loginSubmitButton']");
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));

      selenium().type("loginUsername", dataset.get("registrationUsername"));
      selenium().type("loginPassword", dataset.get("registrationPassword"));
      selenium().click("loginSubmitButton");
      ajaxWait();

      // check bad login
      assertTrue(selenium().isVisible("loginSubmitButton"));

      // and try to login now with the new credentials
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));
      selenium().type("loginUsername", dataset.get("registrationUsername"));
      selenium().type("loginPassword", dataset.get("registrationPassword") + " new");
      selenium().click("loginSubmitButton");
      waitForPageToLoad();
      ajaxWait();

      // check login
      assertTrue(selenium().isTextPresent("Newest Projects"));
      assertTrue(selenium().isElementPresent("xpath=//div[@id='projectContainer']"));

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertFalse(selenium().isTextPresent("Login"));
      assertTrue(selenium().isTextPresent("Logout"));
      assertTrue(selenium().isTextPresent(dataset.get("registrationUsername")));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      selenium().click("xpath=//li[@id='pt-preferences']/a");
      waitForPageToLoad();
      assertEquals("Preferences", selenium().getText("firstHeading"));
      assertFalse(selenium().isTextPresent("Not logged in"));
      closePopUp();

      // logout
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("logoutSubmitButton"));
      selenium().click("logoutSubmitButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));

      // Recovery URL should not work again
      selenium().open(recoveryUrl);
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Sorry! Your recovery url has expired. Please try again."));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='passwordNextSubmit']"));

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryReset." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      log(dataset.get("registrationUsername"));
      captureScreen("PasswordRecoveryTests.passwordRecoveryReset." + dataset.get("registrationUsername"));
      throw e;
    }
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "passwordRecoveryResetUsernames")
  public Object[][] passwordRecoveryResetUsernames() {
    final String randomString1 = CommonData.getRandomShortString(10);

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "John Test " + randomString1);
        put("registrationPassword", "just a simple password!");
        put("registrationEmail", "john" + randomString1 + "@catroid.org");
        put("registrationGender", "male");
        put("registrationMonth", "2");
        put("registrationYear", "1980");
        put("registrationCountry", "IT");
        put("registrationCity", "Padua");
      }
    } } };
    return dataArray;
  }
}
