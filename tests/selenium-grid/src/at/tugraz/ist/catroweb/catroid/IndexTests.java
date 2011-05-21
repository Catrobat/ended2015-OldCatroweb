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

public class IndexTests extends BaseTest {
  @Test(groups = { "index", "firefox", "default" }, description = "location tests")
  public void location() {
    session().open(Config.TESTS_BASE_PATH + "/catroid/index/9999999999999999999");
    waitForPageToLoad();
    ajaxWait();
    // test page title and header title
    assertTrue(session().getTitle().matches("^Catroid Website -.*"));
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));

    String location = CommonData.getRandomLongString();
    session().open(Config.TESTS_BASE_PATH + "/catroid/index/" + location);
    waitForPageToLoad();
    ajaxWait();

    // test page title and header title
    assertTrue(session().getTitle().matches("^Catroid Website -.*"));
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));

    location = CommonData.getRandomLongString();
    session().open(Config.TESTS_BASE_PATH + "/catroid/details/" + location);
    waitForPageToLoad();
    ajaxWait();
    // test page title and header title
    assertRegExp(".*/catroid/errorPage", session().getLocation());
    assertTrue(session().isTextPresent(location));
  }

  @Test(groups = { "index", "firefox", "default" }, description = "click download,header,details -links ")
  public void index() throws Throwable {
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    // test page title and header title
    assertTrue(session().getTitle().matches("^Catroid Website -.*"));
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

    // test catroid header text
    assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));
    // test logo link
    assertTrue(session().isElementPresent("xpath=//div[@class='webHeadLogo']"));
    session().click("xpath=//div[@id='aIndexWebLogoLeft']");
    ajaxWait();

    // test catroid download link
    assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
    session().click("xpath=//a[@id='aIndexWebLogoMiddle']");
    session().selectWindow("_blank");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Catroid_0-4-3d.apk"));
    assertTrue(session().isTextPresent("Paintroid_0.6.4b.apk"));
    session().close();
    session().selectWindow(null);

    // test links to details page
    session().click("xpath=//a[@class='projectListDetailsLink']");
    waitForPageToLoad();
    assertRegExp(".*/catroid/details/[0-9]+", session().getLocation());

    session().goBack();
    waitForPageToLoad();
    waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
    waitForElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']");
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));

    // test home link
    session().click("xpath=//div[@id='aIndexWebLogoLeft']");
    ajaxWait();
    assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));
  }

  @Test(groups = { "index", "firefox", "default" }, description = "page navigation tests")
  public void pageNavigation() throws Throwable {
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    waitForElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']");
    // TODO $this->doUpload();
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
  }
}
