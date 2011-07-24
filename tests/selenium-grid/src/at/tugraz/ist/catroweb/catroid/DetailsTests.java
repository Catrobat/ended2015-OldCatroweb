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

import java.io.File;
import java.util.HashMap;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "DetailsTests" })
public class DetailsTests extends BaseTest {

  @Test(dataProvider = "detailsProject", groups = { "functionality", "upload" }, description = "view + download counter test")
  public void detailsPageCounter(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");
      String title = dataset.get("projectTitle");
      int numOfViews = -1;
      int numOfViewsAfter = -1;
      int numOfDownloads = -1;
      int numOfDownloadsAfter = -1;

      openLocation("catroid/details/" + id);
      waitForElementPresent("xpath=//p[@class='detailsStats']/b");
      // project title
      assertEquals(title, session().getText("xpath=//div[@class='detailsProjectTitle']"));
      // test the view counter
      numOfViews = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats']/b"));

      session().refresh();
      waitForPageToLoad();
      ajaxWait();
      waitForElementPresent("xpath=//p[@class='detailsStats']/b");
      numOfViewsAfter = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats']/b"));
      assertEquals(numOfViews + 1, numOfViewsAfter);

      // test the download counter
      numOfDownloads = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
      session().click("xpath=//div[@class='detailsDownloadButton']/a[1]");

      Thread.sleep(Config.TIMEOUT_THREAD);
      session().keyPressNative("27"); // press escape key
      session().refresh();
      waitForPageToLoad();

      waitForElementPresent("xpath=//p[@class='detailsStats'][2]/b");
      numOfDownloadsAfter = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);
      session().click("xpath=//div[@class='detailsMainImage']/a[1]");
      Thread.sleep(Config.TIMEOUT_THREAD);
      session().keyPressNative("27"); // press escape key
      session().refresh();
      waitForPageToLoad();
      waitForElementPresent("xpath=//p[@class='detailsStats'][2]/b");
      numOfDownloadsAfter = Integer.valueOf(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
      assertEquals(numOfDownloads + 2, numOfDownloadsAfter);

      // check file size
      double filesize = CommonFunctions.getFileSizeRounded(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_DIRECTORY + id + Config.PROJECTS_EXTENTION);
      String downloadButtonText = session().getText("xpath=//span[@class='detailsDownloadButtonText']");
      String displayedfilesize = downloadButtonText.substring(downloadButtonText.indexOf("(") + 1, downloadButtonText.indexOf(" MB)"));
      if(displayedfilesize.startsWith("<"))
        assertEquals("< " + String.valueOf(filesize), displayedfilesize); // smaller
                                                                          // files
                                                                          // are
                                                                          // displayed
                                                                          // as
                                                                          // "< 0.1 MB"
      else
        assertEquals(String.valueOf(filesize), displayedfilesize);

      HashMap<String, String> versionInfo = CommonFunctions.getVersionInfo(id);
      String versionInfoText = session().getText("xpath=//span[@class='versionInfo']");
      assertRegExp("^Catroid version: " + versionInfo.get("version_code") + " [(]" + versionInfo.get("version_name") + "[)]$", versionInfoText);
    } catch(AssertionError e) {
      captureScreen("DetailsTests.detailsPageCounter." + dataset.get("projectTitle"));
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.detailsPageCounter." + dataset.get("projectTitle"));
      throw e;
    }
  }

  @Test(dataProvider = "detailsProject", groups = { "functionality", "upload" }, description = "test inappropriate button")
  public void inappropriateButton(HashMap<String, String> dataset) throws Throwable {
    try {
      String response = projectUploader.upload(dataset);
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");
      openAdminLocation();

      openLocation("catroid/details/" + id);
      assertTrue(session().isElementPresent("reportAsInappropriateButton"));
      session().click("reportAsInappropriateButton");
      assertTrue(session().isVisible("reportInappropriateReason"));
      assertTrue(session().isVisible("reportInappropriateReportButton"));
      assertTrue(session().isVisible("reportInappropriateCancelButton"));
      session().click("reportAsInappropriateButton");
      assertFalse(session().isVisible("reportInappropriateReason"));
      session().click("reportAsInappropriateButton");
      assertTrue(session().isVisible("reportInappropriateReason"));
      session().click("reportInappropriateCancelButton");
      assertFalse(session().isVisible("reportInappropriateReason"));
      session().click("reportAsInappropriateButton");
      session().click("reportInappropriateReportButton");
      ajaxWait();
      assertFalse(session().isVisible("reportInappropriateReason"));
      assertFalse(session().isTextPresent("You reported this project as inappropriate!"));
      session().click("reportAsInappropriateButton");
      session().type("reportInappropriateReason", "my selenium reason");
      session().click("reportInappropriateReportButton");
      ajaxWait();
      assertFalse(session().isVisible("reportInappropriateReason"));
      assertTrue(session().isTextPresent("You reported this project as inappropriate!"));

      session().refresh();
      waitForPageToLoad();
      ajaxWait();
      session().click("reportAsInappropriateButton");
      session().type("reportInappropriateReason", "my selenium reason 2");
      session().focus("reportInappropriateReason");
      session().keyPress("reportInappropriateReason", "\\13");
      ajaxWait();
      assertFalse(session().isVisible("reportInappropriateReason"));
      assertTrue(session().isTextPresent("You reported this project as inappropriate!"));

      openAdminLocation("/tools/inappropriateProjects");
      session().click("resolve" + id);
      waitForTextPresent("The project was succesfully restored and set to visible!");
      assertFalse(session().isTextPresent(id));
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

      assertTrue(session().isElementPresent("showFullDescriptionButton"));
      String shortDescriptionFromPage = session().getText("xpath=//p[@id='detailsDescription']");
      assertFalse(shortDescriptionFromPage.equals(dataset.get("projectDescription")));

      session().click("showFullDescriptionButton");
      String fullDescriptionFromPage = session().getText("xpath=//p[@id='detailsDescription']");
      assertTrue(fullDescriptionFromPage.equals(dataset.get("projectDescription")));
      assertFalse(fullDescriptionFromPage.equals(shortDescriptionFromPage));
      assertTrue(session().isElementPresent("showShortDescriptionButton"));
      session().click("showShortDescriptionButton");
      Thread.sleep(Config.TIMEOUT_THREAD);
      assertEquals(shortDescriptionFromPage, session().getText("xpath=//p[@id='detailsDescription']"));
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
        session().refresh();
        waitForPageToLoad();
        ajaxWait();
        assertFalse(session().isElementPresent("xpath=//img[@class='projectDetailsQRImage']"));
        assertTrue(qrCodeFileNew.renameTo(qrCodeFile));
        session().refresh();
        waitForPageToLoad();
        ajaxWait();
        assertTrue(session().isElementPresent("xpath=//img[@class='projectDetailsQRImage']"));
      } else {
        assertFalse(session().isElementPresent("xpath=//img[@class='projectDetailsQRImage']"));
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
      waitForElementPresent("xpath=//button[@id='showQrCodeInfoButton']");

      assertTrue(session().isElementPresent("xpath=//button[@id='showQrCodeInfoButton']"));
      assertTrue(session().isElementPresent("xpath=//div[@id='qrcodeInfo']"));
      assertTrue(session().isElementPresent("xpath=//button[@id='hideQrCodeInfoButton']"));
      assertTrue(session().isVisible("xpath=//button[@id='showQrCodeInfoButton']"));
      assertFalse(session().isVisible("xpath=//button[@id='hideQrCodeInfoButton']"));
      assertFalse(session().isVisible("xpath=//div[@id='qrcodeInfo']"));
      session().click("showQrCodeInfoButton");
      assertFalse(session().isVisible("xpath=//button[@id='showQrCodeInfoButton']"));
      assertTrue(session().isVisible("xpath=//button[@id='hideQrCodeInfoButton']"));
      assertTrue(session().isVisible("xpath=//div[@id='qrcodeInfo']"));
      session().click("hideQrCodeInfoButton");
      assertTrue(session().isVisible("xpath=//button[@id='showQrCodeInfoButton']"));
      assertFalse(session().isVisible("xpath=//button[@id='hideQrCodeInfoButton']"));
      assertFalse(session().isVisible("xpath=//div[@id='qrcodeInfo']"));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.QRCodeInfo");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "test if project size is visible inside download-button")
  public void ProjectSizeInfo() throws Throwable {
    try {
      openLocation();
      ajaxWait();
      clickLastVisibleProject();
      waitForElementPresent("xpath=//span[@class='detailsDownloadButtonText']");
      assertRegExp(".*Download.*MB.*", session().getText("xpath=//span[@class='detailsDownloadButtonText']"));

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
      assertRegExp(".*/catroid/errorPage", session().getLocation());
      assertTrue(session().isTextPresent("No entry was found for the given ID.:"));
      assertTrue(session().isTextPresent("ID: " + invalidProject));
      assertFalse(session().isElementPresent("xpath=//div[@class='detailsFlexDiv']"));
    } catch(AssertionError e) {
      captureScreen("DetailsTests.invalidProjectID");
      throw e;
    } catch(Exception e) {
      captureScreen("DetailsTests.invalidProjectID");
      throw e;
    }
  }

  @DataProvider(name = "titlesAndDescriptions")
  public Object[][] titlesAndDescriptions() {
    Object[][] returnArray = new Object[][] {
        { CommonData
            .getUploadPayload(
                "more button selenium test",
                "This is a description which should have more characters than defined by the threshold in config.php. And once again: This is a description which should have more characters than defined by the threshold in config.php. Thats it!",
                "", "", "", "", "0") },
        { CommonData
            .getUploadPayload(
                "more button special chars test",
                "This is a description which has special chars like \", \' or < and > in it and it should have more characters than defined by the threshold in config.php. And once again: This is a description with \"special chars\" and should have more characters than defined by the threshold in config.php. Thats it!",
                "", "", "", "", "0") }, };
    return returnArray;
  }

  @DataProvider(name = "detailsProject")
  public Object[][] detailsProject() {
    Object[][] returnArray = new Object[][] {
        { CommonData.getUploadPayload("details_test1small", "details_test_description", "", "", "", "", "0") },
        { CommonData.getUploadPayload("details_test2big", "details_test_description", "test2.zip", "38B9AA38175AEDDD1BABABAD63025C72", "", "", "0") }, };
    return returnArray;
  }
}