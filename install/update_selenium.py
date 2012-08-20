#!/usr/bin/env python

import fileinput, glob, os, shutil, sys

currentSeleniumVersion		= '2.25.0'
currentPostgreSQLdriver		= '9.1-902.jdbc4'

basePath					= os.getcwd()
seleniumDir					= os.path.join(basePath, 'tests', 'selenium-grid')
seleniumLibDir				= os.path.join(seleniumDir, 'lib')
seleniumToolsDir			= os.path.join(seleniumDir, 'tools')

seleniumDownloadURL			= 'https://selenium.googlecode.com/files/'
seleniumServerZip			= 'selenium-server-' + currentSeleniumVersion + '.zip'
seleniumServerJar			= 'selenium-server-' + currentSeleniumVersion + '.jar'
seleniumServerStandaloneJar	= 'selenium-server-standalone-' + currentSeleniumVersion + '.jar'
seleniumTempFiles			= 'selenium-' + currentSeleniumVersion
postgreSQLdriverDownloadURL	= 'http://jdbc.postgresql.org/download/'
postgreSQLdriverJar			= 'postgresql-' + currentPostgreSQLdriver + '.jar'

def cleanUp():
	# remove remaining files
	if os.path.isdir(os.path.join(basePath, seleniumTempFiles)):
		shutil.rmtree(os.path.join(basePath, seleniumTempFiles))
	if os.path.isfile(seleniumServerZip):
		os.remove(seleniumServerZip)

# delete current libs
if os.path.isdir(seleniumLibDir):
	shutil.rmtree(seleniumLibDir)

for jar in glob.glob(os.path.join(seleniumToolsDir, "*.jar")):
	os.remove(jar)

cleanUp()

# get and move files
os.system('wget ' + seleniumDownloadURL + seleniumServerZip)
os.system('unzip '  + seleniumServerZip)
os.rename(os.path.join(basePath, seleniumTempFiles, 'libs'), seleniumLibDir)
os.rename(os.path.join(basePath, seleniumTempFiles, seleniumServerJar), os.path.join(seleniumLibDir, seleniumServerJar))
os.rename(os.path.join(basePath, seleniumTempFiles, seleniumServerStandaloneJar), os.path.join(seleniumToolsDir, seleniumServerStandaloneJar))

os.system('wget ' + postgreSQLdriverDownloadURL + postgreSQLdriverJar)
os.rename(postgreSQLdriverJar, os.path.join(basePath, seleniumLibDir, postgreSQLdriverJar))


# replace version number in build.xml
for line in fileinput.FileInput(os.path.join(seleniumToolsDir, 'build.xml'), inplace=1):
	if '<property name="selenium-tools.version" value="' in line:
		line = '  <property name="selenium-tools.version" value="' + currentSeleniumVersion + '" />\n'
	print line,   #for python3 do: print line, end=''

cleanUp()
