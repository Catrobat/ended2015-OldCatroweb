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

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class LicenseTests extends BaseTest{  
  
  @Test(groups = {"license", "firefox", "default"}, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    this.session.click("xpath=//a[@class='license']");
    waitForPageToLoad();

    assertTrue(this.session.isTextPresent("Privacy Policy"));
    this.session.isElementPresent("xpath=//p[@class='licenseText']/a");
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
	  this.session.open(Config.TESTS_BASE_PATH);
	  waitForPageToLoad();
    this.session.click("xpath=//a[@class='license'][2]");
    waitForPageToLoad();

    assertTrue(this.session.isTextPresent("Welcome to the Catroid community!"));
    assertTrue(this.session.isTextPresent("As part of the Catroid community, you are sharing projects and ideas with people:"));
    clickAndWaitForPopUp("xpath=//p[@class='licenseText'][3]/a","_blank");        
    assertRegExp("test", "test");
    assertRegExp(".*Creative Commons — Attribution-ShareAlike 2.0 Generic — CC BY-SA 2.0.*", this.session.getTitle());
    closePopUp();    

    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[2]","_blank");
    assertTrue(this.session.isTextPresent("GNU GENERAL PUBLIC LICENSE"));
    assertTrue(this.session.isTextPresent("Version 3, 29 June 2007"));
    closePopUp();

    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[3]","_blank");
    assertTrue(this.session.isTextPresent("GNU AFFERO GENERAL PUBLIC LICENSE"));
    assertTrue(this.session.isTextPresent("Version 3, 19 November 2007"));
    closePopUp();

    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[4]","_blank");
    assertRegExp(".*catroid -.*", this.session.getTitle());
    assertRegExp(".*An on-device graphical programming language for Android inspired by Scratch.*", this.session.getTitle());
    closePopUp();    
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check copyright policy link/page")
  public void copyrightPolicy() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    this.session.click("xpath=//a[@class='license'][3]");
    waitForPageToLoad();

    assertTrue(this.session.isTextPresent("Copyright Policy"));
    this.session.isElementPresent("xpath=//p[@class='licenseText']/a");
    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[2]","_blank");    
    assertTrue(this.session.isTextPresent("Directive 2001/29/EC of the European Parliament and of the Council"));
    assertTrue(this.session.isTextPresent("32001L0029"));
    closePopUp();

    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[3]","_blank");
    assertTrue(this.session.isTextPresent("Chilling Effects"));
    assertTrue(this.session.isTextPresent("Chilling Effects Clearinghouse - www.chillingeffects.org"));
    closePopUp();
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check imprint link/page")
  public void imprint() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    this.session.click("xpath=//a[@class='license'][4]");
    waitForPageToLoad();

    assertTrue(this.session.isTextPresent("Address"));
    assertTrue(this.session.isTextPresent("Institut für Softwaretechnologie"));
    assertTrue(this.session.isTextPresent("Technische Universität Graz"));
    assertTrue(this.session.isTextPresent("Inffeldgasse 16B/II"));
    assertTrue(this.session.isTextPresent("8010 Graz"));
    assertTrue(this.session.isTextPresent("Austria"));
    
    clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a","_blank");
    assertRegExp(".*IST web - Index.*", this.session.getTitle());
    closePopUp();
  }
  
  
}
