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

@Test(groups = { "catroid", "RegistrationTests" })
public class RegistrationTests extends BaseTest {

  @Test(dataProvider = "validRegistrationData", groups = { "functionality" }, description = "check registration with valid data")
  public void validRegistration(HashMap<String, String> dataset) throws Throwable {
    try {
      // wiki username creation
      String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();

      openLocation("catroid/registration/");
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationUsername']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationPassword']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationEmail']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationGender']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationMonth']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationYear']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationCountry']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationCity']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationSubmit']"));

      selenium().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
      selenium().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
      selenium().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
      selenium().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
      selenium().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
      selenium().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
      selenium().type("xpath=//select  [@name='registrationCountry']", dataset.get("registrationCountry"));
      selenium().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));

      selenium().click("xpath=//input[@name='registrationSubmit']");
      ajaxWait();
      waitForPageToLoad();
      assertTrue(selenium().isTextPresent(dataset.get("registrationUsername") + "'s Profile"));

      selenium().click("headerProfileButton");
      assertTrue(selenium().isTextPresent("You are logged in as " + dataset.get("registrationUsername") + "!"));
      assertTrue(selenium().isElementPresent("logoutSubmitButton"));
      selenium().click("headerCancelButton");

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertFalse(selenium().isTextPresent("Login"));
      assertTrue(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertTrue(selenium().isTextPresent(wikiUsername));
      selenium().click("xpath=//li[@id='pt-preferences']/a");
      waitForPageToLoad();
      assertEquals("Preferences", selenium().getText("firstHeading"));
      assertFalse(selenium().isTextPresent("Not logged in"));
      closePopUp();
      
      openLocation();
      assertTrue(selenium().isVisible("headerProfileButton"));
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("logoutSubmitButton"));
      selenium().click("logoutSubmitButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      
      selenium().click("headerProfileButton");
      assertTrue(selenium().isVisible("loginSubmitButton"));
      assertTrue(selenium().isVisible("loginUsername"));
      assertTrue(selenium().isVisible("loginPassword"));

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertTrue(selenium().isTextPresent("Login"));
      assertFalse(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertFalse(selenium().isTextPresent(wikiUsername));
      closePopUp();

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {      
      captureScreen("RegistrationTests.validRegistration." + CommonFunctions.getTimeStamp()); 
      log("RegistrationTests.validRegistration.: " + dataset.get("registrationUsername"));      
      throw e;
    } catch(Exception e) {
      captureScreen("RegistrationTests.validRegistration." + CommonFunctions.getTimeStamp()); 
      log("RegistrationTests.validRegistration.: " + dataset.get("registrationUsername"));      
      throw e;
    }
  }

  @Test(dataProvider = "invalidRegistrationData", groups = { "functionality", "popupwindows" }, description = "check registration with invalid data")
  public void invalidRegistration(HashMap<String, String> dataset) throws Throwable {
    try {
      // wiki username creation
      String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();

      openLocation("catroid/registration/");
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationUsername']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationPassword']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationEmail']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationGender']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationMonth']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationYear']"));
      assertTrue(selenium().isElementPresent("xpath=//select[@name='registrationCountry']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationCity']"));
      assertTrue(selenium().isElementPresent("xpath=//input[@name='registrationSubmit']"));

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
      waitForTextPresent(dataset.get("expectedError"));
      assertFalse(selenium().isTextPresent("CATROID registration successfull!"));
      assertFalse(selenium().isTextPresent("BOARD registration successfull!"));
      assertFalse(selenium().isTextPresent("WIKI registration successfull!"));

      openLocation();
      selenium().click("headerProfileButton");
      selenium().type("loginUsername", dataset.get("registrationUsername"));
      selenium().type("loginPassword", dataset.get("registrationPassword"));
      selenium().click("loginSubmitButton");
      ajaxWait();

      selenium().click("headerMenuButton");
      waitForPageToLoad();

      clickAndWaitForPopUp("menuForumButton", "board");
      assertTrue(selenium().isTextPresent("Login"));
      assertFalse(selenium().isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp("menuWikiButton", "wiki");
      assertFalse(selenium().isTextPresent(wikiUsername));
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("RegistrationTests.invalidRegistration." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      captureScreen("RegistrationTests.invalidRegistration." + dataset.get("registrationUsername"));
      throw e;
    }
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "validRegistrationData")
  public Object[][] validRegistrationData() {
    final String randomString1 = CommonData.getRandomShortString(10);
    final String randomString2 = CommonData.getRandomShortString(10);
    final String randomString3 = CommonData.getRandomShortString(10);
    final String chineseString1 = CommonData.getRandomChineseString(10);
    final String chineseString2 = CommonData.getRandomChineseString(10);

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "RegistrationTestValid" + randomString1);
        put("registrationPassword", "myPassword123");
        put("registrationEmail", "test" + randomString1 + "@selenium.at");
        put("registrationGender", "male");
        put("registrationMonth", "2");
        put("registrationYear", "1980");
        put("registrationCountry", "AT");
        put("registrationCity", "Graz");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "RegistrationTestValid" + randomString2);
        put("registrationPassword", "anotherPassword123");
        put("registrationEmail", "test" + randomString2 + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "12");
        put("registrationYear", "1971");
        put("registrationCountry", "DE");
        put("registrationCity", "Berlin");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", chineseString1);
        put("registrationPassword", chineseString2);
        put("registrationEmail", "test" + randomString3 + "@selenium.at");
        put("registrationGender", "male");
        put("registrationMonth", "10");
        put("registrationYear", "1911");
        put("registrationCountry", "CN");
        put("registrationCity", "Bejing");
      }
    } } };
    return dataArray;
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "invalidRegistrationData")
  public Object[][] invalidRegistrationData() {

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "myUnitTest_" + CommonData.getRandomShortString(10));
        put("registrationPassword", "myPassword123");
        put("registrationEmail", "test" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationGender", "male");
        put("registrationMonth", "2");
        put("registrationYear", "1980");
        put("registrationCountry", "AT");
        put("registrationCity", "Graz");
        put("expectedError", "The nickname is invalid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "kittyroid");
        put("registrationPassword", "anotherPassword123");
        put("registrationEmail", "test" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "12");
        put("registrationYear", "1971");
        put("registrationCountry", "DE");
        put("registrationCity", "Berlin");
        put("expectedError", "This nickname is on the blacklist and not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "fuck");
        put("registrationPassword", "anotherPassword456");
        put("registrationEmail", "test" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "12");
        put("registrationYear", "1971");
        put("registrationCountry", "DE");
        put("registrationCity", "Berlin");
        put("expectedError", "There are insulting words in the username field!");
      }
    } } };
    return dataArray;
  }
}
