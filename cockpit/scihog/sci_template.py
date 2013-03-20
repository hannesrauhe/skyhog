from xml.dom.minidom import parse,parseString

class sci_blog(object):
    options = {"number":5,"dir":"article"}
    attributes = []
    content_file = ""
    def __init__(self,content_file,attr):
        self.attributes = attr

    def generate(self):
        return parseString("<article>blog</article>")

class sci_page(object):
    p_file = None
    p_dom = None
    def __init__(self,p_file):
        self.p_file = p_file
#        self.p_dom = parse(p_file)
        self.p_dom = parseString("<p>Im a static page</p>")
        
    def generate_output(self):
        for el in matchingNodes:
            parent = el.parentNode
            if "blog_article"==el.getAttribute("class")[9:]:
                artdom = sci_blog(el.attributes).generate()
                parent.replaceChild(artdom.childNodes[0],el)
        print self.p_dom.toxml()
        