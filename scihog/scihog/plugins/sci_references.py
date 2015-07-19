'''
Copyright 2012,2013 Hannes Rauhe

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
import re
from PyRSS2Gen import *
from bs4 import *
from scihog.iface_plugin import *

class sci_references(iface_generate_plugin):
    options = {}
    p_dom = None

    def generate(self,attr):
        self.p_dom = BeautifulSoup("<h3>References</h3><ol></ol>")
        for a in self.t_dom.find_all('a',{"class":re.compile("link-ref")}):
            entry = None
            if a.attrs.has_key('alt'):
                entry = BeautifulSoup('<li><a href="%s">%s</a></li>'%(a.attrs["href"],a.attrs["alt"]))
            else:
                entry = BeautifulSoup("<li>%s</li>"%str(a))
            self.p_dom.ol.append(entry)
        return self.p_dom