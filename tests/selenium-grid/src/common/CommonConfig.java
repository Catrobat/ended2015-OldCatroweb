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

import org.apache.tools.ant.taskdefs.Definer;

/**
 */
public class CommonConfig  {

	private static CommonConfig instance = new CommonConfig();
	
	/**
	 * determines if the message is printed to the std out
	 * usage: org.testng.Reporter.log(message,true)
	 * @true:  std out + HTML.report
	 * @false: HTML report only	
	 */
	public static final boolean REPORTER_LOG_TO_STD_OUT = true;
	
	/**
	 * set slow mode (selenium)
	 */
	public static final boolean TESTS_SLOW_MODE = false;
	
	/**
	 * value used if slow mode is on; execution time in ms for each command (selenium)
	 */
	public static final int     TESTS_SLOW_SPEED =  1000;
	
	/**
	 *  selenium waitForPageToLoad-command: waits 10000ms  
	 */
	public static final String     WAIT_FOR_PAGE_TO_LOAD_LONG =  "10000";
	
	/**
	 * selenium waitForPageToLoad-command: waits 1000ms 
	 */
	public static final String     WAIT_FOR_PAGE_TO_LOAD_SHORT =  "1000";
	
	/**
	 *  selenium setTimout-command: waits 120000ms  
	 */
	public static final String     TIMEOUT =  "120000";
	
	
	
	
	
	private CommonConfig(){
		
	}
	
	public static CommonConfig getInstance() {
        return instance;
    }
	
	
	


}
