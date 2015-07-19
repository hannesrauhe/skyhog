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
import os,datetime,logging,urlparse
import sqlite3 
from bs4 import *
from scihog.iface_plugin import *

class sci_sitemap(iface_generate_plugin):
    p_dom = None
    
    def _create_entry(self,page,priority=None):
        #this is to get rid of double slashes in the url:
        u = urlparse.urljoin(self._site.url, urlparse.urlparse(self._site.url+page).path.replace('//','/'))
        if priority:
            return "<url><loc>"+u+"</loc><priority>"+str(priority)+"</priority></url>"
        else:
            return "<url><loc>"+u+"</loc></url>"
            
        
    def init2(self):
        pass

    def generate(self,attr):
        pass
    
    def generate_once(self,pages_list):
        connection = sqlite3.connect(self.db_dir+"/scihog.db")
        cursor = connection.cursor()
        cursor.execute("SELECT file,link FROM nav WHERE menu_order>=0 ORDER BY menu_order")
        nav_files = [item[0] if item[0]!=None else item[1] for item in cursor.fetchall()]
        
        xml_text = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"
        #pages from navigation get a priority depending on there positon
        priority=0.9
        if len(nav_files):
          priority_step = 0.5/len(nav_files) 
          for p in nav_files:
            xml_text += self._create_entry(p,priority)
            try:
                pages_list.remove(p)
            except:
                pass
            priority-=priority_step
            
        #everything else which was generated has no priority
        for p in pages_list:  
            xml_text += self._create_entry(p)
        xml_text+="</urlset>"
        
        
        logger = logging.getLogger('scihog.generate')        
        try:
            new_file = open(self.odir+"sitemap.xml",'w')
            #I hope, this outputs UTF-8 in every case...
            new_file.write(xml_text)
            logger.info("sitemap created")
        except:
            logger.error("could not write sitemap")
