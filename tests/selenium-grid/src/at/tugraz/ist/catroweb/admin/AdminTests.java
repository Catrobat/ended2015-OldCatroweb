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

@Test(groups = { "admin", "AdminTests" })
public class AdminTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check admin area login")
  public void successfulLogin() throws Throwable {
    try {
      openAdminLocation();
      assertRegExp(".*Administration - Catroid Website.*", session().getTitle());
      assertTrue(session().isTextPresent("Administration Tools"));
      session().click("xpath=//a[2]");
      waitForPageToLoad();
      assertRegExp(".*Catroid Website.*", session().getTitle());
      session().goBack();
      waitForPageToLoad();
      if (session().isTextPresent("Catroid Administration Site") == false)
      {
        session().goBack();
        waitForPageToLoad();
      }
      assertTrue(session().isTextPresent("Catroid Administration Site"));
    } catch(AssertionError e) {
      captureScreen("AdminTests.successfulLogin");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.successfulLogin");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "clicks all available links in the admin area")
  public void clickAllLinks() throws Throwable {
    try {
      openAdminLocation();
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

      session().click("xpath=//a[6]");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - Language Management"));
      session().goBack();
      waitForPageToLoad();

      session().click("xpath=//a[7]");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of blocked IP-Addresses"));
      session().goBack();
      waitForPageToLoad();

      session().click("xpath=//a[8]");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of blocked users"));
      session().goBack();
      waitForPageToLoad();

      log("AdminTests: check block Users link");
      assertTrue(session().isTextPresent("- back"));
      session().click("xpath=//a[9]");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Catroid Administration Site"));
    } catch(AssertionError e) {
      captureScreen("AdminTests.clickAllLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.clickAllLinks");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload", "popupwindows" }, description = "check report as inappropriate functionality")
  public void inappropriateProjects() throws Throwable {
    try {
      String title = "Testproject " + CommonData.getRandomLongString(200);
      String response = projectUploader.upload(CommonData.getUploadPayload(title, "", "", "", "", "", ""));
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

      openLocation("catroid/details/" + id);
      ajaxWait();
      session().click("reportAsInappropriateButton");
      session().type("reportInappropriateReason", "my selenium reason");
      session().click("reportInappropriateReportButton");
      ajaxWait();
      assertTrue(session().isTextPresent("You reported this project as inappropriate!"));
      openAdminLocation("/tools/inappropriateProjects");
      assertTrue(session().isTextPresent(id));

      clickAndWaitForPopUp("xpath=//a[@id='detailsLink" + id + "']", "_blank");
      assertTrue(session().isTextPresent(title));
      closePopUp();

      session().click("resolve" + id);
      session().getConfirmation();
      waitForPageToLoad();
      assertTrue(session().isTextPresent("The project was succesfully restored and set to visible!"));
      assertFalse(session().isTextPresent(id));
    } catch(AssertionError e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    }
  }
}
