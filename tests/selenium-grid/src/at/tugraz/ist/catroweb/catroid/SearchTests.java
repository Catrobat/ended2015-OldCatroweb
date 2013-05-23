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

import java.util.HashMap;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
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

      openLocation("search/?q=" + projectTitle + "&p=1");
      ajaxWait();

      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "\"]")));

      // test description
      driver().findElement(By.xpath("//*[@id='largeMenu']/div[4]/input")).clear();
      driver().findElement(By.xpath("//*[@id='largeMenu']/div[4]/input")).sendKeys(projectDescription);
      driver().findElement(By.id("largeSearchButton")).click();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "\"]")));
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


  @Test(groups = { "functionality" }, description = "checks all search boxes and buttons")
  public void searchBoxesAndButton() throws Throwable {
    try {
      String projectTitleA = "searchFunctionalityA" + CommonData.getRandomShortString(22);
      projectUploader.upload(CommonData.getUploadPayload(projectTitleA, CommonData.getRandomLongString(200), "", "", "", "", "", ""));
      String projectTitleB = "searchFunctionalityA" + CommonData.getRandomShortString(22);
      projectUploader.upload(CommonData.getUploadPayload(projectTitleB, CommonData.getRandomLongString(200), "", "", "", "", "", ""));

      By largeTopSearchBox = By.xpath("//*[@id='largeMenu']/div[4]/input");
      By largeFooterSearchBox = By.xpath("//*[@id='largeFooterMenu']/div[2]/span[2]/input");
      By mobileSearchBox = By.xpath("//*[@id='smallSearchBar']/input");

      // large layout search bar on the top
      openLocation();
      assertTrue(isVisible(By.id("largeSearchButton")));
      assertTrue(isVisible(largeTopSearchBox));
      
      driver().findElement(largeTopSearchBox).sendKeys(projectTitleA);
      driver().findElement(largeTopSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));

      driver().findElement(largeTopSearchBox).clear();
      driver().findElement(largeTopSearchBox).sendKeys(projectTitleB);
      driver().findElement(largeTopSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
      
      openLocation();
      driver().findElement(largeTopSearchBox).sendKeys(projectTitleA);
      driver().findElement(By.id("largeSearchButton")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));

      driver().findElement(largeTopSearchBox).clear();
      driver().findElement(largeTopSearchBox).sendKeys(projectTitleB);
      driver().findElement(By.id("largeSearchButton")).click();
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
      
      // large layout search bar on the bottom
      openLocation();
      assertTrue(isVisible(By.id("footerSearchButton")));
      assertTrue(isVisible(largeFooterSearchBox));
      
      driver().findElement(largeFooterSearchBox).sendKeys(projectTitleA);
      driver().findElement(largeFooterSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
      
      driver().findElement(largeFooterSearchBox).clear();
      driver().findElement(largeFooterSearchBox).sendKeys(projectTitleB);
      driver().findElement(largeFooterSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
      
      openLocation();
      driver().findElement(largeFooterSearchBox).sendKeys(projectTitleA);
      driver().findElement(By.id("footerSearchButton")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      
      driver().findElement(largeFooterSearchBox).clear();
      driver().findElement(largeFooterSearchBox).sendKeys(projectTitleB);
      driver().findElement(By.id("footerSearchButton")).click();
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));

      // mobile layout search bar on the top
      openMobileLocation();
      assertTrue(isVisible(By.id("mobileSearchButton")));
      assertFalse(isVisible(mobileSearchBox));
      
      driver().findElement(By.id("mobileSearchButton")).click();
      driver().findElement(mobileSearchBox).sendKeys(projectTitleA);
      driver().findElement(mobileSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
      
      driver().findElement(mobileSearchBox).clear();
      driver().findElement(mobileSearchBox).sendKeys(projectTitleB);
      driver().findElement(mobileSearchBox).sendKeys(Keys.RETURN);
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitleA + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitleB + "\"]")));
    } catch(AssertionError e) {
      captureScreen("SearchTests.searchBoxesAndButton");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.searchBoxesAndButton");
      throw e;
    }
  }

  @Test(dataProvider = "specialChars", groups = { "functionality", "upload" }, description = "search forspecial chars")
  public void specialChars(String specialchars) throws Throwable {
    try {
      String projectPrefix = "searchtest" + CommonData.getRandomShortString(10);
      String projectTitle = projectPrefix + specialchars;
      String htmlProjectTitle = projectTitle.replace("&", "&amp;").replace("\"", "&quot;");
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, CommonData.getRandomLongString(200), "", "", "", "", "", ""));

      openLocation();
      ajaxWait();
      
      By searchBox = By.xpath("//*[@id='largeMenu']/div[4]/input");
      for(int i = projectTitle.length() - specialchars.length(); i < projectTitle.length(); i++) {
        driver().findElement(searchBox).clear();
        driver().findElement(searchBox).sendKeys(projectPrefix + projectTitle.substring(projectPrefix.length(), i + 1));
        driver().findElement(By.id("largeSearchButton")).click();
        ajaxWait();
        assertTrue(isElementPresent(By.xpath("//a[@title=\"" + htmlProjectTitle + "\"]")));

        driver().findElement(searchBox).clear();
        driver().findElement(searchBox).sendKeys(CommonData.getRandomShortString(10));
        driver().findElement(By.id("largeSearchButton")).click();
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
        projectUploader.upload(CommonData.getUploadPayload(projectTitle + i, "pagenavigationtest", "", "", "", "", "", ""));
        if((i%Config.PROJECT_PAGE_LOAD_MAX_PROJECTS) == 0) {
          driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();          
        }
      }

      driver().findElement(By.xpath("//*[@id='largeMenu']/div[2]/a")).click();
      ajaxWait();
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "0\"]")));

      By searchBox = By.xpath("//*[@id='largeMenu']/div[4]/input");
      driver().findElement(searchBox).clear();
      driver().findElement(searchBox).sendKeys(projectTitle);
      driver().findElement(By.id("largeSearchButton")).click();
      ajaxWait();

      int pageNr = 0;
      for(; pageNr < Config.PROJECT_PAGE_SHOW_MAX_PAGES; pageNr++) {
        driver().findElement(By.id("moreResults")).click();
        ajaxWait();
        assertRegExp(".*p=" + (pageNr + 2) + ".*", driver().getCurrentUrl());
      }
      pageNr++;
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "0\"]")));

      // test links to details page
      driver().findElement(By.xpath("//*[@id='searchResultContainer']/ul/li/a")).click();
      ajaxWait();
      assertRegExp(".*/details.*", driver().getCurrentUrl());
      driver().navigate().back();
      ajaxWait();
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertRegExp(CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.SEARCH_PROJECTS_PAGE_TITLE + " - " + projectTitle + " - " + (pageNr) + "", driver().getTitle());
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "0\"]")));
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
      String projectTitle = "search_identical"; // + CommonData.getRandomShortString(10);
      String projectTitle1 = projectTitle + "_1";
      String projectTitle2 = projectTitle + "_2";

      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle1, "identical_search_project_2", "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      
      openLocation("/search/?q=" + projectTitle + "&p=1", false);
      ajaxWait();

      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle1 + "\"]")));
      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitle2 + "\"]")));

      projectUploader.upload(CommonData.getUploadPayload(projectTitle2, "identical_search_project_2", "", "", "", "", "", ""));
      driver().navigate().refresh();
      ajaxWait();
      driver().findElement(By.id("largeSearchButton")).click();
      ajaxWait();

      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle1 + "\"]")));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle2 + "\"]")));
    } catch(AssertionError e) {
      captureScreen("SearchTests.identicalSearchQuery");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.identicalSearchQuery");
      throw e;
    }
  }
  
  @Test(groups = { "functionality" }, description = "search project, highlight search query")
  public void highlightSearchQuery() throws Throwable {
    try {
      
      String projectTitle = CommonData.getRandomShortString(7);
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "identical_search_project_2", "", "", "", "", CommonData.getLoginUserDefault(), Config.DEFAULT_UPLOAD_TOKEN));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      
      openLocation("/search/?q=" + projectTitle + "&p=1", false);
      ajaxWait();  
      assertEquals(projectTitle, driver().findElement(By.className("highlight")).getText());
      
      openLocation("/search/?q=" + projectTitle.toUpperCase() + "&p=1", false);
      ajaxWait();
      assertEquals(projectTitle, driver().findElement(By.className("highlight")).getText());
    } catch(AssertionError e) {
      captureScreen("SearchTests.highlightSearchQuery");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.highlightSearchQuery");
      throw e;
    }
  }
  @Test(groups = { "functionality", "upload" }, description = "search and hide project")
  public void searchAndHideProject() throws Throwable {
    try {
      String projectTitle = "search_test_" + CommonData.getRandomShortString(10);
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, "some search project", "", "", "", "", "", ""));
      String projectID = projectUploader.getProjectId(projectTitle);

      openLocation();
      ajaxWait();

      // hide project
      openAdminLocation("/tools/editProjects");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectID)).click();

      openLocation("search/?q=" + projectTitle + "&p=1");
      ajaxWait();

      assertFalse(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "\"]")));

      // unhide project
      openAdminLocation("/tools/editProjects");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectID)).click();

      openLocation("search/?q=" + projectTitle + "&p=1");
      ajaxWait();

      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE.toUpperCase()));
      assertTrue(isElementPresent(By.xpath("//a[@title=\"" + projectTitle + "\"]")));
    } catch(AssertionError e) {
      captureScreen("SearchTests.searchAndHideProject");
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.searchAndHideProject");
      throw e;
    }
  }

  /*
   * at the moment not supported
  @Test(dataProvider = "searchUser", groups = { "functionality", "upload" }, description = "search for user and find projects")
  public void searchAndFindUser(HashMap<String, String> dataset) throws Throwable {
    try {
      projectUploader.upload(dataset);

      String projectTitle = dataset.get("projectTitle");
      
      WebElement webElementProjectDescription;
      List<WebElement> webElementProjectDescriptions;
      Iterator<WebElement> iteratorDescriptions;
      

      openLocation();
      ajaxWait();

      assertTrue(isVisible(By.id("headerSearchBox")));

      driver().findElement(By.id("searchQuery")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("webHeadSearchSubmit")).click();
      ajaxWait();

      assertFalse(isVisible(By.id("fewerProjects")));
      assertFalse(isVisible(By.id("moreProjects")));
      assertTrue(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_TITLE));
      
      assertTrue(isTextPresent(projectTitle));
      
      webElementProjectDescriptions = driver().findElements(By.className(".projectDetailLine"));
      
      iteratorDescriptions = webElementProjectDescriptions.iterator();
      while( iteratorDescriptions.hasNext() ) {
        webElementProjectDescription = iteratorDescriptions.next();
        assertTrue(webElementProjectDescription.getText().contains(CommonData.getLoginUserDefault()));
      }
      
      
      assertFalse(isTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS));

    } catch(AssertionError e) {
      captureScreen("SearchTests.searchUser." + dataset.get("projectTitle"));
      log(dataset.get("projectTitle"));
      log(dataset.get("projectDescription"));
      throw e;
    } catch(Exception e) {
      captureScreen("SearchTests.searchUser." + dataset.get("projectTitle"));
      throw e;
    }
  }*/
  
  @DataProvider(name = "specialChars")
  public Object[][] specialChars() {
    Object[][] returnArray = new Object[][] { { "_äöü\"$%&/=?`+*~#-.:,;|" }, };
    return returnArray;
  }

  @DataProvider(name = "randomProjects")
  public Object[][] randomProjects() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("search_test_long_description_" + CommonData.getRandomShortString(10),
            "long_description_" + CommonData.getRandomLongString(Config.PROJECT_SHORT_DESCRIPTION_MAX_LENGTH), "", "", "", "", "", "") },
        { CommonData.getUploadPayload("search_test_" + CommonData.getRandomShortString(10), CommonData.getRandomShortString(10), "", "", "", "", "", "") }, };
    return returnArray;
  }
  
  
  @DataProvider(name = "searchUser")
  public Object[][] searchUser() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("search_test_long_description_" + CommonData.getRandomShortString(10),
            "long_description_" + CommonData.getRandomLongString(Config.PROJECT_SHORT_DESCRIPTION_MAX_LENGTH), "", "", "", "", "catroid", CommonFunctions.getAuthenticationToken("catroid")) },
        { CommonData.getUploadPayload("search_test_" + CommonData.getRandomShortString(10), CommonData.getRandomShortString(10), "", "", "", "", "catroid", CommonFunctions.getAuthenticationToken("catroid")) }, 
    };
    return returnArray;
  }

}
