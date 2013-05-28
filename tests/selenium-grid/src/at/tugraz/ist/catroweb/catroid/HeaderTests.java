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
import org.openqa.selenium.Keys;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.CommonData;
import at.tugraz.ist.catroweb.common.CommonStrings;

@Test(groups = { "catroid", "HeaderTests" })
public class HeaderTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void headerButtons() throws Throwable {
    try {
      openLocation();
    
      assertTrue(isVisible(By.id("largeMenu")));
      assertTrue(isVisible(By.xpath("//*[@id='largeMenu']/div[2]/a")));
      assertTrue(isVisible(By.id("largeSearchButton")));
      assertTrue(isVisible(By.xpath("//*[@id='largeMenu']/div[4]/input")));
      assertTrue(isVisible(By.id("largeMenuButton")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));

      driver().findElement(By.id("largeMenuButton")).click();
      
      assertRegExp(".*/login", driver().getCurrentUrl());
      driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));

      driver().findElement(By.xpath("//*[@id='largeMenu']/div[4]/input")).sendKeys("test");
      driver().findElement(By.id("largeSearchButton")).click();
      assertRegExp(".*/search/.*", driver().getCurrentUrl());

      driver().findElement(By.id("largeMenuButton")).click();

      driver().findElement(By.id("loginUsername")).sendKeys(CommonData.getLoginUserDefault());
      driver().findElement(By.id("loginPassword")).sendKeys(CommonData.getLoginPasswordDefault());

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      
      assertTrue(containsElementText(By.xpath("//*[@id='largeMenuButton']/button[2]"), CommonData.getLoginUserDefault()));
      driver().findElement(By.id("largeMenuButton")).click();

      assertTrue(isVisible(By.id("menuProfileButton")));
      assertTrue(isVisible(By.id("menuLogoutButton")));

      driver().findElement(By.id("menuProfileButton")).click();
      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/header"), CommonData.getLoginUserDefault().toUpperCase()));

      driver().findElement(By.id("largeMenuButton")).click();
      driver().findElement(By.id("menuLogoutButton")).click();

      driver().findElement(By.id("largeMenuButton")).click();
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/div[1]"), "Login".toUpperCase()));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerMenuButtons");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerMenuButtons");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void headerButtonsMobile() throws Throwable {
    try {
      openMobileLocation();
      
      assertTrue(isVisible(By.id("smallMenuBar")));
      assertTrue(isVisible(By.xpath("//*[@id='smallMenuBar']/a")));
      assertFalse(isVisible(By.id("smallSearchBar")));
      assertTrue(isVisible(By.id("mobileSearchButton")));
      assertTrue(isVisible(By.id("mobileMenuButton")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));
      
      driver().findElement(By.id("mobileMenuButton")).click();
      
      assertRegExp(".*/login", driver().getCurrentUrl());
      driver().findElement(By.xpath("//*[@id='smallMenuBar']/a")).click();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));
      
      driver().findElement(By.id("mobileSearchButton")).click();
      assertTrue(isVisible(By.id("smallSearchBar")));
      driver().findElement(By.id("mobileSearchButton")).click();
      assertFalse(isVisible(By.id("smallSearchBar")));
      driver().findElement(By.id("mobileSearchButton")).click();

      driver().findElement(By.xpath("//*[@id='smallSearchBar']/input")).sendKeys("test");
      driver().findElement(By.xpath("//*[@id='smallSearchBar']/input")).sendKeys(Keys.RETURN);
      assertRegExp(".*/search/.*", driver().getCurrentUrl());
      
      driver().findElement(By.id("mobileMenuButton")).click();
      
      driver().findElement(By.id("loginUsername")).sendKeys(CommonData.getLoginUserDefault());
      driver().findElement(By.id("loginPassword")).sendKeys(CommonData.getLoginPasswordDefault());
      
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      
      driver().findElement(By.id("mobileMenuButton")).click();
      
      assertTrue(isVisible(By.id("menuProfileButton")));
      assertTrue(isVisible(By.id("menuLogoutButton")));
      
      driver().findElement(By.id("menuProfileButton")).click();
      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/header"), CommonData.getLoginUserDefault().toUpperCase()));
      
      driver().findElement(By.id("mobileMenuButton")).click();
      driver().findElement(By.id("menuLogoutButton")).click();
      
      driver().findElement(By.id("mobileMenuButton")).click();
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/div[1]"), "Login".toUpperCase()));
    } catch(AssertionError e) {
      captureScreen("HeaderTests.headerButtonsMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("HeaderTests.headerButtonsMobile");
      throw e;
    }
  }
}
