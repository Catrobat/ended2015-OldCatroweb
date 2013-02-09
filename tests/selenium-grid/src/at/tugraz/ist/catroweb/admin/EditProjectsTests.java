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

package at.tugraz.ist.catroweb.admin;

import org.openqa.selenium.By;
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

      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", ""));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // check that project is shown on index-page
      assertProjectPresent(projectTitle);

      openAdminLocation();
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      assertTrue(isTextPresent("ID"));
      assertTrue(isTextPresent("Title"));
      assertTrue(isTextPresent("Upload Time"));
      assertTrue(isTextPresent("Upload IP"));
      assertTrue(isTextPresent("Downloads"));
      assertTrue(isTextPresent("Flagged"));
      assertTrue(isTextPresent("Visible"));
      assertTrue(isTextPresent("Delete"));
      assertTrue(isElementPresent(By.id("delete" + projectId)));
      assertTrue(isTextPresent(projectTitle));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("delete" + projectId)).click();
      assertTrue(isTextPresent("The project was succesfully deleted!"));
      assertFalse(isElementPresent(By.id("delete" + projectId)));
      assertFalse(isTextPresent(projectTitle));

      // check that project is not shown on index-page
      assertProjectNotPresent(projectTitle);
    } catch(AssertionError e) {
      captureScreen("EditProjectsTests.deleteButton");
      throw e;
    } catch(Exception e) {
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
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      ajaxWait();
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      assertTrue(isElementPresent(By.id("toggle" + projectId)));
      assertTrue(isTextPresent(projectTitle));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectId)).click();
      assertTrue(isTextPresent("The project was succesfully set to state invisible"));

      // project is NOT shown on index-page
      assertProjectNotPresent(projectTitle);

      // toggle project visibility to "hidden"
      openAdminLocation();
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      assertTrue(isElementPresent(By.id("toggle" + projectId)));
      assertTrue(isTextPresent(projectTitle));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("toggle" + projectId)).click();
      assertTrue(isTextPresent("The project was succesfully set to state visible"));

      // project is shown again on index-page
      assertProjectPresent(projectTitle);

      // and delete project
      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      assertTrue(isElementPresent(By.id("delete" + projectId)));
      assertTrue(isTextPresent(projectTitle));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("delete" + projectId)).click();
      assertTrue(isTextPresent("The project was succesfully deleted!"));
      assertFalse(isElementPresent(By.id("delete" + projectId)));
      assertFalse(isTextPresent(projectTitle));

      // and finally project is NOT shown on index-page
      assertProjectNotPresent(projectTitle);
    } catch(AssertionError e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    } catch(Exception e) {
      captureScreen("EditProjectsTests.invisibleButton");
      throw e;
    }
  }
}
