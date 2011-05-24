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

package at.tugraz.ist.catroweb.admin;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class AdminTests extends BaseTest {
  @Test(groups = { "admin" }, description = "check admin area login")
  public void successfulLogin() throws Throwable {
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
    assertRegExp(".*Administration - Catroid Website.*", session().getTitle());
    assertTrue(session().isTextPresent("Administration Tools"));
    session().click("xpath=//a[2]");
    waitForPageToLoad();
    assertRegExp(".*Catroid Website.*", session().getTitle());
    session().goBack();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Catroid Administration Site"));
  }

  @Test(groups = { "admin" }, description = "clicks all available links in the admin area")
  public void clickAllLinks() throws Throwable {
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
    assertRegExp(".*Administration - Catroid Website.*", session().getTitle());
    assertTrue(session().isTextPresent("Catroid Administration Site"));

    session().click("xpath=//a[1]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Administration Tools"));
    assertTrue(session().isTextPresent("remove inconsistant project files"));
    assertTrue(session().isTextPresent("edit projects"));
    assertTrue(session().isTextPresent("thumbnail uploader"));
    assertTrue(session().isTextPresent("inappropriate projects"));
    assertTrue(session().isTextPresent("approve unapproved words"));

    assertRegExp(".*Administration - Catroid Website.*", session().getTitle());

    session().click("xpath=//a[1]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Answer"));
    session().goBack();
    waitForPageToLoad();

    session().click("xpath=//a[2]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Administration Tools - List of available projects"));
    session().goBack();
    waitForPageToLoad();

    session().click("xpath=//a[3]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Administration Tools - Thumbnail Uploader"));
    session().goBack();
    waitForPageToLoad();

    session().click("xpath=//a[4]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Administration Tools - List of inappropriate projects"));
    session().goBack();
    waitForPageToLoad();

    session().click("xpath=//a[5]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Administration Tools - List of unapproved Words"));
    session().goBack();
    waitForPageToLoad();

    assertTrue(session().isTextPresent("- back"));
    session().click("xpath=//a[7]");
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Catroid Administration Site"));
  }

  @Test(groups = { "admin" }, description = "check report as inappropriate functionality")
  public void inappropriateProjects() throws Throwable {
    String title = "Testproject " + CommonData.getRandomLongString();
    String response = projectUploader.upload(CommonData.getUploadPayload(title, "", "", "", "", "", "", ""));
    String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

    session().open(Config.TESTS_BASE_PATH + "catroid/details/" + id);
    waitForPageToLoad();
    ajaxWait();
    session().click("reportAsInappropriateButton");
    session().type("reportInappropriateReason", "my selenium reason");
    session().click("reportInappropriateReportButton");
    ajaxWait();
    assertTrue(session().isTextPresent("You reported this project as inappropriate!"));
    session().open(CommonFunctions.getAdminPath(this.webSite) + "/tools/inappropriateProjects");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(id));

    clickAndWaitForPopUp("xpath=//a[@id='detailsLink" + id + "']", "_blank");
    assertTrue(session().isTextPresent(title));
    closePopUp();

    session().click("resolve" + id);
    session().getConfirmation();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("The project was succesfully restored and set to visible!"));
    assertFalse(session().isTextPresent(id));
  }
}
