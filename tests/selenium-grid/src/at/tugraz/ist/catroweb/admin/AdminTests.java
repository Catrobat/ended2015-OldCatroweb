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
      assertRegExp(".*Administration - Catroid Website.*", selenium().getTitle());
      assertTrue(selenium().isTextPresent("Administration Tools"));
      selenium().click("xpath=//a[2]");
      waitForPageToLoad();
      assertRegExp(".*Catroid Website.*", selenium().getTitle());
      selenium().goBack();
      waitForPageToLoad();
      if (selenium().isTextPresent("Catroid Administration Site") == false)
      {
        selenium().goBack();
        waitForPageToLoad();
      }
      assertTrue(selenium().isTextPresent("Catroid Administration Site"));
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
      assertRegExp(".*Administration - Catroid Website.*", selenium().getTitle());
      assertTrue(selenium().isTextPresent("Catroid Administration Site"));

      selenium().click("xpath=//a[1]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools"));
      assertTrue(selenium().isTextPresent("remove inconsistant project files"));
      assertTrue(selenium().isTextPresent("edit projects"));
      assertTrue(selenium().isTextPresent("thumbnail uploader"));
      assertTrue(selenium().isTextPresent("inappropriate projects"));
      assertTrue(selenium().isTextPresent("approve unapproved words"));

      assertRegExp(".*Administration - Catroid Website.*", selenium().getTitle());

      selenium().click("xpath=//a[1]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Answer"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[2]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of available projects"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[3]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - Thumbnail Uploader"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[4]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of inappropriate projects"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[5]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of unapproved Words"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[6]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - Language Management"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[7]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of blocked IP-Addresses"));
      selenium().goBack();
      waitForPageToLoad();

      selenium().click("xpath=//a[8]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of blocked users"));
      selenium().goBack();
      waitForPageToLoad();

      assertTrue(selenium().isTextPresent("- back"));
      selenium().click("xpath=//a[9]");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Catroid Administration Site"));
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
      selenium().click("reportAsInappropriateButton");
      selenium().type("reportInappropriateReason", "my selenium reason");
      selenium().click("reportInappropriateReportButton");
      ajaxWait();
      assertTrue(selenium().isTextPresent("You reported this project as inappropriate!"));
      openAdminLocation("/tools/inappropriateProjects");
      assertTrue(selenium().isTextPresent(id));

      clickAndWaitForPopUp("xpath=//a[@id='detailsLink" + id + "']", "_blank");
      assertTrue(selenium().isTextPresent(title));
      closePopUp();

      selenium().click("resolve" + id);
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("The project was succesfully restored and set to visible!"));
      assertFalse(selenium().isTextPresent(id));
    } catch(AssertionError e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    }
  }
}
