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

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import org.testng.annotations.DataProvider;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

//dataset.get("username")

@Test(groups = { "api", "UploadTests" })
public class UploaderLoginOrRegisterTests extends BaseTest {

  @Test(dataProvider = "validLoginData", groups = { "upload", "functionality" }, description = "upload login or register")
  public void validLoginForUpload(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = uploaderLoginOrRegister.loginOrRegister(dataset);
      System.out.println("response = uploaderLoginOrRegister.loginOrRegister(dataset);");
      
      assertEquals("login", CommonFunctions.getValueFromJSONobject(response, "statusCodePart"));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

    } catch(AssertionError e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("username"));
      throw e;
    }
  }
  
  @Test(dataProvider = "invalidLoginData", groups = { "upload", "functionality" }, description = "upload login or register")
  public void invalidLoginForUploadAndRegister(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = uploaderLoginOrRegister.loginOrRegister(dataset);
      System.out.println("response = uploaderLoginOrRegister.loginOrRegister(dataset);");
      
      assertEquals("registration", CommonFunctions.getValueFromJSONobject(response, "statusCodePart"));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      CommonFunctions.deleteUserFromDatabase(dataset.get("username"));

    } catch(AssertionError e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("UploadLoginTests.validLoginForUpload." + dataset.get("username"));
      throw e;
    }
  }
  
  @SuppressWarnings("serial")
  @DataProvider(name = "validLoginData")
  public Object[][] validLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "catroweb");
        put("password", "cat.roid.web");
        put("email", "admin@catroid.org");
        put("country", "AT");
      }
    } } };
    return dataArray;
  }
  
  @SuppressWarnings("serial")
  @DataProvider(name = "invalidLoginData")
  public Object[][] invalidLoginData() {
    
    final String randomString1 = CommonData.getRandomShortString(10);
    
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "registrationUsernameWith" + randomString1);
        put("password", "registrationPassword" +randomString1);
        put("email", "test" + randomString1 + "@selenium.at");
        put("country", "CN");
      }
    } } };
    return dataArray;
  }
  
}
