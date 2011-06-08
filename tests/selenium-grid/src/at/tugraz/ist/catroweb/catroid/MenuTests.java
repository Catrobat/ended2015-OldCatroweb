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

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "MenuTests" })
public class MenuTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check button visibility")
  public void buttonVisibility() throws Throwable {
    try {
      openLocation();
      session().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", session().getLocation());

      assertTrue(session().isVisible("menuProfileButton"));
      assertTrue(session().isVisible("menuForumButton"));
      assertTrue(session().isVisible("menuWikiButton"));

      assertTrue(session().isVisible("menuWallButton"));
      assertTrue(session().isVisible("menuLoginButton"));
      assertTrue(session().isVisible("menuSettingsButton"));

      assertFalse(session().isEditable("menuWallButton"));
      assertFalse(session().isEditable("menuSettingsButton"));
      // TODO LOGIN BROKEN
      // session().click("menuLoginButton");
      // waitForPageToLoad();
      //
      // assertRegExp(".*/catroid/login[?]requesturi=catroid/menu",session().getLocation());
      // session().type("xpath=//input[@name='loginUsername']",
      // CommonData.getLoginUserDefault());
      // session().type("xpath=//input[@name='loginPassword']",
      // CommonData.getLoginPasswordDefault());
      // session().click("xpath=//input[@name='loginSubmit']");
      // ajaxWait();
      //
      // assertRegExp(".*/catroid/menu$",session().getLocation());
      // assertTrue(session().isVisible("menuLogoutButton"));
      // assertFalse(session().isVisible("menuLoginButton"));
      //
      // session().click("menuLogoutButton");
      // waitForPageToLoad();
      // assertRegExp(".*/catroid/index(/[0-9]+)?",session().getLocation());
      //
      // session().click("headerMenuButton");
      // waitForPageToLoad();
      //
      // assertFalse(session().isVisible("menuLogoutButton"));
      // assertTrue(session().isVisible("menuLoginButton"));
    } catch(AssertionError e) {
      captureScreen("MenuTests.buttonVisibility");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.buttonVisibility");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check board + wiki links; logged in/out")
  public void boardAndWikiLinks() throws Throwable {
    try {
      openLocation();
      session().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", session().getLocation());

      session().click("menuForumButton");
      session().waitForPopUp("board", Config.TIMEOUT);
      session().selectWindow("board");

      assertRegExp(".*/addons/board(/)?$", session().getLocation());
      assertTrue(session().isTextPresent(("Board index")));
      assertTrue(session().isTextPresent(("Login")));
      session().close();
      session().selectWindow(null);

      session().click("menuWikiButton");
      session().waitForPopUp("wiki", Config.TIMEOUT);
      session().selectWindow("wiki");
      assertRegExp(".*/wiki/Main_Page$", session().getLocation());

      assertTrue(session().isTextPresent(("Main Page")));
      assertFalse(session().isElementPresent("pt-userpage"));
      session().close();
      session().selectWindow(null);
      // TODO Login Broken
      // session().click("menuLoginButton");
      // waitForPageToLoad();
      // session().type("xpath=//input[@name='loginUsername']",
      // CommonData.getLoginUserDefault());
      // session().type("xpath=//input[@name='loginPassword']",
      // CommonData.getLoginPasswordDefault());
      // session().click("xpath=//input[@name='loginSubmit']");
      // ajaxWait();
      // waitForPageToLoad();
      //
      // session().click("menuForumButton");
      // session().waitForPopUp("board", Config.TIMEOUT);
      // session().selectWindow("board");
      // assertRegExp(".*/addons/board(/)?$",session().getLocation());
      //
      // assertTrue(session().isTextPresent(("Board index")));
      // assertTrue(session().isTextPresent(CommonData.getLoginUserDefault()));
      // session().close();
      // session().selectWindow(null);
      //
      // session().click("menuWikiButton");
      // session().waitForPopUp("wiki", Config.TIMEOUT);
      // session().selectWindow("wiki");
      // assertRegExp(".*/wiki/Main_Page[?]action=purge$",session().getLocation());
      //
      // assertTrue(session().isTextPresent(("Main Page")));
      // assertTrue(session().isElementPresent("pt-userpage"));
      // session().close();
      // session().selectWindow(null);
    } catch(AssertionError e) {
      captureScreen("MenuTests.boardAndWikiLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.boardAndWikiLinks");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void homeButton() throws Throwable {
    try {
      openLocation();
      session().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", session().getLocation());

      assertTrue(session().isElementPresent("headerHomeButton"));
      session().click("headerHomeButton");
      waitForPageToLoad();
      assertFalse(session().isElementPresent("headerHomeButton"));
      assertRegExp(".*/catroid/index(/[0-9]+)?", session().getLocation());
    } catch(AssertionError e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    }
  }
}
