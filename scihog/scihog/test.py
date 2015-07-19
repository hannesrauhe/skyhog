from sci_template import *

t_dom = parseString("<body><div class=\"__skyhog_blog_article\" dir=\"articles\" number=\"5\" /><div> ting</div><div class=\"__skyhog_t\"> ting</div></body>")        

p_dom = t_dom.cloneNode(True)
matchingNodes = [node for node in p_dom.getElementsByTagName("div") if node.hasAttribute("class") and node.getAttribute("class").startswith("__skyhog")]           
for el in matchingNodes:
    parent = el.parentNode
    if "blog_article"==el.getAttribute("class")[9:]:
        artdom = sci_blog(el.attributes).generate()
        parent.replaceChild(artdom.childNodes[0],el)
print p_dom.toprettyxml()