/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
      assertTrue(driver().findElement(By.xpath("//a[@class='downloadLink']")).getAttribute("href").contains(
          "mailto:webmaster@catroid.org?subject=Question%20regarding%20the%20privacy%20policy%20of%20Catroid"));
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

      assertTrue(isTextPresent("Welcome to the Catroid community!"));
      assertTrue(isTextPresent("As part of the Catroid community, you are sharing projects and ideas with people:"));
      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText'][3]/a"));
      assertRegExp(".*Creative Commons — Attribution-ShareAlike 2.0 Generic — CC BY-SA 2.0.*", driver().getTitle());
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a[2]"));
      assertTrue(isTextPresent("GNU GENERAL PUBLIC LICENSE"));
      assertTrue(isTextPresent("Version 3, 29 June 2007"));
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a[3]"));
      assertTrue(isTextPresent("GNU AFFERO GENERAL PUBLIC LICENSE"));
      assertTrue(isTextPresent("Version 3, 19 November 2007"));
      closePopUp();

      clickAndWaitForPopUp(By.xpath("//p[@class='licenseText']/a[4]"));
      assertRegExp(".*catroid -.*", driver().getTitle());
      assertRegExp(".*An on-device graphical programming language for Android inspired by Scratch.*", driver().getTitle());
      closePopUp();
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
      assertTrue(driver().findElement(By.xpath("//a[@class='downloadLink']")).
          getAttribute("href").contains("mailto:webmaster@catroid.org"));

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
