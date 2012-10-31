#!/usr/bin/env python

import commands, sys

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class DeadlinkChecker:

	#--------------------------------------------------------------------------------------------------------------------	
	def check(self):
		result = commands.getoutput('linkchecker --ignore-url=^mailto: --ignore-url=^javascript: http://catroid.local')
		if 'error found' in result:
			print result
			sys.exit(1)
		else:
			sys.exit(0)

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	DeadlinkChecker().check()
