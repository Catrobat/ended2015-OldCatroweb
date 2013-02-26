#!/usr/bin/env python
'''   
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
'''

import commands, os, sys

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class CssChecker:
	basePath					= os.getcwd()
	toolsPath					= os.path.join(basePath, 'tools')
	tool							= 'java'
	checker 					= 'cd ' + toolsPath + '; ' + tool + ' -jar stylesheets.jar'

	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self):
		if 'not found' in commands.getoutput(self.tool + ' --version'):
			print '** ERROR ***********************************************************************'
			print 'Please install java.'

		if not os.path.isdir(self.toolsPath):
			print '** ERROR ***********************************************************************'
			print 'Toolsfolder is missing. The build tools should be located in:'
			print ' ' + self.toolsPath

		if 'Unable to access' in commands.getoutput(self.checker + ' --version'):
			print '** ERROR ***********************************************************************'
			print 'Please get all necessary tools.'
		
	#--------------------------------------------------------------------------------------------------------------------	
	def run(self, dir='include'):
		pathToCheck = os.path.join(self.basePath, dir)
		for (path, dirs, files) in os.walk(pathToCheck):
			for file in files:
				if '.css' in file:
					print 'checking ' + file
					if os.system(self.checker + ' --allow-unrecognized-functions --allow-unrecognized-properties ' + os.path.join(path, file) + ' > /dev/null') is not 0:
						sys.exit(-1)


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class DeadlinkChecker:
	location					=	'http://catroid.local'
	tool							= 'linkchecker'
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self):
		if 'not found' in commands.getoutput(self.tool + ' --version'):
			print '** ERROR ***********************************************************************'
			print 'Please install linkchecker.'
		
	#--------------------------------------------------------------------------------------------------------------------	
	def run(self):
		result = os.system(self.tool + ' --ignore-url=^mailto: --ignore-url=^javascript: ' + self.location)
		if result is not 0:
			sys.exit(-1)


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class JsChecker:
	basePath					= os.getcwd()
	toolsPath					= os.path.join(basePath, 'tools')
	tool							= 'java'
	checker 					= 'cd ' + toolsPath + '; ' + tool + ' -jar compiler.jar'

	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self):
		if 'not found' in commands.getoutput(self.tool + ' --version'):
			print '** ERROR ***********************************************************************'
			print 'Please install java.'

		if not os.path.isdir(self.toolsPath):
			print '** ERROR ***********************************************************************'
			print 'Toolsfolder is missing. The build tools should be located in:'
			print ' ' + self.toolsPath

		if 'Unable to access' in commands.getoutput(self.checker + ' --help'):
			print '** ERROR ***********************************************************************'
			print 'Please get all necessary tools.'
		
	#--------------------------------------------------------------------------------------------------------------------	
	def run(self, dir='include'):
		pathToCheck = os.path.join(self.basePath, dir)
		for (path, dirs, files) in os.walk(pathToCheck):
			for file in files:
				if '.js' in file:
					print 'checking ' + file
					if os.system(self.checker + ' --compilation_level SIMPLE_OPTIMIZATIONS --js ' + os.path.join(path, file) + ' > /dev/null') is not 0:
						sys.exit(-1)


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class PhpUnit:
	basePath					= os.getcwd()
	testPath					=	os.path.join(basePath, 'tests', 'phpunit')
	tool							= 'phpunit'
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self):
		if 'not found' in commands.getoutput(self.tool + ' -h'):
			print '** ERROR ***********************************************************************'
			print 'Please install PHPunit.'
		
		if not os.path.isdir(self.testPath):
			print '** ERROR ***********************************************************************'
			print 'Testfolder is missing. The PHPunit tests should be located in:'
			print ' ' + self.testPath


	#--------------------------------------------------------------------------------------------------------------------
	def runTestCase(self, suite):
		testcase = os.path.join(self.testPath, suite)
		if os.path.isfile(testcase):
			result = os.system('cd ' + self.testPath + '; ' + self.tool + ' ' + suite)
			if result is not 0:
				sys.exit(-1)
		else:
			print 'No such test file: ' + testcase

	#--------------------------------------------------------------------------------------------------------------------
	def run(self, suite):
		testsuite = os.path.join(self.testPath, suite)
		if os.path.isdir(testsuite):
			result = os.system('cd ' + self.testPath + '; ' + self.tool + ' ' + suite)
			if result is not 0:
				sys.exit(-1)
		else:
			print 'No such test suite: ' + testsuite


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Selenium:
	basePath					= os.getcwd()
	testPath					=	os.path.join(basePath, 'tests', 'selenium-grid')
	testToolsPath			=	os.path.join(basePath, 'tests', 'selenium-grid', 'tools')
	tool							= 'ant'

	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self):
		if 'not found' in commands.getoutput(self.tool + ' -h'):
			print '** ERROR ***********************************************************************'
			print 'Please install ant.'
		
		if not os.path.isdir(self.testPath):
			print '** ERROR ***********************************************************************'
			print 'Testfolder is missing. The Selenium tests should be located in:'
			print ' ' + self.testPath

	#--------------------------------------------------------------------------------------------------------------------
	def run(self, suite, args=''):
		os.system('cd ' + self.testToolsPath + '; ant launch-hub')
		os.system('cd ' + self.testToolsPath + '; ant launch-remote-control -Drole=webdriver -DhubURL=http://localhost:4444/grid/register -Dport=5556 -DbrowserName=firefox -DbrowserVersion=3.6 -DmaxInstances=3 -Dplatform= -DnodeTimeout=30')
		if suite == 'catroid':
			os.system('cd ' + self.testPath + '; ant run-catroid-tests ' + args)
		if suite == 'single':
			os.system('cd ' + self.testPath + '; ant run-single-test ' + args)
		if suite == 'group':
			os.system('cd ' + self.testPath + '; ant run-group-test ' + args)
		
	#--------------------------------------------------------------------------------------------------------------------
	def stop(self):
		os.system('cd ' + self.testToolsPath + '; ant stop-hub')
		os.system('cd ' + self.testToolsPath + '; ant stop-remote-controls')


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	if len(sys.argv) > 1:
		if sys.argv[1] == 'css':
			CssChecker().run()
		if sys.argv[1] == 'deadlinks':
			DeadlinkChecker().run()
		if sys.argv[1] == 'js':
			JsChecker().run()
		if sys.argv[1] == 'phpunit':
			if len(sys.argv) > 2:
				PhpUnit().run(sys.argv[2])
			else:
				sys.exit(-1)
		if sys.argv[1] == 'selenium':
			if len(sys.argv) > 3:
				Selenium().run(sys.argv[2], sys.argv[3])
			elif len(sys.argv) > 2:
				if sys.argv[2] == 'stop':
					Selenium().stop()
				else:
					Selenium().run(sys.argv[2])
			else:
				sys.exit(-1)
	else:
		CssChecker().run()
		JsChecker().run()
		DeadlinkChecker().run()
		PhpUnit().runAll()
		Selenium().run('catroid')
