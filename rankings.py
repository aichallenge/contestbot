#!/usr/bin/env python
# -*- coding: utf-8 -*-
from urllib2 import urlopen

def rankingsGetter(url="http://ai-contest.com/rankings.php"):
	html=urlopen(url).read()

	html=html[html.find("<tbody>")+7:]
	html=html[:html.find("</tbody>")]

	html=html.split("</tr>")
	html=map(lambda x: x.split("</td>"),html)

	def cleanuptr(tr):
		def cleanuptd(td):
			td=td.split("\n")[-1]
			td=td.strip()
			td=td[4:]
			if td.startswith("<"):
				td=td[1:]
			return td
		tr=map(cleanuptd, tr)
		tr=filter(lambda x: x!="",tr)
		#return tr
		if len(tr)<5:
			return []
		return [
			tr[0],
			tr[1][tr[1].find(">")+1:tr[1].find("<")],
			tr[3][tr[3].find(">")+1:tr[3].find("<")],
			tr[4][tr[4].find(">")+1:tr[4].find("<")],
			tr[5]
		]

	html=map(cleanuptr,html)
	return filter(lambda x: x!=[],html)

#print ', '.join([rank[4] for rank in rankingsGetter()[:10]])