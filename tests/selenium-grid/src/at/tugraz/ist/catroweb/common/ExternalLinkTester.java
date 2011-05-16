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

package at.tugraz.ist.catroweb.common;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import com.thoughtworks.selenium.DefaultSelenium;
import com.thoughtworks.selenium.Selenium;
import com.thoughtworks.selenium.SeleniumException;

public class ExternalLinkTester {
  protected List<Selenium> selenium;
  protected String seleniumHost;
  protected int seleniumPort;
  protected String browser;
  
  public ExternalLinkTester(String seleniumHost, int seleniumPort, String browser) {
    this.seleniumHost = seleniumHost;
    this.seleniumPort = seleniumPort;
    this.browser = browser;
    this.selenium = Collections.synchronizedList(new ArrayList<Selenium>());
  }
  
  public void cleanup() {
    for(Selenium session : this.selenium) { 
      session.close();
      session.stop();
    }
    this.selenium.clear();
  }

  public void stopSession(Selenium session) {
    try {
      this.selenium.remove(session);
      session.close();
      session.stop();
    } catch(SeleniumException e) {
      System.out.println("This session has been stopped before! " + e.getMessage());
    }
  }

  public Selenium getSession(Selenium session, String locator) {
    String url = session.getAttribute(locator + "/@href");
    url = url.replace("http://", "");

    String[] temp = url.split("/", 2);
    if(temp.length < 2) {
      return getSession("http://" + url + "/", "");
    }
    return getSession("http://" + temp[0] + "/", temp[1]);
  }
  
  public Selenium getSession(String basePath, String path) {
    Selenium session = new DefaultSelenium(this.seleniumHost, this.seleniumPort, this.browser, basePath);
    session.start();
    session.open(path);
    session.waitForPageToLoad(CommonConfig.TIMEOUT);
    this.selenium.add(session);
    return session;
  }
}
