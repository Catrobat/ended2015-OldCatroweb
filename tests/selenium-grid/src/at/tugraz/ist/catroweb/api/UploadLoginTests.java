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

import static org.testng.AssertJUnit.*;
import org.testng.annotations.Test;
import org.testng.annotations.DataProvider;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

//dataset.get("username")

@Test(groups = { "api", "UploadTests" })
public class UploadLoginTests extends BaseTest {

  @Test(dataProvider = "validProjectsForUpload", groups = { "upload", "functionality" }, description = "upload login or register")
  public void validLoginForUpload(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = uploaderLoginOrRegister.loginOrRegister(dataset);
      System.out.println("response = uploaderLoginOrRegister.loginOrRegister(dataset);");
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      System.out.println("assertEquals(200)");
      openLocation();
      ajaxWait();
      //assertTrue(isTextPresent(dataset.get("projectTitle")));
    } catch(AssertionError e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("projectTitle"));
      throw e;
    }
  }
  
  
  // put here new login data!!!

  @DataProvider(name = "validProjectsForUpload")
  public Object[][] validProjectsForUpload() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("testing project upload", "some description for my test project.", "test.zip", "583783A335BD40D3D0195A13432AFABB", "",
            "", "0") },
        { CommonData.getUploadPayload("my test project with spaces and some uppercases in fileChecksum", "some description for my test project.", "test.zip",
            "583783A335BD40D3D0195A13432AFABB", "", "", "0") },
        { CommonData.getUploadPayload("my spÄc1al c´har ' t3ßt pröjec+", "some description ' with -äöüÜÖÄß- for my test project.%&()[]{}_|~#", "test.zip",
            "583783A335BD40D3D0195A13432AFABB", "", "", "0") },
        { CommonData
            .getUploadPayload(
                "my_test_project_with_looong_description",
                "some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. ",
                "test.zip", "583783A335BD40D3D0195A13432AFABB", "", "", "0") },
        { CommonData.getUploadPayload("project with thumbnail", "this project has its own thumbnail inside the zip", "test2.zip",
            "38B9AA38175AEDDD1BABABAD63025C72", "", "", "0") },
        { CommonData.getUploadPayload("project v6 with thumbnail and xml-project extention",
            "this project has its own thumbnail and is v6 and has xml extention instead of spf", "test_version_6_xml.zip", "5451117C121B89EE9BFB41C5381F357A",
            "", "", "0") },
            { CommonData.getUploadPayload("new catroid extention",
                "this project has catroid as extention", "test.catroid", "583783A335BD40D3D0195A13432AFABB",
                "", "", "0") } };
    return returnArray;
  }

  @DataProvider(name = "invalidProjectsForUpload")
  public Object[][] invalidProjectsForUpload() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("insulting word in description", "fuck the project!!!!", "test.zip", "583783A335BD40D3D0195A13432AFABB", "", "", "0") },
        { CommonData.getUploadPayload("fucking word in title", "some description", "test.zip", "583783A335BD40D3D0195A13432AFABB", "", "", "0") },
        { CommonData.getUploadPayload("no token given", "some description", "test.zip", "583783A335BD40D3D0195A13432AFABB", "", "", " ") },
        { CommonData.getUploadPayload("wrong checksum", "some description", "test.zip", "2c2d13d52cf670ea55b2014b336d1b4d", "", "", " ") },
        { CommonData.getUploadPayload("wrong token given", "some description", "test.zip", "583783A335BD40D3D0195A13432AFABB", "", "", "123") },
        { CommonData.getUploadPayload("invalid xml in project file", "some description", "invalid_xml.zip", "AADC2DEDE19CDB559E362DB2E119F038", "", "", " ") },
        { CommonData.getUploadPayload("invalid zip file", "some description", "not_a_zip.zip", "D1B761A18F525A2A20CAA2A5DA12BBF1", "", "", " ") } };
    return returnArray;
  }
  
  @SuppressWarnings("serial")
  @DataProvider(name = "validLoginData")
  public Object[][] validLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "catroweb");
        put("password", "cat.roid.web");
      }
    } } };
    return dataArray;
  }
}
