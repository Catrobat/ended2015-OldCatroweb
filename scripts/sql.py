#!/usr/bin/env python
'''   
 *    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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
'''

import commands, glob, os, sys

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Sql:
	basePath					= os.getcwd()
	dbUser						= 'website'
	sqlPath						= os.path.join(basePath, 'sql')
	stateTable				= 'Record_of_my_Database_State'
	cli								= 'psql -w -A -U ' + dbUser + ' '
	run								= None

	#--------------------------------------------------------------------------------------------------------------------	
	def localCommand(command):
		return commands.getoutput(command)

	#--------------------------------------------------------------------------------------------------------------------	
	def error(self, command):
		return 'FATAL ERROR: No database connection.'
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, callback=localCommand):
		self.run = callback
		if self.checkConnection():
			print 'Please enter your password, it is necessary to restart apache:'
			os.system('sudo service apache2 restart')

	#--------------------------------------------------------------------------------------------------------------------
	def checkConnection(self):
		if 'FATAL' in self.run(self.cli + '-d template1 -c "\l"'):
			print '** ERROR ***********************************************************************'
			print 'couldn\'t connect to database!!!'
			self.run = self.error
			return False
		return True

	#--------------------------------------------------------------------------------------------------------------------	
	def initDbs(self):
		for database in os.listdir(self.sqlPath):
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				self.createDb(database)
				
	#--------------------------------------------------------------------------------------------------------------------	
	def purgeDbs(self):
		for database in os.listdir(self.sqlPath):
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				self.dropDb(database)

	#--------------------------------------------------------------------------------------------------------------------	
	def createDb(self, database):
		result = self.run(self.cli + '-d template1 -c "CREATE DATABASE ' + database + ' WITH ENCODING \'UTF8\';"')
		if 'ERROR' in result:
			print 'couldn\'t create ' + database
		else:
			print 'created ' + database
			
		self.run(self.cli + '-d ' + database + ' -c "CREATE TABLE IF NOT EXISTS ' + self.stateTable + ' (statement character varying(511));"')
		self.executeFiles(database, 'init')
		self.executeFiles(database, 'updates')
		
	#--------------------------------------------------------------------------------------------------------------------	
	def dropDb(self, database):
		result = self.run(self.cli + '-d template1 -c "DROP DATABASE IF EXISTS ' + database + '"')
		if 'ERROR' in result:
			print 'couldn\'t drop ' + database
			print result
		else:
			print 'dropped ' + database

	#--------------------------------------------------------------------------------------------------------------------	
	def executeFiles(self, database, type):
		print ' ' + type + ':'
		for sqlFile in sorted(glob.glob(os.path.join(self.sqlPath, database, type, '*.sql'))):
			basename = os.path.basename(sqlFile)
			sqlFile =  os.path.relpath(sqlFile, self.basePath)
			alreadyExecuted = self.run(self.cli + '-d ' + database + ' -c "SELECT * FROM ' + self.stateTable + ' WHERE statement=\'' + type + basename + '\';"')
			
			if '0 rows' in alreadyExecuted or 'does not exist' in alreadyExecuted:
				result = self.run(self.cli + '-d ' + database + ' -f ' + sqlFile)
				if 'ERROR' in result:
					print ' - error executing ' + basename
					print result
				else:
					self.run(self.cli + '-d ' + database + ' -c "INSERT INTO ' + self.stateTable + ' VALUES (\'' + type + basename + '\');"')
					print ' - executed ' + basename
			else:
				print ' - skipped ' + basename
		print ''

	#--------------------------------------------------------------------------------------------------------------------	
	def dumpDb(self, database):
		result = self.run('pg_dump ' + database +  ' -U ' + self.dbUser + ' -c -Ft | gzip -c9 > ' + database + '.tar.gz')
		if 'ERROR' in result:
			print 'error dumping ' + database
			print result
		else:
			print 'dumped ' + database

	#--------------------------------------------------------------------------------------------------------------------	
	def restoreDb(self, database):
		self.dropDb(database)

		result = self.run('gzip -dc ' + database + '.tar.gz | pg_restore -U ' + self.dbUser + ' -c')
		if 'ERROR' in result:
			print 'error restoring ' + database
			print result
		else:
			print 'restored ' + database

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	Sql().purgeDbs()
	Sql().initDbs()
	Sql().dumpDb('catroweb')
	#Sql().restoreDb('catroweb')
