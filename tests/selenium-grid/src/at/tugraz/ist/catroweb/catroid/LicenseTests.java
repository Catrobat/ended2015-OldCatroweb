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


  @Test(groups = { "visibility" }, description = "check if license links are present")
  public void licenseLinks() throws Throwable {
    try {
      openLocation();

      assertTrue(isTextPresent("Privacy policy"));
      assertTrue(isTextPresent("Terms of Use"));
      assertTrue(isTextPresent("Imprint"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.licenseLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.licenseLinks");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check if license links are present on mobile site")
  public void licenseLinksMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      assertTrue(isElementPresent(By.id("footerLessButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      assertTrue(isTextPresent("Privacy policy"));
      assertTrue(isTextPresent("Terms of Use"));
      assertTrue(isTextPresent("Imprint"));
      
      driver().findElement(By.id("footerLessButton")).click();
      
      assertFalse(isTextPresent("Privacy policy"));
      assertFalse(isTextPresent("Terms of Use"));
      assertFalse(isTextPresent("Imprint"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.licenseLinksMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.licenseLinksMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    try {
      openLocation();
      
      By privacy = By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[1]/a");
      assertTrue(isElementPresent(privacy));
      
      clickAndWaitForPopUp(privacy);
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

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicyMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      By privacy = By.xpath("//*[@id='mobileFooterMenu']/ul/li[1]/a");
      assertTrue(isElementPresent(privacy));
      
      clickAndWaitForPopUp(privacy);
      assertRegExp(".*Privacy Policy.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.privacyPolicyMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.privacyPolicyMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[2]/a")).click();
      ajaxWait();

      assertTrue(isTextPresent("Welcome to the Catrobat community!"));
      assertTrue(isTextPresent("As part of the Catrobat community, you are sharing programs and ideas with people:"));

      // click onto licenseofuploadedprojects link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]"));
      assertRegExp(".*Licenses of uploaded Catrobat programs.*", driver().getTitle());
      closePopUp();
      
      // click onto licenseofsystem link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]"));
      assertRegExp(".*Licenses of the Catrobat System.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[2]"));
      assertRegExp(".*Catrobat.*", driver().getTitle());
      closePopUp();

      // click onto termsofservice link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[5]/li[3]/a[1]"));
      assertRegExp(".*Terms of Service.*", driver().getTitle());
      closePopUp();

      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][7]/a")).getAttribute("href")
          .contains("mailto:webmaster@catrobat.org?subject=Terms%20of%20Use"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of use link/page")
  public void termsOfUseMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      ajaxWait();
      
      driver().findElement(By.xpath("//*[@id='mobileFooterMenu']/ul/li[2]/a")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("Welcome to the Catrobat community!"));
      assertTrue(isTextPresent("As part of the Catrobat community, you are sharing programs and ideas with people:"));
      
      // click onto licenseofuploadedprojects link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]"));
      assertRegExp(".*Licenses of uploaded Catrobat programs.*", driver().getTitle());
      closePopUp();
      
      // click onto licenseofsystem link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]"));
      assertRegExp(".*Licenses of the Catrobat System.*", driver().getTitle());
      closePopUp();
      
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[2]"));
      assertRegExp(".*Catrobat.*", driver().getTitle());
      closePopUp();
      
      // click onto termsofservice link
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[5]/li[3]/a[1]"));
      assertRegExp(".*Terms of Service.*", driver().getTitle());
      closePopUp();
      
      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][7]/a")).getAttribute("href")
          .contains("mailto:webmaster@catrobat.org?subject=Terms%20of%20Use"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.termsOfUseMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.termsOfUseMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check imprint link/page")
  public void imprint() throws Throwable {
    try {
      openLocation();
      clickAndWaitForPopUp(By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[3]/a"));
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
  
  @Test(groups = { "visibility", "popupwindows" }, description = "check imprint link/page")
  public void imprintMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//*[@id='mobileFooterMenu']/ul/li[3]/a"));
      assertRegExp(".*Imprint.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.imprintMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.imprintMobile");
      throw e;
    }
  }
}
