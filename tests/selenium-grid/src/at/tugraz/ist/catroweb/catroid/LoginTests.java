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

@Test(groups = { "catroid", "LoginTests" })
public class LoginTests extends BaseTest {

  @Test(dataProvider = "validLoginData", groups = { "functionality", "popupwindows" }, description = "check login with valid data")
  public void validLogin(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation();

      // wiki username creation
      String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();

      // check if we are not logged in to board & wiki
      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertTrue(selenium().isTextPresent("Login"));
      assertFalse(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertFalse(selenium().isTextPresent(wikiUsername));
      closePopUp();

      // test login
      openLocation();
      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertFalse(selenium().isVisible("headerProfileButton"));
      assertTrue(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));
      selenium().click("headerCancelButton");
      assertTrue(selenium().isVisible("headerProfileButton"));
      assertFalse(selenium().isVisible("headerCancelButton"));
      assertFalse(selenium().isVisible("loginSubmitButton"));
      assertFalse(selenium().isVisible("loginUsername"));
      assertFalse(selenium().isVisible("loginPassword"));
      selenium().click("headerProfileButton");
      assertFalse(selenium().isVisible("headerProfileButton"));
      assertTrue(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));

      selenium().type("loginUsername", dataset.get("username"));
      selenium().type("loginPassword", dataset.get("password"));

      selenium().click("loginSubmitButton");
      waitForPageToLoad();

      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("logoutSubmitButton"));
      selenium().click("headerCancelButton");

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertFalse(selenium().isTextPresent("Login"));
      assertTrue(selenium().isTextPresent("Logout"));
      assertTrue(selenium().isTextPresent(dataset.get("username")));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertTrue(selenium().isTextPresent(wikiUsername));
      selenium().click("xpath=//li[@id='pt-preferences']/a");
      waitForPageToLoad();
      assertEquals("Preferences", selenium().getText("firstHeading"));
      assertFalse(selenium().isTextPresent("Not logged in"));
      closePopUp();

      // test logout
      openLocation();
      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("logoutSubmitButton"));
      selenium().click("logoutSubmitButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("loginSubmitButton"));
      selenium().click("headerCancelButton");

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertTrue(selenium().isTextPresent("Login"));
      assertFalse(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertFalse(selenium().isTextPresent(wikiUsername));
      closePopUp();
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

      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertFalse(selenium().isVisible("headerProfileButton"));
      assertTrue(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));
      selenium().click("headerCancelButton");
      assertTrue(selenium().isVisible("headerProfileButton"));
      assertFalse(selenium().isVisible("headerCancelButton"));
      assertFalse(selenium().isVisible("loginSubmitButton"));
      assertFalse(selenium().isVisible("loginUsername"));
      assertFalse(selenium().isVisible("loginPassword"));
      selenium().click("headerProfileButton");
      assertFalse(selenium().isVisible("headerProfileButton"));
      assertTrue(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));

      selenium().type("loginUsername", dataset.get("username"));
      selenium().type("loginPassword", dataset.get("password"));

      selenium().click("loginSubmitButton");
      ajaxWait();

      assertTrue(selenium().isVisible("loginSubmitButton"));
      selenium().click("headerCancelButton");

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertTrue(selenium().isTextPresent("Login"));
      assertFalse(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertFalse(selenium().isTextPresent(wikiUsername));
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
