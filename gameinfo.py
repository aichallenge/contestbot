#!/usr/bin/env python
# -*- coding: utf-8 -*-
from urllib2 import urlopen

gamedatatemplate="http://ai-contest.com/game_info.php?game_id=%d"
gamelinktemplate="http://ai-contest.com/visualizer_canvas.php?game_id=%d"

def gameinfo(gameid,withlink=False):
	"""Info about a game."""
	data=urlopen(gamedatatemplate % gameid).readlines()
	data=dict(map(lambda line:tuple(line[:-1].split("=")),data))
	
	data[data["player_one_id"]]=data["player_one"]
	data[data["player_two_id"]]=data["player_two"]
	data["winner"]=data[data["winner"]]
	data["loser"]=data[data["loser"]]
	
	reply="Game played at %(timestamp)s, on map %(map_id)s, %(winner)s beat %(loser)s. " % data
	
	if withlink:
		url=gamelinktemplate % gameid
		reply+=url
	
	return reply

print gameinfo(4415957,True)