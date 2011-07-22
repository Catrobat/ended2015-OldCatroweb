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

      selenium().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
      selenium().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
      selenium().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
      selenium().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
      selenium().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
      selenium().type("xpath=//select[@name='registrationCountry']", dataset.get("registrationCountry"));
      selenium().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));

      selenium().click("xpath=//input[@name='registrationSubmit']");
      ajaxWait();
      waitForPageToLoad();

      selenium().click("headerProfileButton");
      assertTrue(selenium().isTextPresent("You are logged in as " + dataset.get("registrationUsername") + "!"));
      assertTrue(selenium().isElementPresent("logoutSubmitButton"));
      selenium().click("headerCancelButton");

      assertTrue(selenium().isTextPresent(dataset.get("registrationUsername") + "\'s Profile"));
      assertTrue(selenium().isTextPresent("change my password"));
      assertTrue(selenium().isTextPresent(dataset.get("registrationEmail")));
      assertTrue(selenium().isTextPresent("from "));

      selenium().click("xpath=//a[@id='profileChangePasswordOpen']");
      ajaxWait();
      selenium().click("xpath=//a[@id='profileChangePasswordClose']");
      ajaxWait();
      selenium().click("xpath=//a[@id='profileChangePasswordOpen']");
      ajaxWait();

      assertTrue(selenium().isVisible("xpath=//input[@id='profileOldPassword']"));
      assertTrue(selenium().isVisible("xpath=//input[@id='profileNewPassword']"));
      assertTrue(selenium().isVisible("xpath=//input[@id='profilePasswordSubmit']"));

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("changedPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      waitForTextPresent("You updated your password successfully.");

      selenium().click("xpath=//a[@id='profileChangePasswordOpen']");
      ajaxWait();

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("shortPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      assertTrue(selenium().isTextPresent("The old password was incorrect."));
      assertTrue(selenium().isTextPresent("The new password must have at least 6 characters."));

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("changedPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("emptyPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      assertTrue(selenium().isTextPresent("The new password is missing."));

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("emptyPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("shortPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      assertTrue(selenium().isTextPresent("The old password is missing."));
      assertTrue(selenium().isTextPresent("The new password must have at least 6 characters."));

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("changedPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("registrationPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      assertTrue(selenium().isTextPresent("You updated your password successfully."));

      selenium().click("xpath=//a[@id='profileChangePasswordOpen']");
      ajaxWait();

      selenium().type("xpath=//input[@id='profileOldPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@id='profileNewPassword']", dataset.get("registrationPassword"));
      selenium().click("xpath=//input[@id='profilePasswordSubmit']");
      ajaxWait();

      assertTrue(selenium().isTextPresent("You updated your password successfully."));

      selenium().click("xpath=//a[@id='profileChangeEmailOpen']");
      selenium().type("xpath=//input[@id='profileEmail']", dataset.get("changedEmail"));
      selenium().click("xpath=//input[@id='profileEmailSubmit']");
      waitForPageToLoad();
      ajaxWait();

      assertTrue(selenium().isTextPresent(dataset.get("changedEmail")));

      selenium().click("xpath=//a[@id='profileChangeEmailOpen']");
      ajaxWait();
      Thread.sleep(Config.TIMEOUT_THREAD);

      selenium().type("xpath=//input[@id='profileEmail']", dataset.get("registrationEmail"));
      selenium().click("xpath=//input[@id='profileEmailSubmit']");
      waitForPageToLoad();
      ajaxWait();

      assertTrue(selenium().isTextPresent(dataset.get("registrationEmail")));

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
