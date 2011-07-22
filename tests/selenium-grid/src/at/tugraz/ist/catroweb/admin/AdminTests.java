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

package at.tugraz.ist.catroweb.admin;

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

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
      clickLink(By.xpath("//a[2]"));
      assertRegExp(".*Catroid Website.*", driver().getTitle());
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

  @Test(groups = { "visibility" }, description = "clicks all available links in the admin area")
  public void clickAllLinks() throws Throwable {
    try {
      openAdminLocation();
      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());
      assertTrue(isTextPresent("Catroid Administration Site"));

      clickLink(By.xpath("//a[1]"));
      assertTrue(isTextPresent("Administration Tools"));
      assertTrue(isTextPresent("remove inconsistant project files"));
      assertTrue(isTextPresent("edit projects"));
      assertTrue(isTextPresent("thumbnail uploader"));
      assertTrue(isTextPresent("inappropriate projects"));
      assertTrue(isTextPresent("approve unapproved words"));
      assertTrue(isTextPresent("manage Languages"));
      assertTrue(isTextPresent("block IPs"));
      assertTrue(isTextPresent("block Users"));

      assertRegExp(".*Administration - Catroid Website.*", driver().getTitle());

      clickLink(By.xpath("//a[1]"));
      assertTrue(isTextPresent("Answer"));
      driver().navigate().back();

      clickLink(By.xpath("//a[2]"));
      assertTrue(isTextPresent("Administration Tools - List of available projects"));
      driver().navigate().back();

      clickLink(By.xpath("//a[3]"));
      assertTrue(isTextPresent("Administration Tools - Thumbnail Uploader"));
      driver().navigate().back();

      clickLink(By.xpath("//a[4]"));
      assertTrue(isTextPresent("Administration Tools - List of inappropriate projects"));
      driver().navigate().back();

      clickLink(By.xpath("//a[5]"));
      assertTrue(isTextPresent("Administration Tools - List of unapproved Words"));
      driver().navigate().back();

      clickLink(By.xpath("//a[6]"));
      assertTrue(isTextPresent("Administration Tools - Language Management"));
      driver().navigate().back();

      clickLink(By.xpath("//a[7]"));
      assertTrue(isTextPresent("Administration Tools - List of blocked IP-Addresses"));
      driver().navigate().back();

      clickLink(By.xpath("//a[8]"));
      assertTrue(isTextPresent("Administration Tools - List of blocked users"));
      driver().navigate().back();

      assertTrue(isTextPresent("- back"));
      clickLink(By.xpath("//a[9]"));
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
      String title = "Testproject " + CommonData.getRandomLongString(200);
      String response = projectUploader.upload(CommonData.getUploadPayload(title, "", "", "", "", "", ""));
      String id = CommonFunctions.getValueFromJSONobject(response, "projectId");

      openLocation("catroid/details/" + id);
      ajaxWait();
      clickLink(By.id("reportAsInappropriateButton"));
      driver().findElement(By.id("reportInappropriateReason")).sendKeys("my selenium reason");
      clickLink(By.id("reportInappropriateReportButton"));
      ajaxWait();
      assertTrue(isTextPresent("You reported this project as inappropriate!"));
      openAdminLocation("/tools/inappropriateProjects");
      assertTrue(isTextPresent(id));

      clickAndWaitForPopUp(By.xpath("//a[@id='detailsLink" + id + "']"));
      assertTrue(isTextPresent(title));
      closePopUp();

      clickOkOnNextConfirmationBox();
      clickLink(By.id("resolve" + id));
      assertTrue(isTextPresent("The project was succesfully restored and set to visible!"));
      assertFalse(isTextPresent(id));
    } catch(AssertionError e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("AdminTests.inappropriateProjects");
      throw e;
    }
  }
}
