/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2014 The Catrobat Team
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

@Test(groups = { "catroid", "FooterTests" })
public class FooterTests extends BaseTest {


  @Test(groups = { "visibility" }, description = "check if footer links are present")
  public void footerLinks() throws Throwable {
    try {
      openLocation();

      assertTrue(isTextPresent("Tutorial"));
      assertTrue(isTextPresent("About"));
      assertTrue(isTextPresent("Google Play"));
      assertTrue(isTextPresent("Privacy Policy"));
      assertTrue(isTextPresent("Terms of Use"));
      assertTrue(isTextPresent("Imprint"));
    } catch(AssertionError e) {
      captureScreen("FooterTests.footerLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.footerLinks");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check if footer links are present on mobile site")
  public void footerLinksMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      assertTrue(isElementPresent(By.id("footerLessButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      assertTrue(isTextPresent("Tutorial"));
      assertTrue(isTextPresent("About"));
      assertTrue(isTextPresent("Google Play"));
      assertTrue(isTextPresent("Privacy Policy"));
      assertTrue(isTextPresent("Terms of Use"));
      assertTrue(isTextPresent("Imprint"));
      
      driver().findElement(By.id("footerLessButton")).click();
      
      assertFalse(isTextPresent("Tutorial"));
      assertFalse(isTextPresent("About"));
      assertFalse(isTextPresent("Google Play"));
      assertFalse(isTextPresent("Privacy Policy"));
      assertFalse(isTextPresent("Terms of Use"));
      assertFalse(isTextPresent("Imprint"));
    } catch(AssertionError e) {
      captureScreen("FooterTests.footerLinksMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.footerLinksMobile");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check about link/page")
  public void help() throws Throwable {
    try {
      openLocation();
      
      By help = By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[1]/a");
      assertTrue(isElementPresent(help));
      
      clickAndWaitForPopUp(help);
      assertRegExp(".*Pocket Code Website - Help -*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.help");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.help");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check about link/page")
  public void helpMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      By help = By.xpath("//*[@id='mobileFooterMenu']/ul/li[1]/a");
      assertTrue(isElementPresent(help));
      
      clickAndWaitForPopUp(help);
      assertRegExp(".*Pocket Code Website - Help -*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.helpMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.helpMobile");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check about link/page")
  public void about() throws Throwable {
    try {
      openLocation();
      
      By about = By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[2]/a");
      assertTrue(isElementPresent(about));
      
      clickAndWaitForPopUp(about);
      assertRegExp(".*Catrobat*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.about");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.about");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check about link/page")
  public void aboutMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      By about = By.xpath("//*[@id='mobileFooterMenu']/ul/li[2]/a");
      assertTrue(isElementPresent(about));
      
      clickAndWaitForPopUp(about);
      assertRegExp(".*Catrobat*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.aboutMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.aboutMobile");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check google play link/page")
  public void googlePlay() throws Throwable {
    try {
      openLocation();
      
      By googlePlay = By.xpath("//*[@id='largeFooterMenu']/div[1]/ul/li[3]/a");
      assertTrue(isElementPresent(googlePlay));
      
      clickAndWaitForPopUp(googlePlay);
      assertRegExp(".*Pocket Code Beta - Android-Apps auf Google Play*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.googlePlay");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.googlePlay");
      throw e;
    }
  }
  
  @Test(groups = { "visibility" }, description = "check google play link/page")
  public void googlePlayMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      By googlePlay = By.xpath("//*[@id='mobileFooterMenu']/ul/li[3]/a");
      assertTrue(isElementPresent(googlePlay));
      
      clickAndWaitForPopUp(googlePlay);
      assertRegExp(".*Pocket Code Beta - Android-Apps auf Google Play*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.googlePlayMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.googlePlayMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    try {
      openLocation();
      
      By privacy = By.xpath("//*[@id='largeFooterMenu']/div[1]/ul[2]/li[1]/a");
      assertTrue(isElementPresent(privacy));
      
      clickAndWaitForPopUp(privacy);
      assertRegExp(".*Privacy Policy.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.privacyPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.privacyPolicy");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicyMobile() throws Throwable {
    try {
      openMobileLocation();
      assertTrue(isElementPresent(By.id("footerMoreButton")));
      driver().findElement(By.id("footerMoreButton")).click();
      
      By privacy = By.xpath("//*[@id='mobileFooterMenu']/ul/li[4]/a");
      assertTrue(isElementPresent(privacy));
      
      clickAndWaitForPopUp(privacy);
      assertRegExp(".*Privacy Policy.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.privacyPolicyMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.privacyPolicyMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//*[@id='largeFooterMenu']/div[1]/ul[2]/li[2]/a")).click();
      ajaxWait();

      assertTrue(isTextPresent("WELCOME TO THE CATROBAT COMMUNITY!"));
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
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[5]/li[5]/a[1]"));
      assertRegExp(".*Terms of Use and Service.*", driver().getTitle());
      closePopUp();

      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][7]/a")).getAttribute("href")
          .contains("mailto:webmaster@catrobat.org?subject=Terms%20of%20Use"));
    } catch(AssertionError e) {
      captureScreen("FooterTests.termsOfUse");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.termsOfUse");
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
      
      driver().findElement(By.xpath("//*[@id='mobileFooterMenu']/ul/li[5]/a")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("WELCOME TO THE CATROBAT COMMUNITY!"));
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
      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[5]/li[5]/a[1]"));
      assertRegExp(".*Terms of Use and Service.*", driver().getTitle());
      closePopUp();
      
      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][7]/a")).getAttribute("href")
          .contains("mailto:webmaster@catrobat.org?subject=Terms%20of%20Use"));
    } catch(AssertionError e) {
      captureScreen("FooterTests.termsOfUseMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.termsOfUseMobile");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check imprint link/page")
  public void imprint() throws Throwable {
    try {
      openLocation();
      clickAndWaitForPopUp(By.xpath("//*[@id='largeFooterMenu']/div[1]/ul[2]/li[3]/a"));
      assertRegExp(".*Imprint.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.imprint");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.imprint");
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

      clickAndWaitForPopUp(By.xpath("//*[@id='mobileFooterMenu']/ul/li[6]/a"));
      assertRegExp(".*Imprint.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("FooterTests.imprintMobile");
      throw e;
    } catch(Exception e) {
      captureScreen("FooterTests.imprintMobile");
      throw e;
    }
  }
}
