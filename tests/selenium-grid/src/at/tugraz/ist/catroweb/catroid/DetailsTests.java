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

import org.openqa.selenium.By;
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

      By viewsElement = By.xpath("//*[@id='projectDetailsContainer']/div[5]/ul/li[5]/div[2]");
      By downloadsElement = By.xpath("//*[@id='projectDetailsContainer']/div[5]/ul/li[4]/div[2]/span");
      By filesizeElement = By.xpath("//*[@id='projectDetailsContainer']/div[5]/ul/li[3]/div[2]");
      By downloadsButton = By.xpath("//*[@id='projectDetailsContainer']/div[3]/div/a[1]/div/span");

      openLocation("details/" + id);
      ajaxWait();
      // project title
      assertTrue(containsElementText(By.id("projectDetailsProjectTitle"), title.toUpperCase()));
      // test the view counter
      numOfViews = Integer.parseInt(driver().findElement(viewsElement).getText().split(" ")[0]);

      driver().navigate().refresh();
      ajaxWait();
      numOfViewsAfter = Integer.parseInt(driver().findElement(viewsElement).getText().split(" ")[0]);
      assertEquals(numOfViews + 1, numOfViewsAfter);

      // test the download counter
      numOfDownloads = Integer.parseInt(driver().findElement(downloadsElement).getText().split(" ")[0]);
      driver().findElement(downloadsButton).click();

      driver().navigate().refresh();
      ajaxWait();
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(downloadsElement).getText().split(" ")[0]);
      assertEquals(numOfDownloads + 1, numOfDownloadsAfter);

      // check file size
      double filesize = CommonFunctions.getFileSizeRounded(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_DIRECTORY + id + Config.PROJECTS_EXTENTION);
      String filesizeText = driver().findElement(filesizeElement).getText();
      String displayedfilesize = filesizeText.substring(0, filesizeText.indexOf(" MB"));
      if(displayedfilesize.startsWith("<")) {
        // smaller files are displayed as "< 0.1 MB"
        assertEquals("< " + String.valueOf(filesize), displayedfilesize);
      } else {
        assertEquals(String.valueOf(filesize), displayedfilesize);
      }

      HashMap<String, String> versionInfo = CommonFunctions.getVersionInfo(id);
      String versionInfoText = driver().findElement(By.id("projectDetailsDownloadVersion")).getText();
      assertEquals("Pocket Code version: " + versionInfo.get("version_name"), versionInfoText);
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
      
      By downloadsElement = By.xpath("//*[@id='projectDetailsContainer']/div[5]/ul/li[4]/div[2]/span");
      
      openLocation("details/" + id);
      
      // test the download counter
      numOfDownloads = Integer.parseInt(driver().findElement(downloadsElement).getText().split(" ")[0]);
      driver().findElement(By.id("projectDetailsThumbnailImage")).click();
      driver().navigate().refresh();
      ajaxWait();
      numOfDownloadsAfter = Integer.parseInt(driver().findElement(downloadsElement).getText().split(" ")[0]);
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

      openLocation("details/" + id);
      assertTrue(isElementPresent(By.id("reportAsInappropriateButton")));
      
      driver().findElement(By.id("reportAsInappropriateButton")).click();
      ajaxWait();
      driver().findElement(By.xpath("//*[@id='reportAsInappropriateDialog']/a")).click();
      ajaxWait();

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

  @Test(groups = { "visibility" }, description = "check if invalid project id redirects to errorpage")
  public void invalidProjectID() throws Throwable {
    try {
      String invalidProject = CommonData.getRandomShortString(10);
      openLocation("details/" + invalidProject);
      assertRegExp(".*/error", driver().getCurrentUrl());
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