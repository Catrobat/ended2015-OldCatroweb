/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2013 The Catrobat Team
  *(<http://developer.catrobat.org/credits>)
  *
  *This program is free software: you can redistribute it and/or modify
  *it under the terms of the GNU Affero General Public License as
  *published by the Free Software Foundation, either version 3 of the
  *License, or (at your option) any later version.
  *
  *An additional term exception under section 7 of the GNU Affero
  *General Public License, version 3, is available at
  *http://developer.catrobat.org/license_additional_term
  *
  *This program is distributed in the hope that it will be useful,
  *but WITHOUT ANY WARRANTY; without even the implied warranty of
  *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  *GNU Affero General Public License for more details.
  *
  *You should have received a copy of the GNU Affero General Public License
  *along with this program. If not, see <http://www.gnu.org/licenses/>.
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

  @Test(groups = { "functionality" }, description = "check password recovery redirection if logged in")
  public void passwordRecoveryRedirectWhenLoggedIn() throws Throwable {
    try {
      login("index");
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();

      assertTrue(isTextPresent("My Profile"));
      assertTrue(isTextPresent(CommonData.getLoginUserDefault()));
      
      openLocation("passwordrecovery");
      
      assertFalse(isTextPresent("Recover your password"));
      assertTrue(isTextPresent("My Profile"));
      assertTrue(isTextPresent(CommonData.getLoginUserDefault()));
    } catch(AssertionError e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryRedirectWhenLoggedIn");
      throw e;
    } catch(Exception e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryRedirectWhenLoggedIn");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check password recovery intro")
  public void passwordRecoveryIntro() throws Throwable {
    try {
      openLocation("menu");
      
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
      openLocation("registration");

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
      logout("passwordrecovery");
      assertTrue(isTextPresent("Enter your nickname or email address:"));
      assertTrue(isElementPresent(By.id("passwordRecoveryUserdata")));
      assertTrue(isElementPresent(By.id("passwordRecoverySendLink")));
      
      driver().findElement(By.id("passwordRecoveryUserdata")).clear();
      driver().findElement(By.id("passwordRecoveryUserdata")).sendKeys(dataset.get("registrationUsername") + " to test");
      driver().findElement(By.id("passwordRecoverySendLink")).click();
      ajaxWait();

      // check error message
      assertTrue(isTextPresent("Enter your nickname or email address:"));
      assertTrue(isAjaxMessagePresent("The nickname or email address was not found."));
      assertTrue(isElementPresent(By.id("passwordRecoveryUserdata")));
      assertTrue(isElementPresent(By.id("passwordRecoverySendLink")));

      // now use real name
      driver().findElement(By.id("passwordRecoveryUserdata")).clear();
      driver().findElement(By.id("passwordRecoveryUserdata")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("passwordRecoverySendLink")).click();
      ajaxWait();
      assertTrue(isAjaxMessagePresent(Config.TESTS_BASE_PATH + "passwordrecovery?c="));

      // get recovery url and open it
      String recoveryUrl = getRecoveryUrl();
      openLocation(recoveryUrl);
      ajaxWait();

      // enter 2short password
      assertTrue(isTextPresent("Please enter your new password:"));
      driver().findElement(By.id("passwordSavePassword")).clear();
      driver().findElement(By.id("passwordSavePassword")).sendKeys("short");
      driver().findElement(By.id("passwordSaveSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent("Please enter your new password:"));
      assertTrue(isElementPresent(By.id("passwordSavePassword")));
      assertTrue(isAjaxMessagePresent("password must have at least"));

      // enter the new password correctly
      driver().findElement(By.id("passwordSavePassword")).clear();
      driver().findElement(By.id("passwordSavePassword")).sendKeys(dataset.get("registrationPassword") + " new");
      driver().findElement(By.id("passwordSaveSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationUsername")));

      // and try to login with the old credentials to verify password recovery
      // worked
      logout();
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
      assertEquals("https://groups.google.com/forum/?fromgroups=#!forum/pocketcode",  driver().getCurrentUrl());
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertEquals("https://github.com/Catrobat/Catroid/wiki/_pages",  driver().getCurrentUrl());
      closePopUp();

      // logout
      logout();
      ajaxWait();
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      // Recovery URL should not work again
      openLocation(recoveryUrl);
      ajaxWait();
      assertTrue(isAjaxMessagePresent("Recovery hash was not found."));

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
