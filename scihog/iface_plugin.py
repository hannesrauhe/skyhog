from yapsy.IPlugin import IPlugin
import types

class iface_plugin(IPlugin):
    def activate(self):
        """
        Call the parent class's acivation method
        """
        IPlugin.activate(self)
        return


    def deactivate(self):
        """
        Just call the parent class's method
        """
        IPlugin.deactivate(self)
        
class iface_unknown_plugin(iface_plugin):     
    def activate(self):
        """
        Call the parent class's acivation method
        """
        iface_plugin.activate(self)
        return


    def deactivate(self):
        """
        Just call the parent class's method
        """
        iface_plugin.deactivate(self)
        
class iface_generate_plugin(iface_plugin):        
    options = None
    option_widgets = {}
    _site = ''
    
    def activate(self):
        """
        Call the parent class's acivation method
        """
        iface_plugin.activate(self)
        return


    def deactivate(self):
        """
        Just call the parent class's method
        """
        iface_plugin.deactivate(self)
        
    def init(self,idir,ifile_name,odir,ofile_name,t_dom,site):
        self._site = site
        #TODO(Hannes): safe/get every site-info in/from site-object
        self.idir = idir
        self.ifile_name = ifile_name
        self.odir = odir
        self.ofile_name = ofile_name
        self.t_dom = t_dom
        self.db_dir = site.preview_dir
        self.init2()
        
    def init2(self):
        pass
    
    def generate(self,attr):
        raise NotImplementedError("generate method of plugin not implemented")
        
    def read_options_from_file(self):
        if not self.options:
            return
        for o,v in self.options.iteritems():
            if self.hasConfigOption(o):
                new_v = self.getConfigOption(o)
                try:
                    if type(v)==types.IntType:
                        self.options[o] = int(new_v)
                    elif type(v)==types.BooleanType:
                        self.options[o] = bool(new_v)
                    elif type(v)==types.StringType:
                        self.options[o] = new_v
                    else:
                        print "type of value",o,v,"not supported, using default"
                except:
                    print "could not convert value of",o,"from config to type",type(v),"(",new_v,") using default"
