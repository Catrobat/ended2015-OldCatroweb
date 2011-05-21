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

package at.tugraz.ist.catroweb.api;

import java.util.HashMap;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import static org.testng.AssertJUnit.*;
import org.testng.annotations.Test;
import org.testng.annotations.DataProvider;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class UploadTests extends BaseTest {
  @Test(dataProvider = "validProjectsForUpload", groups = { "upload", "firefox", "default" }, description = "upload valid projects")
  public void uploadValidProjects(HashMap<String, String> dataset) {
    String response = projectUploader.upload(dataset);
    assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    assertTrue(session().isTextPresent(dataset.get("projectTitle")));
  }

  @Test(dataProvider = "validProjectsForUpload", groups = { "upload", "firefox", "default" }, description = "upload invalid projects")
  public void uploadInvalidProjects(HashMap<String, String> dataset) {
    String response = projectUploader.upload(dataset);
    assertNotSame("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
  }

  @DataProvider(name="validProjectsForUpload")
  public Object[][] validProjectsForUpload(){
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("testing project upload", "some description for my test project.", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("my test project with spaces", "some description for my test project.", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("my spÄc1al c´har t3ßt pröjec+", "some description with -äöüÜÖÄß- for my test project.%&()[]{}_|~#", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("my_test_project_with_looong_description", "some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. ", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("project with thumbnail", "this project has its own thumbnail inside the zip", "test2.zip", "149c6b242dc410650a061292cd40f7d5", "", "", "", "0") }
      };
     return returnArray;
  }
  
  @DataProvider(name="invalidProjectsForUpload")
  public Object[][] invalidProjectsForUpload(){
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("insulting word in description", "fuck the project!!!!", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("fucking word in title", "some description", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0") },
        { CommonData.getUploadPayload("no token given", "some description", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", " ") },
        { CommonData.getUploadPayload("wrong token given", "some description", "test.zip", "72ed87fbd5119885009522f08b7ee79f", "", "", "", "123") }
      };
     return returnArray;
  }
}
