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
import os,shutil,time,re,logging,urlparse
from bs4 import *
from PyRSS2Gen import *
from iface_plugin import *
from yapsy.PluginManager import PluginManagerSingleton

class site_info(object):
    preview_dir = ''
    url = ''
    page_list = []      
    
    def __init__(self,dir,url):
        self.preview_dir = dir
        u = urlparse.urlparse(url)
        if not urlparse.urlparse(url).scheme:
            url = "http://"+url
            u = urlparse.urlparse(url)
        self.url = u.geturl()+"/"
        self._find_pages(dir)
           
    def template_name_condition(self,f):
        return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')

    def _find_pages(self, dir):
        try:
            file_list = [ os.path.join(dir,item) for item in os.listdir(dir) if self.template_name_condition(item)]
            for item in os.listdir(dir):
                if not item.startswith(".") and os.path.isdir(os.path.join(dir, item)):
                    self._find_pages(os.path.join(dir, item))
            for file in file_list:
                h = file.rsplit('/',1) 
                f = h[1] # filename
                d = h[0].replace(self.preview_dir[:-1],'',1) # remove the base directory output_dir from the path
                self.page_list.append((d,f,f[1:]))
        except:
            logging.getLogger('scihog.generate').error("_find_pages throw an error on %s"%dir)
        return self.page_list

class creator(object):
    '''
    classdocs
    '''
    templ_path = ""        
    templ = []  
    output_dir = ''
    input_dir = ''
    page_dir = ''
    _site = ''
        
    #TODO: find a way to determine this modules path
    plugin_dirs = [os.path.abspath(os.path.dirname(sys.argv[0]))+"/scihog/plugins"]
    _pm = None
    
    def __init__(self,t,page_directory,site_url):
        '''
        Constructor
        '''
        self.page_dir = page_directory+'/'
        self.output_dir = os.path.dirname(t)+'/'
        self.input_dir = self.output_dir
        self.db_dir = self.input_dir

        self.templ_path = t
           
        self._site = site_info(self.input_dir,url=site_url)
        
        self._pm = PluginManagerSingleton.get()
        self._pm.setPluginPlaces(self.plugin_dirs)
        self._pm.setCategoriesFilter({
           "unknown" : iface_unknown_plugin,
           "generate" : iface_generate_plugin
           })
        self._pm.collectPlugins()
            
    def generate(self):
        logger = logging.getLogger('scihog.generate')
        
        #this is now done for every page found
        for d,src_file,target_file in self._site.page_list: 
            p_dom = BeautifulSoup(open(self.templ_path,"r").read().strip())    

            pluged_in = {}
            
            while True:
                el = p_dom.find("div",{"class":re.compile("__skyhog")})
                if None==el:
                    break;
                for c in el["class"]:
                    if c.startswith("__skyhog"):
                        plugin_name = el["class"][0][9:]
                        
                if not pluged_in.has_key(plugin_name):
                    plugin_object = self._pm.activatePluginByName(plugin_name,"generate")
                    logger.debug("loading plugin with name %s: %s",plugin_name,str(plugin_object))
                    if plugin_object:
                        pluged_in[plugin_name] = plugin_object
                        pluged_in[plugin_name].init(self.input_dir+'/'+d,src_file,self.output_dir+'/'+d,target_file,p_dom,self._site)
                    else:
                        logger.error("removed unknown element of class %s",el["class"])
                        el.extract()
                        continue
                try:
                    attr = el["class"]
                    attr.remove("__skyhog_"+plugin_name)
                    artdom = pluged_in[plugin_name].generate(attr)
                    el.replace_with(artdom)
                except :
                    logger.error("generating code for plugin %s with parameters %s not successful, removing class",plugin_name,el["class"])
                    logger.error(sys.exc_info())
                    el.extract()
#                print "___________________"
#                print p_dom.prettify()
#                print "____________________________!"
            new_file = open(self.output_dir+'/'+d+'/'+target_file,'w')
            #I hope, this outputs UTF-8 in every case...
            new_file.write(str(p_dom))
            logger.info("generated "+d+'/'+target_file)
            
        
        #the following is done once in every generation step
        p_dom = BeautifulSoup(open(self.templ_path,"r").read().strip())    
        plugin_name = "sitemap"
        plugin_object = self._pm.activatePluginByName(plugin_name,"generate")
        logger.debug("loading plugin with name %s: %s",plugin_name,str(plugin_object))
        if plugin_object:
            plugin_object.init(self.input_dir,"",self.output_dir,"",p_dom,self._site)
            plugin_object.generate_once([item[0]+item[2] for item in self._site.page_list])
            
    def move_to_page_dir(self,bak_dir):
        if os.path.isdir(self.page_dir):
            if not os.path.isdir(bak_dir):
                os.mkdir(bak_dir)
            shutil.move(self.page_dir, bak_dir+"/"+str(time.time()))
        print self.output_dir,self.page_dir
        shutil.copytree(self.output_dir, self.page_dir, True, shutil.ignore_patterns('_*.html','.git'))
        
    def list_available_plugins(self):
        print "looking in",self.plugin_dirs
        for p in self._pm.getAllPlugins():
            print p.name,p.plugin_object
        
