import os
import subprocess
import time

class CowBuilder():
    
    def __init__(self, dist, arch, logfile, buildresult):
        
        self.arch = arch
        self.dist = dist
        self.logfile = logfile
        self.buildresult = buildresult

    def update(self):
        
        update_command = ["/usr/sbin/cowbuilder", "--update"]
        update_command.extend(["--logfile", self.logfile + ".update"])
        update_command.extend(["--override-config"])
        
        return self.execute(update_command)

    def build(self, dsc):
        
        build_command = ["/usr/sbin/cowbuilder", "--build", dsc]
        build_command.extend(["--logfile", self.logfile])
        build_command.extend(["--buildresult", self.buildresult])
        
        return self.execute(build_command)

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
