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

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "IndexTests" })
public class IndexTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "location tests")
  public void location() {
    log("TODO: IndexTests: location");
    // try {
    // openLocation("catroid/index/9999999999999999999");
    // ajaxWait();
    // // test page title and header title
    // assertTrue(session().getTitle().matches("^Catroid Website -.*"));
    // assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    // assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
    //
    // String location = CommonData.getRandomLongString(200);
    // openLocation("catroid/index/" + location);
    // ajaxWait();
    //
    // // test page title and header title
    //
    // assertTrue(session().getTitle().matches("^Catroid Website -.*"));
    // assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    // assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
    //
    // location = CommonData.getRandomLongString(200);
    // openLocation("catroid/details/" + location);
    // ajaxWait();
    // // test page title and header title
    // assertRegExp(".*/catroid/errorPage", session().getLocation());
    // assertTrue(session().isTextPresent(location));
    // } catch(AssertionError e) {
    // captureScreen("IndexTests.location");
    // throw e;
    // }

  }

  @Test(groups = { "visibility", "popupwindows" }, description = "click download,header,details -links ")
  public void index() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      // test page title and header title
      assertTrue(session().getTitle().matches("^Catroid Website.*"));
      waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);

      // test catroid header text
      assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));
      // test logo link
      assertTrue(session().isElementPresent("xpath=//div[@class='webHeadLogo']"));
      session().click("xpath=//div[@id='aIndexWebLogoLeft']");
      ajaxWait();
      // test catroid download link
      assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
      clickAndWaitForPopUp("xpath=//a[@id='aIndexWebLogoMiddle']", "_blank");
      assertTrue(session().isTextPresent("Catroid_0-4-3d.apk"));
      assertTrue(session().isTextPresent("Paintroid_0.6.4b.apk"));
      closePopUp();
      
      clickLastVisibleProject();
      assertRegExp(".*/catroid/details/[0-9]+", session().getLocation());
      session().goBack();
      waitForPageToLoad();
      ajaxWait();
      waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
      waitForElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']");
      assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
      assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));

      // test home link
      session().click("xpath=//div[@id='aIndexWebLogoLeft']");
      ajaxWait();
      assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));
    } catch(AssertionError e) {
      captureScreen("IndexTests.index");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "page navigation tests")
  public void pageNavigation() throws Throwable {
    try {
      openLocation();
      ajaxWait();

      for(int i = 0; i < Config.PROJECT_PAGE_LOAD_MAX_PROJECTS * (Config.PROJECT_PAGE_SHOW_MAX_PAGES + 1); i++) {
        System.out.print(".");
        projectUploader.upload();
      }
      session().refresh();
      waitForPageToLoad();
      ajaxWait();
      assertFalse(session().isVisible("fewerProjects"));
      assertTrue(session().isVisible("moreProjects"));
      assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
      int i = 0;
      for(i = 0; i < Config.PROJECT_PAGE_SHOW_MAX_PAGES; i++) {
        session().click("moreProjects");
        ajaxWait();
        assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i + 2) + "$", session().getTitle());
      }

      assertTrue(session().isVisible("fewerProjects"));
      assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));
      session().click("fewerProjects");
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$", session().getTitle());

      // test session
      session().refresh();
      waitForPageToLoad();
      ajaxWait();
      waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$", session().getTitle());

      // test links to details page
      session().click("xpath=//a[@class='projectListDetailsLink']");
      waitForPageToLoad();
      assertRegExp(".*/catroid/details.*", session().getLocation());
      session().goBack();
      waitForPageToLoad();
      ajaxWait();
      waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$", session().getTitle());

      // test header click
      session().click("aIndexWebLogoLeft");
      ajaxWait();
      assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (1) + "$", session().getTitle());
    } catch(AssertionError e) {
      captureScreen("IndexTests.pageNavigation");
      throw e;
    }
  }
}
