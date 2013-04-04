import os,datetime
from bs4 import *
from PyRSS2Gen import *

class sci_interface(object):
    def __init__(self,idir,ifile_name,odir,ofile_name,t_dom):
        self.idir = idir
        self.ifile_name = ifile_name
        self.odir = odir
        self.ofile_name = ofile_name
        self.t_dom = t_dom
        self.init()

    def init(self):
        pass
    
    def generate(self,attr):
        pass
    
class sci_nav(sci_interface):
    def generate(self,attr):
        import sqlite3 
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
        
class sci_blog(sci_interface):
    options = {"number":5,"dir":"./","prefix":"_article"}
    p_dom = None
    rss = None
    
    def createRSS(self):
        self.rss = RSS2(
            title = self.t_dom.title.get_text(), 
            link = "notapaper.de",
            description = "",       
            lastBuildDate = datetime.datetime.now()
        )
        
    def init(self):
        articles_dir = self.idir+"/"+self.options["dir"]+"/"
        if not os.path.isdir(articles_dir):
            print "ERROR:",articles_dir,"with articles not found"
            return parseString("<article>ERROR: Dir with articles not found</article>")
        
        self.p_dom = BeautifulSoup("<div class=\"blog\"></div>")
        self.list_dom = BeautifulSoup("<ul class=\"blog_list\"></ol>").ul
        self.createRSS()
        article_files = [f for f in os.listdir(articles_dir) if f.startswith(self.options["prefix"])]
        article_files.sort(reverse=True)
        for a in article_files:
            article_dom = BeautifulSoup(open(articles_dir+a,"r").read())
            a_title = article_dom.h2.get_text().strip()
            a_short = article_dom.find("div",{"class":"short"}).get_text()
            a_date = article_dom.find("div",{"class":"date"}).get_text().strip()
            
            l_entry = article_dom.new_tag("li")
            l_link = article_dom.new_tag("a",href=a[1:])
            l_link.append(a_title)
            l_entry.insert(1,l_link)
            self.p_dom.div.append(article_dom.contents[0])
            self.list_dom.append(l_entry)
            self.rss.items.append(
                RSSItem(
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
        

class sci_page(sci_interface):        
    def generate(self,attr):
        self.p_dom = BeautifulSoup(open(self.idir+"/"+self.ifile_name,"r").read())
        return self.p_dom.contents[0]
        
