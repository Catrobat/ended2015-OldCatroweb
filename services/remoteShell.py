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

import paramiko

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class RemoteShell:
	ssh								= None
	sftp							= None
	remoteDir					= ''
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, server, user, password, port=22, remoteDir='/var/www/catroid'):
		try:
			self.remoteDir = remoteDir
			self.ssh = paramiko.SSHClient()
			self.ssh.load_system_host_keys()
			self.ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
			self.ssh.connect(server, port, user, password)
			self.sftp = paramiko.SFTPClient.from_transport(self.ssh.get_transport())
		except Exception, e:
			print "ERROR: Couldn't connect to " + user + "@" + server + " on port " + str(port) + "."
			print 'Exception: %s' % e
			sys.exit(-1)

	#--------------------------------------------------------------------------------------------------------------------
	def run(self, command):
		(stdin, stdout, stderr) = self.ssh.exec_command('cd ' + self.remoteDir + '; ' + command)
		error = stderr.readlines()
		if len(error) > 0:
			print '** Output **********************************************************************'
			print ''.join(error)
			return ''.join(stdout.readlines() + error).strip()
		return ''.join(stdout.readlines()).strip()

