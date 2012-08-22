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

package at.tugraz.ist.catroweb.catroid;

import java.util.HashMap;

import org.openqa.selenium.By;
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
      openLocation("catroid/menu");

      // check password recovery link
      driver().findElement(By.id("menuLoginButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      assertTrue(isVisible(By.id("loginSubmitButton")));

      ajaxWait();
      driver().findElement(By.id("headerCancelButton")).click();
      ajaxWait();
      
      assertFalse(isVisible(By.id("loginUsername")));
      assertFalse(isVisible(By.id("loginPassword")));
      assertFalse(isVisible(By.id("loginSubmitButton")));
      
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

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationUsername")));

      // goto lost password page and test reset by email and nickname, at first
      // use some wrong nickname or email
      openLocation("catroid/passwordrecovery");
      assertTrue(isTextPresent("Enter your nickname or email address:"));
      assertTrue(isElementPresent(By.name("passwordRecoveryUserdata")));
      assertTrue(isElementPresent(By.name("passwordRecoverySendLink")));
      
      driver().findElement(By.name("passwordRecoveryUserdata")).clear();
      driver().findElement(By.name("passwordRecoveryUserdata")).sendKeys(dataset.get("registrationUsername") + " to test");
      driver().findElement(By.name("passwordRecoverySendLink")).click();
      ajaxWait();

      // check error message
      assertTrue(isTextPresent("Enter your nickname or email address:"));
      assertTrue(isTextPresent("The nickname or email address was not found."));
      assertTrue(isElementPresent(By.name("passwordRecoveryUserdata")));
      assertTrue(isElementPresent(By.name("passwordRecoverySendLink")));

      // now use real name
      driver().findElement(By.name("passwordRecoveryUserdata")).clear();
      driver().findElement(By.name("passwordRecoveryUserdata")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.name("passwordRecoverySendLink")).click();
      ajaxWait();
      assertTrue(isTextPresent(Config.TESTS_BASE_PATH + "catroid/passwordrecovery?c="));
      assertTrue(isTextPresent("An email was sent to your email address."));
      assertTrue(isTextPresent("Please check your inbox."));

      // get recovery url and click it
      String recoveryUrl = driver().findElement(By.id("forgotPassword")).getText();
      driver().findElement(By.id("forgotPassword")).click();

      // enter 2short password
      assertTrue(isTextPresent("Please enter your new password:"));
      driver().findElement(By.id("passwordSavePassword")).clear();
      driver().findElement(By.id("passwordSavePassword")).sendKeys("short");
      driver().findElement(By.name("passwordSaveSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent("Please enter your new password:"));
      assertTrue(isElementPresent(By.name("passwordSavePassword")));
      assertTrue(isTextPresent("password must have at least"));

      // enter the new password correctly
      driver().findElement(By.id("passwordSavePassword")).clear();
      driver().findElement(By.id("passwordSavePassword")).sendKeys(dataset.get("registrationPassword") + " new");
      driver().findElement(By.name("passwordSaveSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent("Your new password is set."));

      // and try to login with the old credentials to verify password recovery
      // worked
      openLocation();
      ajaxWait();
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      ajaxWait();

      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      // check bad login
      assertTrue(isVisible(By.id("loginSubmitButton")));

      // and try to login now with the new credentials
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      driver().findElement(By.id("loginUsername")).clear();
      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("loginPassword")).clear();
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("registrationPassword") + " new");
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      // check login
      assertTrue(isTextPresent("Newest Projects"));
      assertTrue(isElementPresent(By.id("projectContainer")));

      driver().findElement(By.id("headerMenuButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertFalse(isTextPresent("Login"));
      assertTrue(isTextPresent("Logout"));
      assertTrue(isTextPresent(dataset.get("registrationUsername")));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      waitForElementPresent(By.id("pt-preferences"));
      driver().findElement(By.id("pt-preferences")).findElement(By.tagName("a")).click();
      assertTrue(containsElementText(By.id("firstHeading"), "Preferences"));
      assertFalse(isTextPresent("Not logged in"));
      closePopUp();

      // logout
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      ajaxWait();
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      // Recovery URL should not work again
      driver().get(recoveryUrl);
      ajaxWait();
      assertTrue(isTextPresent("Sorry! Your recovery url has expired. Please try again."));
      assertTrue(isElementPresent(By.name("passwordNextSubmit")));

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
        put("registrationUsername", "JohnTest" + randomString1);
        put("registrationPassword", "just a simple password!");
        put("registrationEmail", "john" + randomString1 + "@catroid.org");
        put("registrationCountry", "Italy");
        put("registrationCity", "Padua");
        put("registrationMonth", "February");
        put("registrationYear", "1980");
        put("registrationGender", "male");
      }
    } } };
    return dataArray;
  }
}
