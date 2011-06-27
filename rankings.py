#!/usr/bin/env python
# -*- coding: utf-8 -*-
from urllib2 import urlopen
import json

def rankingsGetter(url="http://aichallengebeta.hypertriangle.com/ranking_json.php?page=1"):
	data=urlopen(url).read()
	data=json.loads(data)
	
	#reformat the data
	fields=data["fields"]
	values=data["values"]
	data=[]
	for user in values:
		data.append(dict(zip(fields,user)))
	
	return data

if __name__=="__main__":
	print ', '.join([user["username"] for user in rankingsGetter()[:10]])