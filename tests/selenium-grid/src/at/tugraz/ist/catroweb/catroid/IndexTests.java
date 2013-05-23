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
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "IndexTests" })
public class IndexTests extends BaseTest {

  @Test(groups = { "visibility", "popupwindows" }, description = "click download,header,details -links ")
  public void index() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Pocket Code Website.*"));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));

      clickLastVisibleProject();
      ajaxWait();
      assertRegExp(".*/details/[0-9]+", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));

      // test home link
      driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();
    } catch(AssertionError e) {
      captureScreen("IndexTests.index");
      throw e;
    } catch(Exception e) {
      captureScreen("IndexTests.index");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "page navigation tests")
  public void pageNavigation() throws Throwable {
    try {
      String projectTitle = CommonData.getRandomShortString(10);
      openLocation();
      ajaxWait();

      int uploadCount = Config.PROJECT_PAGE_LOAD_MAX_PROJECTS * (Config.PROJECT_PAGE_SHOW_MAX_PAGES + 1);
      System.out.println("*** NOTICE *** Uploading " + uploadCount + " projects");
      for(int i = 0; i < uploadCount; i++) {
        projectUploader.upload(CommonData.getUploadPayload(projectTitle + i, "pagenavigationtest", "", "", "", "", "", ""));
        if((i%Config.PROJECT_PAGE_LOAD_MAX_PROJECTS) == 0) {
          driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();          
        }
      }
      
      driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//*[@id='newestProjects']/ul/li/a[@title=\"" + projectTitle + "0\"]")));

      assertTrue(isVisible(By.id("newestShowMore")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
      int pageNr = 0;
      for(; pageNr < Config.PROJECT_PAGE_SHOW_MAX_PAGES; pageNr++) {
        driver().findElement(By.id("newestShowMore")).click();
        ajaxWait();
      }
      assertTrue(isElementPresent(By.xpath("//*[@id='newestProjects']/ul/li/a[@title=\"" + projectTitle + "0\"]")));

      // test session
      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//*[@id='newestProjects']/ul/li/a[@title=\"" + projectTitle + "0\"]")));

      // test links to details page
      driver().findElement(By.xpath("//*[@id='newestProjects']/ul/li/a")).click();
      ajaxWait();
      assertRegExp(".*/details.*", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//*[@id='newestProjects']/ul/li/a[@title=\"" + projectTitle + "0\"]")));

      driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//*[@id='newestProjects']/ul/li/a[@title=\"" + projectTitle + "0\"]")));
    } catch(AssertionError e) {
      captureScreen("IndexTests.pageNavigation");
      throw e;
    } catch(Exception e) {
      captureScreen("IndexTests.pageNavigation");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "language select tests")
  public void languageSelect() throws Throwable {
    try {
      openLocation("termsOfUse");
      assertTrue(isTextPresent("Terms of Use".toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//html[@lang='" + Config.SITE_DEFAULT_LANGUAGE + "']")));
      openLocation("termsOfUse", false);
      assertTrue(isElementPresent(By.id("switchLanguage")));
      (new Select(driver().findElement(By.id("switchLanguage")))).selectByValue("de");
      ajaxWait();
      assertTrue(isTextPresent("Nutzungsbedingungen".toUpperCase()));
      assertTrue(isElementPresent(By.id("switchLanguage")));
      assertTrue(isElementPresent(By.xpath("//html[@lang='de']")));
      (new Select(driver().findElement(By.id("switchLanguage")))).selectByValue("en");
      ajaxWait();
      assertTrue(isTextPresent("Terms of Use".toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//html[@lang='en']")));
    } catch(AssertionError e) {
      captureScreen("IndexTests.languageSelect");
      throw e;
    } catch(Exception e) {
      captureScreen("IndexTests.languageSelect");
      throw e;
    }
  }
}
