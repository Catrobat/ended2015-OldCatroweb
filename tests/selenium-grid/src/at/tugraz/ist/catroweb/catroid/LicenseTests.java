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
      assertTrue(isElementPresent(By.xpath("//a[@class='license']")));
      driver().findElement(By.xpath("//a[@class='license']")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.xpath("//a[@class='downloadLink']")));
      assertTrue(driver().findElement(By.xpath("//a[@class='downloadLink']")).getAttribute("href")
          .contains("mailto:webmaster@catroid.org?subject=Question%20regarding%20the%20privacy%20policy%20of%20Catroid"));
      assertTrue(isTextPresent("Privacy Policy"));
      assertTrue(isElementPresent(By.xpath("//p[@class='licenseText']/a")));
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
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();

      assertTrue(isTextPresent("Welcome to the Catroid community!"));
      assertTrue(isTextPresent("As part of the Catroid community, you are sharing projects and ideas with people:"));

      // click onto licenseofuploadedprojects link
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]")).click();
      assertTrue(isTextPresent("Licenses of uploaded Catroid projects"));
      driver().navigate().back();
      ajaxWait();

      // click onto licenseofsystem link
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]")).click();
      assertTrue(isTextPresent("Licenses of the Catroid system"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[2]"));
      assertRegExp(".*Catrobat/Catroid · GitHub.*", driver().getTitle());
      closePopUp();

      // click onto termsofservice link
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[5]/li[3]/a[1]")).click();
      assertTrue(isTextPresent("Terms of Service"));
      driver().navigate().back();
      ajaxWait();

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

  @Test(groups = { "visibility", "popupwindows" }, description = "check license of uploaded projects link/page")
  public void licenseOfUploadedProjects() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]")).click();
      ajaxWait();
      assertTrue(isTextPresent("Licenses of uploaded Catroid projects"));

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[1]"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[2]")).click();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[3]"));
      assertRegExp(".*GNU Affero General Public License - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[2]/a[1]")).click();
      assertTrue(isTextPresent("cc - Attribution-ShareAlike 3.0 License"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[2]/a[2]"));
      assertRegExp(".*Creative Commons — Attribution-ShareAlike 3.0 Unported — CC BY-SA 3.0.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[1]")).click();
      assertTrue(isTextPresent("Terms of Service"));
      driver().navigate().back();
      ajaxWait();

      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][3]/a")).getAttribute("href")
          .contains("mailto:webmaster@catroid.org?subject=Licenses%20of%20uploaded%20Catroid%20projects"));

      driver().findElement(By.xpath("//p[@class='licenseText'][5]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.licenseOfUploadedProjects");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.licenseOfUploadedProjects");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check license of system link/page")
  public void licenseOfSystem() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]")).click();
      ajaxWait();
      assertTrue(isTextPresent("Licenses of the Catroid system"));

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[1]"));
      assertRegExp(".*What is free software\\? - GNU Project - Free Software Foundation \\(FSF\\).*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[2]"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[3]")).click();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[4]"));
      assertRegExp(".*GNU Affero General Public License - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[2]/em[1]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
      driver().navigate().back();
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[2]/em[2]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
      driver().navigate().back();
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[1]")).click();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[2]"));
      assertRegExp(".*GCC Runtime Library Exception Rationale and FAQ - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[3]")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
      driver().navigate().back();
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[4]"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[5]")).click();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[6]"));
      assertRegExp(".*GNU Affero General Public License - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[7]")).click();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[8]")).click();
      assertTrue(isTextPresent("Additional term exception"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[9]")).click();
      assertTrue(isTextPresent("Terms of Use"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[10]")).click();
      assertTrue(isTextPresent("Licenses of uploaded Catroid projects"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[4]/a[1]")).click();
      assertTrue(isTextPresent("cc - Attribution-ShareAlike 3.0 License"));
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[1]/li[4]/a[2]"));
      assertRegExp(".*Creative Commons — Attribution-ShareAlike 3.0 Unported — CC BY-SA 3.0.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[2]/li[1]/a[1]"));
      assertRegExp(".*XStream - About XStream.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[2]/li[1]/a[2]"));
      assertRegExp(".*XStream - License.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[2]/li[2]/a[1]"));
      assertRegExp(".*Libgdx - Desktop/Android/HTML5 Game Development.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//div[@class='licenseText']/ul[2]/li[2]/a[3]"));
      assertRegExp(".*People - libgdx - Android/HTML5/desktop game development framework - Google Project Hosting.*", driver().getTitle());
      closePopUp();

      assertTrue(driver().findElement(By.xpath("//p[@class='licenseText'][4]/a")).getAttribute("href")
          .contains("mailto:webmaster@catroid.org?subject=Licenses%20of%20the%20Catroid%20system"));

      driver().findElement(By.xpath("//p[@class='licenseText'][6]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.licenseOfSystem");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.licenseOfSystem");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of service link/page")
  public void termsOfService() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[5]/li[3]/a[1]")).click();
      ajaxWait();
      assertTrue(isTextPresent("Terms of Service"));

      driver().findElement(By.xpath("//div[@class='licenseText']/ol[1]/li[1]/a[1]")).click();
      assertTrue(isTextPresent("Privacy Policy"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ol[1]/li[1]/a[2]")).click();
      assertTrue(isTextPresent("Terms of Use"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ol[1]/li[2]/a[1]")).click();
      assertTrue(isTextPresent("Terms of Service"));

      driver().findElement(By.xpath("//div[@class='licenseText']/ol[6]/li[3]/a[1]")).click();
      assertTrue(isTextPresent("Licenses of uploaded Catroid projects"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/ol[6]/li[5]/a[1]")).click();
      assertTrue(isTextPresent("Terms of Use"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//div[@class='licenseText']/p[8]/a[1]")).click();
      assertTrue(isTextPresent("Privacy Policy"));
      driver().navigate().back();
      ajaxWait();

      driver().findElement(By.xpath("//p[@class='licenseText'][2]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.termsOfService");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.termsOfService");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check license additional term link/page")
  public void licenseAdditionalTerm() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[4]/li[3]/a[1]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[3]/a[8]")).click();
      ajaxWait();
      assertTrue(isTextPresent("Additional term exception"));

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][2]/a"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();

      driver().findElement(By.xpath("//p[@class='licenseText'][3]/a")).click();
      ajaxWait();
      assertTrue(isTextPresent("Newest Projects"));
      driver().navigate().back();
      driver().navigate().back();
      ajaxWait();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][17]/a[1]"));
      assertRegExp(".*GCC Runtime Library Exception - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][17]/a[2]"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.licenseAdditionalTerm");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.licenseAdditionalTerm");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check agpl version 3 standalone link/page")
  public void agplVersion3Standalone() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[1]/a[2]")).click();
      ajaxWait();
      assertTrue(isTextPresent("AGPL Version 3 standalone"));

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][1]/a[1]"));
      assertRegExp(".*GNU Affero General Public License - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][2]/a[1]"));
      assertRegExp(".*Free Software Foundation — Free Software Foundation — working together for free software.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.id("aGNUlink"));
      assertRegExp(".*Licenses - GNU Project - Free Software Foundation.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.agplVersion3Standalone");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.agplVersion3Standalone");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check share alike 3 link/page")
  public void shareAlike3() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][2]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[3]/li[4]/a[1]")).click();
      ajaxWait();
      driver().findElement(By.xpath("//div[@class='licenseText']/ul[1]/li[2]/a[1]")).click();
      ajaxWait();
      assertTrue(isTextPresent("cc - Attribution-ShareAlike 3.0 License"));

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][1]/a[1]"));
      assertRegExp(".*Creative Commons Legal Code.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.id("aCclink"));
      assertRegExp(".*Creative Commons.*", driver().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.shareAlike3");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.shareAlike3");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check copyright policy link/page")
  public void copyrightPolicy() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.xpath("//a[@class='license'][3]")).click();

      assertTrue(isTextPresent("Copyright Policy"));
      assertTrue(isElementPresent(By.xpath("//p[@class='licenseText']/a")));
      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a[2]"));
      assertTrue(isTextPresent("Directive 2001/29/EC of the European Parliament and of the Council"));
      assertTrue(isTextPresent("32001L0029"));
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a[3]"));
      assertTrue(isTextPresent("Chilling Effects"));
      assertTrue(isTextPresent("Chilling Effects Clearinghouse - www.chillingeffects.org"));
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
      driver().findElement(By.xpath("//a[@class='license'][4]")).click();
      
      assertTrue(isTextPresent("Address"));
      assertTrue(isTextPresent("Institute for Software Technology"));
      assertTrue(isTextPresent("Graz University of Technology"));
      assertTrue(isTextPresent("Inffeldgasse 16B/II"));
      assertTrue(isTextPresent("8010 Graz"));
      assertTrue(isTextPresent("Austria"));

      assertTrue(isElementPresent(By.xpath("//a[@class='downloadLink']")));
      assertTrue(driver().findElement(By.xpath("//a[@class='downloadLink']")).getAttribute("href").contains("mailto:webmaster@catroid.org"));

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a"));
      assertRegExp(".*IST web - Index.*", driver().getTitle());
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
