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
import fileinput
import glob
import os
import re
import shutil
import string
import sys


class Sql:
	basePath = os.getcwd()
	dbUser = ''
	dbPass = ''
	sqlPath = os.path.join(basePath, 'sql')
	overviewPath = os.path.join(basePath, 'sql', 'overview')
	databases = ['catroboard', 'catroweb', 'catrowiki']
	
	stateTable = 'record_of_my_database_state'
	cli = ''
	run = None


	def localCommand(command):
		return commands.getoutput(command)

	
	def error(self, command):
		return 'FATAL ERROR: Something is wrong with your database. Try to reinitialize it.'


	def __init__(self, callback=localCommand):
		self.run = callback
		
		match = re.match(r".*?\\'DB_USER\\',\\'(?P<dbUser>.+?)\\'.*?\\'DB_PASS\\',\\'(?P<dbPass>.+?)\\'", self.run('cat passwords.php').encode('string-escape'))
		try:
			self.dbUser = match.groupdict()['dbUser']
			self.dbPass = match.groupdict()['dbPass']
			self.cli = 'psql -w -A -U %s ' % self.dbUser

			if self.checkConnection():
				## CAUTON: pg_stat_activity.procpid may change to pg_stat_activity.pid in postgres 9.2
				self.run('%s -d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catroboard\';"' % self.cli)
				self.run('%s -d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catroweb\';"' % self.cli)
				self.run('%s -d template1 -c "SELECT dbo.pg_kill_user_process(pg_stat_activity.procpid) FROM pg_stat_activity WHERE pg_stat_activity.datname = \'catrowiki\';"' % self.cli)
			else:
				self.run = self.error
		except:
			print('** ERROR ***********************************************************************')
			print("couldn't extract your database user, have you entered a wrong user or")
			print("is your 'passwords.php' missing?")
			self.run = self.error


	def checkConnection(self):
		if 'FATAL' in self.run('%s -d template1 -c "\l"' % self.cli):
			print('** ERROR ***********************************************************************')
			print("couldn't connect to database!!!")
			return False
		if 'dbo' not in self.run('%s -d template1 -c "SELECT nspname FROM pg_catalog.pg_namespace;"' % self.cli):
			print('** ERROR ***********************************************************************')
			print("couldn't find schema dbo!!!")
			return False
		return True


	def initDbs(self):
		for database in self.databases:
			if os.path.isdir(os.path.join(self.sqlPath, database)):
				self.createDb(database)
				self.executeSql(database)
			else:
				print("couldn't init database %s" % database)


	def purgeDbs(self):
		for database in self.databases:
			self.dropDb(database)


	def backupDbs(self):
		dumps = ''
		for database in self.databases:
			dumps += '%s-sql.tar.gz ' % database
			self.dumpDb(database)

		self.run('tar -cjf sql.tar %s' % dumps)
		self.run('rm %s' % dumps)


	def restoreDbs(self, backup):
		if 'No such file' not in self.run('ls ' + backup):
			self.run('tar -xvf %s' % backup)
			for database in string.split(self.run('ls *-sql.tar.gz'), '\n'):
				self.restoreDb(database.replace('-sql.tar.gz', ''))
				self.run('rm %s' % database)
		else:
			print('FATAL ERROR: Backup not found: %s' % backup)
			sys.exit(-1)


	def createDocs(self):
		autodoc = 'postgresql_autodoc'
		if 'not found' in commands.getoutput('cd /tmp; %s -h' % autodoc):
			print('** WARNING *********************************************************************')
			print('%s is not installed.' % autodoc)
			return
		
		if os.path.isdir(self.overviewPath):
			shutil.rmtree(self.overviewPath)
		os.mkdir(self.overviewPath)
		for database in self.databases:
			print('creating doc for database %s' % database)
			
			self.run('cd %s; ls -al; %s -h localhost -p 5432 -d %s -u %s --password=%s' % (self.overviewPath, autodoc, database, self.dbUser, self.dbPass))
			self.run('cd %s; dot -Tpng %s.dot > %s.png' % (self.overviewPath, database, database))
			
			for line in fileinput.FileInput(os.path.join(self.overviewPath, database + '.html'), inplace=1):
				if 'public' in line:
					line = line.replace('public</a></li><ul>', 'public</a></li><ul>\n	  <li><a href="%s.png" target="_blank">overview</a></li>' % database) 
				sys.stdout.write(line)

		for file in glob.glob(os.path.join(self.overviewPath, '*')):
			if '.dia' in file or '.dot' in file or '.neato' in file or '.xml' in file:
				os.remove(file)


	def createDb(self, database):
		if database not in self.run('%s -d template1 -c "SELECT datname FROM pg_database;"' % self.cli):
			result = self.run('%s -d template1 -c "CREATE DATABASE %s WITH ENCODING \'UTF8\';"' % (self.cli, database))
			if 'ERROR' in result:
				print("couldn't create database %s" % database)
			else:
				print("created database %s" % database)


	def executeSql(self, database):
		result = self.run('%s -d %s -c "CREATE TABLE IF NOT EXISTS %s (statement character varying(511));"' % (self.cli, database, self.stateTable))
		if not 'ERROR' in result:
			self.executeFiles(database, 'init')
			self.executeFiles(database, 'updates')


	def dropDb(self, database):
		result = self.run('%s -d template1 -c "DROP DATABASE IF EXISTS %s"' % (self.cli, database))
		if 'ERROR' in result:
			print("couldn't drop database %s" % database)
			print(result)
		else:
			print('dropped database %s' % database)


	def executeFiles(self, database, type):
		print(' %s:' % type)
		for sqlFile in sorted(glob.glob(os.path.join(self.sqlPath, database, type, '*.sql'))):
			basename = os.path.basename(sqlFile)
			sqlFile =  os.path.relpath(sqlFile, self.basePath)
			alreadyExecuted = self.run('%s -d %s -c "SELECT * FROM %s WHERE statement=\'%s%s\';"' % (self.cli, database, self.stateTable, type, basename))
			
			if '0 rows' in alreadyExecuted or 'does not exist' in alreadyExecuted:
				result = self.run('%s -d %s -f %s' % (self.cli, database, sqlFile))
				if 'ERROR' in result:
					print(' - error executing %s' % basename)
					print(result)
				else:
					self.run('%s -d %s -c "INSERT INTO %s VALUES (\'%s%s\');"' % (self.cli, database, self.stateTable, type, basename))
					print(' - executed %s' % basename)
			else:
				print(' - skipped %s' % basename)
		print('')


	def dumpDb(self, database):
		result = self.run('pg_dump -n public %s -U %s -c -Ft | gzip -c9 > %s-sql.tar.gz' % (database, self.dbUser, database))
		if 'ERROR' in result:
			print('error dumping %s' % database)
			print(result)
		else:
			print('dumped %s' % database)


	def restoreDb(self, database):
		if 'No such file' not in self.run('ls %s-sql.tar.gz' % database):
			self.dropDb(database)
			self.createDb(database)

			result = self.run('gzip -dc %s-sql.tar.gz | pg_restore --no-owner -d %s -U %s' % (database, database, self.dbUser))
			if 'ERROR' in result:
				print('error restoring %s' % database)
				print(result)
			else:
				print('restored data of %s' % database)
				print('')
		else:
			print('FATAL ERROR: Found no file to restore.')


	def getProjectList(self):
		result = self.run('%s -d catroweb -c "SELECT id FROM projects;"' % self.cli)
		
		ids = []
		for line in result.split('\n'):
			try:
				ids.append(int(line))
			except:
				pass
		return ids
	
	def getFeaturedProjectList(self):
		result = self.run('%s -d catroweb -c "SELECT project_id FROM featured_projects;"' % self.cli)
		
		ids = []
		for line in result.split('\n'):
			try:
				ids.append(int(line))
			except:
				pass
		return ids



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'init':
			Sql().initDbs()
			Sql().createDocs()
		elif sys.argv[1] == 'purge':
			Sql().purgeDbs()
		elif sys.argv[1] == 'backup':
			Sql().backupDbs()
		elif sys.argv[1] == 'restore':
			Sql().restoreDbs(sys.argv[2])
			Sql().createDocs()
		elif sys.argv[1] == 'docs':
			Sql().createDocs()
		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  init                  Initializes or updates the database.')
		print('  purge                 Purges all tables of the database.')
		print('  backup                Creates a backup of your current database.')
		print('  restore <BACKUP>      Restores the given backup file. Overwrites current')
		print('                        database.')
		print('  docs                  Creates a SQL database overview and documentation.')
