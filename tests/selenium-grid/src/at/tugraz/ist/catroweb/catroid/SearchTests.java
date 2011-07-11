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

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import java.util.HashMap;
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

      assertFalse(session().isVisible("headerSearchBox"));
      session().click("headerSearchButton");
      assertTrue(session().isVisible("headerSearchBox"));

      session().type("searchQuery", projectTitle);
      // session().click("xpath=//input[@class='webHeadSearchSubmit']");
      session().click("webHeadSearchSubmit");
      ajaxWait();

      assertFalse(session().isVisible("fewerProjects"));
      assertFalse(session().isVisible("moreProjects"));
      assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(session().isTextPresent(projectTitle));

      // test description
      session().type("searchQuery", projectDescription);
      // session().click("xpath=//input[@class='webHeadSearchSubmit']");
      session().click("webHeadSearchSubmit");
      ajaxWait();
      waitForTextPresent(projectTitle);
      assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));

      assertFalse(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));
      assertTrue(session().isTextPresent(projectTitle));
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
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, CommonData.getRandomLongString(200), "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "",
          "", "", "0"));

      openLocation();
      ajaxWait();

      for(int i = projectTitle.length() - specialchars.length(); i < projectTitle.length(); i++) {
        session().click("headerSearchButton");
        session().type("searchQuery", projectPrefix + projectTitle.substring(projectPrefix.length(), i + 1));
        // session().click("xpath=//input[@class='webHeadSearchSubmit']");
        session().click("webHeadSearchSubmit");
        ajaxWait();
        waitForTextPresent(projectTitle);

        assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
        assertTrue(session().isTextPresent(projectTitle));
        assertFalse(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

        session().type("searchQuery", CommonData.getRandomShortString(10));
        // session().click("xpath=//input[@class='webHeadSearchSubmit']");
        session().click("webHeadSearchSubmit");
        ajaxWait();

        assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
        assertFalse(session().isTextPresent(projectTitle));
        assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));
      }
    } catch(AssertionError e) {
      captureScreen("SearchTests.specialChars");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.specialChars");
      throw e;
    }
  }

  @Test(groups = { "visibility", "upload" }, description = "TODO! when bugfix available")
  public void checkButtonVisibility() throws Throwable {
    try {
      /*
       * 
       * assertFalse(session().isVisible("fewerProjects"));
       * assertFalse(session().isVisible("moreProjects"));
       */
      // TODO, when bugfix available
      /*
       * session().click("headerCancelButton"); ajaxWait();
       * assertFalse(session().isTextPresent
       * (CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
       * assertTrue(session().isTextPresent
       * (CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
       * 
       * session().click("headerSearchButton"); session().type("searchQuery",
       * projectTitle);
       * session().click("xpath=//input[@class='webHeadSearchSubmit']");
       * ajaxWait();
       */
    } catch(AssertionError e) {
      captureScreen("SearchTests.checkButtonVisibility");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.checkButtonVisibility");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "search test with page navigation")
  public void pageNavigation() throws Throwable {
    try {
      String projectTitle = CommonData.getRandomShortString(10);

      int uploadCount = Config.PROJECT_PAGE_LOAD_MAX_PROJECTS * (Config.PROJECT_PAGE_SHOW_MAX_PAGES + 1);

      System.out.println("*** NOTICE *** Uploading " + uploadCount + " projects");
      for(int i = 0; i < uploadCount; i++) {
        projectUploader.upload(CommonData.getUploadPayload(projectTitle + i, "pagenavigationtest", "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "", "", "",
            "0"));
      }
      openLocation();
      ajaxWait();

      session().click("headerSearchButton");
      session().type("searchQuery", projectTitle);
      // session().click("xpath=//input[@class='webHeadSearchSubmit']");
      session().click("webHeadSearchSubmit");
      ajaxWait();

      int i = 0;
      for(i = 0; i < Config.PROJECT_PAGE_SHOW_MAX_PAGES; i++) {
        session().click("moreProjects");
        ajaxWait();
        assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (i + 2) + "", session()
            .getTitle());
      }

      assertTrue(session().isVisible("fewerProjects"));
      assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_PREV_BUTTON));
      session().click("fewerProjects");
      ajaxWait();
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (i) + "", session()
          .getTitle());

      assertFalse(session().isVisible("fewerProjects"));
      assertTrue(session().isVisible("moreProjects"));

      // test session
      session().refresh();
      waitForPageToLoad();
      ajaxWait();
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (i) + "", session()
          .getTitle());

      assertTrue(session().isVisible("fewerProjects"));
      assertTrue(session().isVisible("moreProjects"));
      // test links to details page
      session().click("xpath=//a[@class='projectListDetailsLink'][1]");
      waitForPageToLoad();
      assertRegExp(".*/catroid/details.*", session().getLocation());
      session().goBack();
      waitForPageToLoad();
      ajaxWait();
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (i) + "", session()
          .getTitle());

      assertTrue(session().isVisible("fewerProjects"));
      assertTrue(session().isVisible("moreProjects"));
      // test header click
      session().click("aIndexWebLogoLeft");
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (1) + "$", session().getTitle());
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
      String projectTitle = "search_identical"; //+ CommonData.getRandomShortString(10);
      String projectTitle1 = projectTitle + "_1";
      String projectTitle2 = projectTitle + "_2";

      projectUploader.upload(CommonData.getUploadPayload(projectTitle1, "identical_search_project_2", "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "", "", "", "0"));
      openLocation();
      ajaxWait();

      session().click("headerSearchButton");
      session().type("searchQuery", projectTitle);
      session().click("webHeadSearchSubmit");
      ajaxWait();

      waitForTextPresent(projectTitle1);
      assertFalse(session().isTextPresent(projectTitle2));
      
      projectUploader.upload(CommonData.getUploadPayload(projectTitle2, "identical_search_project_2", "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "", "",
          "", "0"));
      session().refresh();
      session().click("webHeadSearchSubmit");
      ajaxWait();
      
      waitForTextPresent(projectTitle1);
      assertTrue(session().isTextPresent(projectTitle2));
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
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, "some search project", "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "", "", "", "0"));
      String projectID = projectUploader.getProjectId(projectTitle);

      openLocation();
      ajaxWait();

      // hide project
      openAdminLocation("/tools/editProjects");
      session().click("toggle" + projectID);
      session().getConfirmation();
      waitForPageToLoad();

      openLocation();
      ajaxWait();
      session().click("headerSearchButton");
      session().type("searchQuery", projectTitle);
      // session().click("xpath=//input[@class='webHeadSearchSubmit']");
      session().click("webHeadSearchSubmit");
      ajaxWait();

      assertFalse(session().isTextPresent(projectTitle));
      assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

      session().click("aIndexWebLogoLeft");
      ajaxWait();
      assertFalse(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

      // unhide project
      openAdminLocation("/tools/editProjects");
      session().click("toggle" + projectID);
      session().getConfirmation();
      waitForPageToLoad();

      openLocation();
      ajaxWait();
      session().click("headerSearchButton");
      session().type("searchQuery", projectTitle);
      // session().click("xpath=//input[@class='webHeadSearchSubmit']");
      session().click("webHeadSearchSubmit");
      ajaxWait();

      assertTrue(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      assertTrue(session().isTextPresent(projectTitle));
      assertFalse(session().isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

      assertFalse(session().isVisible("fewerProjects"));
      assertFalse(session().isVisible("moreProjects"));
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
            "long_description_" + CommonData.getRandomLongString(Config.PROJECT_SHORT_DESCRIPTION_MAX_LENGTH), "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d",
            "", "", "", "0") },
        { CommonData.getUploadPayload("search_test_" + CommonData.getRandomShortString(10), CommonData.getRandomShortString(10), "test.zip",
            "2c2d13d52cf670ea55b2014b336d1b4d", "", "", "", "0") }, };
    return returnArray;
  }
}
