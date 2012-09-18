def generate(generator_class,content_name):
    import sqlite3 
    connection = sqlite3.connect(generator_class.output_dir+"/scihog.db")
    cursor = connection.cursor()
    cursor.execute("SELECT id,link,name FROM nav WHERE menu_order>=0 ORDER BY menu_order")
    code = []
    code.append('<nav>\n')
    code.append('<ul>\n')
    for entry in cursor.fetchall():
        if content_name==entry[0]:
            code.append('<li id="%s" class="m_active"><a href="%s">%s</a></li>\n' % entry[:3])
        else:
            code.append('<li id="%s"><a href="%s">%s</a></li>\n' % entry[:3])
    code.append('</ul>\n')
    code.append('</nav>\n')
    return code
