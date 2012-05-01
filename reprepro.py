# copyright 2012 Stefano Karapetsas <stefano@karapetsas.com>

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

import functions
import os

class Reprepro():
    
    def __init__(self, instance_path, dist, archs):
        
        self.instance_path = instance_path
        self.dist = dist
        self.archs = archs
        self.name = functions.distro_name(dist)

    def create_config(self):
        
        reprepro_path = os.path.join(self.instance_path, "repository", self.name)
        reprepro_path_conf = os.path.join(reprepro_path, "conf")
        if not os.path.exists(reprepro_path_conf):
            os.makedirs(reprepro_path_conf)
        
        config_file = open(os.path.join(reprepro_path_conf, "distributions"), "a")
        config_file.write("Codename: %s\n" % self.dist)
        config_file.write("Components: main\n")
        config_file.write("UDebComponents: main\n")
        config_file.write("Architectures: %s source\n" % " ".join(self.archs))
        config_file.write("SignWith: yes\n")
        config_file.write("DscIndices: Sources Release . .gz .bz2\n")
        config_file.write("DebIndices: Packages Release . .gz .bz2\n")
        config_file.write("\n")
        config_file.close()
        
        os.system("reprepro --basedir %s export" % reprepro_path)
        
        os.system("chmod 777 -R %s/" % reprepro_path)

    def include_packages(self, build_dir):
        
        reprepro_path = os.path.join(self.instance_path, "repository", self.name)
        
        # add source
        os.system("reprepro --basedir %(basedir)s includedsc %(dist) %(dsc)" % \
            {
                "basedir": reprepro_path,
                "dist": self.dist,
                "dsc": os.path.join(build_dir, "source", "*.dsc")
            })
        
        # add architecture "all" packages from first arch
        os.system("reprepro --basedir %(basedir)s includedeb %(dist) %(deb)" % \
            {
                "basedir": reprepro_path,
                "dist": self.dist,
                "deb": os.path.join(build_dir, "result", self.dist, self.archs[0], "*all.deb")
            })
        
        # add other arch-specific packages
        for arch in self.archs:
            os.system("reprepro --basedir %(basedir)s includedeb %(dist) %(deb)" % \
            {
                "basedir": reprepro_path,
                "dist": self.dist,
                "deb": os.path.join(build_dir, "result", self.dist, self.archs[0], "*" + arch + ".deb")
            })

