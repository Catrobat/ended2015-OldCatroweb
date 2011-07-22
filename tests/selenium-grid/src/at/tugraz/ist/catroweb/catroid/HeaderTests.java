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

@Test(groups = { "catroid", "HeaderTests" })
public class HeaderTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void headerMenuButtons() throws Throwable {
    try {
      openLocation();
      assertFalse(selenium().isElementPresent("headerHomeButton"));
      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));

      selenium().click("headerMenuButton");
      waitForPageToLoad();
      assertTrue(selenium().isVisible("headerHomeButton"));
      assertFalse(selenium().isElementPresent("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));

      selenium().click("headerHomeButton");
      waitForPageToLoad();
      ajaxWait();
      assertRegExp(".*/catroid/index(/[0-9]+)?", selenium().getLocation());
      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));
      assertFalse(selenium().isElementPresent("headerHomeButton"));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerMenuButtons");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerMenuButtons");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check header buttons, search bar visibility, etc.")
  public void headerButtonsIndex() throws Throwable {
    try {
      openLocation();
      ajaxWait();

      assertFalse(selenium().isVisible("headerSearchBox"));
      assertFalse(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("headerSearchButton"));
      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));

      selenium().click("headerSearchButton");
      ajaxWait();
      assertTrue(selenium().isVisible("headerSearchBox"));
      assertTrue(selenium().isVisible("headerCancelButton"));
      assertFalse(selenium().isVisible("headerSearchButton"));
      assertFalse(selenium().isVisible("headerMenuButton"));
      assertFalse(selenium().isVisible("headerProfileButton"));

      selenium().click("headerCancelButton");
      ajaxWait();
      assertFalse(selenium().isVisible("headerSearchBox"));
      assertFalse(selenium().isVisible("headerCancelButton"));
      assertTrue(selenium().isVisible("headerSearchButton"));
      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));

      selenium().click("headerMenuButton");
      waitForPageToLoad();
      assertRegExp(".*/catroid/menu$", selenium().getLocation());
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerButtonsIndex");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerButtonsIndex");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "home button: check button visibility")
  public void headerHomeButton() throws Throwable {
    try {
      openLocation();

      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerSearchButton"));
      assertFalse(selenium().isElementPresent("headerHomeButton"));

      selenium().click("xpath=//a[@class='license'][4]");
      waitForPageToLoad();
      Thread.sleep(500);
      assertTrue(selenium().isVisible("headerHomeButton"));
      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));
      assertFalse(selenium().isElementPresent("headerSearchButton"));

      selenium().click("aIndexWebLogoLeft");
      waitForPageToLoad();
      ajaxWait();

      assertTrue(selenium().isVisible("headerMenuButton"));
      assertTrue(selenium().isVisible("headerSearchButton"));
      assertTrue(selenium().isVisible("headerProfileButton"));
      assertFalse(selenium().isElementPresent("headerHomeButton"));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    }
  }
}
