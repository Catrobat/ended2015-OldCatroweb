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

public class IndexTests extends BaseTest{
  
  @Test(groups = {"index", "firefox", "default"}, description = "location tests")    
  public void location() {
    this.session.open("/catroid/index/9999999999999999999");
    waitForPageToLoad();
    ajaxWait();
    //test page title and header title        
    assertTrue(this.session.getTitle().matches("^Catroid Website -.*"));        
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertFalse(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));        

    String location = DataProvider.getRandomLongString();
    this.session.open("/catroid/index/"+location);
    waitForPageToLoad();
    ajaxWait();
    
    //test page title and header title        
    assertTrue(this.session.getTitle().matches("^Catroid Website -.*"));        
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));    
    assertFalse(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));        
    
    location = DataProvider.getRandomLongString();
    this.session.open("/catroid/details/"+location);
    waitForPageToLoad();
    ajaxWait();   
    //test page title and header title        
    assertRegExp(".*/catroid/errorPage", this.session.getLocation());
    assertTrue(this.session.isTextPresent(location));   
  }
     
  @Test(groups = {"index", "firefox", "default"}, description = "click download,header,details -links ")
  public void index() throws Throwable {
    this.session.open("/");
    waitForPageToLoad();
    ajaxWait();    
    //test page title and header title        
    assertTrue(this.session.getTitle().matches("^Catroid Website -.*"));        
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

    // test catroid header text
    assertTrue(this.session.isElementPresent("xpath=//img[@class='catroidLettering']"));        
    // test logo link
    assertTrue(this.session.isElementPresent("xpath=//div[@class='webHeadLogo']"));
    this.session.click("xpath=//div[@id='aIndexWebLogoLeft']");        
    ajaxWait();
        
    //test catroid download link
    assertTrue(this.session.isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
    this.session.click("xpath=//a[@id='aIndexWebLogoMiddle']");
    this.session.selectWindow("_blank");
    waitForPageToLoad();
    assertTrue(this.session.isTextPresent("Catroid_0-4-3d.apk"));
    assertTrue(this.session.isTextPresent("Paintroid_0.6.4b.apk"));
    this.session.close();        
    this.session.selectWindow(null);
        
    //test links to details page
    this.session.click("xpath=//a[@class='projectListDetailsLink']");
    waitForPageToLoad();
    assertRegExp(".*/catroid/details/[0-9]+",this.session.getLocation());
        
    this.session.goBack();
    waitForPageToLoad();                
    waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);        
    waitForElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']");
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));        
    assertTrue(this.session.isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
        
    //test home link
    this.session.click("xpath=//div[@id='aIndexWebLogoLeft']");        
    ajaxWait();
    assertTrue(this.session.isElementPresent("xpath=//img[@class='catroidLettering']"));
  }
    
  @Test(groups = {"index", "firefox", "default"}, description = "page navigation tests")
  public void pageNavigation() throws Throwable {       
    this.session.open("/");
    waitForPageToLoad();
    ajaxWait();
    waitForElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']");
    // TODO $this->doUpload();
    assertFalse(this.session.isVisible("fewerProjects"));
    assertTrue(this.session.isVisible("moreProjects"));
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));
    int i=0;    
    for(i=0; i < Config.PROJECT_PAGE_SHOW_MAX_PAGES; i++) {
      this.session.click("moreProjects");
      ajaxWait();
      assertRegExp("^"+CommonStrings.WEBSITE_TITLE+" - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i+2) + "$",this.session.getTitle());
    }
    
    assertTrue(this.session.isVisible("fewerProjects"));
    assertTrue(this.session.isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));
    this.session.click("fewerProjects");    
    ajaxWait();
    assertRegExp("^"+ CommonStrings.WEBSITE_TITLE +" - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$",this.session.getTitle());

    // test session
    this.session.refresh();                
    waitForPageToLoad();
    ajaxWait();            
    waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
    assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$",this.session.getTitle());

    //test links to details page
    this.session.click("xpath=//a[@class='projectListDetailsLink']");
    waitForPageToLoad();
    assertRegExp(".*/catroid/details.*", this.session.getLocation());
    this.session.goBack();        
    waitForPageToLoad();
    ajaxWait();
    waitForTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE);
    assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (i) + "$",this.session.getTitle());

    // test header click
    this.session.click("aIndexWebLogoLeft");
    ajaxWait();
    assertRegExp("^" + CommonStrings.WEBSITE_TITLE + " - " + CommonStrings.NEWEST_PROJECTS_PAGE_TITLE + " - " + (1) + "$",this.session.getTitle());        
  }
}
