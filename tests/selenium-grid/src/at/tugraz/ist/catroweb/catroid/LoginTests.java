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

@Test(groups = { "catroid", "LoginTests" })
public class LoginTests extends BaseTest {

  @Test(dataProvider = "validLoginData", groups = { "functionality", "popupwindows" }, description = "check login with valid data")
  public void validLogin(HashMap<String, String> dataset) throws Throwable {
    // log out if necessary
    openLocation();

    // wiki username creation
    String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();

    // check if we are not logged in to board & wiki
    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("menuLoginButton"));
    clickAndWaitForPopUp("menuForumButton", "board");
    assertTrue(session().isTextPresent("Login"));
    assertFalse(session().isTextPresent("Logout"));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertFalse(session().isTextPresent(wikiUsername));
    closePopUp();

    // test login
    openLocation();
    assertTrue(session().isVisible("headerProfileButton"));
    session().click("headerProfileButton");
    assertFalse(session().isVisible("headerProfileButton"));
    assertTrue(session().isVisible("headerCancelButton"));
    assertTrue(session().isVisible("loginSubmitButton"));
    assertTrue(session().isVisible("loginUsername"));
    assertTrue(session().isVisible("loginPassword"));
    session().click("headerCancelButton");
    assertTrue(session().isVisible("headerProfileButton"));
    assertFalse(session().isVisible("headerCancelButton"));
    assertFalse(session().isVisible("loginSubmitButton"));
    assertFalse(session().isVisible("loginUsername"));
    assertFalse(session().isVisible("loginPassword"));
    session().click("headerProfileButton");
    assertFalse(session().isVisible("headerProfileButton"));
    assertTrue(session().isVisible("headerCancelButton"));
    assertTrue(session().isVisible("loginSubmitButton"));
    assertTrue(session().isVisible("loginUsername"));
    assertTrue(session().isVisible("loginPassword"));

    session().type("loginUsername", dataset.get("username"));
    session().type("loginPassword", dataset.get("password"));

    session().click("loginSubmitButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("headerProfileButton"));
    session().click("headerProfileButton");
    assertTrue(session().isVisible("logoutSubmitButton"));
    session().click("headerCancelButton");

    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("menuLogoutButton"));

    clickAndWaitForPopUp("menuForumButton", "board");
    assertFalse(session().isTextPresent("Login"));
    assertTrue(session().isTextPresent("Logout"));
    assertTrue(session().isTextPresent(dataset.get("username")));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertTrue(session().isTextPresent(wikiUsername));
    session().click("xpath=//li[@id='pt-preferences']/a");
    waitForPageToLoad();
    assertEquals("Preferences", session().getText("firstHeading"));
    assertFalse(session().isTextPresent("Not logged in"));
    closePopUp();

    // test logout
    openLocation();
    assertTrue(session().isVisible("headerProfileButton"));
    session().click("headerProfileButton");
    assertTrue(session().isVisible("logoutSubmitButton"));
    session().click("logoutSubmitButton");
    Thread.sleep(Config.TIMEOUT_THREAD);
    assertTrue(session().isVisible("headerProfileButton"));
    session().click("headerProfileButton");
    assertTrue(session().isVisible("loginSubmitButton"));
    session().click("headerCancelButton");

    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("menuLoginButton"));

    clickAndWaitForPopUp("menuForumButton", "board");
    assertTrue(session().isTextPresent("Login"));
    assertFalse(session().isTextPresent("Logout"));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertFalse(session().isTextPresent(wikiUsername));
    closePopUp();
  }

  @Test(dataProvider = "invalidLoginData", groups = { "functionality", "popupwindows" }, description = "check login with invalid data")
  public void invalidLogin(HashMap<String, String> dataset) throws Throwable {
    // log out if necessary
    openLocation();

    // wiki username creation
    String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();

    assertTrue(session().isVisible("headerProfileButton"));
    session().click("headerProfileButton");
    assertFalse(session().isVisible("headerProfileButton"));
    assertTrue(session().isVisible("headerCancelButton"));
    assertTrue(session().isVisible("loginSubmitButton"));
    assertTrue(session().isVisible("loginUsername"));
    assertTrue(session().isVisible("loginPassword"));
    session().click("headerCancelButton");
    assertTrue(session().isVisible("headerProfileButton"));
    assertFalse(session().isVisible("headerCancelButton"));
    assertFalse(session().isVisible("loginSubmitButton"));
    assertFalse(session().isVisible("loginUsername"));
    assertFalse(session().isVisible("loginPassword"));
    session().click("headerProfileButton");
    assertFalse(session().isVisible("headerProfileButton"));
    assertTrue(session().isVisible("headerCancelButton"));
    assertTrue(session().isVisible("loginSubmitButton"));
    assertTrue(session().isVisible("loginUsername"));
    assertTrue(session().isVisible("loginPassword"));

    session().type("loginUsername", dataset.get("username"));
    session().type("loginPassword", dataset.get("password"));

    session().click("loginSubmitButton");
    ajaxWait();
    session().getAlert();
    assertTrue(session().isVisible("loginSubmitButton"));
    session().click("headerCancelButton");

    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("menuLoginButton"));

    clickAndWaitForPopUp("menuForumButton", "board");
    assertTrue(session().isTextPresent("Login"));
    assertFalse(session().isTextPresent("Logout"));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertFalse(session().isTextPresent(wikiUsername));
    closePopUp();
  }

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
