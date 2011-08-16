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

@Test(groups = { "catroid", "RegistrationTests" })
public class RegistrationTests extends BaseTest {

  @Test(dataProvider = "validRegistrationData", groups = { "functionality" }, description = "check registration with valid data")
  public void validRegistration(HashMap<String, String> dataset) throws Throwable {
    try {
      // wiki username creation
      String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();

      openLocation("catroid/registration/");

      assertTrue(isElementPresent(By.name("registrationUsername")));
      assertTrue(isElementPresent(By.name("registrationPassword")));
      assertTrue(isElementPresent(By.name("registrationEmail")));
      assertTrue(isElementPresent(By.name("registrationMonth")));
      assertTrue(isElementPresent(By.name("registrationYear")));
      assertTrue(isElementPresent(By.name("registrationGender")));
      assertTrue(isElementPresent(By.name("registrationCountry")));
      assertTrue(isElementPresent(By.name("registrationCity")));
      assertTrue(isElementPresent(By.name("registrationSubmit")));

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
      assertTrue(isTextPresent(dataset.get("registrationUsername") + "'s Profile"));

      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isTextPresent("You are logged in as " + dataset.get("registrationUsername") + "!"));
      assertTrue(isElementPresent(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      driver().findElement(By.id("headerMenuButton")).click();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertFalse(isTextPresent("Login"));
      assertTrue(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      driver().findElement(By.id("pt-preferences")).findElement(By.tagName("a")).click();
      assertEquals("Preferences", driver().findElement(By.id("firstHeading")).getText());
      assertFalse(isTextPresent("Not logged in"));
      closePopUp();
      
      openLocation();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      ajaxWait();
      
      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isVisible(By.id("loginSubmitButton")));
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));

      driver().findElement(By.id("headerCancelButton")).click();
      ajaxWait();
      driver().findElement(By.id("headerMenuButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertTrue(isTextPresent("Login"));
      assertFalse(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertFalse(isTextPresent(wikiUsername));
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
      
      assertTrue(isElementPresent(By.name("registrationUsername")));
      assertTrue(isElementPresent(By.name("registrationPassword")));
      assertTrue(isElementPresent(By.name("registrationEmail")));
      assertTrue(isElementPresent(By.name("registrationMonth")));
      assertTrue(isElementPresent(By.name("registrationYear")));
      assertTrue(isElementPresent(By.name("registrationGender")));
      assertTrue(isElementPresent(By.name("registrationCountry")));
      assertTrue(isElementPresent(By.name("registrationCity")));
      assertTrue(isElementPresent(By.name("registrationSubmit")));

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
      
      assertTrue(isTextPresent(dataset.get("expectedError")));
      assertFalse(isTextPresent("CATROID registration successfull!"));
      assertFalse(isTextPresent("BOARD registration successfull!"));
      assertFalse(isTextPresent("WIKI registration successfull!"));

      openLocation();
      driver().findElement(By.id("headerProfileButton")).click();
      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertTrue(isTextPresent("Login"));
      assertFalse(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertFalse(isTextPresent(wikiUsername));
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
        put("registrationUsername", "registrationUsernameWith" + CommonData.getRandomShortString(10));
        put("registrationPassword", "registrationPassword" + CommonData.getRandomShortString(10));
        put("registrationEmail", "test" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationGender", "male");
        put("registrationMonth", "10");
        put("registrationYear", "1911");
        put("registrationCountry", "CN");
        put("registrationCity", "Bejing");
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
    } }, { new HashMap<String, String>() {
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
