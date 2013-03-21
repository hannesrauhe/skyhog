import os
from bs4 import *

class sci_interface(object):
    def __init__(self,idir,ifile_name,odir,ofile_name,attr):
        self.idir = idir
        self.ifile_name = ifile_name
        self.odir = odir
        self.ofile_name = ofile_name
        self.attr = attr

    def generate(self):
        pass
    
class sci_nav(sci_interface):
    def generate(self):
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

    def generate(self):
        articles_dir = self.idir+"/"+self.options["dir"]+"/"
        if not os.path.isdir(articles_dir):
            print "ERROR:",articles_dir,"with articles not found"
            return parseString("<article>ERROR: Dir with articles not found</article>")
        
        self.p_dom = BeautifulSoup("<div class=\"blog\"></div>")
        article_files = [f for f in os.listdir(articles_dir) if f.startswith(self.options["prefix"])]
#        article_files.reverse()
        for a in article_files:
            self.p_dom.div.append(BeautifulSoup(open(articles_dir+a,"r").read()).body.contents[0])
#        print self.p_dom.body.contents[0]
        return self.p_dom.div
        

class sci_page(sci_interface):        
    def generate(self):
        self.p_dom = BeautifulSoup(open(self.idir+"/"+self.ifile_name,"r").read())
        if None==self.p_dom.body:
            return self.p_dom
        else:
            return self.p_dom.body.contents[0]
        
