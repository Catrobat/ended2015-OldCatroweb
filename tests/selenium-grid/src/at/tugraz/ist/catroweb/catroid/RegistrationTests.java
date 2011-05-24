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

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import java.util.HashMap;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class RegistrationTests extends BaseTest {
  @Test(dataProvider = "validRegistrationData", groups = { "catroid" }, description = "check registration with valid data")
  public void validRegistration(HashMap<String, String> dataset) throws Throwable {
    // log out if necessary
    session().open(Config.TESTS_BASE_PATH + "catroid/login/");
    waitForPageToLoad();

    // wiki username creation
    String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();

    session().open(Config.TESTS_BASE_PATH + "catroid/registration/");
    waitForPageToLoad();
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationUsername']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationPassword']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationEmail']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationGender']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationMonth']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationYear']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationCountry']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationCity']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationSubmit']"));

    session().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
    session().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
    session().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
    session().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
    session().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
    session().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
    session().type("xpath=//select[@name='registrationCountry']", dataset.get("registrationCountry"));
    session().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));

    session().click("xpath=//input[@name='registrationSubmit']");
    ajaxWait();
    assertTrue(session().isTextPresent("CATROID registration successfull!"));
    assertTrue(session().isTextPresent("BOARD registration successfull!"));
    assertTrue(session().isTextPresent("WIKI registration successfull!"));

    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();

    session().click("headerProfileButton");
    session().type("loginUsername", dataset.get("registrationUsername"));
    session().type("loginPassword", dataset.get("registrationPassword"));
    session().click("loginSubmitButton");
    ajaxWait();

    assertTrue(session().isTextPresent("Newest Projects"));

    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("xpath=//button[@id='menuLogoutButton']"));

    session().click("menuForumButton");
    session().selectWindow("board");
    waitForPageToLoad();
    assertFalse(session().isTextPresent("Login"));
    assertTrue(session().isTextPresent("Logout"));
    session().close();
    session().selectWindow(null);

    session().click("menuWikiButton");
    session().selectWindow("wiki");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(wikiUsername));
    session().click("xpath=//li[@id='pt-preferences']/a");
    waitForPageToLoad();
    assertEquals("Preferences", session().getText("firstHeading"));
    assertFalse(session().isTextPresent("Not logged in"));
    session().close();
    session().selectWindow(null);

    session().click("menuLogoutButton");
    waitForPageToLoad();
    ajaxWait();

    session().click("headerProfileButton");
    assertTrue(session().isVisible("loginSubmitButton"));
    assertTrue(session().isVisible("loginUsername"));
    assertTrue(session().isVisible("loginPassword"));

    session().click("headerMenuButton");
    waitForPageToLoad();

    clickAndWaitForPopUp("menuForumButton", "board");
    assertTrue(session().isTextPresent("Login"));
    assertFalse(session().isTextPresent("Logout"));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertFalse(session().isTextPresent(wikiUsername));
    closePopUp();
  }

  @Test(dataProvider = "invalidRegistrationData", groups = { "catroid" }, description = "check registration with invalid data")
  public void invalidRegistration(HashMap<String, String> dataset) throws Throwable {
    // wiki username creation
    String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();

    session().open(Config.TESTS_BASE_PATH + "catroid/registration/");
    waitForPageToLoad();
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationUsername']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationPassword']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationEmail']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationGender']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationMonth']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationYear']"));
    assertTrue(session().isElementPresent("xpath=//select[@name='registrationCountry']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationCity']"));
    assertTrue(session().isElementPresent("xpath=//input[@name='registrationSubmit']"));

    session().type("xpath=//input[@name='registrationUsername']", dataset.get("registrationUsername"));
    session().type("xpath=//input[@name='registrationPassword']", dataset.get("registrationPassword"));
    session().type("xpath=//input[@name='registrationEmail']", dataset.get("registrationEmail"));
    session().type("xpath=//select[@name='registrationGender']", dataset.get("registrationGender"));
    session().type("xpath=//select[@name='registrationMonth']", dataset.get("registrationMonth"));
    session().type("xpath=//select[@name='registrationYear']", dataset.get("registrationYear"));
    session().type("xpath=//select[@name='registrationCountry']", dataset.get("registrationCountry"));
    session().type("xpath=//input[@name='registrationCity']", dataset.get("registrationCity"));

    session().click("xpath=//input[@name='registrationSubmit']");
    ajaxWait();
    assertTrue(session().isTextPresent(dataset.get("expectedError")));
    assertFalse(session().isTextPresent("CATROID registration successfull!"));
    assertFalse(session().isTextPresent("BOARD registration successfull!"));
    assertFalse(session().isTextPresent("WIKI registration successfull!"));

    session().open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();

    session().click("headerProfileButton");
    session().type("loginUsername", dataset.get("registrationUsername"));
    session().type("loginPassword", dataset.get("registrationPassword"));
    session().click("loginSubmitButton");
    ajaxWait();

    session().getAlert();
    session().click("headerMenuButton");
    waitForPageToLoad();

    assertTrue(session().isVisible("menuLoginButton"));

    clickAndWaitForPopUp("menuForumButton", "board");
    assertTrue(session().isTextPresent("Login"));
    assertFalse(session().isTextPresent("Logout"));
    closePopUp();

    clickAndWaitForPopUp("menuWikiButton", "wiki");
    assertFalse(session().isTextPresent(wikiUsername));
    closePopUp();
  }

  @DataProvider(name = "validRegistrationData")
  public Object[][] validRegistrationData() {
    final String randomString1 = CommonData.getRandomShortString();
    final String randomString2 = CommonData.getRandomShortString();

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "myUnitTest" + randomString1);
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
        put("registrationUsername", "myUnitTest" + randomString2);
        put("registrationPassword", "anotherPassword123");
        put("registrationEmail", "test" + randomString2 + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "12");
        put("registrationYear", "1971");
        put("registrationCountry", "DE");
        put("registrationCity", "Berlin");
      }
    } } };
    return dataArray;
  }

  @DataProvider(name = "invalidRegistrationData")
  public Object[][] invalidRegistrationData() {
    final String randomString1 = CommonData.getRandomShortString();
    final String randomString2 = CommonData.getRandomShortString();

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "myUnitTest_" + randomString1);
        put("registrationPassword", "myPassword123");
        put("registrationEmail", "test" + randomString1 + "@selenium.at");
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
        put("registrationEmail", "test" + randomString2 + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "12");
        put("registrationYear", "1971");
        put("registrationCountry", "DE");
        put("registrationCity", "Berlin");
        put("expectedError", "This nickname is on the blacklist and not allowed.");
      }
    } } };
    return dataArray;
  }
}
