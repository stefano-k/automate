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
