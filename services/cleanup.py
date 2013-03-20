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

import fileinput, glob, os, shutil, sys
from sql import Sql

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Cleaner:
	basePath					= os.getcwd()
	buildDir					= os.path.join(basePath, 'build')
	resourceDir				= os.path.join(basePath, 'resources')
	seleniumDir				= os.path.join(basePath, 'tests', 'selenium-grid')
	seleniumLibDir		= os.path.join(seleniumDir, 'lib')
	seleniumToolsDir	= os.path.join(seleniumDir, 'tools')
	toolsDir					= os.path.join(basePath, 'tools')
	cacheDir					= os.path.join(basePath, 'cache')
	sqlOverviewDir = os.path.join(basePath, 'sql', 'overview')

	#--------------------------------------------------------------------------------------------------------------------	
	def dropDatabases(self):
		Sql().purgeDbs()

	#--------------------------------------------------------------------------------------------------------------------	
	def cleanResources(self):
		for entry in glob.glob(os.path.join(self.resourceDir, 'catroid', '*')):
			if os.path.isdir(entry):
				os.system('rm -rf ' + entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'projects', '*')):
			if not 'projects' in entry and not '/1.' in entry and not '/2.' in entry:
				os.remove(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'qrcodes', '*.png')):
			if not '/1_' in entry and not '/2_' in entry:
				os.remove(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'thumbnails', '*.png')):
			if not '/1_' in entry and not '/2_' in entry and not '/thumbnail_' in entry:
				os.remove(entry)

	#--------------------------------------------------------------------------------------------------------------------
	def cleanSQLoverview(self):
		if os.path.isdir(self.sqlOverviewDir):
			shutil.rmtree(self.sqlOverviewDir)

	#--------------------------------------------------------------------------------------------------------------------
	def removeSeleniumLibs(self):
		if os.path.isdir(self.seleniumLibDir):
			shutil.rmtree(self.seleniumLibDir)
		for jar in glob.glob(os.path.join(self.seleniumToolsDir, "*.jar")):
			os.remove(jar)

	#--------------------------------------------------------------------------------------------------------------------
	def removeJSCompiler(self):
		if os.path.isfile(os.path.join(self.toolsDir, 'compiler.jar')):
			os.remove(os.path.join(self.toolsDir, 'compiler.jar'))

	#--------------------------------------------------------------------------------------------------------------------
	def removeCSSCompiler(self):
		if os.path.isfile(os.path.join(self.toolsDir, 'stylesheets.jar')):
			os.remove(os.path.join(self.toolsDir, 'stylesheets.jar'))

	#--------------------------------------------------------------------------------------------------------------------
	def clearCache(self):
		for js in glob.glob(os.path.join(self.cacheDir, "*.js")):
			os.system('rm -f ' + js)
		for css in glob.glob(os.path.join(self.cacheDir, "*.css")):
			os.system('rm -f ' + css)

	#--------------------------------------------------------------------------------------------------------------------
	def cleanDatabaseAndResources(self):
		if os.path.isdir(self.buildDir):
			shutil.rmtree(self.buildDir)

		self.dropDatabases()
		self.cleanResources()

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	if len(sys.argv) > 1:
		if sys.argv[1] == 'website':
			clean = Cleaner()
			clean.clearCache()
			clean.cleanDatabaseAndResources()
			clean.cleanSQLoverview()
		if sys.argv[1] == 'tools':
			clean = Cleaner()
			clean.removeSeleniumLibs()
			clean.removeJSCompiler()
			clean.removeCSSCompiler()
	else:
		print "no argument given. did you mean 'website'?"
