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


import commands
import os
import re
import sys


class CssChecker:
	basePath = os.getcwd()
	toolsPath = os.path.join(basePath, 'tools')
	tool = 'java'
	checker = 'cd %s; %s -jar stylesheets.jar' % (toolsPath, tool)


	def __init__(self):
		if 'not found' in commands.getoutput('%s --version' % self.tool):
			print('** ERROR ***********************************************************************')
			print('Please install java.')
			sys.exit(-1)

		if not os.path.isdir(self.toolsPath):
			print('** ERROR ***********************************************************************')
			print('Tools folder is missing. The build tools should be located in:')
			print(' %s' % self.toolsPath)
			sys.exit(-1)

		if 'Unable to access' in commands.getoutput('%s --version' % self.checker):
			print('** ERROR ***********************************************************************')
			print('Please get all necessary tools (make init-all).')
			sys.exit(-1)
		
	
	def run(self, dir='include'):
		pathToCheck = os.path.join(self.basePath, dir)
		for (path, dirs, files) in os.walk(pathToCheck):
			for file in files:
				if '.css' in file:
					print('checking %s' % file)
					result = os.system('%s --allow-unrecognized-functions --allow-unrecognized-properties %s > /dev/null' % (self.checker, os.path.join(path, file)))
					if result is not 0:
						sys.exit(-1)



class DeadlinkChecker:
	tool = 'linkchecker'
	

	def __init__(self):
		if 'not found' in commands.getoutput('%s --version' % self.tool):
			print('** ERROR ***********************************************************************')
			print('Please install linkchecker.')
			sys.exit(-1)
		
	
	def run(self, location='http://catroid.local'):
		result = os.system('%s --ignore-url=^mailto: --ignore-url=^javascript: %s' % (self.tool, location))
		if result is not 0:
			sys.exit(-1)



class JsChecker:
	basePath = os.getcwd()
	toolsPath = os.path.join(basePath, 'tools')
	tool = 'java'
	checker = 'cd %s; %s -jar compiler.jar' % (toolsPath, tool)


	def __init__(self):
		if 'not found' in commands.getoutput('%s --version' % self.tool):
			print('** ERROR ***********************************************************************')
			print('Please install java.')
			sys.exit(-1)

		if not os.path.isdir(self.toolsPath):
			print('** ERROR ***********************************************************************')
			print('Tools folder is missing. The build tools should be located in:')
			print(' %s' % self.toolsPath)
			sys.exit(-1)

		if 'Unable to access' in commands.getoutput('%s --help' % self.checker):
			print('** ERROR ***********************************************************************')
			print('Please get all necessary tools (make init-all).')
			sys.exit(-1)
		
	
	def run(self, dir='include'):
		pathToCheck = os.path.join(self.basePath, dir)
		for (path, dirs, files) in os.walk(pathToCheck):
			for file in files:
				if '.js' in file:
					print('checking %s' % file)
					result = os.system('%s --compilation_level SIMPLE_OPTIMIZATIONS --js %s > /dev/null' % (self.checker, os.path.join(path, file)))
					if result is not 0:
						sys.exit(-1)



class PhpUnit:
	basePath = os.getcwd()
	testPath = os.path.join(basePath, 'tests', 'phpunit')
	tool = 'phpunit'
	

	def __init__(self):
		if 'not found' in commands.getoutput('%s -h' % self.tool):
			print('** ERROR ***********************************************************************')
			print('Please install PHPunit.')
			sys.exit(-1)
		
		if not os.path.isdir(self.testPath):
			print('** ERROR ***********************************************************************')
			print('Test folder is missing. The PHPunit tests should be located in:')
			print(' %s' % self.testPath)
			sys.exit(-1)


	def run(self, suite):
		testsuite = os.path.join(self.testPath, suite)
		if os.path.isdir(testsuite):
			result = os.system('cd %s; %s %s' % (self.testPath, self.tool, suite))
			if result is not 0:
				sys.exit(-1)
		elif os.path.isfile(testsuite):
			result = os.system('cd %s; %s %s' % (self.testPath, self.tool, suite))
			if result is not 0:
				sys.exit(-1)
		else:
			print('No such test suite: %s' % testsuite)
			sys.exit(-1)



class Selenium:
	basePath = os.getcwd()
	testPath = os.path.join(basePath, 'tests', 'selenium-grid')
	testToolsPath = os.path.join(basePath, 'tests', 'selenium-grid', 'tools')
	tool = 'ant'


	def __init__(self):
		if 'not found' in commands.getoutput('%s -h' % self.tool):
			print('** ERROR ***********************************************************************')
			print('Please install ant.')
			sys.exit(-1)
		
		if not os.path.isdir(self.testPath):
			print('** ERROR ***********************************************************************')
			print('Test folder is missing. The Selenium tests should be located in:')
			print(' %s' % self.testPath)
			sys.exit(-1)


	def run(self, suite, args=''):
		browser = 'firefox'
		match = re.match(r".*-Dbrowser=(?P<browser>.+?) ", args)
		try:
			browser = match.groupdict()['browser']
		except:
			pass
		
		os.system('cd %s; ant launch-hub' % self.testToolsPath)
		os.system('cd %s; ant launch-remote-control -Drole=webdriver -DhubURL=http://localhost:4444/grid/register -Dport=5556 -DbrowserName=%s -DmaxInstances=3 -Dplatform= -DnodeTimeout=30' % (self.testToolsPath, browser))
		if suite == 'catroid':
			os.system('cd %s; ant run-catroid-tests %s' % (self.testPath, args))
		elif suite == 'single':
			os.system('cd %s; ant run-single-test %s' % (self.testPath, args))
		elif suite == 'group':
			os.system('cd %s; ant run-group-test %s' % (self.testPath, args))
		else:
			print('No such test suite: %s' % suite)
			sys.exit(-1)
		

	def stop(self):
		os.system('cd %s; ant stop-hub' % self.testToolsPath)
		os.system('cd %s; ant stop-remote-controls' % self.testToolsPath)



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'css':
			if len(sys.argv) > 2:
				CssChecker().run(sys.argv[2])
			else:
				CssChecker().run()

		elif sys.argv[1] == 'deadlinks':
			if len(sys.argv) > 2:
				DeadlinkChecker().run(sys.argv[2])
			else:
				DeadlinkChecker().run()

		elif sys.argv[1] == 'js':
			if len(sys.argv) > 2:
				JsChecker().run(sys.argv[2])
			else:
				JsChecker().run()

		elif sys.argv[1] == 'phpunit':
			PhpUnit().run(sys.argv[2])

		elif sys.argv[1] == 'selenium':
			if len(sys.argv) > 3:
				Selenium().run(sys.argv[2], sys.argv[3])
			else:
				if sys.argv[2] == 'stop':
					Selenium().stop()
				else:
					Selenium().run(sys.argv[2])

		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  css <FOLDER>          Static checks CSS files in given folder.')
		print('  deadlinks <LOCATION>  Checks for dead links for given location.')
		print('  js <FOLDER>           Static checks JS files in given folder.')
		print('  phpunit <TESTSUITE>   Runs a PHPunit test suite.')
		print('                        (suites: framework, catroid, admin, api, common)')
		print('  selenium <TESTSUITE>  Runs a Selenium test suite.')
		print('                        (suites: catroid, single, group)')
		print('  selenium stop         Stops all running Selenium processes')
