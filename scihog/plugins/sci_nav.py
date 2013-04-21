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
import sqlite3 
from bs4 import *
from scihog.iface_plugin import *

class sci_nav(iface_generate_plugin):
    def generate(self,attr):
        connection = sqlite3.connect(self.idir+"/scihog.db")
        cursor = connection.cursor()
        cursor.execute("SELECT id,link,name FROM nav WHERE menu_order>=0 ORDER BY menu_order")
        code = []
        code.append('<nav>\n')
        code.append('<ul>\n')
        for entry in cursor.fetchall():
            if self.ofile_name==entry[1]:
                code.append('<li id="%s" class="m_active"><a href="%s">%s</a></li>\n' % entry[:3])
            else:
                code.append('<li id="%s"><a href="%s">%s</a></li>\n' % entry[:3])
        code.append('</ul>\n')
        code.append('</nav>\n')
        self.p_dom = BeautifulSoup("".join(code))
        return self.p_dom.nav