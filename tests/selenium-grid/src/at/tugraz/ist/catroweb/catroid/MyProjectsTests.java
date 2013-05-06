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

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.CommonData;
import at.tugraz.ist.catroweb.common.CommonFunctions;

@Test(groups = { "catroid", "MyProjectsTests" })
public class MyProjectsTests extends BaseTest {

  @Test(groups = { "functionality" }, description = "try to delete one of my projects")
  public void deleteMyProject() throws Throwable {
    try {
      // login
      openLocation();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      driver().findElement(By.id("loginUsername")).sendKeys(CommonData.getLoginUserDefault());
      driver().findElement(By.id("loginPassword")).sendKeys(CommonData.getLoginPasswordDefault());

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();
      
      // upload a project
      String title = "Delete this project";
      String response = projectUploader.upload(CommonData.getUploadPayload(title, "", "", "", "", "", "", ""));
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      // delete uploaded project
      openLocation("myprojects");
      assertTrue(isTextPresent(title));
      assertTrue(isVisible(By.id(id)));
      
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id(id)).click();
      ajaxWait();
      
      assertFalse(isTextPresent(title));
    } catch(AssertionError e) {
      captureScreen("deleteMyProject.deleteMyProject");
      throw e;
    } catch(Exception e) {
      captureScreen("deleteMyProject.deleteMyProject");
      throw e;
    }
  }
}
