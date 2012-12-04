/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "admin", "UploadTest" })
public class UploadTest extends BaseTest {

  @Test(groups = { "upload" }, description = "upload and delete a project")
  public void uploadTest() throws Throwable {
    try {
      // check for project uploader and send invalid request
      openAdminLocation();
      driver().findElement(By.id("aProjectUploader")).click();
      assertTrue(isTextPresent("Project Uploader"));
      driver().findElement(By.name("uploadButton")).click();
      assertTrue(isTextPresent("501"));
      
      // upload a project
      String projectTitle = "testproject" + CommonData.getRandomLongString(200);
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", ""));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // delete project
      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      assertTrue(isTextPresent(projectTitle));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("delete" + projectId)).click();
      assertFalse(isTextPresent(projectTitle));

      // verify deletion
      assertProjectNotPresent(projectTitle);
    } catch(AssertionError e) {
      captureScreen("UploadTest.uploadTest");
      throw e;
    } catch(Exception e) {
      captureScreen("UploadTest.uploadTest");
      throw e;
    }
  }
}
