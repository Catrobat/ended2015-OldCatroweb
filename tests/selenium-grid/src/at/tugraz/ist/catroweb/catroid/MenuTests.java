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

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "MenuTests" })
public class MenuTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check button visibility")
  public void buttonVisibility() throws Throwable {
    try {
      openLocation();
      selenium().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", selenium().getLocation());

      assertTrue(selenium().isVisible("menuRegistrationButton"));
      assertTrue(selenium().isVisible("menuForumButton"));
      assertTrue(selenium().isVisible("menuWikiButton"));

      assertTrue(selenium().isVisible("menuWallButton"));
      assertTrue(selenium().isVisible("menuSettingsButton"));

      assertFalse(selenium().isEditable("menuWallButton"));
      assertFalse(selenium().isEditable("menuSettingsButton"));
      // TODO LOGIN BROKEN
      // selenium().click("menuLoginButton");
      // waitForPageToLoad();
      //
      // assertRegExp(".*/catroid/login[?]requesturi=catroid/menu",selenium().getLocation());
      // selenium().type("xpath=//input[@name='loginUsername']",
      // CommonData.getLoginUserDefault());
      // selenium().type("xpath=//input[@name='loginPassword']",
      // CommonData.getLoginPasswordDefault());
      // selenium().click("xpath=//input[@name='loginSubmit']");
      // ajaxWait();
      //
      // assertRegExp(".*/catroid/menu$",selenium().getLocation());
      // assertTrue(selenium().isVisible("menuLogoutButton"));
      // assertFalse(selenium().isVisible("menuLoginButton"));
      //
      // selenium().click("menuLogoutButton");
      // waitForPageToLoad();
      // assertRegExp(".*/catroid/index(/[0-9]+)?",selenium().getLocation());
      //
      // selenium().click("headerMenuButton");
      // waitForPageToLoad();
      //
      // assertFalse(selenium().isVisible("menuLogoutButton"));
      // assertTrue(selenium().isVisible("menuLoginButton"));
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
      selenium().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", selenium().getLocation());

      clickAndWaitForPopUp("menuForumButton", "board");
      assertRegExp(".*/addons/board(/)?$", selenium().getLocation());
      assertTrue(selenium().isTextPresent(("Board index")));
      assertTrue(selenium().isTextPresent(("Login")));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertRegExp(".*/wiki/Main_Page$", selenium().getLocation());

      assertTrue(selenium().isTextPresent(("Main Page")));
      assertFalse(selenium().isElementPresent("pt-userpage"));
      closePopUp();
      // TODO Login Broken
      // selenium().click("menuLoginButton");
      // waitForPageToLoad();
      // selenium().type("xpath=//input[@name='loginUsername']",
      // CommonData.getLoginUserDefault());
      // selenium().type("xpath=//input[@name='loginPassword']",
      // CommonData.getLoginPasswordDefault());
      // selenium().click("xpath=//input[@name='loginSubmit']");
      // ajaxWait();
      // waitForPageToLoad();
      //
      // selenium().click("menuForumButton");
      // selenium().waitForPopUp("board", Config.TIMEOUT);
      // selenium().selectWindow("board");
      // assertRegExp(".*/addons/board(/)?$",selenium().getLocation());
      //
      // assertTrue(selenium().isTextPresent(("Board index")));
      // assertTrue(selenium().isTextPresent(CommonData.getLoginUserDefault()));
      // selenium().close();
      // selenium().selectWindow(null);
      //
      // selenium().click("menuWikiButton");
      // selenium().waitForPopUp("wiki", Config.TIMEOUT);
      // selenium().selectWindow("wiki");
      // assertRegExp(".*/wiki/Main_Page[?]action=purge$",selenium().getLocation());
      //
      // assertTrue(selenium().isTextPresent(("Main Page")));
      // assertTrue(selenium().isElementPresent("pt-userpage"));
      // selenium().close();
      // selenium().selectWindow(null);
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
      selenium().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", selenium().getLocation());

      assertTrue(selenium().isElementPresent("headerHomeButton"));
      selenium().click("headerHomeButton");
      waitForPageToLoad();
      assertFalse(selenium().isElementPresent("headerHomeButton"));
      assertRegExp(".*/catroid/index(/[0-9]+)?", selenium().getLocation());
    } catch(AssertionError e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    }
  }
}
