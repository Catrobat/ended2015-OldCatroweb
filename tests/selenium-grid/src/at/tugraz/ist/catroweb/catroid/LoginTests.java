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

import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.CommonFunctions;
import at.tugraz.ist.catroweb.common.CommonStrings;

@Test(groups = { "catroid", "LoginTests" })
public class LoginTests extends BaseTest {

  @Test(dataProvider = "validLoginData", groups = { "functionality", "popupwindows" }, description = "check login with valid data")
  public void validLogin(HashMap<String, String> dataset) throws Throwable {
    try {
      // test login
      openLocation();
      assertTrue(isVisible(By.id("largeMenuButton")));
      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      

      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();
      driver().findElement(By.id("menuProfileButton")).click();
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), dataset.get("username")));
      
      // test logout
      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("menuLogoutButton")));
      driver().findElement(By.id("menuLogoutButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginSubmitButton")));
    } catch(AssertionError e) {
      captureScreen("LoginTests.validLogin." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("LoginTests.validLogin." + dataset.get("username"));
      throw e;
    }
  }

  @Test(dataProvider = "validLoginData", groups = { "functionality", "popupwindows" }, description = "if logged in, registration page should redirect to profile page")
  public void redirection(HashMap<String, String> dataset) throws Throwable {
    try {      
      // test login
      openLocation();
      assertTrue(isVisible(By.id("largeMenuButton")));
      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), dataset.get("username")));
      
      openLocation("registration");
      assertFalse(isTextPresent(CommonStrings.REGISTRATION_PAGE_TITLE.toUpperCase()));
      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), dataset.get("username")));
    } catch(AssertionError e) {
      captureScreen("LoginTests.redirection." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("LoginTests.redirection." + dataset.get("username"));
      throw e;
    }
  }
  
  @Test(groups = { "functionality" }, description = "try login with different lower upper case in username")
  public void differentCaseInUsernameLogin() throws Throwable {
    String username = "maxmustermann";
    String password = "password";
    String email = "max" + System.currentTimeMillis() + "@gmail.com";
    String country = "Switzerland";

    try {
      CommonFunctions.deleteUserFromDatabase(username);
      openLocation("registration/");
      
      driver().findElement(By.id("registrationUsername")).sendKeys(username);
      driver().findElement(By.id("registrationPassword")).sendKeys(password);
      driver().findElement(By.id("registrationEmail")).sendKeys(email);
      driver().findElement(By.id("registrationCountry")).sendKeys(country);
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();

      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), username));
      logout("index");

      openLocation("login");
      driver().findElement(By.id("loginUsername")).sendKeys("MAXmUstermann");
      driver().findElement(By.id("loginPassword")).sendKeys("password");

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("menuProfileButton")));
      driver().findElement(By.id("menuProfileButton")).click();
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), username));
      
      CommonFunctions.deleteUserFromDatabase(username);
    } catch(AssertionError e) {
      captureScreen("LoginTests.differentCaseInUsernameLogin." + username);
      throw e;
    } catch(Exception e) {
      captureScreen("LoginTests.differentCaseInUsernameLogin." + username);
      throw e;
    }
  }
  
  @Test(dataProvider = "invalidLoginData", groups = { "functionality", "popupwindows" }, description = "check login with invalid data; waitpage after five attempts")
  public void invalidLogin(HashMap<String, String> dataset) throws Throwable {
    try {
      // test login
      openLocation();
      assertTrue(isVisible(By.id("largeMenuButton")));
      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      for(int i = 0; i < 5; i++) {
        driver().findElement(By.id("loginSubmitButton")).click();
        ajaxWait();
        assertTrue(isTextPresent("The password or username was incorrect."));
      }
      CommonFunctions.removeAllBlockedIps();
      
      for(int i = 0; i < 5; i++) {
        driver().findElement(By.id("loginSubmitButton")).click();
        ajaxWait();
        assertTrue(isTextPresent("The password or username was incorrect."));
      }

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      assertTrue(isTextPresent("Your IP-Address has been blocked for 30 seconds."));
      CommonFunctions.removeAllBlockedIps();
    } catch(AssertionError e) {
      captureScreen("LoginTests.invalidLogin." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("LoginTests.invalidLogin." + dataset.get("username"));
      throw e;
    }
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "validLoginData")
  public Object[][] validLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "catroweb");
        put("password", "cat.roid.web");
      }
    } } };
    return dataArray;
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "invalidLoginData")
  public Object[][] invalidLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "wrongUser");
        put("password", "wrongPassword");
      }
    } } };
    return dataArray;
  }
}
