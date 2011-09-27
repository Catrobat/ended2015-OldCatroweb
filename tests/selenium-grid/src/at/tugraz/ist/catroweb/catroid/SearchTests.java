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

import java.util.HashMap;

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "SearchTests" })
public class SearchTests extends BaseTest {

  @Test(dataProvider = "randomProjects", groups = { "functionality", "upload" }, description = "search for random title and description")
  public void titleAndDescription(HashMap<String, String> dataset) throws Throwable {
    try {
      projectUploader.upload(dataset);

      String projectTitle = dataset.get("projectTitle");
      String projectDescription = dataset.get("projectDescription");

      openLocation();
      ajaxWait();

      assertFalse(isVisible(By.id("headerSearchBox")));
      driver().findElement(By.id("headerSearchButton")).click();
      assertTrue(isVisible(By.id("headerSearchBox")));

      driver().findElement(By.id("searchQuery")).sendKeys(projectTitle);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      assertFalse(isVisible(By.id("fewerProjects")));
      assertFalse(isVisible(By.id("moreProjects")));
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(isTextPresent(projectTitle));

      // test description
      driver().findElement(By.id("searchQuery")).clear();
      driver().findElement(By.id("searchQuery")).sendKeys(projectDescription);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(isTextPresent(projectTitle));

      assertFalse(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));
      assertTrue(isTextPresent(projectTitle));
    } catch(AssertionError e) {
      captureScreen("SearchTests.titleAndDescription." + dataset.get("projectTitle"));
      log(dataset.get("projectTitle"));
      log(dataset.get("projectDescription"));
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.titleAndDescription." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(dataProvider = "specialChars", groups = { "functionality", "upload" }, description = "search forspecial chars")
  public void specialChars(String specialchars) throws Throwable {
    try {
      String projectPrefix = "searchtest";
      String projectTitle = projectPrefix + specialchars;
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, CommonData.getRandomLongString(200), "", "", "", "", "0"));

      openLocation();
      ajaxWait();
      driver().findElement(By.id("headerSearchButton")).click();
      
      for(int i = projectTitle.length() - specialchars.length(); i < projectTitle.length(); i++) {
        driver().findElement(By.id("searchQuery")).clear();
        driver().findElement(By.id("searchQuery")).sendKeys(projectPrefix + projectTitle.substring(projectPrefix.length(), i + 1));
        driver().findElement(By.id("webHeadSearchSubmit")).click();
        ajaxWait();
        assertTrue(isTextPresent(projectTitle));

        driver().findElement(By.id("searchQuery")).clear();
        driver().findElement(By.id("searchQuery")).sendKeys(CommonData.getRandomShortString(10));
        driver().findElement(By.id("webHeadSearchSubmit")).click();
        ajaxWait();
      }
    } catch(AssertionError e) {
      captureScreen("SearchTests.specialChars");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.specialChars");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "search test with page navigation")
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

      driver().findElement(By.id("headerSearchButton")).click();
      ajaxWait();
      driver().findElement(By.id("searchQuery")).clear();
      driver().findElement(By.id("searchQuery")).sendKeys(projectTitle);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      int pageNr = 0;
      for(; pageNr < Config.PROJECT_PAGE_SHOW_MAX_PAGES; pageNr++) {
        driver().findElement(By.id("moreProjects")).click();
        ajaxWait();
        assertRegExp(".*p=" + (pageNr + 2) + ".*", driver().getCurrentUrl());
      }

      assertTrue(isVisible(By.id("fewerProjects")));
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_PREV_BUTTON));
      driver().findElement(By.id("fewerProjects")).click();
      ajaxWait();
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (pageNr) + "", driver().getTitle());

      assertTrue(isVisible(By.id("moreProjects")));

      // test session
      openLocation("catroid/search/?q=" + projectTitle + "&p=" + String.valueOf(pageNr));
      ajaxWait();

      assertRegExp(".*p=" + (pageNr) + ".*", driver().getCurrentUrl());

      assertTrue(isVisible(By.id("fewerProjects")));
      assertTrue(isVisible(By.id("moreProjects")));
      // test links to details page
      driver().findElement(By.xpath("//a[@class='projectListDetailsLink'][1]")).click();
      assertRegExp(".*/catroid/details.*", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (pageNr) + "", driver().getTitle());

      assertTrue(isVisible(By.id("fewerProjects")));
      assertTrue(isVisible(By.id("moreProjects")));

      // test header click
      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (1) + "$", driver().getTitle());
    } catch(AssertionError e) {
      captureScreen("SearchTests.pageNavigation");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.pageNavigation");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "search project, upload project with identical name, reload")
  public void identicalSearchQuery() throws Throwable {
    try {
      String projectTitle = "search_identical"; // +
                                                // CommonData.getRandomShortString(10);
      String projectTitle1 = projectTitle + "_1";
      String projectTitle2 = projectTitle + "_2";

      projectUploader.upload(CommonData.getUploadPayload(projectTitle1, "identical_search_project_2", "", "", "", "", "0"));
      openLocation();
      ajaxWait();

      driver().findElement(By.id("headerSearchButton")).click();
      driver().findElement(By.id("searchQuery")).clear();
      driver().findElement(By.id("searchQuery")).sendKeys(projectTitle);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(projectTitle1));
      assertFalse(isTextPresent(projectTitle2));

      projectUploader.upload(CommonData.getUploadPayload(projectTitle2, "identical_search_project_2", "", "", "", "", "0"));
      driver().navigate().refresh();
      ajaxWait();
      driver().findElement(By.id("webHeadSearchSubmit")).click();

      assertTrue(isTextPresent(projectTitle1));
      assertTrue(isTextPresent(projectTitle2));
    } catch(AssertionError e) {
      captureScreen("SearchTests.identicalSearchQuery");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.searchAndHideProjectidenticalSearchQuery");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "search and hide project")
  public void searchAndHideProject() throws Throwable {
    try {
      String projectTitle = "search_test_" + CommonData.getRandomShortString(10);
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, "some search project", "", "", "", "", "0"));
      String projectID = projectUploader.getProjectId(projectTitle);

      openLocation();
      ajaxWait();

      // hide project
      openAdminLocation("/tools/editProjects");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectID)).click();
      ajaxWait();

      openLocation();
      ajaxWait();
      driver().findElement(By.id("headerSearchButton")).click();
      driver().findElement(By.id("searchQuery")).clear();
      driver().findElement(By.id("searchQuery")).sendKeys(projectTitle);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      assertFalse(isTextPresent(projectTitle));
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

      driver().findElement(By.id("aIndexWebLogoLeft")).click();
      ajaxWait();
      assertFalse(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

      // unhide project
      openAdminLocation("/tools/editProjects");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectID)).click();
      ajaxWait();

      openLocation();
      ajaxWait();
      driver().findElement(By.id("headerSearchButton")).click();
      driver().findElement(By.id("searchQuery")).clear();
      driver().findElement(By.id("searchQuery")).sendKeys(projectTitle);
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(isTextPresent(projectTitle));
      assertFalse(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

      assertFalse(isVisible(By.id("fewerProjects")));
      assertFalse(isVisible(By.id("moreProjects")));
    } catch(AssertionError e) {
      captureScreen("SearchTests.searchAndHideProject");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.searchAndHideProject");
      throw e;
    }
  }

  @DataProvider(name = "specialChars")
  public Object[][] specialChars() {
    Object[][] returnArray = new Object[][] { { "_äöü\"$%&/=?`+*~#-.:,;|" }, };
    return returnArray;
  }

  @DataProvider(name = "randomProjects")
  public Object[][] randomProjects() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("search_test_long_description_" + CommonData.getRandomShortString(10),
            "long_description_" + CommonData.getRandomLongString(Config.PROJECT_SHORT_DESCRIPTION_MAX_LENGTH), "", "", "", "", "0") },
        { CommonData.getUploadPayload("search_test_" + CommonData.getRandomShortString(10), CommonData.getRandomShortString(10), "", "", "", "", "0") }, };
    return returnArray;
  }
}
