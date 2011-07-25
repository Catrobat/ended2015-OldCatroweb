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

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "ProfileTests" })
public class ProfileTests extends BaseTest {

  @Test(dataProvider = "loginData", groups = { "functionality", "visibility" }, description = "check profile page")
  public void profilePage(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation("catroid/registration/");
      
      driver().findElement(By.name("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.name("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.name("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.name("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.name("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.name("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.name("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.name("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.name("registrationSubmit")).click();
      ajaxWait();

      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isTextPresent("You are logged in as " + dataset.get("registrationUsername") + "!"));
      assertTrue(isElementPresent(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      assertTrue(isTextPresent(dataset.get("registrationUsername") + "\'s Profile"));
      assertTrue(isTextPresent("change my password"));
      assertTrue(isTextPresent(dataset.get("registrationEmail")));
      assertTrue(isTextPresent("from "));

      driver().findElement(By.id("profileChangePasswordOpen")).click();
      ajaxWait();
      driver().findElement(By.id("profileChangePasswordClose")).click();
      ajaxWait();
      driver().findElement(By.id("profileChangePasswordOpen")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("profileOldPassword")));
      assertTrue(isVisible(By.id("profileNewPassword")));
      assertTrue(isVisible(By.id("profilePasswordSubmit")));

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("You updated your password successfully."));

      driver().findElement(By.id("profileChangePasswordOpen")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).clear();
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).clear();
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("The old password was incorrect."));
      assertTrue(isTextPresent("The new password must have at least 6 characters."));

      driver().findElement(By.id("profileOldPassword")).clear();
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileNewPassword")).clear();
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("The new password is missing."));

      driver().findElement(By.id("profileOldPassword")).clear();
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.id("profileNewPassword")).clear();
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("shortPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("The old password is missing."));
      assertTrue(isTextPresent("The new password must have at least 6 characters."));

      driver().findElement(By.id("profileOldPassword")).clear();
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileNewPassword")).clear();
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("You updated your password successfully."));

      driver().findElement(By.id("profileChangePasswordOpen")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).clear();
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).clear();
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent("You updated your password successfully."));

      driver().findElement(By.id("profileChangeEmailOpen")).click();
      driver().findElement(By.id("profileEmail")).sendKeys(dataset.get("changedEmail"));
      driver().findElement(By.id("profileEmailSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(dataset.get("changedEmail")));

      driver().findElement(By.id("profileChangeEmailOpen")).click();
      ajaxWait();

      driver().findElement(By.id("profileEmail")).clear();
      driver().findElement(By.id("profileEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("profileEmailSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(dataset.get("registrationEmail")));

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "loginData")
  public Object[][] loginData() {
    final String randomString = CommonData.getRandomShortString(10);

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "ProfileTest" + randomString);
        put("registrationPassword", "myPassword123");
        put("changedPassword", "anotherPassword123");
        put("shortPassword", "short");
        put("emptyPassword", "");
        put("registrationEmail", "test" + randomString + "@selenium().at");
        put("changedEmail", "other" + randomString + "@selenium().at");
        put("registrationGender", "male");
        put("registrationMonth", "2");
        put("registrationYear", "1980");
        put("registrationCountry", "AT");
        put("registrationCity", "Graz");
      }
    } } };
    return dataArray;
  }
}
