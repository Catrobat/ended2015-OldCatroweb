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

public class EditProjectsTests extends BaseTest {
  @Test(groups = { "admin" }, description = "delete project button")
  public void deleteButton() throws Throwable {
    String projectTitle = "Testproject for AdminEditProjects Upload Test Title DELETE";
    String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", "", ""));
    String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

    // check that project is shown on index-page
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertTrue(session().isTextPresent(projectTitle));

    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
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
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertTrue(session().isElementPresent("xpath=//img[@id='aIndexWebLogoLeft']"));
    assertFalse(session().isTextPresent(projectTitle));
  }

  @Test(groups = { "admin" }, description = "set invisible button")
  public void invisibleButton() throws Throwable {
    String projectTitle = "Testproject for AdminEditProjects Upload Test Title INVISIBLE";
    String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", "", ""));
    String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

    // check that project is shown on index-page
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertTrue(session().isTextPresent(projectTitle));

    // toggle project visibility to "hidden"
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
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
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertFalse(session().isTextPresent(projectTitle));

    // toggle project visibility to "hidden"
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
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
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertTrue(session().isTextPresent(projectTitle));

    // and delete project
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
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
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertFalse(session().isTextPresent(projectTitle));
  }
}
