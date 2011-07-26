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

package at.tugraz.ist.catroweb.catroid;

import java.util.HashMap;
import java.util.Random;

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "ReportAsInappropriateTests" })
public class ReportAsInappropriateTests extends BaseTest {

  @Test(dataProvider = "loginDataAndReportOwnProject", groups = { "functionality", "upload" }, description = "login and report own project as inappropriate")
  public void reportOwnProjectAsInappropriate(HashMap<String, String> dataset) throws Throwable {
    try {
      // upload project
      Random rand = new Random();
      String projectTitle = "Testproject_for_report_as_inappropriate_" + rand.nextInt(9999);
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, dataset.get("projectDescription"), "", "", "", "",
          dataset.get("token")));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // login first
      openLocation();
      driver().findElement(By.id("headerProfileButton")).click();
      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();
      assertProjectPresent(projectTitle);

      // goto details page
      openLocation("catroid/details/" + projectId);
      ajaxWait();
      assertTrue(isTextPresent(projectTitle));
      assertTrue(isTextPresent(dataset.get("projectDescription")));

      // report as inappropriate not visible
      assertFalse(isElementPresent(By.id("reportAsInappropriateButton")));

      // check if reportAsInappropriate button is visible for a foreign project
      openLocation("catroid/details/1");
      ajaxWait();
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));

      // logout
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();

      openLocation("catroid/details/" + projectId);
      ajaxWait();
      assertTrue(isTextPresent(projectTitle));
      assertTrue(isTextPresent(dataset.get("projectDescription")));
      // report as inappropriate visible again after logout
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));

      openLocation("catroid/logout");
      ajaxWait();
    } catch(AssertionError e) {
      captureScreen("ReportAsInappropriateTests.reportOwnProjectAsInappropriate");
      throw e;
    } catch(Exception e) {
      captureScreen("ReportAsInappropriateTests.reportOwnProjectAsInappropriate");
      throw e;
    }
  }

  @Test(dataProvider = "loginDataAndReportOwnProjectAnonymous", groups = { "functionality", "upload" }, description = "report own project as inappropriate anonymously")
  public void reportAnonymousProjectAsInappropriate(HashMap<String, String> dataset) throws Throwable {
    try {
      // upload project
      Random rand = new Random();
      String projectTitle = "Testproject_for_report_as_inappropriate_(anonymous user)_" + rand.nextInt(9999);
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, dataset.get("projectDescription"), "", "", "", "",
          dataset.get("token")));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      assertProjectPresent(projectTitle);

      // goto details page
      openLocation("catroid/details/" + projectId);
      ajaxWait();
      assertTrue(isTextPresent(projectTitle));
      assertTrue(isTextPresent(dataset.get("projectDescription")));

      // report as inappropriate
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();

      assertTrue(isVisible(By.id("reportInappropriateReason")));
      assertTrue(isVisible(By.id("reportInappropriateReportButton")));
      assertTrue(isVisible(By.id("reportInappropriateCancelButton")));

      driver().findElement(By.id("reportInappropriateReason")).sendKeys("my selenium reason");
      driver().findElement(By.id("reportInappropriateReportButton")).click();
      ajaxWait();

      assertFalse(isVisible(By.id("reportInappropriateReason")));
      assertTrue(isTextPresent("You reported this project as inappropriate!"));

      // project is hidden
      openLocation();
      ajaxWait();
      assertFalse(isTextPresent(projectTitle));
    } catch(AssertionError e) {
      captureScreen("ReportAsInappropriateTests.testReportAnonymousProjectAsInappropriate");
      throw e;
    } catch(Exception e) {
      captureScreen("ReportAsInappropriateTests.testReportAnonymousProjectAsInappropriate");
      throw e;
    }
  }

  private String createToken(String username, String password) {
    return CommonFunctions.md5(CommonFunctions.md5(username) + ":" + CommonFunctions.md5(password));
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "loginDataAndReportOwnProject")
  public Object[][] loginDataAndReportOwnProject() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("projectDescription", "some description for my test project connected to my user id after registration and login at catroid.org.");
        put("username", CommonData.getLoginUserDefault());
        put("password", CommonData.getLoginPasswordDefault());
        put("token", createToken(CommonData.getLoginUserDefault(), CommonData.getLoginPasswordDefault()));
      }
    } } };
    return dataArray;
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "loginDataAndReportOwnProjectAnonymous")
  public Object[][] loginDataAndReportOwnProjectAnonymous() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("projectDescription", "some description for my test project connected to anonymous user id (0) after registration and login at catroid.org.");
        put("token", "0");
      }
    } } };
    return dataArray;
  }
}
