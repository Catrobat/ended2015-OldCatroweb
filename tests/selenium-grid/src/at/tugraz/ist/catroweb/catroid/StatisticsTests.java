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
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "StatisticsTests" })
public class StatisticsTests extends BaseTest {

  @Test(groups = { "functionality"}, description = "load more projects on statistics page")
  public void moreProjects() throws Throwable {
    try {
      openLocation("statistics/userStats");
      
      assertTrue(isTextPresent("User Statistics".toUpperCase()));
      
      By loadProjects = By.id("loadProjects");
      assertTrue(isElementPresent(loadProjects));
      
      By results = By.id("results");
      assertTrue(isElementPresent(results));
      
      
      String resultString = driver().findElement(results).getText();
      driver().findElement(loadProjects).click();
      ajaxWait();
      assertNotSame(resultString, driver().findElement(results).getText());
      
      try {
        while(driver().findElement(loadProjects).isEnabled()) {
          resultString = driver().findElement(results).getText();
          driver().findElement(loadProjects).click();
          ajaxWait();
          assertNotSame(resultString, driver().findElement(results).getText());
        }
      } catch(NoSuchElementException ignore) {
      }
      assertTrue(isTextPresent("No more projects!"));
      
    } catch(AssertionError e) {
      captureScreen("StatisticsTests.moreProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("StatisticsTests.moreProjects");
      throw e;
    }
  }
}
