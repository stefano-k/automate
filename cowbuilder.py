# copyright 2011-2012 Stefano Karapetsas <stefano@karapetsas.com>

# This file is part of AutoMate.
#
# AutoMate is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# AutoMate is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with AutoMate.  If not, see <http://www.gnu.org/licenses/>.


import os
import subprocess
import time

class CowBuilder():
    
    def __init__(self, dist, arch, configfile, logfile, buildresult):
        
        self.dist = dist
        self.arch = arch
        self.configfile = configfile
        self.logfile = logfile
        self.buildresult = buildresult

    def create(self):
        
        command = ["/usr/sbin/cowbuilder", "--create"]
        command.extend(["--configfile", self.configfile])
        
        return self.execute(command)

    def update(self):
        
        command = ["/usr/sbin/cowbuilder", "--update"]
        command.extend(["--configfile", self.configfile])
        command.extend(["--logfile", self.logfile + ".update"])
        command.extend(["--override-config"])
        
        return self.execute(command)

    def build(self, dsc):
        
        command = ["/usr/sbin/cowbuilder", "--build", dsc]
        command.extend(["--configfile", self.configfile])
        command.extend(["--logfile", self.logfile])
        command.extend(["--buildresult", self.buildresult])
        
        return self.execute(command)

    def execute(self, command):
        
        os.environ["DIST"] = self.dist
        os.environ["ARCH"] = self.arch
        #print "    dist:", os.environ["DIST"]
        #print "    arch:", os.environ["ARCH"]
        #print "    " + " ".join(command)
        DEVNULL = open('/dev/null', 'w')
        p = subprocess.Popen(command, shell=False, stdout=DEVNULL, stderr=DEVNULL)
        while p.returncode is None:
            p.poll()
            time.sleep(1)
        #log = p.stdout.read()
        DEVNULL.close()
        return p.returncode
