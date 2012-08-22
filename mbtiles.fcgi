#!/usr/bin/python

import sqlite3

def myapp(environ, start_response):

    try:
        layer, z, x, y  = environ['PATH_INFO'].split('/')[1:]
        ext = y.split('.')[1]
        if (ext == 'jpg'):
		ext = 'jpeg'
        y = y.split('.')[0]
    except:
        start_response('200 OK', [('Content-Type', 'text/plain')])
        return ['MBTiles FastCGI server!\n'+str(environ)]

    try:
        db = sqlite3.connect("%s.mbtiles" % layer)
        c = db.cursor()
    except:
        start_response('404 Not found', [('Content-Type', 'text/plain')])
        return ['MBTiles FastCGI server!\n'+ "Not found: %s.mbtiles" % (layer,)]

    c.execute("select tile_data from tiles where tile_column=? and tile_row=? and zoom_level=?", (x, y, z))
    res = c.fetchone()
    if res:
        start_response('200 OK', [('Content-Type', "image/%s" % ext)])
        data = str(res[0])
        return [data]

if __name__ == '__main__':
    from flup.server.fcgi import WSGIServer
    WSGIServer(myapp).run()
