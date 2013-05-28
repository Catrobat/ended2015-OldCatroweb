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


import fileinput
import glob
import os
import shutil
import sys


class Selenium:
	currentSeleniumVersion = '2.33.0'
	currentPostgreSQLdriver = '9.1-902.jdbc4'
	
	basePath = os.getcwd()
	seleniumDir = os.path.join(basePath, 'tests', 'selenium-grid')
	seleniumLibDir = os.path.join(seleniumDir, 'lib')
	seleniumToolsDir = os.path.join(seleniumDir, 'tools')
	
	seleniumDownloadURL = 'https://selenium.googlecode.com/files/'
	seleniumServerZip = 'selenium-server-%s.zip' % currentSeleniumVersion
	seleniumServerJar = 'selenium-server-%s.jar' % currentSeleniumVersion
	seleniumServerStandaloneJar = 'selenium-server-standalone-%s.jar' % currentSeleniumVersion
	seleniumTempFiles = 'selenium-%s' % currentSeleniumVersion
	postgreSQLdriverDownloadURL	= 'http://jdbc.postgresql.org/download/'
	postgreSQLdriverJar = 'postgresql-%s.jar' % currentPostgreSQLdriver


	def __init__(self):
		if not os.path.isdir(self.seleniumDir):
			print('creating directory %s' % self.seleniumDir)
			os.makedirs(self.seleniumDir)
		if not os.path.isdir(self.seleniumToolsDir):
			print('creating directory %s' % self.seleniumToolsDir)
			os.makedirs(self.seleniumToolsDir)

	
	def cleanTempFiles(self):
		if os.path.isdir(os.path.join(self.basePath, self.seleniumTempFiles)):
			shutil.rmtree(os.path.join(self.basePath, self.seleniumTempFiles))
		if os.path.isfile(self.seleniumServerZip):
			os.remove(self.seleniumServerZip)


	def removeSeleniumLibs(self):
		print('removing Selenium libraries')
		if os.path.isdir(self.seleniumLibDir):
			shutil.rmtree(self.seleniumLibDir)
		for jar in glob.glob(os.path.join(self.seleniumToolsDir, "*.jar")):
			os.remove(jar)

	
	def downloadLibs(self): 
		os.system('wget %s%s' % (self.seleniumDownloadURL, self.seleniumServerZip))
		os.system('unzip %s' % self.seleniumServerZip)
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, self.seleniumTempFiles, self.seleniumTempFiles, 'libs'), self.seleniumLibDir)
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, self.seleniumTempFiles, self.seleniumTempFiles, self.seleniumServerJar), os.path.join(self.seleniumLibDir, self.seleniumServerJar))
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, self.seleniumTempFiles, self.seleniumTempFiles, self.seleniumServerStandaloneJar), os.path.join(self.seleniumToolsDir, self.seleniumServerStandaloneJar))
		
		os.system('wget %s%s' % (self.postgreSQLdriverDownloadURL, self.postgreSQLdriverJar))
		os.rename(self.postgreSQLdriverJar, os.path.join(self.basePath, self.seleniumLibDir, self.postgreSQLdriverJar))
		
	
	def updateVersionNumber(self):
		for line in fileinput.FileInput(os.path.join(self.seleniumToolsDir, 'build.xml'), inplace=1):
			if '<property name="selenium-tools.version" value="' in line:
				line = '  <property name="selenium-tools.version" value="%s" />\n' % self.currentSeleniumVersion
			sys.stdout.write(line)
	

	def update(self):
		self.removeSeleniumLibs()
		self.cleanTempFiles()
		self.downloadLibs()
		self.updateVersionNumber()
		self.cleanTempFiles()
		print('updated Selenium libraries')



class JSCompiler:
	basePath = os.getcwd()
	toolsDir = os.path.join(basePath, 'tools')
	
	downloadURL = 'https://closure-compiler.googlecode.com/files/'
	serverZip = 'compiler-latest.zip'
	serverJar = 'compiler.jar'


	def __init__(self):
		if not os.path.isdir(self.toolsDir):
			print('creating directory %s' % self.toolsDir)
			os.makedirs(self.toolsDir)


	def cleanTempFiles(self):
		if os.path.isfile('COPYING'):
			os.remove('COPYING')
		if os.path.isfile('README'):
			os.remove('README')
		if os.path.isfile(self.serverZip):
			os.remove(self.serverZip)


	def removeCompiler(self):
		print('removing JavaScript compiler')
		if os.path.isfile(os.path.join(self.toolsDir, self.serverJar)):
			os.remove(os.path.join(self.toolsDir, self.serverJar))


	def downloadLibs(self): 
		os.system('wget %s%s' % (self.downloadURL, self.serverZip))
		os.system('unzip %s' % self.serverZip)
		os.rename(os.path.join(self.basePath, self.serverJar), os.path.join(self.toolsDir, self.serverJar))
		os.chmod(os.path.join(self.toolsDir, self.serverJar), 0444) 


	def update(self):
		self.removeCompiler()
		self.cleanTempFiles()
		self.downloadLibs()
		self.cleanTempFiles()
		print('updated JavaScript compiler')



class CSSCompiler:
	basePath = os.getcwd()
	toolsDir = os.path.join(basePath, 'tools')
	
	downloadURL = 'https://closure-stylesheets.googlecode.com/files/'
	serverJar = 'closure-stylesheets-20111230.jar'
	localJar = 'stylesheets.jar'


	def __init__(self):
		if not os.path.isdir(self.toolsDir):
			print('creating directory %s' % self.toolsDir)
			os.makedirs(self.toolsDir)


	def cleanTempFiles(self):
		if os.path.isfile(self.serverJar):
			os.remove(self.serverJar)


	def removeCompiler(self):
		print('removing CSS compiler')
		if os.path.isfile(os.path.join(self.toolsDir, self.localJar)):
			os.remove(os.path.join(self.toolsDir, self.localJar))


	def downloadLibs(self): 
		os.system('wget %s%s' % (self.downloadURL, self.serverJar))
		os.rename(os.path.join(self.basePath, self.serverJar), os.path.join(self.toolsDir, self.localJar))
		os.chmod(os.path.join(self.toolsDir, self.localJar), 0444)


	def update(self):
		self.removeCompiler()
		self.cleanTempFiles()
		self.downloadLibs()
		self.cleanTempFiles()
		print('updated CSS compiler')



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'selenium':
			if len(sys.argv) > 2 and sys.argv[2] == 'clean':
				Selenium().removeSeleniumLibs()
			else:
				Selenium().update()

		elif sys.argv[1] == 'jscompiler':
			if len(sys.argv) > 2 and sys.argv[2] == 'clean':
				JSCompiler().removeCompiler()
			else:
				JSCompiler().update()

		elif sys.argv[1] == 'csscompiler':
			if len(sys.argv) > 2 and sys.argv[2] == 'clean':
				CSSCompiler().removeCompiler()
			else:
				CSSCompiler().update()

		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  selenium              Inits or updates Selenium libraries.')
		print('  selenium clean        Removes selenium libraries.')
		print('  jscompiler            Inits or updates JavaScript compiler.')
		print('  jscompiler clean      Removes JavaScript compiler.')
		print('  csscompiler           Inits or updates CSS compiler.')
		print('  csscompiler clean     Removes CSS compiler.')
