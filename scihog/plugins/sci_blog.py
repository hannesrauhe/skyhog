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
import os,datetime
from PyRSS2Gen import *
from bs4 import *
from scihog.iface_plugin import *

class sci_blog(iface_generate_plugin):
    options = {"number":5,"dir":"./","prefix":"_article"}
    p_dom = None
    rss = None
    
    def _createRSS(self):
        self.rss = RSS2(
            title = self.t_dom.title.get_text(), 
            link = self._site.url,
            description = "",       
            lastBuildDate = datetime.datetime.now()
        )
        
    def _createOverviewItem(self):
        pass
        
    def init2(self):
        articles_dir = self.idir+"/"+self.options["dir"]+"/"
        if not os.path.isdir(articles_dir):
            print "ERROR:",articles_dir,"with articles not found"
            return parseString("<article>ERROR: Dir with articles not found</article>")
        
        self.p_dom = BeautifulSoup("<div class=\"blog\"></div>")
        self.list_dom = BeautifulSoup("<ul class=\"blog_list\"></ol>").ul
        self._createRSS()
        article_files = [f for f in os.listdir(articles_dir) if f.startswith(self.options["prefix"])]
        article_files.sort(reverse=True)
        for a in article_files:
            article_dom = BeautifulSoup(open(articles_dir+a,"r").read())
            a_title = article_dom.h2.get_text().strip()
            a_short = article_dom.find("div",{"class":"short"}).get_text()
            a_date = article_dom.find("div",{"class":"date"}).get_text().strip()
            a_author = "Hannes Rauhe"# article_dom.find("div",{"class":"author"}).get_text()
            
            l_entry = article_dom.new_tag("li")
            l_link = article_dom.new_tag("a",href=a[1:])
            l_link.append(a_title)
            l_entry.insert(1,l_link)
            self.p_dom.div.append(article_dom.contents[0])
            self.list_dom.append(l_entry)
            self.rss.items.append(
                RSSItem(
                 author = a_author,
                 title = a_title,
                 link = a[1:],
                 description = a_short,
                 pubDate = datetime.datetime.strptime(a_date,"%Y-%m-%d"))
            )

    def generate(self,attr):
        if "blog_list" in attr:
            return self.list_dom
        elif "rss_link" in attr:
            self.rss.write_xml(open(self.odir+"/rss.xml", "w"))
            rssl = self.p_dom.new_tag("link", rel="alternate", type="application/rss+xml", title="HTML-Feed", href="rss.xml")
            return rssl
        return self.p_dom.div