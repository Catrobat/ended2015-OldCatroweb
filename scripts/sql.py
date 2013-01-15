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

import commands, glob, os, string, sys
from datetime import date

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Sql:
	basePath					= os.getcwd()
	dbUser						= 'website'
	sqlPath						= os.path.join(basePath, 'sql')
	stateTable				= 'Record_of_my_Database_State'
	cli								= 'psql -w -A -U ' + dbUser + ' '
	run								= None
	today							= date.today().strftime("%Y%m%d")

	#--------------------------------------------------------------------------------------------------------------------	
	def localCommand(command):
		return commands.getoutput(command)

	#--------------------------------------------------------------------------------------------------------------------	
	def error(self, command):
		return 'FATAL ERROR: Something is wrong with your database. Try to reinitialize it.'
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, callback=localCommand):
		self.run = callback
		if self.checkConnection():
			## CAUTON: pg_stat_activity.procpid may change to pg_stat_activity.pid in postgres 9.2
			self.run(self.cli + '-d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catroboard\';"')
			self.run(self.cli + '-d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catroweb\';"')
			self.run(self.cli + '-d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catrowiki\';"')
		else:
			self.run = self.error

	#--------------------------------------------------------------------------------------------------------------------
	def checkConnection(self):
		if 'FATAL' in self.run(self.cli + '-d template1 -c "\l"'):
			print '** ERROR ***********************************************************************'
			print 'couldn\'t connect to database!!!'
			return False
		if 'dbo' not in self.run(self.cli + '-d template1 -c "SELECT nspname FROM pg_catalog.pg_namespace;"'):
			print '** ERROR ***********************************************************************'
			print 'couldn\'t find schema dbo!!!'
			return False
		return True

	#--------------------------------------------------------------------------------------------------------------------	
	def initDbs(self):
		for database in os.listdir(self.sqlPath):
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				self.createDb(database)
				self.executeSql(database)
				
	#--------------------------------------------------------------------------------------------------------------------	
	def purgeDbs(self):
		for database in os.listdir(self.sqlPath):
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				self.dropDb(database)

	#--------------------------------------------------------------------------------------------------------------------	
	def backupDbs(self):
		dumps = ''
		for database in os.listdir(self.sqlPath):
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				dumps += database + '-sql.tar.gz '
				self.dumpDb(database)

		self.run('tar -cjf sql-' + self.today + '.tar ' + dumps)
		self.run('rm ' + dumps)

	#--------------------------------------------------------------------------------------------------------------------	
	def restoreDbs(self, backup):
		if 'No such file' not in self.run('ls ' + backup):
			self.run('tar -xvf ' + backup)
			for database in string.split(self.run('ls *-sql.tar.gz'), '\n'):
				self.restoreDb(database.replace('-sql.tar.gz', ''))
				self.run('rm ' + database)
		else:
			print 'FATAL ERROR: Backup not found.'
		
	#--------------------------------------------------------------------------------------------------------------------	
	def createDb(self, database):
		if database not in self.run(self.cli + ' -d template1 -c "SELECT datname FROM pg_database;"'):
			result = self.run(self.cli + '-d template1 -c "CREATE DATABASE ' + database + ' WITH ENCODING \'UTF8\';"')
			if 'ERROR' in result:
				print 'couldn\'t create ' + database
			else:
				print 'created ' + database

	#--------------------------------------------------------------------------------------------------------------------	
	def executeSql(self, database):
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
		result = self.run('pg_dump -n public ' + database +  ' -U ' + self.dbUser + ' -c -Ft | gzip -c9 > ' + database + '-sql.tar.gz')
		if 'ERROR' in result:
			print 'error dumping ' + database
			print result
		else:
			print 'dumped ' + database

	#--------------------------------------------------------------------------------------------------------------------	
	def restoreDb(self, database):
		if 'No such file' not in self.run('ls ' + database + '-sql.tar.gz'):
			self.dropDb(database)
			self.createDb(database)

			result = self.run('gzip -dc ' + database + '-sql.tar.gz | pg_restore -d ' + database + ' -U ' + self.dbUser)
			if 'ERROR' in result:
				print 'error restoring ' + database
				print result
			else:
				print 'restored data of ' + database
				print ''
		else:
			print 'FATAL ERROR: Found no file to restore.'

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	#Sql().purgeDbs()
	#Sql().initDbs()
	#Sql().dumpDb('catroweb')
	#Sql().restoreDb('catroweb')

	#Sql().backupDbs()
	Sql().restoreDbs('sql-20130115.tar')