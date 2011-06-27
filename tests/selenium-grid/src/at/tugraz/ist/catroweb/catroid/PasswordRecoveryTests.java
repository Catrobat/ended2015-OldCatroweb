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

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

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
      assertTrue(session().isTextPresent("Login"));
      assertTrue(session().isTextPresent("click here if you forgot your password?"));
      session().isElementPresent("xpath=//div[@class='loginMain']");
      session().isElementPresent("xpath=//div[@class='loginFormContainer']");
      session().isElementPresent("xpath=//div[@class='loginHelper']");
      session().isElementPresent("xpath=//a[@id='forgotPassword']");
      session().click("xpath=//a[@id='forgotPassword']");

      // check password recovery form
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Change your password"));
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

      assertTrue(session().isElementPresent("xpath=//input[@name='registrationUsername']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='registrationPassword']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='registrationEmail']"));
      assertTrue(session().isElementPresent("xpath=//select[@name='registrationMonth']"));
      assertTrue(session().isElementPresent("xpath=//select[@name='registrationYear']"));
      assertTrue(session().isElementPresent("xpath=//select[@name='registrationGender']"));
      assertTrue(session().isElementPresent("xpath=//select[@name='registrationCountry']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='registrationCity']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='registrationSubmit']"));

      session().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
      session().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
      session().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
      session().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
      session().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
      session().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
      session().type("xpath=//select[@name='registrationCountry']", dataset.get("registrationCountry"));
      session().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));
      session().click("xpath=//input[@name='registrationSubmit']");
      ajaxWait();
      waitForTextPresent(dataset.get("registrationUsername"));

      // goto lost password page and test reset by email and nickname, at first
      // use some wrong nickname or email
      openLocation("catroid/passwordrecovery");
      assertTrue(session().isTextPresent("Enter your nickname or email address:"));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordRecoverySubmit']"));
      session().type("xpath=//input[@name='passwordRecoveryUserdata']", dataset.get("registrationUsername") + " to test");
      session().click("xpath=//input[@name='passwordRecoverySubmit']");
      ajaxWait();

      // check error message
      assertTrue(session().isTextPresent("Enter your nickname or email address:"));
      assertTrue(session().isTextPresent("The nickname or email address was not found."));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordRecoverySubmit']"));

      // now use real name
      session().type("xpath=//input[@name='passwordRecoveryUserdata']", dataset.get("registrationUsername"));
      session().click("xpath=//input[@name='passwordRecoverySubmit']");
      ajaxWait();
      assertTrue(session().isTextPresent(Config.TESTS_BASE_PATH + "catroid/passwordrecovery?c="));
      assertTrue(session().isTextPresent("An email was sent to your email address. Please check your inbox."));
      session().click("xpath=//a[@id='forgotPassword']");

      // enter 2short password
      waitForPageToLoad();
      String recoveryUrl = session().getLocation();
      assertTrue(session().isTextPresent("Please enter your new password:"));
      session().type("xpath=//input[@name='passwordSavePassword']", "short");
      session().click("xpath=//input[@name='passwordSaveSubmit']");
      ajaxWait();
      assertTrue(session().isTextPresent("Please enter your new password:"));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordSavePassword']"));
      assertTrue(session().isTextPresent("The password must have at least 6 characters."));

      // enter the new password correctly
      session().type("xpath=//input[@name='passwordSavePassword']", dataset.get("registrationPassword") + " new");
      session().click("xpath=//input[@name='passwordSaveSubmit']");
      ajaxWait();
      assertTrue(session().isTextPresent("Your new password is set."));
      assertFalse(session().isTextPresent("Please enter your new password:"));

      // and try to login with the old credentials to verify password recovery
      // worked
      openLocation();
      ajaxWait();
      session().click("headerProfileButton");
      assertTrue(session().isVisible("logoutSubmitButton"));
      session().click("logoutSubmitButton");
      waitForPageToLoad();
      ajaxWait();

      session().click("headerProfileButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      waitForElementPresent("xpath=//input[@id='loginSubmitButton']");
      assertTrue(session().isVisible("loginSubmitButton"));
      assertTrue(session().isVisible("loginUsername"));
      assertTrue(session().isVisible("loginPassword"));

      session().type("loginUsername", dataset.get("registrationUsername"));
      session().type("loginPassword", dataset.get("registrationPassword"));
      session().click("loginSubmitButton");
      ajaxWait();

      // check bad login
      assertTrue(session().isVisible("loginSubmitButton"));

      // and try to login now with the new credentials
      assertTrue(session().isVisible("loginSubmitButton"));
      assertTrue(session().isVisible("loginUsername"));
      assertTrue(session().isVisible("loginPassword"));
      session().type("loginUsername", dataset.get("registrationUsername"));
      session().type("loginPassword", dataset.get("registrationPassword") + " new");
      session().click("loginSubmitButton");
      waitForPageToLoad();
      ajaxWait();

      // check login
      assertTrue(session().isTextPresent("Newest Projects"));
      assertTrue(session().isElementPresent("xpath=//div[@id='projectContainer']"));

      session().click("headerMenuButton");
      waitForPageToLoad();

      assertTrue(session().isVisible("menuLogoutButton"));

      clickAndWaitForPopUp("menuForumButton", "board");
      assertFalse(session().isTextPresent("Login"));
      assertTrue(session().isTextPresent("Logout"));
      assertTrue(session().isTextPresent(dataset.get("registrationUsername")));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      session().click("xpath=//li[@id='pt-preferences']/a");
      waitForPageToLoad();
      assertEquals("Preferences", session().getText("firstHeading"));
      assertFalse(session().isTextPresent("Not logged in"));
      closePopUp();

      // logout
      session().click("headerProfileButton");
      assertTrue(session().isVisible("logoutSubmitButton"));
      session().click("logoutSubmitButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      session().click("headerProfileButton");
      assertTrue(session().isVisible("loginSubmitButton"));
      assertTrue(session().isVisible("loginUsername"));
      assertTrue(session().isVisible("loginPassword"));

      // Recovery URL should not work again
      session().open(recoveryUrl);
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Sorry! Your recovery url has expired. Please try again."));
      assertTrue(session().isElementPresent("xpath=//input[@name='passwordNextSubmit']"));

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
