from yapsy.IPlugin import IPlugin
import types

class iface_plugin(IPlugin):
    options = None
    option_widgets = {}
    
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
        
    def init(self,idir,ifile_name,odir,ofile_name,t_dom,db_dir):
        self.idir = idir
        self.ifile_name = ifile_name
        self.odir = odir
        self.ofile_name = ofile_name
        self.t_dom = t_dom
        self.db_dir = db_dir
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
