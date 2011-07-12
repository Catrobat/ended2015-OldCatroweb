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

@Test(groups = { "admin", "EditProjectsTests" })
public class EditProjectsTests extends BaseTest {

  @Test(groups = { "functionality", "upload" }, description = "delete project button")
  public void deleteButton() throws Throwable {
    try {
      String projectTitle = "Testproject_delete_test_" + CommonData.getRandomLongString(200);

      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "",""));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // check that project is shown on index-page
      openLocation();
      ajaxWait();
      waitForTextPresent(projectTitle);

      openAdminLocation();
      session().click("aAdministrationTools");
      waitForPageToLoad();
      session().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(session().isTextPresent("ID"));
      assertTrue(session().isTextPresent("Title"));
      assertTrue(session().isTextPresent("Upload Time"));
      assertTrue(session().isTextPresent("Upload IP"));
      assertTrue(session().isTextPresent("Downloads"));
      assertTrue(session().isTextPresent("Flagged"));
      assertTrue(session().isTextPresent("Visible"));
      assertTrue(session().isTextPresent("Delete"));
      assertTrue(session().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertTrue(session().isTextPresent(projectTitle));
      session().click("xpath=//input[@id='delete" + projectId + "']");
      session().getConfirmation();
      waitForPageToLoad();
      assertTrue(session().isTextPresent("The project was succesfully deleted!"));
      assertFalse(session().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertFalse(session().isTextPresent(projectTitle));

      // check that project is not shown on index-page
      openLocation();
      assertTrue(session().isElementPresent("xpath=//div[@id='aIndexWebLogoLeft']"));
      assertFalse(session().isTextPresent(projectTitle));
    } catch(AssertionError e) {
      captureScreen("EditProjectsTests.deleteButton");
      throw e;
    }catch(Exception e) {
      captureScreen("EditProjectsTests.deleteButton");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "set invisible button")
  public void invisibleButton() throws Throwable {
    try {
      String projectTitle = "Testproject_invisible_test_" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", ""));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // check that project is shown on index-page
      assertProjectPresent(projectTitle);

      // toggle project visibility to "hidden"
      openAdminLocation();
      session().click("aAdministrationTools");
      waitForPageToLoad();
      session().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(session().isElementPresent("xpath=//input[@id='toggle" + projectId + "']"));
      assertTrue(session().isTextPresent(projectTitle));
      session().click("xpath=//input[@id='toggle" + projectId + "']");
      session().getConfirmation();
      waitForPageToLoad();
      assertTrue(session().isTextPresent("The project was succesfully set to state invisible"));

      // project is NOT shown on index-page
      openLocation();
      ajaxWait();
      assertFalse(session().isTextPresent(projectTitle));

      // toggle project visibility to "hidden"
      openAdminLocation();
      session().click("aAdministrationTools");
      waitForPageToLoad();
      session().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(session().isElementPresent("xpath=//input[@id='toggle" + projectId + "']"));
      assertTrue(session().isTextPresent(projectTitle));
      session().click("xpath=//input[@id='toggle" + projectId + "']");
      session().getConfirmation();
      waitForPageToLoad();
      assertTrue(session().isTextPresent("The project was succesfully set to state visible"));

      // project is shown again on index-page
      openLocation();
      ajaxWait();
      assertTrue(session().isTextPresent(projectTitle));

      // and delete project
      openAdminLocation();
      session().click("aAdministrationTools");
      waitForPageToLoad();
      session().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(session().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertTrue(session().isTextPresent(projectTitle));
      session().click("xpath=//input[@id='delete" + projectId + "']");
      session().getConfirmation();
      waitForPageToLoad();
      assertTrue(session().isTextPresent("The project was succesfully deleted!"));
      assertFalse(session().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertFalse(session().isTextPresent(projectTitle));

      // and finally project is NOT shown on index-page
      openLocation();
      ajaxWait();
      assertFalse(session().isTextPresent(projectTitle));
    } catch(AssertionError e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    } catch(Exception e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    }
  }
}
