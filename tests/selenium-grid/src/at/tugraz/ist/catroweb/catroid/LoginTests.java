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

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "LoginTests" })
public class LoginTests extends BaseTest {

  @Test(dataProvider = "validLoginData", groups = { "functionality", "popupwindows" }, description = "check login with valid data")
  public void validLogin(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation();

      // wiki username creation
      String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();

      // check if we are not logged in to board & wiki
      driver().findElement(By.id("headerMenuButton")).click();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertTrue(isTextPresent("Login"));
      assertFalse(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertFalse(isTextPresent(wikiUsername));
      closePopUp();

      // test login
      openLocation();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertFalse(isVisible(By.id("headerProfileButton")));
      assertTrue(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      driver().findElement(By.id("headerCancelButton")).click();
      assertTrue(isVisible(By.id("headerProfileButton")));
      assertFalse(isVisible(By.id("headerCancelButton")));
      assertFalse(isVisible(By.id("loginSubmitButton")));
      assertFalse(isVisible(By.id("loginUsername")));
      assertFalse(isVisible(By.id("loginPassword")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertFalse(isVisible(By.id("headerProfileButton")));
      assertTrue(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      driver().findElement(By.id("headerMenuButton")).click();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertFalse(isTextPresent("Login"));
      assertTrue(isTextPresent("Logout"));
      assertTrue(isTextPresent(dataset.get("username")));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertTrue(isTextPresent(wikiUsername));
      driver().findElement(By.id("pt-preferences")).findElement(By.tagName("a")).click();
      assertEquals("Preferences", driver().findElement(By.id("firstHeading")).getText());
      assertFalse(isTextPresent("Not logged in"));
      closePopUp();

      // test logout
      openLocation();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("loginSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      driver().findElement(By.id("headerMenuButton")).click();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertTrue(isTextPresent("Login"));
      assertFalse(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertFalse(isTextPresent(wikiUsername));
      closePopUp();
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
      openLocation();
      driver().findElement(By.id("headerProfileButton")).click();

      assertFalse(isTextPresent("You are logged in as"));
      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isTextPresent(dataset.get("username")));
      
      openLocation("catroid/registration/");
      ajaxWait();

      assertRegExp(".*/catroid/profile/" + dataset.get("username") + ".*", driver().getCurrentUrl());
      //assertTrue(isElementPresent(By.xpath("//a[@href='" + this.webSite + "catroid/details/1']")));
      
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      
      assertTrue(isTextPresent("from"));
      
      openLocation("catroid/registration/");
      ajaxWait();
      assertTrue(isTextPresent("Create a new account"));
      assertRegExp(".*/catroid/registration/.*", driver().getCurrentUrl());
      
      
    } catch(AssertionError e) {
      captureScreen("LoginTests.validLogin." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("LoginTests.validLogin." + dataset.get("username"));
      throw e;
    }
  }
  

  @Test(dataProvider = "invalidLoginData", groups = { "functionality", "popupwindows" }, description = "check login with invalid data")
  public void invalidLogin(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation();

      // wiki username creation
      String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();

      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertFalse(isVisible(By.id("headerProfileButton")));
      assertTrue(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      driver().findElement(By.id("headerCancelButton")).click();
      assertTrue(isVisible(By.id("headerProfileButton")));
      assertFalse(isVisible(By.id("headerCancelButton")));
      assertFalse(isVisible(By.id("loginSubmitButton")));
      assertFalse(isVisible(By.id("loginUsername")));
      assertFalse(isVisible(By.id("loginPassword")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertFalse(isVisible(By.id("headerProfileButton")));
      assertTrue(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("loginSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      driver().findElement(By.id("headerMenuButton")).click();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertTrue(isTextPresent("Login"));
      assertFalse(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertFalse(isTextPresent(wikiUsername));
      closePopUp();
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
