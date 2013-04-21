'''
Copyright 2012 Hannes Rauhe

This file is part of Skyhog.

Skyhog is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
    
Skyhog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Skyhog.  If not, see <http://www.gnu.org/licenses/>.
'''
from bs4 import *
from scihog.iface_plugin import iface_plugin
     
class sci_page(iface_plugin):
    def generate(self,attr):
        self.p_dom = BeautifulSoup(open(self.idir+"/"+self.ifile_name,"r").read())
        return self.p_dom.contents[0]