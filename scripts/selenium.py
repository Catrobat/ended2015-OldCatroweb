#!/usr/bin/env python

import fileinput, glob, os, shutil, sys
from cleanup import Cleaner

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Selenium:
	currentSeleniumVersion			= '2.25.0'
	currentPostgreSQLdriver			= '9.1-902.jdbc4'
	
	basePath										= os.getcwd()
	seleniumDir									= os.path.join(basePath, 'tests', 'selenium-grid')
	seleniumLibDir							= os.path.join(seleniumDir, 'lib')
	seleniumToolsDir						= os.path.join(seleniumDir, 'tools')
	
	seleniumDownloadURL					= 'https://selenium.googlecode.com/files/'
	seleniumServerZip						= 'selenium-server-' + currentSeleniumVersion + '.zip'
	seleniumServerJar						= 'selenium-server-' + currentSeleniumVersion + '.jar'
	seleniumServerStandaloneJar	= 'selenium-server-standalone-' + currentSeleniumVersion + '.jar'
	seleniumTempFiles						= 'selenium-' + currentSeleniumVersion
	postgreSQLdriverDownloadURL	= 'http://jdbc.postgresql.org/download/'
	postgreSQLdriverJar					= 'postgresql-' + currentPostgreSQLdriver + '.jar'

	#--------------------------------------------------------------------------------------------------------------------	
	def cleanUp(self):
		# remove downloaded files
		if os.path.isdir(os.path.join(self.basePath, self.seleniumTempFiles)):
			shutil.rmtree(os.path.join(self.basePath, self.seleniumTempFiles))
		if os.path.isfile(self.seleniumServerZip):
			os.remove(self.seleniumServerZip)

	#--------------------------------------------------------------------------------------------------------------------	
	def downloadLibs(self): 
		# get and move files
		os.system('wget ' + self.seleniumDownloadURL + self.seleniumServerZip)
		os.system('unzip ' + self.seleniumServerZip)
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, 'libs'), self.seleniumLibDir)
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, self.seleniumServerJar), os.path.join(self.seleniumLibDir, self.seleniumServerJar))
		os.rename(os.path.join(self.basePath, self.seleniumTempFiles, self.seleniumServerStandaloneJar), os.path.join(self.seleniumToolsDir, self.seleniumServerStandaloneJar))
		
		os.system('wget ' + self.postgreSQLdriverDownloadURL + self.postgreSQLdriverJar)
		os.rename(self.postgreSQLdriverJar, os.path.join(self.basePath, self.seleniumLibDir, self.postgreSQLdriverJar))
		
	#--------------------------------------------------------------------------------------------------------------------	
	def updateVersionNumber(self):
		# replace version number in build.xml
		for line in fileinput.FileInput(os.path.join(self.seleniumToolsDir, 'build.xml'), inplace=1):
			if '<property name="selenium-tools.version" value="' in line:
				line = '  <property name="selenium-tools.version" value="' + self.currentSeleniumVersion + '" />\n'
			print line, #for python3 do: print line, end=''
	
	#--------------------------------------------------------------------------------------------------------------------	
	def update(self):
		Cleaner().removeSeleniumLibs()
		self.cleanUp()
		self.downloadLibs()
		self.updateVersionNumber()
		self.cleanUp()

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	if len(sys.argv) > 1:
		if sys.argv[1] == 'update':
			Selenium().update()
