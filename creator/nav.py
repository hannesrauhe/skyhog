def generate(content_name):
    import sqlite3 
    connection = sqlite3.connect("creator.db")
    cursor = connection.cursor()
    cursor.execute("SELECT * FROM nav ORDER BY menu_order")
    code = []
    code.append('<nav>\n')
    code.append('<ul>\n')
    for entry in cursor.fetchall():
        if content_name==entry[0]:
            code.append('<a href="%s"><li id="%s" class="m_active">%s</li></a>\n' % entry[:3])
        else:
            code.append('<a href="%s"><li id="%s">%s</li></a>\n' % entry[:3])
    code.append('</ul>\n')
    code.append('</nav>\n')
    return code