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
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("headerMenuButton")).click();
      assertTrue(isVisible(By.id("aIndexWebLogoLeft")));
      assertFalse(isElementPresent(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();
      assertRegExp(".*/catroid/index(/[0-9]+)?", driver().getCurrentUrl());
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));
      
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

      assertTrue(isVisible(By.id("headerSearchBox")));
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

      driver().findElement(By.xpath("//a[@class='license'][4]")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));

      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("headerMenuButton")));
      assertTrue(isVisible(By.id("headerProfileButton")));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerHomeButton");
      throw e;
    }
  }
}
