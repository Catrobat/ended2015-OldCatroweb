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

import java.lang.Thread;
import java.lang.InterruptedException;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import org.apache.tools.ant.taskdefs.Definer;

public class CommonFunctions  {
  /** Description of setSpeed()
   * @return			selenium string to use with setSpeed();
   */
  public static String setSpeed() {
    if(CommonConfig.TESTS_SLOW_MODE) {
      System.out.println("********************************************************************");
      System.out.println("* WARNING: You are running this test in slow mode!                 *");
      System.out.println("********************************************************************");
      return String.valueOf(CommonConfig.TESTS_SLOW_SPEED);
    }
    return "1";		
  }
	
  /** Description of getAjaxWaitString()	  
  * @return			selenium string to use with waitForCondition();
  */
  public static String getAjaxWaitString() {
    return "selenium.browserbot.getCurrentWindow().jQuery.active == 0";
  }
	
  /** Description of getWaitForConditionIsElementPresentString(String locator)
  *  i.e.  session().waitForCondition(getWaitForConditionIsElementPresentString(locator,"10000"));
  * @param locator	locator to be waited on
  * @return			selenium string condition 
  */
  public static String getWaitForConditionIsTextPresentString(String locator) {
    return "value = selenium.isTextPresent('"+locator+"'); value == true";
  }

  /** Description of getWaitForConditionIsElementPresentString(String locator)
  *  i.e.  session().waitForCondition(getWaitForConditionIsElementPresentString(locator,"10000"));
  * @param locator	locator to be waited on
  * @return			selenium string condition 
  */
  public static String getWaitForConditionIsElementPresentString(String locator) {
    return "value = selenium.isElementPresent('"+locator.replace("'", "\\'")+ "'); value == true";
  }
}
