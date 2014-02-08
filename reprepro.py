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
import glob
import os

class Reprepro():
    
    def __init__(self, instance_path, dist, archs, config):
        
        self.instance_path = instance_path
        self.dist = dist
        self.archs = archs
        self.config = config
        self.name = functions.distro_name(dist)

    def config_exists(self):
        reprepro_path = os.path.join(self.instance_path, "repository", self.name)
        reprepro_path_conf_file = os.path.join(reprepro_path, "db", "packages.db")
        return os.path.exists(reprepro_path_conf_file)

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
        if self.config.as_bool("gpg_sign"):
            config_file.write("SignWith: yes\n")
        config_file.write("DscIndices: Sources Release . .gz .bz2\n")
        config_file.write("DebIndices: Packages Release . .gz .bz2\n")
        config_file.write("\n")
        config_file.close()
        
        os.system("reprepro --basedir %s export" % reprepro_path)
        
        os.system("chmod 777 -R %s/" % reprepro_path)

    def include_packages(self, build_dir):
        
        reprepro_path = os.path.join(self.instance_path, "repository", self.name)
        import_log_file = os.path.join(build_dir, "import.log")
        
        os.system("date -R >> %(logfile)s" % {"logfile": import_log_file})
        
        # add source
        os.system("reprepro -V --basedir %(basedir)s -C main includedsc %(dist)s %(dsc)s >> %(logfile)s" % \
            {
                "basedir": reprepro_path,
                "dist": self.dist,
                "dsc": os.path.join(build_dir, "source", "*.dsc"),
                "logfile": import_log_file
            })
        
        # add architecture "all" packages from first arch
        all_debs = os.path.join(build_dir, "result", self.dist, self.archs[0], "*all.deb")
        if len(glob.glob(all_debs)) > 0:
            os.system("reprepro -V --basedir %(basedir)s -C main includedeb %(dist)s %(deb)s >> %(logfile)s" % \
                {
                    "basedir": reprepro_path,
                    "dist": self.dist,
                    "deb": all_debs,
                    "logfile": import_log_file
                })
        
        # add other arch-specific packages
        for arch in self.archs:
            arch_debs = os.path.join(build_dir, "result", self.dist, arch, "*" + arch + ".deb")
            if len(glob.glob(arch_debs)) > 0:
                os.system("reprepro -V --basedir %(basedir)s -C main includedeb %(dist)s %(deb)s >> %(logfile)s" % \
                {
                    "basedir": reprepro_path,
                    "dist": self.dist,
                    "deb": arch_debs,
                    "logfile": import_log_file
                })

