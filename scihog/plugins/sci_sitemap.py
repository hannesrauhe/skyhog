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
import os,datetime
from bs4 import *
from scihog.iface_plugin import *

class sci_sitemap(iface_generate_plugin):
    p_dom = None
        
    def init2(self):

    def generate(self,attr):
        rssl = self.p_dom.new_tag("link", rel="alternate", type="application/rss+xml", title="HTML-Feed", href="rss.xml")

        return self.p_dom.div