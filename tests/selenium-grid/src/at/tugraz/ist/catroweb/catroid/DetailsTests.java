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

import java.io.File;
import java.util.HashMap;

import org.openqa.selenium.By;
import org.testng.Reporter;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "DetailsTests" })
public class DetailsTests extends BaseTest {

  @Test(dataProvider = "detailsProject", groups = { "functionality", "upload" }, description = "view + download counter test")
  public void detailsPageCounterLink(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");
      String title = dataset.get("projectTitle");
      int numOfViews = -1;
      int numOfViewsAfter = -1;
      int numOfDownloads = -1;
      int numOfDownloadsAfter = -1;

      openLocation("catroid/details/" + id);
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats']/strong")));
      // project title
      assertTrue(containsElementText(By.xpath("//div[@class='detailsProjectTitle']"), title));
      // test the view counter
      numOfViews = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats']/strong")).getText());

      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats']/strong")));
      numOfViewsAfter = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats']/strong")).getText());
      assertEquals(numOfViews + 1, numOfViewsAfter);

      // test the download counter
      numOfDownloads = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      driver().findElement(By.xpath("//div[@class='detailsDownloadButton']/a[1]")).click();

      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats'][2]/strong")));
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);

      // check file size
      double filesize = CommonFunctions.getFileSizeRounded(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_DIRECTORY + id + Config.PROJECTS_EXTENTION);
      String downloadButtonText = driver().findElement(By.xpath("//div[@class='detailsFileSize']")).getText();
      String displayedfilesize = downloadButtonText.substring(downloadButtonText.indexOf(":") + 2, downloadButtonText.indexOf(" MB"));
      if(displayedfilesize.startsWith("<")) {
        // smaller files are displayed as "< 0.1 MB"
        assertEquals("< " + String.valueOf(filesize), displayedfilesize);
      } else {
        assertEquals(String.valueOf(filesize), displayedfilesize);
      }

      HashMap<String, String> versionInfo = CommonFunctions.getVersionInfo(id);
      String versionInfoText = driver().findElement(By.xpath("//span[@class='versionInfo']")).getText();
      // assertRegExp("^Catroid version: " + versionInfo.get("version_name") + "$", versionInfoText);
      assertEquals("Catroid version: " + versionInfo.get("version_name"), versionInfoText);
    } catch(AssertionError e) {
      captureScreen("DetailsTests.detailsPageCounterLink." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.detailsPageCounterLink." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(dataProvider = "detailsProject", groups = { "functionality", "upload" }, description = "view + download counter test")
  public void detailsPageCounterThumbnail(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");
      int numOfDownloads = -1;
      int numOfDownloadsAfter = -1;
      
      openLocation("catroid/details/" + id);
      
      // test the download counter
      numOfDownloads = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      driver().findElement(By.xpath("//div[@class='detailsMainImage']/a[1]")).click();
      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats'][2]/strong")));
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);
    } catch(AssertionError e) {
      captureScreen("DetailsTests.detailsPageCounterThumbnail." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.detailsPageCounterThumbnail." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(dataProvider = "detailsProject", groups = { "functionality", "upload" }, description = "test inappropriate button")
  public void inappropriateButton(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

      openLocation("catroid/details/" + id);
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
      
      driver().findElement(By.id("headerProfileButton")).click();
      driver().findElement(By.id("loginUsername")).sendKeys(CommonData.getLoginUserDefault());
      driver().findElement(By.id("loginPassword")).sendKeys(CommonData.getLoginPasswordDefault());
      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      assertTrue(isVisible(By.id("reportInappropriateReason")));
      assertTrue(isVisible(By.id("reportInappropriateReportButton")));
      assertTrue(isVisible(By.id("reportInappropriateCancelButton")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      assertFalse(isVisible(By.id("reportInappropriateReason")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      assertTrue(isVisible(By.id("reportInappropriateReason")));
      driver().findElement(By.id("reportInappropriateCancelButton")).click();
      assertFalse(isVisible(By.id("reportInappropriateReason")));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      driver().findElement(By.id("reportInappropriateReportButton")).click();
      ajaxWait();
      assertFalse(isVisible(By.id("reportInappropriateReason")));
      assertFalse(isTextPresent("You reported this project as inappropriate!"));
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      driver().findElement(By.id("reportInappropriateReason")).sendKeys("my selenium reason");
      driver().findElement(By.id("reportInappropriateReportButton")).click();
      ajaxWait();
      assertFalse(isVisible(By.id("reportInappropriateReason")));
      ajaxWait();
      assertTrue(isTextPresent("You reported this project as inappropriate!"));

      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isTextPresent("No entry was found for the given ID"));

      openAdminLocation("/tools/inappropriateProjects");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("resolve" + id)).click();
      assertTrue(isTextPresent("The project was succesfully restored and set to visible!"));
      assertFalse(isTextPresent(id));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.inappropriateButton." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.inappropriateButton." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(dataProvider = "titlesAndDescriptions", groups = { "visibility", "upload" }, description = "test more button + QR Code image")
  public void moreButton(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String projectId = CommonFunctions.getValueFromJSONobject(response, "projectId");
      openLocation("catroid/details/" + projectId);

      assertTrue(isElementPresent(By.id("showFullDescriptionButton")));
      String shortDescriptionFromPage = driver().findElement(By.id("detailsDescription")).getText();
      assertFalse(shortDescriptionFromPage.equals(dataset.get("projectDescription")));

      driver().findElement(By.id("showFullDescriptionButton")).click();
      String fullDescriptionFromPage = driver().findElement(By.id("detailsDescription")).getText();
      assertTrue(fullDescriptionFromPage.equals(dataset.get("projectDescription")));
      assertFalse(fullDescriptionFromPage.equals(shortDescriptionFromPage));
      assertTrue(isElementPresent(By.id("showShortDescriptionButton")));
      driver().findElement(By.id("showShortDescriptionButton")).click();
      assertTrue(containsElementText(By.id("detailsDescription"), shortDescriptionFromPage));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.moreButton." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.moreButton." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test QR Code image")
  public void QRCodeImage() throws Throwable {
    try {
      HashMap<String, String> data = CommonData.getRandomProject();
      File qrCodeFile = new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_QR_DIRECTORY + data.get("projectDescription") + Config.PROJECTS_QR_EXTENTION);
      File qrCodeFileNew = new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_QR_DIRECTORY + data.get("projectDescription") + "_new"
          + Config.PROJECTS_QR_EXTENTION);

      if(qrCodeFile.exists()) {
        assertTrue(qrCodeFile.renameTo(qrCodeFileNew));
        driver().navigate().refresh();
        ajaxWait();
        assertFalse(isElementPresent(By.xpath("//img[@class='projectDetailsQRImage']")));
        assertTrue(qrCodeFileNew.renameTo(qrCodeFile));
        driver().navigate().refresh();
        ajaxWait();
        assertTrue(isElementPresent(By.xpath("//img[@class='projectDetailsQRImage']")));
      } else {
        assertFalse(isElementPresent(By.xpath("//img[@class='projectDetailsQRImage']")));
      }
    } catch(AssertionError e) {
      captureScreen("DetailsTests.QRCodeImage");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.QRCodeImage");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test QR Code info")
  public void QRCodeInfo() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      clickLastVisibleProject();
      ajaxWait();

      assertTrue(isElementPresent(By.id("showQrCodeInfoButton")));
      assertTrue(isElementPresent(By.id("qrcodeInfo")));
      assertTrue(isElementPresent(By.id("hideQrCodeInfoButton")));
      assertTrue(isVisible(By.id("showQrCodeInfoButton")));
      assertFalse(isVisible(By.id("hideQrCodeInfoButton")));
      assertFalse(isVisible(By.id("qrcodeInfo")));
      driver().findElement(By.id("showQrCodeInfoButton")).click();
      assertFalse(isVisible(By.id("showQrCodeInfoButton")));
      assertTrue(isVisible(By.id("hideQrCodeInfoButton")));
      assertTrue(isVisible(By.id("qrcodeInfo")));
      driver().findElement(By.id("hideQrCodeInfoButton")).click();
      assertTrue(isVisible(By.id("showQrCodeInfoButton")));
      assertFalse(isVisible(By.id("hideQrCodeInfoButton")));
      assertFalse(isVisible(By.id("qrcodeInfo")));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    }
  }

  @Test(dataProvider = "oldVersion", groups = { "visibility" }, description = "check old version warning")
  public void oldVersionView(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

      openLocation("catroid/details/" + id);
      ajaxWait();

      assertTrue(isTextPresent("We are sorry, but this project was created with an older version of Catroid and cannot be downloaded any more."));
      assertTrue(isTextPresent("If you are the author of this project and want to update it, you can upload it again from within the current version of Catroid."));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.oldVersionView");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.oldVersionView");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test download info")
  public void downloadInfo() throws Throwable {
    try {
      openLocation("catroid/details/2");
      ajaxWait();

      assertTrue(isElementPresent(By.id("downloadCatroidSwitch")));
      assertTrue(isElementPresent(By.id("downloadAppSwitch")));
      assertTrue(isElementPresent(By.id("downloadInfoButton")));
      assertTrue(isVisible(By.id("downloadCatroidSection")));
      assertFalse(isVisible(By.id("downloadAppSection")));
      assertFalse(isVisible(By.id("downloadCatroidInfo")));
      assertFalse(isVisible(By.id("downloadAppInfo")));
      
      driver().findElement(By.id("downloadInfoButton")).click();
      assertTrue(isVisible(By.id("downloadCatroidInfo")));
      
      driver().findElement(By.id("downloadAppSwitch")).click();
      assertFalse(isVisible(By.id("downloadCatroidInfo")));
      assertTrue(isVisible(By.id("downloadAppInfo")));
      
      driver().findElement(By.id("downloadInfoButton")).click();
      assertFalse(isVisible(By.id("downloadCatroidInfo")));
      assertFalse(isVisible(By.id("downloadAppInfo")));
      
      driver().findElement(By.id("downloadCatroidSwitch")).click();
      driver().findElement(By.id("downloadInfoButton")).click();
      assertTrue(isVisible(By.id("downloadCatroidInfo")));
      assertFalse(isVisible(By.id("downloadAppInfo")));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test download app link")
  public void testAppDownloadLink() throws Throwable {
    try {
      int numOfDownloads = -1;
      int numOfDownloadsAfter = -1;
      
      openLocation("catroid/details/2");
      ajaxWait();

      assertTrue(isElementPresent(By.id("downloadCatroidSwitch")));
      assertTrue(isElementPresent(By.id("downloadAppSwitch")));
      assertTrue(isElementPresent(By.id("downloadInfoButton")));
      assertTrue(isVisible(By.id("downloadCatroidSection")));
      assertFalse(isVisible(By.id("downloadAppSection")));
      assertFalse(isVisible(By.id("downloadCatroidInfo")));
      assertFalse(isVisible(By.id("downloadAppInfo")));
      
      driver().findElement(By.id("downloadAppSwitch")).click();
      assertTrue(isVisible(By.id("downloadAppButton")));
      assertRegExp(".*apk.*", driver().findElement(By.id("downloadAppProjectLink")).getAttribute("href"));
      numOfDownloads = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      driver().findElement(By.id("downloadAppProjectLink")).click();
      ajaxWait();

      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats'][2]/strong")));
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);
      
     
      // check file size
      driver().findElement(By.id("downloadAppSwitch")).click();
      assertTrue(isVisible(By.id("downloadAppButton")));
      
      double filesize = CommonFunctions.getFileSizeRounded(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_DIRECTORY + "2.apk");
      String downloadButtonText = driver().findElement(By.xpath("//div[@class='detailsFileSize']")).getText();
      String displayedfilesize = downloadButtonText.substring(downloadButtonText.indexOf(":") + 2, downloadButtonText.indexOf(" MB"));
      if(displayedfilesize.startsWith("<")) {
        // smaller files are displayed as "< 0.1 MB"
        assertEquals("< " + String.valueOf(filesize), displayedfilesize);
      } else {
        assertEquals(String.valueOf(filesize), displayedfilesize);
      }
    } catch(AssertionError e) {
      captureScreen("DetailsTests.testAppDownloadLink");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.testAppDownloadLink");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test download app thumbnail")
  public void testAppDownloadThumbnail() throws Throwable {
    try {
      int numOfDownloads = -1;
      int numOfDownloadsAfter = -1;
      
      openLocation("catroid/details/2");
      ajaxWait();
      
      driver().findElement(By.id("downloadAppSwitch")).click();
      assertTrue(isVisible(By.id("downloadAppButton")));
      
      String projectThumbnailLink = driver().findElement(By.id("downloadProjectThumb")).getAttribute("href");
      assertRegExp(".*apk.*", projectThumbnailLink);
      numOfDownloads = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      driver().get(projectThumbnailLink);
      
      driver().navigate().refresh();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//p[@class='detailsStats'][2]/strong")));
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(By.xpath("//p[@class='detailsStats'][2]/strong")).getText());
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);
    } catch(AssertionError e) {
      captureScreen("DetailsTests.testAppDownloadThumbnail");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.testAppDownloadThumbnail");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test if project size is visible inside download-button")
  public void projectSizeInfo() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      clickLastVisibleProject();
      ajaxWait();
      
      assertTrue(isElementPresent(By.xpath("//span[@class='detailsDownloadButtonText']")));
      assertRegExp(".*Download.*", driver().findElement(By.id("downloadCatroidProjectLink")).getText());
      
      openLocation("catroid/details/2");
      driver().findElement(By.id("downloadAppSwitch")).click();
      
      assertTrue(isElementPresent(By.xpath("//span[@class='detailsDownloadButtonText']")));
      assertRegExp(".*Download.*", driver().findElement(By.xpath("//span[@class='detailsDownloadButtonText']")).getText());
    } catch(AssertionError e) {
      captureScreen("DetailsTests.ProjectSizeInfo");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.ProjectSizeInfo");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check if invalid project id redirects to errorpage")
  public void invalidProjectID() throws Throwable {
    try {
      String invalidProject = CommonData.getRandomShortString(10);
      openLocation("catroid/details/" + invalidProject);
      assertRegExp(".*/catroid/errorPage", driver().getCurrentUrl());
      assertTrue(isTextPresent("No entry was found for the given ID:"));
      assertTrue(isTextPresent("ID: " + invalidProject));
      assertFalse(isElementPresent(By.xpath("//div[@class='detailsFlexDiv']")));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.invalidProjectID");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.invalidProjectID");
      throw e;
    }
  }

  @DataProvider(name = "oldVersion")
  public Object[][] oldVersion() {
    Object[][] returnArray = new Object[][] {
        { CommonData
            .getUploadPayload(
                "This is a very old project.",
                "The user is not allowed to download this project any more.",
                "test.zip", "583783a335bd40d3d0195a13432afabb", "", "", "", "") } };
    return returnArray;
  }
  
  @DataProvider(name = "titlesAndDescriptions")
  public Object[][] titlesAndDescriptions() {
    Object[][] returnArray = new Object[][] {
        { CommonData
          .getUploadPayload(
              "more button selenium test",
              "This is a description which should have more characters than defined by the threshold in config.php. And once again: This is a description which should have more characters than defined by the threshold in config.php. Thats it!",
              "", "", "", "", "", "") },
              { CommonData
                .getUploadPayload(
                    "more button special chars test",
                    "This is a description which has special chars like \", & or < and > in it and it should have more characters than defined by the threshold in config.php. And once again: This is a description with \"special chars\" and should have more characters than defined by the threshold in config.php. Thats it!",
                    "", "", "", "", "", "") }, };
    return returnArray;
  }

  @DataProvider(name = "detailsProject")
  public Object[][] detailsProject() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("details_test1small", "details_test_description", "", "", "", "", "catroid", CommonFunctions.getAuthenticationToken("catroid")) },
        { CommonData.getUploadPayload("details_test2big", "details_test_description", "", "", "", "", "catroid", CommonFunctions.getAuthenticationToken("catroid")) }, };
    return returnArray;
  }
}