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

import org.openqa.selenium.By;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "LicenseTests" })
public class LicenseTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    try {
      openLocation();
      assertTrue(isElementPresent(By.id("_privacy")));
      
      clickAndWaitForPopUp(By.id("_privacy"));
      assertRegExp(".*Privacy Policy.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("_termsofuse")).click();
      ajaxWait();

      assertTrue(isTextPresent("Welcome to the Catroid community!"));
      assertTrue(isTextPresent("As part of the Catroid community, you are sharing projects and ideas with people:"));

      // click onto licenseofuploadedprojects link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]"));
      assertRegExp(".*Licenses of uploaded Catrobat programs.*", driver().getTitle());
      closePopUp();
      
      // click onto licenseofsystem link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]"));
      assertRegExp(".*Licenses of the Catrobat System.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[2]"));
      assertRegExp(".*Catrobat/Catroid · GitHub.*", driver().getTitle());
      closePopUp();

      // click onto termsofservice link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[5]/li[3]/a[1]"));
      assertRegExp(".*Terms of Service.*", driver().getTitle());
      closePopUp();

      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][7]/a")).getAttribute("href")
          .contains("mailto:webmaster@catroid.org?subject=Terms%20of%20Use"));

      // click onto catroid link
      driver().findElement(By.xpath("//p[@class='licenseText'][9]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check copyright policy link/page")
  public void copyrightPolicy() throws Throwable {
    try {
      openLocation();
      
      clickAndWaitForPopUp(By.id("_copyright"));
      assertRegExp(".*Copyright Policy.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.copyrightPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.copyrightPolicy");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check imprint link/page")
  public void imprint() throws Throwable {
    try {
      openLocation();
      clickAndWaitForPopUp(By.id("_imprint"));
      assertRegExp(".*Imprint.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.imprint");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.imprint");
      throw e;
    }
  }
}
