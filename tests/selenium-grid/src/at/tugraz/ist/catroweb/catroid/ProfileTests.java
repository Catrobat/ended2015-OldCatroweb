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

      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isElementPresent(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      driver().findElement(By.id("profileChangeLanguageOpen")).click();
      (new Select(driver().findElement(By.id("profileSwitchLanguage")))).selectByValue(dataset.get("registrationLanguage"));
      
      assertTrue(isTextPresent(dataset.get("registrationUsername") + "\'s Profile"));
      assertTrue(isTextPresent("change my password"));
      assertTrue(isTextPresent(dataset.get("registrationEmail")));

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

      driver().findElement(By.id("email0")).click();
      ajaxWait();
      driver().findElement(By.id("profileEmail")).clear();
      driver().findElement(By.id("profileEmail")).sendKeys(dataset.get("changedEmail"));
      driver().findElement(By.id("buttonProfileChangeEmailSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedEmail")));

      driver().findElement(By.id("email0")).click();
      ajaxWait();
      driver().findElement(By.id("profileEmail")).clear();
      driver().findElement(By.id("profileEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("buttonProfileChangeEmailSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationEmail")));
      
      driver().findElement(By.id("profileChangeCityOpen")).click();
      ajaxWait();
      driver().findElement(By.id("profileCity")).clear();
      driver().findElement(By.id("profileCity")).sendKeys(dataset.get("changedCity"));
      driver().findElement(By.id("profileCitySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedCity")));
      
      driver().findElement(By.id("profileChangeCityOpen")).click();
      ajaxWait();
      driver().findElement(By.id("profileCity")).clear();
      driver().findElement(By.id("profileCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("profileCitySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationCity")));

      driver().findElement(By.id("profileChangeCountryOpen")).click();
      ajaxWait();
      Select selectCountry = new Select(driver().findElement(By.id("profileCountry")));
      selectCountry.selectByValue(dataset.get("changedCountryID"));
      driver().findElement(By.id("profileCountrySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedCountry")));
      
      driver().findElement(By.id("profileChangeCountryOpen")).click();
      ajaxWait();
      selectCountry = new Select(driver().findElement(By.id("profileCountry")));
      selectCountry.selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("profileCountrySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationCountry")));    
      
      
      driver().findElement(By.id("profileChangeBirthOpen")).click();
      ajaxWait();
      Select selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      Select selectYear = new Select(driver().findElement(By.id("profileYear")));
      selectMonth.selectByValue(dataset.get("changedMonthID"));
      selectYear.selectByValue(dataset.get("changedYearEmpty"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent("Please select the month and the year of your birthday"));
      
      selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      selectYear = new Select(driver().findElement(By.id("profileYear")));
      selectMonth.selectByValue(dataset.get("changedMonthEmpty"));
      selectYear.selectByValue(dataset.get("changedYear"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent("Please select the month and the year of your birthday")); 
      
      selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      selectYear = new Select(driver().findElement(By.id("profileYear")));
      selectMonth.selectByValue(dataset.get("changedMonthID"));
      selectYear.selectByValue(dataset.get("changedYear"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedMonth")));
      assertTrue(isTextPresent(dataset.get("changedYear")));
      
      driver().findElement(By.id("profileChangeBirthOpen")).click();
      ajaxWait();
      selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      selectYear = new Select(driver().findElement(By.id("profileYear"))); 
      selectMonth.selectByValue(dataset.get("registrationMonthID"));
      selectYear.selectByValue(dataset.get("registrationYear"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationMonth")));
      assertTrue(isTextPresent(dataset.get("registrationYear")));

      
      driver().findElement(By.id("profileChangeGenderOpen")).click();
      ajaxWait();
      Select selectGender = new Select(driver().findElement(By.id("profileGender")));
      selectGender.selectByValue(dataset.get("changedGender"));
      driver().findElement(By.id("profileGenderSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedGender")));

      driver().findElement(By.id("profileChangeGenderOpen")).click();
      ajaxWait();
      selectGender = new Select(driver().findElement(By.id("profileGender")));
      selectGender.selectByValue(dataset.get("registrationGender"));
      driver().findElement(By.id("profileGenderSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationGender")));

      
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }
  
  @Test(dataProvider = "loginAndAddData", groups = { "functionality", "visibility" }, description = "check profile page")
  public void profilePageAddUserData(HashMap<String, String> dataset) throws Throwable {
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

      driver().findElement(By.id("headerProfileButton")).click();
      assertTrue(isElementPresent(By.id("logoutSubmitButton")));
      driver().findElement(By.id("headerCancelButton")).click();

      assertTrue(isTextPresent(dataset.get("registrationUsername") + "\'s Profile"));
      assertTrue(isTextPresent("change my password"));
      assertTrue(isTextPresent(dataset.get("registrationEmail")));
      
      driver().findElement(By.id("profileChangeCityOpen")).click();
      ajaxWait();
      driver().findElement(By.id("profileCity")).clear();
      driver().findElement(By.id("profileCity")).sendKeys(dataset.get("changedCity"));
      driver().findElement(By.id("profileCitySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedCity")));
      
      driver().findElement(By.id("profileChangeCityOpen")).click();
      ajaxWait();
      driver().findElement(By.id("profileCity")).clear();
      driver().findElement(By.id("profileCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("profileCitySubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationCity")));
      
      
      driver().findElement(By.id("profileChangeBirthOpen")).click();
      ajaxWait();
      Select selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      Select selectYear = new Select(driver().findElement(By.id("profileYear")));
      selectMonth.selectByValue(dataset.get("changedMonthID"));
      selectYear.selectByValue(dataset.get("changedYear"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedMonth")));
      assertTrue(isTextPresent(dataset.get("changedYear")));
      
      driver().findElement(By.id("profileChangeBirthOpen")).click();
      ajaxWait();
      selectMonth = new Select(driver().findElement(By.id("profileMonth")));
      selectYear = new Select(driver().findElement(By.id("profileYear"))); 
      selectMonth.selectByValue(dataset.get("registrationMonthID"));
      selectYear.selectByValue(dataset.get("registrationYear"));
      driver().findElement(By.id("profileBirthSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationMonth")));
      assertTrue(isTextPresent(dataset.get("registrationYear")));

      
      driver().findElement(By.id("profileChangeGenderOpen")).click();
      ajaxWait();
      Select selectGender = new Select(driver().findElement(By.id("profileGender")));
      selectGender.selectByValue(dataset.get("changedGender"));
      driver().findElement(By.id("profileGenderSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("changedGender")));

      driver().findElement(By.id("profileChangeGenderOpen")).click();
      ajaxWait();
      selectGender = new Select(driver().findElement(By.id("profileGender")));
      selectGender.selectByValue(dataset.get("registrationGender"));
      driver().findElement(By.id("profileGenderSubmit")).click();
      ajaxWait();
      assertTrue(isTextPresent(dataset.get("registrationGender")));
      
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
      
      openLocation("catroid/profile/");
      
      driver().findElement(By.name("profileMyProfileOpen")).click();
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
  public Object[][] loginAndAddData() {
    final String randomString = CommonData.getRandomShortString(10);

    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("registrationUsername", "ProfileTest" + randomString);
        put("registrationPassword", "myPassword123");
        put("registrationEmail", "email_" + randomString + "@selenium.at");
        put("registrationCountry", "Austria");
        put("registrationCountryID", "AT");
        put("changedCity", "München");
        put("registrationCity", "Graz");
        put("changedMonthID", "4");
        put("changedMonth", "April");
        put("registrationMonthID", "8");
        put("registrationMonth", "August");
        put("changedYear", "1978");
        put("registrationYear", "1999");
        put("changedGender", "female");
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
        put("registrationCountryID", "AT");
        put("registrationCountry", "AUSTRIA");
        put("changedCountryID", "DE");
        put("changedCountry", "GERMANY");
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
