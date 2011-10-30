/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "IndexTests" })
public class IndexTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "information box, description and screenshot")
  public void infoBox() throws Throwable {
    openLocation();
    ajaxWait();

    assertTrue(isVisible(By.id("catroidDescription")));
    assertTrue(isTextPresent("Visual Programming Language"));
    assertTrue(isTextPresent("Catroid is an on-device graphical programming language for Android devices that is inspired by the Scratch programming language for PCs, developed by the Lifelong Kindergarten Group at the MIT Media Lab. It is the aim of the Catroid project to facilitate the learning of programming skills among children and users of all ages."));

    // test catroid download link
    assertTrue(isElementPresent(By.id("aIndexInfoboxDownloadButton")));
    clickAndWaitForPopUp(By.id("aIndexInfoboxDownloadButton"));
    assertTrue(isTextPresent("Catroid_0-4-3d.apk"));
    assertTrue(isTextPresent("Paintroid_0.6.4b.apk"));
    closePopUp();
    
    // test screenshot link
    assertTrue(isElementPresent(By.id("aIndexInfoboxScreenshotLink")));
    clickAndWaitForPopUp(By.id("aIndexInfoboxScreenshotLink"));
    assertTrue(isTextPresent("Catroid release 4"));
    closePopUp();

    driver().findElement(By.id("catroidDescriptionCloseButton")).click();
    ajaxWait();
    assertFalse(isVisible(By.id("catroidDescription")));
  }
  
  @Test(groups = { "visibility" }, description = "location tests")
  public void location() throws Throwable {
    String longPageNr = "99999999";
    try {
      openLocation("catroid/index/" + longPageNr);
      ajaxWait();

      assertTrue(isElementPresent(By.id("projectListTitle")));
      assertTrue(isVisible(By.id("projectListTitle")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertTrue(driver().getTitle().matches("^Catroid Website -.*"));
      // random page nr should redirect to last page
      if(CommonFunctions.getProjectsCount(true) > Config.PROJECT_PAGE_LOAD_MAX_PROJECTS * Config.PROJECT_PAGE_SHOW_MAX_PAGES) {
        assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));
        assertFalse(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
      }

      // random string instead of page nr should redirect to first page
      String location = CommonData.getRandomLongString(20);
      openLocation("catroid/index/" + location);
      ajaxWait();

      assertTrue(isElementPresent(By.id("projectListTitle")));
      assertTrue(isVisible(By.id("projectListTitle")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertFalse(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));
      assertTrue(driver().getTitle().matches("^Catroid Website -.*"));

      location = CommonData.getRandomLongString(200);
      openLocation("catroid/details/" + location);
      ajaxWait();
      // test page title and header title
      assertRegExp(".*/catroid/errorPage", driver().getCurrentUrl());
      assertTrue(isTextPresent(location));

      // random string instead of page nr should redirect to first page
      openLocation("catroid/search/?q=test&p=" + CommonData.getRandomShortString(10));
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertRegExp(".*/catroid/search/[?]q=test[&]p=1.*", driver().getCurrentUrl());

      openLocation("catroid/profile");
      ajaxWait();
      assertRegExp(".*/catroid/login.*", driver().getCurrentUrl());
    } catch(AssertionError e) {
      captureScreen("IndexTests.location");
      throw e;
    } catch(Exception e) {
      captureScreen("IndexTests.location");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "click download,header,details -links ")
  public void index() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Catroid Website.*"));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

      // test catroid header text
      assertTrue(isElementPresent(By.xpath("//img[@class='catroidLettering']")));
      // test logo link
      assertTrue(isElementPresent(By.xpath("//div[@class='webHeadLogo']")));
      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();

      clickLastVisibleProject();
      assertRegExp(".*/catroid/details/[0-9]+", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertTrue(isElementPresent(By.id("aIndexWebLogoMiddle")));

      // test home link
      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//img[@class='catroidLettering']")));
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
        projectUploader.upload(CommonData.getUploadPayload(projectTitle + i, "pagenavigationtest", "", "", "", "", "0"));
        if((i%5) == 0) {
          driver().findElement(By.id("aIndexWebLogoLeft")).click();          
        }
      }
      
      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();

      assertFalse(isVisible(By.id("fewerProjects")));
      assertTrue(isVisible(By.id("moreProjects")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
      int pageNr = 0;
      for(; pageNr < Config.PROJECT_PAGE_SHOW_MAX_PAGES; pageNr++) {
        driver().findElement(By.id("moreProjects")).click();
        ajaxWait();
        assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (pageNr + 2) + "$", driver().getTitle());
      }

      assertTrue(isVisible(By.id("fewerProjects")));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));
      driver().findElement(By.id("fewerProjects")).click();
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (pageNr) + "$", driver().getTitle());

      // test session
      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (pageNr) + "$", driver().getTitle());

      // test links to details page
      driver().findElement(By.xpath("//a[@class='projectListDetailsLink']")).click();
      assertRegExp(".*/catroid/details.*", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (pageNr) + "$", driver().getTitle());

      // test header click
      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (1) + "$", driver().getTitle());
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
      openLocation("catroid/imprint/");
      assertTrue(isTextPresent("Graz University of Technology"));
      assertTrue(isElementPresent(By.xpath("//html[@lang='" + Config.SITE_DEFAULT_LANGUAGE + "']")));
      openLocation("catroid/imprint/", false);
      assertTrue(isElementPresent(By.id("switchLanguage")));
      (new Select(driver().findElement(By.id("switchLanguage")))).selectByValue("de");
      ajaxWait();
      assertTrue(isTextPresent("Technische Universität Graz"));
      assertTrue(isElementPresent(By.id("switchLanguage")));
      assertTrue(isElementPresent(By.xpath("//html[@lang='de']")));
      (new Select(driver().findElement(By.id("switchLanguage")))).selectByValue("en");
      ajaxWait();
      assertTrue(isTextPresent("Graz University of Technology"));
      assertTrue(isElementPresent(By.xpath("//html[@lang='en']")));
    } catch(AssertionError e) {
      captureScreen("IndexTests.pageNavigation");
      throw e;
    } catch(Exception e) {
      captureScreen("IndexTests.pageNavigation");
      throw e;
    }
  }
}
