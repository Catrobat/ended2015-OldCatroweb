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
      selenium().click("aAdministrationTools");
      waitForPageToLoad();
      selenium().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(selenium().isTextPresent("ID"));
      assertTrue(selenium().isTextPresent("Title"));
      assertTrue(selenium().isTextPresent("Upload Time"));
      assertTrue(selenium().isTextPresent("Upload IP"));
      assertTrue(selenium().isTextPresent("Downloads"));
      assertTrue(selenium().isTextPresent("Flagged"));
      assertTrue(selenium().isTextPresent("Visible"));
      assertTrue(selenium().isTextPresent("Delete"));
      assertTrue(selenium().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertTrue(selenium().isTextPresent(projectTitle));
      selenium().click("xpath=//input[@id='delete" + projectId + "']");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("The project was succesfully deleted!"));
      assertFalse(selenium().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertFalse(selenium().isTextPresent(projectTitle));

      // check that project is not shown on index-page
      openLocation();
      assertTrue(selenium().isElementPresent("xpath=//div[@id='aIndexWebLogoLeft']"));
      assertFalse(selenium().isTextPresent(projectTitle));
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
      selenium().click("aAdministrationTools");
      waitForPageToLoad();
      selenium().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(selenium().isElementPresent("xpath=//input[@id='toggle" + projectId + "']"));
      assertTrue(selenium().isTextPresent(projectTitle));
      selenium().click("xpath=//input[@id='toggle" + projectId + "']");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("The project was succesfully set to state invisible"));

      // project is NOT shown on index-page
      openLocation();
      ajaxWait();
      assertFalse(selenium().isTextPresent(projectTitle));

      // toggle project visibility to "hidden"
      openAdminLocation();
      selenium().click("aAdministrationTools");
      waitForPageToLoad();
      selenium().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(selenium().isElementPresent("xpath=//input[@id='toggle" + projectId + "']"));
      assertTrue(selenium().isTextPresent(projectTitle));
      selenium().click("xpath=//input[@id='toggle" + projectId + "']");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("The project was succesfully set to state visible"));

      // project is shown again on index-page
      openLocation();
      ajaxWait();
      assertTrue(selenium().isTextPresent(projectTitle));

      // and delete project
      openAdminLocation();
      selenium().click("aAdministrationTools");
      waitForPageToLoad();
      selenium().click("aAdminToolsEditProjects");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("Administration Tools - List of available projects"));
      assertTrue(selenium().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertTrue(selenium().isTextPresent(projectTitle));
      selenium().click("xpath=//input[@id='delete" + projectId + "']");
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent("The project was succesfully deleted!"));
      assertFalse(selenium().isElementPresent("xpath=//input[@id='delete" + projectId + "']"));
      assertFalse(selenium().isTextPresent(projectTitle));

      // and finally project is NOT shown on index-page
      openLocation();
      ajaxWait();
      assertFalse(selenium().isTextPresent(projectTitle));
    } catch(AssertionError e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    } catch(Exception e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    }
  }
}
