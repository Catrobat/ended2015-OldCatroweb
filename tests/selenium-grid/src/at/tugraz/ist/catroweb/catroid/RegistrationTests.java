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

import org.openqa.selenium.Alert;
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
      openLocation("registration/");
      
      assertTrue(isTextPresent(CommonStrings.REGISTRATION_PAGE_TITLE.toUpperCase()));
      
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
      
      assertTrue((driver().findElement(By.id("registrationSubmit"))).getText().contains(CommonStrings.REGISTRATION_SUBMIT.toUpperCase()));
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
      openLocation("registration/");

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();

      Alert alert = driver().switchTo().alert();
      String message = alert.getText().replace("<", "&lt;").replace(">", "&gt;");
      alert.accept();
      assertTrue(message.contains(dataset.get("expectedError")));
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
      openLocation("registration/");

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

      assertTrue(isTextPresent(dataset.get("registrationUsername")));
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

      openLocation("registration/");

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationCountry")).sendKeys(dataset.get("registrationCountry"));
      driver().findElement(By.id("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("registrationSubmit")).click();

      Alert alert = driver().switchTo().alert();
      String message = alert.getText().replace("<", "&lt;").replace(">", "&gt;");
      alert.accept();
      assertTrue(message.contains(dataset.get("expectedError")));
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
        put("expectedError", "The nickname is missing.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError", "The password is missing.");
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
        put("expectedError", "The nickname is invalid. Underscores (_) are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "password" + CommonData.getRandomShortString(10));
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError", "The email address is missing.");
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
        put("expectedError", "The nickname is invalid. Hash signs (#) are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "password" + CommonData.getRandomShortString(10));
        put("registrationEmail", "email" + CommonData.getRandomShortString(10) + "@catroid.org");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError", "The country is missing.");
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
        put("expectedError", "The nickname is invalid. Vertical bars (|) are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "12");
        put("registrationEmail", "");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError", "Your password must have at least");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "password" + CommonData.getRandomShortString(10));
        put("registrationEmail", "invalidMailAddress");
        put("registrationCountry", "");
        put("registrationMonth", "");
        put("registrationYear", "");
        put("registrationGender", "");
        put("expectedError", "The email address is not valid.");
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
        put("expectedError", "The nickname is invalid. Curly braces ({ or }) are not allowed.");
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
        put("expectedError", "The nickname is invalid. Less than or greater than signs (&lt; or &gt;) are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "usernameIsTheSameAsThePassword");
        put("registrationPassword", "usernameIsTheSameAsThePassword");
        put("registrationEmail", "testuser-selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError", "The password must differ from the nickname.");
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
        put("expectedError", "The nickname is invalid. Square brackets ([ or ]) are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "validPassword");
        put("registrationEmail", "webmaster@catroid.org");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError", "This email address already exists.");
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
        put("expectedError", "The nickname is invalid. Spaces (\" \") are not allowed.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "123456789012345678901234567890123");
        put("registrationEmail", "testuser@selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError", "Your password can have a maximum of");
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
        put("expectedError", "There are insulting words in the username field!");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser-selenium.com");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError", "The email address is not valid.");
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
        put("expectedError", "The nickname is invalid.");
      }
    } }, { new HashMap<String, String>() {
      {
        put("registrationUsername", "username" + CommonData.getRandomShortString(10));
        put("registrationPassword", "validPassword");
        put("registrationEmail", "testuser@selenium");
        put("registrationCountry", "Austria");
        put("registrationMonth", "February");
        put("registrationYear", "2008");
        put("registrationGender", "female");
        put("expectedError", "The email address is not valid.");
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
        put("expectedError", "This nickname is on the blacklist and not allowed.");
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
        put("expectedError", "This nickname already exists.");
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
