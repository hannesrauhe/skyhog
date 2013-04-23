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
import os,shutil,time,re,logging
from bs4 import *
from PyRSS2Gen import *
from iface_plugin import *
from yapsy.PluginManager import PluginManagerSingleton


class creator(object):
    '''
    classdocs
    '''
    templ_path = ""        
    templ = []  
    template_files = []
    output_dir = ''
    input_dir = ''
    page_dir = ''
    
    #TODO: find a way to determine this modules path
    plugin_dirs = [os.path.abspath(os.path.dirname(sys.argv[0]))+"/scihog/plugins"]
    _pm = None
    
    def __init__(self,t,page_directory):
        '''
        Constructor
        '''
        self.page_dir = page_directory+'/'
        self.output_dir = os.path.dirname(t)+'/'
        self.input_dir = self.output_dir
        self.db_dir = self.input_dir

        self.templ_path = t
        template_file = open(t, 'r')
        self.templ = template_file.readlines()
        #self.template_files = [item for item in os.listdir(self.output_dir) if self.template_name_condition(item)]
        self.template_files = self.find_files(self.output_dir)
#        print self.template_files
        
        self._pm = PluginManagerSingleton.get()
        self._pm.setPluginPlaces(self.plugin_dirs)
        self._pm.setCategoriesFilter({
           "unknown" : iface_unknown_plugin,
           "generate" : iface_generate_plugin
           })
        self._pm.collectPlugins()
#        self.list_available_plugins()

    def find_files(self, dir):
        file_list = [ os.path.join(dir,item) for item in os.listdir(dir) if self.template_name_condition(item)]
        for file in [ item for item in os.listdir(dir) if os.path.isdir(os.path.join(dir, item))]:
            file_list.extend(self.find_files(os.path.join(dir, file)))
        return file_list
        
    def template_name_condition(self,f):
        return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')
            
    def generate(self):
        logger = logging.getLogger('scihog.generate')
        for f in self.template_files: 
            p_dom = BeautifulSoup(open(self.templ_path,"r").read().strip())    
            #content_file_path = self.input_dir+f
            pluged_in = {}
 
            h = f.rsplit('/',1) 
            f = h[1] # filename
            d = h[0].replace(self.output_dir[:-1],'',1) # remove the base directory output_dir from the path
            while True:
                el = p_dom.find("div",{"class":re.compile("__skyhog")})
                if None==el:
                    break;
                for c in el["class"]:
                    if c.startswith("__skyhog"):
                        plugin_name = el["class"][0][9:]
                        
                if not pluged_in.has_key(plugin_name):
#                    if "static_page"==plugin_name:
#                        pluged_in[plugin_name] = sci_page(self.input_dir+'/'+d,f,self.output_dir+'/'+d,f[1:],p_dom,self.db_dir)
#                    if "blog"==el["class"][0][9:]:
#                        pluged_in[plugin_name] = sci_blog(self.input_dir+'/'+d,f,self.output_dir+'/'+d,f[1:],p_dom,self.db_dir)
#                    elif "nav"==el["class"][0][9:]:
#                        pluged_in[plugin_name] = sci_nav(self.input_dir+'/'+d,f,self.output_dir+'/'+d,f[1:],p_dom,self.db_dir)
#                    else:
                    plugin_object = self._pm.activatePluginByName(plugin_name,"generate")
                    logger.debug("loading plugin with name %s: %s",plugin_name,str(plugin_object))
                    if plugin_object:
                        pluged_in[plugin_name] = plugin_object
                        pluged_in[plugin_name].init(self.input_dir+'/'+d,f,self.output_dir+'/'+d,f[1:],p_dom,self.db_dir)
                    else:
                        logger.error("removed unknown element of class %s",el["class"])
                        el.extract()
                        continue
                try:
                    artdom = pluged_in[plugin_name].generate(el["class"])
                    el.replace_with(artdom)
                except :
                    logger.error("generating code for plugin %s with parameters %s not successful, removing class",plugin_name,el["class"])
                    logger.error(sys.exc_info())
                    el.extract()
#                print "___________________"
#                print p_dom.prettify()
#                print "____________________________!"
            new_file = open(self.output_dir+'/'+d+'/'+f[1:],'w')
            new_file.write(p_dom.prettify().encode("UTF-8"))
            print "generated",d+'/'+f[1:]
            
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
        
