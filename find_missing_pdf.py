#!/usr/bin/env python
import os
import MySQLdb as mysql

def main():
    conn = mysql.connect(host='localhost',user='wangliang',passwd='gameover',db='ref')
    cursor = conn.cursor()
    sql = 'select id, adscode,journal2, year,volume, page from paper order by id'
    n = cursor.execute(sql)
    res = cursor.fetchall()
    rid_lst = []
    for rec in res:
        rid      = rec[0]
        rid_lst.append(rid)
        adscode  = rec[1]
        journal2 = rec[2]
        jour = journal2.replace('&','')
        year     = rec[3]
        volume   = rec[4]
        page     = rec[5]
        code = '%d/%s/%s/%s'%(year,jour,volume,page)
        filepath = '%04d'%rid
        filepath = filepath[0:2]
        filepath = str(int(filepath))

        find = False

        # if arxiv
        if adscode[4:12]=='astro.ph' or adscode[4:9]=='arXiv':
            if os.path.exists('./fulltext/arxiv/%4d/%s.pdf'%(year,adscode)):
                find = True
        if adscode[9:13] in ['conf','work']:
            jour = adscode[4:9].replace('.','')
            if os.path.exists('./fulltext/conference/%s/%s.pdf'%(jour,adscode)):
                find = True
        elif adscode[9:13]=='book':
            if os.path.exists('./fulltext/book/%s.pdf'%adscode):
                find = True
        # scan the normal folders
        for kind in ['journal','conference','bulletin']:
            if os.path.exists('./fulltext/%s/%s/%s/%s.pdf'%(kind,jour,volume,adscode)):
                find = True
                break
            if os.path.exists('./fulltext/%s/%s/%s.pdf'%(kind,jour,adscode)):
                find = True
                break
        # scan the old files
        if os.path.exists('/var/www/ref/fulltext2/%s/%d.pdf'%(filepath,rid)):
            find = True
            
        if not find:
            print 'http://127.0.0.1/ref/ref-%d (%s) does not exist'%(rid, journal2)
        #if journal2 == 'A&A' and page[0]=='A':
        #    print 'http://127.0.0.1/ref/ref-%d, %s'%(rid, new_file)
    
    for rid in range(1,max(rid_lst)):
        if rid not in rid_lst:
            print 'Void rid: %4d'%rid


def plot_year():
    conn = mysql.connect(host='localhost',user='wangliang',passwd='gameover',db='ref')
    cursor = conn.cursor()
    year_lst,count_lst = [],[]
    sql = 'select year,count(id) from paper group by year order by year'
    n = cursor.execute(sql)
    res = cursor.fetchall()
    for rec in res:
        year_lst.append(rec[0])
        count_lst.append(rec[1])
    fig = plt.figure()
    ax = fig.gca()
    ax.step(year_lst, count_lst)
    plt.show()


if __name__=='__main__':
    main()
    #plot_year()
