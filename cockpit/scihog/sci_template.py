from xml.dom.minidom import parse,parseString

class sci_blog(object):
    options = {"number":5,"dir":"article"}
    attributes = []
    def __init__(self,attr):
        self.attributes = attr
        print attr
    def generate(self):
        return parseString("<article>blog</article>")

class sci_page(object):
    p_file = None
    p_dom = None
    def __init__(self,p_file):
        self.p_file = p_file
#        self.p_dom = parse(p_file)
        self.p_dom = parseString("<body><div class=\"__skyhog_blog_article\" dir=\"articles\" number=\"5\" /><div> ting</div><div class=\"__skyhog_t\"> ting</div></body>")
        
    def generate_output(self):
        matchingNodes = [node for node in self.p_dom.getElementsByTagName("div") if node.hasAttribute("class") and node.getAttribute("class").startswith("__skyhog")]
        for el in matchingNodes:
            parent = el.parentNode
            if "blog_article"==el.getAttribute("class")[9:]:
                artdom = sci_blog(el.attributes).generate()
                parent.replaceChild(artdom.childNodes[0],el)
        print self.p_dom.toxml()
        
sci_page("daeng").generate_output()