/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
  
  @Test(groups = { "visibility" }, description = "check expected elements are visible and texts are present")
  public void checkElementsVisibleAndTextPresent() throws Throwable {
    try {
      openLocation("catroid/registration/");
      
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_PAGE_TITLE));
      assertTrue(isElementPresent(By.id("registrationErrorMsg")));
      assertFalse(isVisible(By.id("registrationErrorMsg")));
      
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_NICKNAME));
      assertTrue(isElementPresent(By.id("registrationUsername")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_NICKNAME_INFO));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_PASSWORD));
      assertTrue(isElementPresent(By.id("registrationPassword")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_EMAIL));
      assertTrue(isElementPresent(By.id("registrationEmail")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_EMAIL_INFO));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_COUNTRY));
      assertTrue(isElementPresent(By.id("registrationCountry")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_CITY));
      assertTrue(isElementPresent(By.id("registrationCity")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_BIRTHDAY));
      assertTrue(isElementPresent(By.id("registrationMonth")));
      assertTrue(isElementPresent(By.id("registrationYear")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_GENDER));
      assertTrue(isElementPresent(By.id("registrationGender")));
      
      assertTrue((driver().findElement(By.id("registrationSubmit"))).
          getAttribute("value").contains(CommonStrings.REGISTRATION_SUBMIT));
      assertTrue(isElementPresent(By.id("registrationSubmit")));
      
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_LOGIN));
      assertTrue(isElementPresent(By.id("registrationLogin")));
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_FORGOT_PWD));
      assertTrue(isElementPresent(By.id("forgotPassword")));
    } catch(AssertionError e) {      
      captureScreen("RegistrationTests.checkElementsVisible." + CommonFunctions.getTimeStamp()); 
      throw e;
    } catch(Exception e) {
      captureScreen("RegistrationTests.checkElementsVisible." + CommonFunctions.getTimeStamp()); 
      throw e;
    }
  }

  @Test(dataProvider = "triggerErrorsData", groups = { "functionality" }, description = "trigger all error messages and check whether they present")
  public void checkErrorMessages(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation("catroid/registration/");

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("registrationErrorMsg")));
      assertTrue(isTextPresent(dataset.get("expectedError1")));
      assertTrue(isTextPresent(dataset.get("expectedError2")));
    } catch(AssertionError e) {      
      captureScreen("RegistrationTests.checkElementsVisible." + CommonFunctions.getTimeStamp()); 
      throw e;
    } catch(Exception e) {
      captureScreen("RegistrationTests.checkElementsVisible." + CommonFunctions.getTimeStamp()); 
      throw e;
    }
  }
 
  @Test(dataProvider = "validRegistrationData", groups = { "functionality" }, description = "check registration with valid data")
  public void validRegistration(HashMap<String, String> dataset) throws Throwable {
    try {
      // wiki username creation
      String wikiUsername = dataset.get("registrationUsername").substring(0, 1).toUpperCase() + dataset.get("registrationUsername").substring(1).toLowerCase();
      openLocation("catroid/registration/");

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(dataset.get("registrationUsername") + "'s Profile"));
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isTextPresent("You are logged in as " + dataset.get("registrationUsername") + "!"));
      assertTrue(isElementPresent(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();
      ajaxWait();
      driver().findElement(By.id("headerMenuButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertFalse(isTextPresent("Login"));
      assertTrue(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      waitForElementPresent(By.id("pt-preferences"));
      driver().findElement(By.id("pt-preferences")).findElement(By.tagName("a")).click();
      assertTrue(containsElementText(By.id("firstHeading"), "Preferences"));
      assertFalse(isTextPresent("Not logged in"));
      closePopUp();
      
      openLocation();
      assertTrue(isVisible(By.id("headerProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      ajaxWait();
      
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
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

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();
      
      assertTrue(isTextPresent(dataset.get("expectedError")));

      openLocation();
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
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
  @DataProvider(name = "triggerErrorsData")
  public Object[][] triggerErrorsData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "");
        put("registrationPassword", "");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError1", "The nickname is missing.");
        put("expectedError2", "The password is missing.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My_Nick");
        put("registrationPassword", "");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError1", "The nickname is invalid. Underscores (_) are not allowed.");
        put("expectedError2", "The email address is missing.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My#Nick");
        put("registrationPassword", "");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError1", "The nickname is invalid. Hash signs (#) are not allowed.");
        put("expectedError2", "The country is missing.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My|Nick");
        put("registrationPassword", "12");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError1", "The nickname is invalid. Vertical bars (|) are not allowed.");
        put("expectedError2", "Your password must have at least");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My(Nick}");
        put("registrationPassword", "");
        put("registrationEmail", "invalidMailAddress");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError1", "The nickname is invalid. Curly braces ({ or }) are not allowed.");
        put("expectedError2", "The email address is not valid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My<Nick>");
        put("registrationPassword", "My<Nick>");
        put("registrationEmail", "testuser-selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "The nickname is invalid. Less than or greater than signs (< or >) are not allowed.");
        put("expectedError2", "The password must differ from the nickname.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My[Nick]");
        put("registrationPassword", "validPassword");
        put("registrationEmail", "webmaster@catroid.org");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "The nickname is invalid. Square brackets ([ or ]) are not allowed.");
        put("expectedError2", "This email address already exists.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "My Nick");
        put("registrationPassword", "123456789012345678901234567890123");
        put("registrationEmail", "testuser@selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "The nickname is invalid. Spaces (\" \") are not allowed.");
        put("expectedError2", "and maximal");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "Fuck");
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser-selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "There are insulting words in the username field!");
        put("expectedError2", "The email address is not valid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "127.0.0.1");
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser@selenium");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "The nickname is invalid.");
        put("expectedError2", "The email address is not valid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "catroid");
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser@selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "This nickname is on the blacklist and not allowed.");
        put("expectedError2", "");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "anonymous");
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser@selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError1", "This nickname already exists.");
        put("expectedError2", "");
      }
    } } };
    return dataArray;
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
        put("registrationUsername", "registrationUsername" + randomString1);
        put("registrationPassword", "registrationPassword" + randomString2);
        put("registrationEmail", "test" + randomString1 + "@selenium.de");
        put("registrationGender", "female");
        put("registrationMonth", "December");
        put("registrationYear", "1971");
        put("registrationCountry", "Germany");
        put("registrationCity", "Berlin");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", chineseString1);
        put("registrationPassword", chineseString2);
        put("registrationEmail", "test" + randomString3 + "@selenium.cn");
        put("registrationGender", "male");
        put("registrationMonth", "October");
        put("registrationYear", "1911");
        put("registrationCountry", "China");
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
        put("registrationMonth", "February");
        put("registrationYear", "1980");
        put("registrationCountry", "Austria");
        put("registrationCity", "Graz");
        put("expectedError", "The nickname is invalid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "kittyroid");
        put("registrationPassword", "anotherPassword123");
        put("registrationEmail", "test" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationGender", "female");
        put("registrationMonth", "December");
        put("registrationYear", "1971");
        put("registrationCountry", "Germany");
        put("registrationCity", "Berlin");
        put("expectedError", "This nickname is on the blacklist and not allowed.");
      }
    } } };
    return dataArray;
  }
}
