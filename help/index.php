<?php
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

require_once('../config.php');
define('REAL_BASE_PATH',str_replace('help/', '', BASE_PATH));
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Catroid Cheat Sheet</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <link rel="icon" href="<?php echo REAL_BASE_PATH?>images/logo/favicon.png" type="image/png" />
</head>
<body>
	<div id="all">
		<div id="antcommands">
			<h1>ant cheatsheet</h1>
			<pre>
--- local tests

    ant run-phpunit-tests		 
    ant run-selenium-local-tests
    
    <i>+ run specific test</i>
    ant run-selenium-single-test    
    ant run-selenium-single-test -Dtest.browserName=firefox -Dtest.class=catroid.LicenseTests -Dtest.method=imprint
    
    <i>+ run specific group of tests</i>
    ant run-selenium-group-test
    ant run-selenium-group-test -Dtest.browserName=firefox -Dtest.group=admin
    
    <i>+ run tests on Internet Explorer</i>
    add following parameters to any selenium command: -Dtest.browserName "internet explorer" -Dtest.browserVersion=9 -Dtest.plattform ",platform=WINDOWS"
    e.g.: ant run-selenium-local-tests -Dtest.browserName "internet explorer" -Dtest.browserVersion=9 -Dtest.platform ",platform=WINDOWS"
    
    <i>+ Shut down local Selenium processes</i> 
    ant stop-selenium-grid

--- android tests
    --> see WIKI for installation instructions!     
    <i>+ starts android emulator with webdriver configuration</i>
    ant start-android-emulator
    <i>+ run tests with -DwebSite=http://10.0.2.2/</i> i.e.:    
    ant run-selenium-single-test -Dtest.browserName=android -Dtest.class=catroid.LicenseTests -Dtest.method=imprint -DwebSite=http://10.0.2.2/

--- remote tests

    <i>+ Connect remote controls to kittyroid test server (http://catroidtestserver.ist.tugraz.at)</i>
    ant selenium-tools.launch-remote-control -DhubURL http://catroidtestserver.ist.tugraz.at:4444/grid/register

    <i>+ Start remote test on kityroid test server (http://catroidtestserver.ist.tugraz.at)</i>
    ant run-selenium-remote-tests -Dhost.user=catroid -Dhost.pass=cat.roid.web -Dtest.browserName=firefox

--- database 

    ant init-db
    ant update-db
      </pre>
		</div>
		<div id="links">
			<h1>Localhost</h1>
			<a href="http://<?php echo $_SERVER['SERVER_NAME']?>:4444/grid/console" target="_blank">Grid	Console</a> - Selenium-Grid Server Status<br/> 
			<a href="<?php echo REAL_BASE_PATH;?>tests/selenium-grid/target/reports/" target="_blank">Test	Reports</a> - Test results of last testrun<br/> 
			<a href="http://<?php echo $_SERVER['SERVER_NAME']?>/phppgadmin/" target="_blank">phpPgAdmin</a> -  WebBased SQL administration tool<br/>
			
			<h1>kittyroidlocal</h1> 
			<h3>(http://catroidtestserver.ist.tugraz.at)</h3>
      <a href="http://catroidtestserver.ist.tugraz.at/" target="_blank">Kittyroidlocal</a> - Testserver at the Catroid Room<br/>
			<a href="http://catroidtestserver.ist.tugraz.at:4444/grid/console" target="_blank">Grid Console</a> - Selenium-Grid Server Status<br/>
			<a href="http://catroidtestserver.ist.tugraz.at/tests/selenium-grid/target/reports/" target="_blank">Test Reports</a> - Test results of last testrun<br/>
      <a href="http://catroidtestserver.ist.tugraz.at/phppgadmin/" target="_blank">phpPgAdmin</a>* -  WebBased SQL administration tool<br/>
      
      <h1>Catroidwebtest</h1> 
      <a href="http://catroidwebtest.ist.tugraz.at/" target="_blank">Catroidwebtest</a> - Testserver for the Catroweb Team<br/>
      <a href="http://catroidwebtest.ist.tugraz.at:8080" target="_blank">Pootle Server</a> - Translation Server<br/>
      <a href="http://catroidwebtest.ist.tugraz.at/sql-overview/" target="_blank">SQL	Overview</a> - Grafical Database Overview<br/>
      <a href="http://catroidwebtest.ist.tugraz.at/phppgadmin/" target="_blank">phpPgAdmin</a>* -  WebBased SQL administration tool<br/>
      
      <h1>Public Server</h1> <a href="http://catroid.org" target="_blank">Catroid.org</a> - Public Server<br/>
			<a href="http://kittyroid.org" target="_blank">Kittyroid.org</a> - Public Server<br/>
      <a href="http://catroid.org/phppgadmin/" target="_blank">phpPgAdmin</a>* -  WebBased SQL administration tool<br/>
      
      <h1>Other</h1> 
			<a href="http://catroidtest.ist.tugraz.at/" target="_blank">Catroidtest</a> - Testserver for the Catroid Team<br/>
      <a href="http://catroidtest.ist.tugraz.at/phppgadmin/" target="_blank">phpPgAdmin</a>* -  WebBased SQL administration tool<br/>
      <a href="https://selenium.googlecode.com/svn/trunk/java/CHANGELOG" target="_blank">Selenium changelog</a> - Most recent changes<br/>
      
      <p><strong>* Note:</strong> only accessible with an TUGraz IP address</p>	
		</div>
	</div>
</body>
</html>
<?
?>