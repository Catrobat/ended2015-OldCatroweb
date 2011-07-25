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

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "MenuTests" })
public class MenuTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check button visibility")
  public void buttonVisibility() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      assertTrue(isVisible(By.id("menuRegistrationButton")));
      assertTrue(isVisible(By.id("menuForumButton")));
      assertTrue(isVisible(By.id("menuWikiButton")));

      assertTrue(isVisible(By.id("menuWallButton")));
      assertTrue(isVisible(By.id("menuSettingsButton")));

      assertFalse(isEditable(By.id("menuWallButton")));
      assertFalse(isEditable(By.id("menuSettingsButton")));
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
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertRegExp(".*/addons/board(/)?$", driver().getCurrentUrl());
      assertTrue(isTextPresent(("Board index")));
      assertTrue(isTextPresent(("Login")));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertRegExp(".*/wiki/Main_Page$", driver().getCurrentUrl());

      assertTrue(isTextPresent(("Main Page")));
      assertFalse(isElementPresent(By.id("pt-userpage")));
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
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      assertTrue(isElementPresent(By.id("headerHomeButton")));
      driver().findElement(By.id("headerHomeButton")).click();
      assertFalse(isElementPresent(By.id("headerHomeButton")));
      assertRegExp(".*/catroid/index(/[0-9]+)?", driver().getCurrentUrl());
    } catch(AssertionError e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    }
  }
}
