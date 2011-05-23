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

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class BlockedIpTests extends BaseTest {
  
  @Test(dataProvider = "blockedIps", groups = { "catroid", "firefox", "default" }, description = "test blocked ips")
  public void blockedIps(String project_id, String blocked_ip) throws Throwable {
    log("*** blockedIps test: functionality currently not available");
    //   TODO
//    session().open(Config.TESTS_BASE_PATH);
//    waitForPageToLoad();
//    
//    blockIp(blocked_ip);    
//    session().open(Config.TESTS_BASE_PATH+"catroid/details/"+project_id);
//    waitForPageToLoad();
//    assertTrue(session().isElementPresent("xpath=//div[@class='errorMessage']"));
//    assertTrue(session().isTextPresent("Your IP-Address has been blocked."));
//    
//    session().open(Config.TESTS_BASE_PATH);
//    waitForPageToLoad();
//    assertTrue(session().isElementPresent("xpath=//div[@class='errorMessage']"));
//    assertTrue(session().isTextPresent("Your IP-Address has been blocked."));
//    unblockIp(blocked_ip);   
  }
  
  /**
   * @dataProvider unblockedIps
   */
  @Test(dataProvider = "unblockedIps", groups = { "catroid", "firefox", "default" }, description = "test unblocked ips")
  public void unblockedIps(String project_id, String unblocked_ip) throws Throwable {
    log("*** unblockedIps test: functionality currently not available");
    //TODO
//    blockIp(unblocked_ip);
//    session().open(Config.TESTS_BASE_PATH+"catroid/details/"+project_id);
//    waitForPageToLoad();
//    assertFalse(session().isElementPresent("xpath=//div[@class='errorMessage']"));
//    assertFalse(session().isTextPresent("Your IP-Address has been blocked."));
//    
//    session().open(Config.TESTS_BASE_PATH);
//    waitForPageToLoad();
//    assertFalse(session().isElementPresent("xpath=//div[@class='errorMessage']"));
//    assertFalse(session().isTextPresent("Your IP-Address has been blocked."));
//    unblockIp(unblocked_ip);
  }
  
  @DataProvider(name="blockedIps")
  public Object[][] blockedIpsData(){
    Object[][] returnArray = new Object[][] {
        {"1", "127.0.0.1"},
        {"1", "127.0.0."},
        {"1", "127.0."},
        {"1", "127."}        
      };
     return returnArray;
  }
  
  @DataProvider(name="unblockedIps")
  public Object[][] unblockedIpsData(){
    Object[][] returnArray = new Object[][] {
        {"1", "127.0.0.2"},
        {"1", "127.12.0."},
        {"1", "127.12."},
        {"1", "129.0.0.1"}
      };
     return returnArray;
  }
  
  private void blockIp(String ip) {
    try {
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();      
      statement.executeUpdate("INSERT INTO blocked_ips(ip_address) " + "VALUES ('"+ip+"')");
      statement.close();
      connection.close();
    } catch(SQLException e) {
      System.out.println("BlockedIpTests: blockIp: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
  }
  
  private void  unblockIp(String ip) {
    try {
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM blocked_ips WHERE id_address='" + ip + "';");
      statement.close();
      connection.close();
    } catch(SQLException e) {
      System.out.println("BlockedIpTests: unblockIp: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
  }
}
