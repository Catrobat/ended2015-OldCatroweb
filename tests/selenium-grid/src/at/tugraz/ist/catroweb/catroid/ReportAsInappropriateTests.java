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
          dataset.get("username"), dataset.get("token")));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");

      // goto details page
      login("details/" + projectId);
      assertTrue(isTextPresent(projectTitle.toUpperCase()));
      assertTrue(isTextPresent(dataset.get("projectDescription")));

      // report as inappropriate visible
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));

      // check if reportAsInappropriate button is visible for a foreign project
      openLocation("details/1");
      ajaxWait();
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));

      logout("details/" + projectId);
      assertTrue(isTextPresent(projectTitle.toUpperCase()));
      assertTrue(isTextPresent(dataset.get("projectDescription")));
      // report as inappropriate still visible after logout
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
    } catch(AssertionError e) {
      captureScreen("ReportAsInappropriateTests.reportOwnProjectAsInappropriate");
      throw e;
    } catch(Exception e) {
      captureScreen("ReportAsInappropriateTests.reportOwnProjectAsInappropriate");
      throw e;
    }
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "loginDataAndReportOwnProject")
  public Object[][] loginDataAndReportOwnProject() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("projectDescription", "some description for my test project connected to my user id after registration and login at catroid.org.");
        put("username", CommonData.getLoginUserDefault());
        put("password", CommonData.getLoginPasswordDefault());
        put("token", CommonFunctions.getAuthenticationToken(CommonData.getLoginUserDefault()));
      }
    } } };
    return dataArray;
  }
}
