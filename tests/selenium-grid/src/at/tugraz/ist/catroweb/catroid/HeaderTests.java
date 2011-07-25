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

@Test(groups = { "catroid", "HeaderTests" })
public class HeaderTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void headerMenuButtons() throws Throwable {
    try {
      openLocation();
      assertFalse(isElementPresent(By.id("headerHomeButton")));
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerMenuButton")).click();
      assertTrue(isVisible(By.id("headerHomeButton")));
      assertFalse(isElementPresent(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerHomeButton")).click();
      ajaxWait();
      assertRegExp(".*/catroid/index(/[0-9]+)?", driver().getCurrentUrl());
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));
      assertFalse(isElementPresent(By.id("headerHomeButton")));
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

      assertFalse(isVisible(By.id("headerSearchBox")));
      assertFalse(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("headerSearchButton")));
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerSearchButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("headerSearchBox")));
      assertTrue(isVisible(By.id("headerCancelButton")));
      assertFalse(isVisible(By.id("headerSearchButton")));
      assertFalse(isVisible(By.id("headerMenuButton")));
      assertFalse(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerCancelButton")).click();
      ajaxWait();
      assertFalse(isVisible(By.id("headerSearchBox")));
      assertFalse(isVisible(By.id("headerCancelButton")));
      assertTrue(isVisible(By.id("headerSearchButton")));
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());
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

      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerSearchButton")));
      assertFalse(isElementPresent(By.id("headerHomeButton")));

      driver().findElement(By.xpath("//a[@class='license'][4]")).click();
      assertTrue(isVisible(By.id("headerHomeButton")));
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));
      assertFalse(isElementPresent(By.id("headerSearchButton")));

      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerSearchButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));
      assertFalse(isElementPresent(By.id("headerHomeButton")));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    }
  }
}
