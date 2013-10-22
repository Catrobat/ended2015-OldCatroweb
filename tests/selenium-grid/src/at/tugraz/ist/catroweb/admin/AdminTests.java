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

package at.tugraz.ist.catroweb.admin;

import org.openqa.selenium.By;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;
import java.util.HashMap;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "admin", "AdminTests" })
public class AdminTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check admin area login")
  public void successfulLogin() throws Throwable {
    try {
      openAdminLocation();
      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());
      assertTrue(isTextPresent("Administration Tools"));
      driver().findElement(By.id("aAdminToolsBackToCatroidweb")).click();
      ajaxWait();
      assertRegExp(".*Pocket Code Website.*", driver().getTitle());
      driver().navigate().back();
      if(isTextPresent("Catroid Administration Site") == false) {
        driver().navigate().back();
      }
      assertTrue(isTextPresent("Catroid Administration Site"));
    } catch(AssertionError e) {
      captureScreen("AdminTests.successfulLogin");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.successfulLogin");
      throw e;
    }
  }
  
  @Test(dataProvider = "validRegistrationData", groups = { "visibility" }, description = "deleting user and check is deleted?")
  public void deleteUser(HashMap<String, String> dataset) throws Throwable {
    try {
      String idButton = "deleteUser";
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
      assertTrue(CommonFunctions.isUserInDatabase(dataset.get("registrationUsername")));
      
      openAdminLocation();
      driver().findElement(By.id("aProjectUploader")).click();
      assertTrue(isTextPresent("Project Uploader"));
      driver().findElement(By.name("uploadButton")).click();
      assertTrue(isTextPresent("501"));
      
      // upload a project
      String projectTitle = "testproject" + CommonData.getRandomLongString(200);
      String auth_token = CommonFunctions.getAuthenticationToken(dataset.get("registrationUsername"));
      String response = projectUploader.upload(CommonData.getUploadPayload(projectTitle, "", "", "", "", "", dataset.get("registrationUsername"), auth_token));
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");
      
      assertTrue(CommonFunctions.isProjectInDatabase(CommonFunctions.getUserIDFromDatabase(dataset.get("registrationUsername"))));
      
      openAdminLocation();
      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());
      assertTrue(isTextPresent("Administration Tools"));
      
      driver().findElement(By.id("aAdministrationTools")).click();
      assertTrue(isTextPresent("delete Users"));
      
      driver().findElement(By.id("aAdminToolsDeleteUser")).click();
      assertTrue(isTextPresent("List of all users:"));
      
      idButton += CommonFunctions.getUserIDFromDatabase(dataset.get("registrationUsername"));
      clickOkOnNextConfirmationBox();
      
      driver().findElement(By.id(idButton)).click();    
      assertTrue(isTextPresent("The user " + dataset.get("registrationUsername") + " was deleted."));

      assertTrue(!(CommonFunctions.isUserInDatabase(dataset.get("registrationUsername"))));
      assertTrue(!(CommonFunctions.isProjectInDatabase(CommonFunctions.getUserIDFromDatabase(dataset.get("registrationUsername")))));
      
    } catch(AssertionError e) {
      captureScreen("AdminTests.deleteUser");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.deleteUser");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "clicks all available links in the admin area")
  public void clickAllLinks() throws Throwable {
    try {
      openAdminLocation();
      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());
      assertTrue(isTextPresent("Catroid Administration Site"));

      driver().findElement(By.id("aAdministrationTools")).click();
      assertTrue(isTextPresent("Administration Tools"));
      assertTrue(isTextPresent("remove inconsistant project files"));
      assertTrue(isTextPresent("edit projects"));
      assertTrue(isTextPresent("add featured projects"));
      assertTrue(isTextPresent("edit featured projects"));
      assertTrue(isTextPresent("thumbnail uploader"));
      assertTrue(isTextPresent("inappropriate projects"));
      assertTrue(isTextPresent("approve unapproved words"));
      assertTrue(isTextPresent("manage Languages"));
      assertTrue(isTextPresent("block IPs"));
      assertTrue(isTextPresent("block Users"));
      assertTrue(isTextPresent("send e-mail notification"));
      
      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());

      driver().findElement(By.id("aAdminToolsRemoveInconsitantProjectFiles")).click();
      assertTrue(isTextPresent("Answer"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsEditProjects")).click();
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsAddFeaturedProjects")).click();
      assertTrue(isTextPresent("Administration Tools - Add Featured Projects"));
      driver().navigate().back();
      ajaxWait();
      
      driver().findElement(By.id("aAdminToolsEditFeaturedProjects")).click();
      assertTrue(isTextPresent("Administration Tools - Edit Featured Projects"));
      driver().navigate().back();
      ajaxWait();      
      
      driver().findElement(By.id("aAdminToolsThumbnailUploader")).click();
      assertTrue(isTextPresent("Administration Tools - Thumbnail Uploader"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsInappropriateProjects")).click();
      assertTrue(isTextPresent("Administration Tools - List of inappropriate projects"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent("Administration Tools - List of unapproved Words"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsLanguageManagement")).click();
      assertTrue(isTextPresent("Administration Tools - Language Management"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsBlockIp")).click();
      assertTrue(isTextPresent("Administration Tools - List of blocked IP-Addresses"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsBlockUser")).click();
      assertTrue(isTextPresent("Administration Tools - List of blocked users"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.id("aAdminToolsUpdateBrowserDetection")).click();
      assertTrue(isTextPresent("Administration Tools - Update browser-detection RegEx-Pattern"));
      driver().navigate().back();
      ajaxWait();
      
      driver().findElement(By.id("aAdminToolsSendEmailNotification")).click();
      assertTrue(isTextPresent("Administration Tools - Send e-mail notification"));
      driver().navigate().back();
      ajaxWait();
      
      driver().findElement(By.id("aAdminToolsUploadNotificationsList")).click();
      assertTrue(isTextPresent("Administration Tools - Upload Notifications List"));
      driver().navigate().back();
      ajaxWait();

      assertTrue(isTextPresent("- back"));
      driver().findElement(By.id("aAdminToolsBackToCatroidweb")).click();
      assertTrue(isTextPresent("Catroid Administration Site"));
    } catch(AssertionError e) {
      captureScreen("AdminTests.clickAllLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.clickAllLinks");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload", "popupwindows" }, description = "check report as inappropriate functionality")
  public void inappropriateProjects() throws Throwable {
    try {
      login("details/1");

      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      driver().findElement(By.id("reportInappropriateReason")).sendKeys("my selenium reason");
      driver().findElement(By.id("reportInappropriateReportButton")).click();
      ajaxWait();
      assertTrue(isTextPresent("You reported this program as inappropriate!"));
      openAdminLocation("/tools/inappropriateProjects");
      assertTrue(isTextPresent("1"));

      clickAndWaitForPopUp(By.xpath("//a[@id='detailsLink1']"));
      assertTrue(isTextPresent("testproject".toUpperCase()));
      closePopUp();

      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("resolve1")).click();
      assertTrue(isTextPresent("The project was succesfully restored and set to visible!"));
      assertFalse(isTextPresent("1"));
    } catch(AssertionError e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    }
  }
  
  @Test(groups = { "functionality", "notificationlist" }, description = "check upload notifications list")
  public void uploadNotificationsList() throws Throwable {
    try {
      openAdminLocation();
      assertTrue(isTextPresent("Catroid Administration Site"));
      driver().findElement(By.id("aAdministrationTools")).click();
      driver().findElement(By.id("aAdminToolsUploadNotificationsList")).click();
      assertTrue(isTextPresent("Administration Tools - Upload Notifications List"));
      driver().findElement(By.id("emailInput")).sendKeys("e@mailcom");
      driver().findElement(By.id("addEmail")).click();
      ajaxWait();
      assertTrue(isTextPresent("Error: could NOT add E-Mail!"));
      driver().findElement(By.id("emailInput")).sendKeys("e@mail.com");
      driver().findElement(By.id("addEmail")).click();
      ajaxWait();
      assertTrue(isTextPresent("E-Mail added successfully!"));
      assertTrue(isTextPresent("e@mail.com"));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.xpath("//*[@id='projectTableId']/tbody/tr[2]/td[2]/form/input[3]")).click();
      ajaxWait();
      assertTrue(isTextPresent("E-Mail removed successfully!"));
      
    } catch(AssertionError e) {
      captureScreen("AdminTests.uploadNotificationsList");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.uploadNotificationsList");
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
}
