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
      openLocation("registration/");
      
      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      (new Select(driver().findElement(By.id("registrationCountry")))).selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("registrationCity")).sendKeys(dataset.get("registrationCity"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();

      assertTrue(isTextPresent(dataset.get("registrationUsername")));
      assertEquals(dataset.get("registrationEmail"), driver().findElement(By.xpath("//*[@id='profileEmail']/input")).getAttribute("value"));

      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      assertTrue(isVisible(By.xpath("//*[@id='profileNewPassword']/input")));
      assertTrue(isVisible(By.xpath("//*[@id='profileRepeatPassword']/input")));
      assertTrue(isVisible(By.id("profileSaveChanges")));

      driver().findElement(By.xpath("//*[@id='profileNewPassword']/input")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.xpath("//*[@id='profileRepeatPassword']/input")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      assertTrue(isTextPresent("saved!"));

      openLocation("profile/");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      driver().findElement(By.xpath("//*[@id='profileNewPassword']/input")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.xpath("//*[@id='profileRepeatPassword']/input")).sendKeys(dataset.get("changedPassword"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      assertTrue(isTextPresent("You entered two different passwords."));
      
      openLocation("profile/");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      driver().findElement(By.xpath("//*[@id='profileNewPassword']/input")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.xpath("//*[@id='profileRepeatPassword']/input")).sendKeys(dataset.get("emptyPassword"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      openLocation("profile/");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      driver().findElement(By.xpath("//*[@id='profileNewPassword']/input")).sendKeys(dataset.get("shortPassword"));
      driver().findElement(By.xpath("//*[@id='profileRepeatPassword']/input")).sendKeys(dataset.get("shortPassword"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("Your password must have at least 6 characters."));
      
      openLocation("profile/");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      driver().findElement(By.xpath("//*[@id='profileNewPassword']/input")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.xpath("//*[@id='profileRepeatPassword']/input")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      assertTrue(isTextPresent("saved!"));
      
      openLocation("profile/");
      By countrySelect = By.xpath("//*[@id='wrapper']/article/div[1]/div[2]/div[1]/div[4]/select");
      Select selectCountry = new Select(driver().findElement(countrySelect));
      assertEquals(dataset.get("registrationCountry"), selectCountry.getFirstSelectedOption().getText());
      
      selectCountry.selectByValue(dataset.get("changedCountryID"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();

      assertTrue(isTextPresent("saved!"));
      
      openLocation("profile/");
      selectCountry = new Select(driver().findElement(countrySelect));
      assertEquals(dataset.get("changedCountry"), selectCountry.getFirstSelectedOption().getText());
      
      selectCountry.selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("saved!"));

      openLocation("profile/");
      selectCountry = new Select(driver().findElement(countrySelect));
      assertEquals(dataset.get("registrationCountry"), selectCountry.getFirstSelectedOption().getText());

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }
  
  @Test(dataProvider = "loginAndChangeData", groups = { "functionality", "visibility" }, description = "upadte user avatar")
  public void profileChangeAvatarTest(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation("registration/");

      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      (new Select(driver().findElement(By.id("registrationCountry")))).selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();
      
      By avatarImage = By.xpath("//*[@id='wrapper']/article/div[1]/div[1]/img");
      assertTrue(driver().findElement(avatarImage).getAttribute("src").contains("images/symbols/avatar_default.png"));
      makeVisible(By.id("profileAvatarFile"), "");
      makeVisible(By.id("profileAvatarFileWrapper"), "arguments[0].style.width='100px';arguments[0].style.height='100px';");
      driver().findElement(By.id("profileAvatarFile")).sendKeys(Config.FILESYSTEM_BASE_PATH + Config.SELENIUM_GRID_TESTDATA + "test.zip");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      assertTrue(driver().findElement(avatarImage).getAttribute("src").contains("images/symbols/avatar_default.png"));
      assertTrue(isTextPresent("Invalid image, allowed image types are gif, png or jpeg."));
      
      driver().findElement(By.id("profileAvatarFile")).sendKeys(Config.FILESYSTEM_BASE_PATH + Config.SELENIUM_GRID_TESTDATA + "catrobat.png");
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      assertFalse(driver().findElement(avatarImage).getAttribute("src").contains("images/symbols/avatar_default.png"));

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profileChangeAvatarTest." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profileChangeAvatarTest." + dataset.get("registrationUsername"));
      throw e;
    }
  }
  
  @Test(dataProvider = "emailTest", groups = { "functionality", "visibility" }, description = "check add/delete email")
  public void profilePageEmailTest(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation("registration/");
      
      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      (new Select(driver().findElement(By.id("registrationCountry")))).selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();

      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/header"), dataset.get("registrationUsername").toUpperCase()));
      
      clickOkOnNextConfirmationBox();
      driver().findElement(By.xpath("//*[@id='profileEmail']/input")).clear();
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      assertTrue(isTextPresent("Error while updating this e-mail address. You must have at least one validated e-mail address."));
      
      openLocation("profile/");
      
      driver().findElement(By.xpath("//*[@id='profileSecondEmail']/input")).sendKeys(dataset.get("registrationEmail"));
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("This email address already exists."));
      
      openLocation("profile/");
      
      driver().findElement(By.xpath("//*[@id='profileSecondEmail']/input")).sendKeys(dataset.get("secondEmail"));
      driver().findElement(By.id("profileSaveChanges")).click();
      
      Alert alert = driver().switchTo().alert();
      String message = alert.getText();
      alert.accept();
      assertRegExp(".*/emailvalidation.*", message);

      // get validation url and open it
      String validationUrl = getValidationUrl(message);
      openLocation(validationUrl + "_invalid");
      assertTrue(isTextPresent("Recovery hash was not found."));
      openLocation(validationUrl);
      assertTrue(isTextPresent("You have successfully validated your email address."));

      openLocation("profile/");
      ajaxWait();

      clickOkOnNextConfirmationBox();
      driver().findElement(By.xpath("//*[@id='profileEmail']/input")).clear();
      driver().findElement(By.id("profileSaveChanges")).click();
      ajaxWait();
      assertTrue(isTextPresent("saved!"));
      
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profilePage." + dataset.get("registrationUsername"));
      throw e;
    }
  }

  @Test(dataProvider = "emailTest", groups = { "functionality", "visibility" }, description = "Tests user profile page")
  public void profileForeignProfilePage(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation("registration/");
      
      driver().findElement(By.id("registrationUsername")).sendKeys(dataset.get("registrationUsername"));
      driver().findElement(By.id("registrationPassword")).sendKeys(dataset.get("registrationPassword"));
      driver().findElement(By.id("registrationEmail")).sendKeys(dataset.get("registrationEmail"));
      (new Select(driver().findElement(By.id("registrationCountry")))).selectByValue(dataset.get("registrationCountryID"));
      driver().findElement(By.id("registrationMonth")).sendKeys(dataset.get("registrationMonth"));
      driver().findElement(By.id("registrationYear")).sendKeys(dataset.get("registrationYear"));
      driver().findElement(By.id("registrationGender")).sendKeys(dataset.get("registrationGender"));
      driver().findElement(By.id("registrationSubmit")).click();
      ajaxWait();
      
      logout("profile/" + dataset.get("registrationUsername"));
      assertTrue(isTextPresent("Login".toUpperCase()));

      login("profile/" + dataset.get("registrationUsername"));
      assertTrue(isTextPresent(dataset.get("registrationUsername").toUpperCase()));
      assertTrue(isTextPresent(dataset.get("registrationCountry")));
      assertTrue(isTextPresent("0"));
      
      String projectTitle = dataset.get("registrationUsername");
      String authToken = CommonFunctions.getAuthenticationToken(dataset.get("registrationUsername"));
      projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", dataset.get("registrationUsername"), authToken));
      assertProjectPresent(projectTitle);

      openLocation("profile/" + dataset.get("registrationUsername"));
      assertTrue(isTextPresent("1"));

      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
    } catch(AssertionError e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profileForeignProfilePage." + dataset.get("registrationUsername"));
      throw e;
    } catch(Exception e) {
      CommonFunctions.deleteUserFromDatabase(dataset.get("registrationUsername"));
      captureScreen("ProfileTests.profileForeignProfilePage." + dataset.get("registrationUsername"));
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
