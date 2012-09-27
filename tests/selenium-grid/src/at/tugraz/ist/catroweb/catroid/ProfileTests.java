/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;
 

@Test(groups = { "catroid", "ProfileTests" })
public class ProfileTests extends BaseTest {

  @Test(dataProvider = "loginAndChangeData", groups = { "functionality", "visibility" }, description = "check profile page")
  public void profilePageChangeUserData(HashMap<String, String> dataset) throws Throwable {
    try {
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

      (new Select(driver().findElement(By.id("profileSwitchLanguage")))).selectByValue(dataset.get("registrationLanguage"));
      
      assertTrue(isTextPresent(dataset.get("registrationUsername")));
      assertTrue(isTextPresent(dataset.get("registrationEmail")));

      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      assertTrue(isVisible(By.id("profileOldPassword")));
      assertTrue(isVisible(By.id("profileNewPassword")));
      assertTrue(isVisible(By.id("profilePasswordSubmit")));

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isAjaxMessagePresent("You updated your password successfully."));

      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isAjaxMessagePresent("The old password was incorrect."));
      
      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();
      
      assertTrue(isAjaxMessagePresent("The new password is missing."));
      
      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("shortPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isAjaxMessagePresent("The old password is missing."));
      
      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();
      
      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("shortPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();
      
      assertTrue(isAjaxMessagePresent("Your password must have at least 6 characters."));
      
      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isAjaxMessagePresent("You updated your password successfully."));
      
      openLocation("catroid/profile/");
      driver().findElement(By.id("profileChangePassword")).click();
      ajaxWait();

      driver().findElement(By.id("profileOldPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileNewPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profilePasswordSubmit")).click();
      ajaxWait();

      assertTrue(isAjaxMessagePresent("You updated your password successfully."));

      driver().findElement(By.id("cityInput")).clear();
      driver().findElement(By.id("cityInput")).sendKeys(dataset.get("changedCity"));
      blur(By.id("cityInput"));
      assertTrue(isAjaxMessagePresent("You updated your hometown successfully."));
      
      openLocation("catroid/profile/");
      assertEquals(dataset.get("changedCity"), driver().findElement(By.id("cityInput")).getAttribute("value"));
      
      driver().findElement(By.id("cityInput")).clear();
      driver().findElement(By.id("cityInput")).sendKeys(dataset.get("registrationCity"));
      blur(By.id("cityInput"));
      assertTrue(isAjaxMessagePresent("You updated your hometown successfully."));
      
      openLocation("catroid/profile/");
      assertEquals(dataset.get("registrationCity"), driver().findElement(By.id("cityInput")).getAttribute("value"));
      
      Select selectCountry = new Select(driver().findElement(By.id("countrySelect")));
      selectCountry.selectByValue(dataset.get("changedCountryID"));
      blur(By.id("countrySelect"));
      assertTrue(isAjaxMessagePresent("You updated your country successfully."));
      
      openLocation("catroid/profile/");
      selectCountry = new Select(driver().findElement(By.id("countrySelect")));
      assertEquals(dataset.get("changedCountry"), selectCountry.getFirstSelectedOption().getText());
      
      selectCountry.selectByValue(dataset.get("registrationCountryID"));
      blur(By.id("countrySelect"));
      assertTrue(isAjaxMessagePresent("You updated your country successfully."));

      openLocation("catroid/profile/");
      selectCountry = new Select(driver().findElement(By.id("countrySelect")));
      assertEquals(dataset.get("registrationCountry"), selectCountry.getFirstSelectedOption().getText());

      Select selectMonth = new Select(driver().findElement(By.id("birthdayMonthSelect")));
      Select selectYear = new Select(driver().findElement(By.id("birthdayYearSelect")));
      selectMonth.selectByValue(dataset.get("changedMonthID"));
      selectYear.selectByValue(dataset.get("changedYear"));
      blur(By.id("birthdayYearSelect"));
      assertTrue(isAjaxMessagePresent("You updated your birthday successfully."));

      openLocation("catroid/profile/");
      selectMonth = new Select(driver().findElement(By.id("birthdayMonthSelect")));
      selectYear = new Select(driver().findElement(By.id("birthdayYearSelect"))); 
      assertEquals(dataset.get("changedMonth"), selectMonth.getFirstSelectedOption().getText());
      assertEquals(dataset.get("changedYear"), selectYear.getFirstSelectedOption().getText());
      
      selectMonth = new Select(driver().findElement(By.id("birthdayMonthSelect")));
      selectYear = new Select(driver().findElement(By.id("birthdayYearSelect"))); 
      selectMonth.selectByValue(dataset.get("registrationMonthID"));
      selectYear.selectByValue(dataset.get("registrationYear"));
      blur(By.id("birthdayYearSelect"));
      assertTrue(isAjaxMessagePresent("You updated your birthday successfully."));

      openLocation("catroid/profile/");
      selectMonth = new Select(driver().findElement(By.id("birthdayMonthSelect")));
      selectYear = new Select(driver().findElement(By.id("birthdayYearSelect"))); 
      assertEquals(dataset.get("registrationMonth"), selectMonth.getFirstSelectedOption().getText());
      assertEquals(dataset.get("registrationYear"), selectYear.getFirstSelectedOption().getText());

      
      Select selectGender = new Select(driver().findElement(By.id("genderSelect")));
      selectGender.selectByValue(dataset.get("changedGender"));
      blur(By.id("genderSelect"));
      assertTrue(isAjaxMessagePresent("You updated your gender successfully."));

      openLocation("catroid/profile/");
      selectGender = new Select(driver().findElement(By.id("genderSelect")));
      assertEquals(dataset.get("changedGender"), selectGender.getFirstSelectedOption().getText());

      selectGender = new Select(driver().findElement(By.id("genderSelect")));
      selectGender.selectByValue(dataset.get("registrationGender"));
      blur(By.id("genderSelect"));
      assertTrue(isAjaxMessagePresent("You updated your gender successfully."));
      
      openLocation("catroid/profile/");
      selectGender = new Select(driver().findElement(By.id("genderSelect")));
      assertEquals(dataset.get("registrationGender"), selectGender.getFirstSelectedOption().getText());

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }
  
  @Test(dataProvider = "emailTest", groups = { "functionality", "visibility" }, description = "check add/delete email")
  public void profilePageEmailTest(HashMap<String, String> dataset) throws Throwable {
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

      assertTrue(isTextPresent(dataset.get("registrationUsername")));
      assertTrue(isTextPresent("change my password"));
      assertTrue(isTextPresent("Add another email address:"));
      assertTrue(isTextPresent(dataset.get("registrationEmail")));
      
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("emailDeleteButtons")).findElement(By.tagName("button")).click();
      assertTrue(isAjaxMessagePresent("Error while deleting this e-mail address. You must have at least 1 e-mail address."));
      
      openLocation("catroid/profile/");
      
      driver().findElement(By.id("addEmailInput")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("addEmailButton")).click();
      assertTrue(isAjaxMessagePresent("The email address already exists."));
      
      openLocation("catroid/profile/");
      
      driver().findElement(By.id("addEmailInput")).sendKeys(dataset.get("secondEmail"));
      driver().findElement(By.id("addEmailButton")).click();
      assertTrue(isAjaxMessagePresent("The new e-mail address was successfully added."));

      openLocation("catroid/profile/");
      ajaxWait();

      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("emailDeleteButtons")).findElements(By.tagName("button")).get(0).click();
      assertTrue(isAjaxMessagePresent("You deleted your e-mail address successfully."));
      
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }

  
  @Test(groups = { "functionality", "visibility" }, description = "Link to my Projects")
  public void profilePageLinkToMyProjects() throws Throwable {
    try {
      // login
      login("catroid/profile/");
      
      assertTrue(isTextPresent("My Projects"));
      driver().findElement(By.id("profileMyProfileLink")).click();
      assertTrue(isTextPresent(CommonStrings.MYPROJECTS_TITLE));
      
    } catch(AssertionError e) {
      captureScreen("ProfileTests.profilePageLinkToMyProjects.");
      throw e;
    } catch(Exception e) {
      captureScreen("ProfileTests.profilePageLinkToMyProjects.");
      throw e;
    }
  }  
  
  @SuppressWarnings("serial")
  @DataProvider(name = "loginAndAddData")
  public Object[][] emailTest() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "ProfileTest" + CommonData.getRandomShortString(10));
        put("registrationPassword", "myPassword123");
        put("registrationEmail", "email_" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("secondEmail", "email_" + CommonData.getRandomShortString(10) + "@selenium.at");
        put("registrationCountry", "Austria");
        put("registrationCountryID", "at");
        put("registrationCity", "Graz");
        put("registrationMonthID", "8");
        put("registrationMonth", "August");
        put("registrationYear", "1999");
        put("registrationGender", "male");
        put("registrationLanguage", "en");
      }
    } } };
    return dataArray;
  }
  
  @SuppressWarnings("serial")
  @DataProvider(name = "loginAndChangeData")
  public Object[][] loginAndChangeData() {
    final String randomString = CommonData.getRandomShortString(10);

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "ProfileTest" + randomString);
        put("registrationPassword", "myPassword123");
        put("changedPassword", "anotherPassword123");
        put("shortPassword", "short");
        put("emptyPassword", "");
        put("registrationEmail", "email" + randomString + "@selenium.at");
        put("changedEmail", "anotherMail" + randomString + "@selenium.at");
        put("registrationCountryID", "at");
        put("registrationCountry", "Austria");
        put("changedCountryID", "de");
        put("changedCountry", "Germany");
        put("registrationCity", "Graz");
        put("changedCity", "München");
        put("registrationMonthID", "8");
        put("registrationMonth", "August");
        put("changedMonthEmpty", "");
        put("changedMonthID", "4");
        put("changedMonth", "April");
        put("registrationYear", "1999");
        put("changedYearEmpty", "");
        put("changedYear", "1978");
        put("registrationGender", "male");
        put("changedGender", "female");
        put("registrationLanguage", "en");
      }
    } } };
    return dataArray;
  }
}
