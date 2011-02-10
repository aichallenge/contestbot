###
# Copyright (c) 2010, Alexandru Stan
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
#   * Redistributions of source code must retain the above copyright notice,
#    this list of conditions, and the following disclaimer.
#   * Redistributions in binary form must reproduce the above copyright notice,
#    this list of conditions, and the following disclaimer in the
#    documentation and/or other materials provided with the distribution.
#   * Neither the name of the author of this software nor the name of
#    contributors to this software may be used to endorse or promote products
#    derived from this software without specific prior written consent.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
# ARE DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

###

from urllib2 import URLError

import supybot.utils as utils
from supybot.commands import *
import supybot.plugins as plugins
import supybot.ircutils as ircutils
import supybot.callbacks as callbacks

from rankings import rankingsGetter
import gameinfo

class AIChallenge(callbacks.Plugin):
	"""Functions related to the Google AI Challenge, includes rankings retriever."""
	threaded = True
	regexps = ['titleSnarfer']
	
	def rankings(self, irc, msg, args, howmany):
		"""[how many]
		
		Get the top $(how many) players."""
		if howmany not in range(1,50):
			irc.error("Cannot do that many players.")
		else:
			try:
				irc.reply("Top %s players: %s" % (howmany,', '.join(["%s(%s)" % (rank[1],rank[4]) for rank in rankingsGetter()[:howmany]])))
			except URLError:
				irc.error("There was a problem accessing the interface to ai-contest.com")
	rankings = wrap(rankings, [optional('int',10)])
	
	def match(self, irc, msg, args, player1, player2):
		"""player1 [player2]
		
		Get the most recent match of $player1. If $player2 is specified, then the most recent match between $player1 and $player2"""
		#irc.reply(self.titleSnarfer.__doc__)
		raise NotImplemented()
		#irc.reply("Top %s players: %s" % (howmany,', '.join(["%s(%s)" % (rank[1],rank[4]) for rank in rankingsGetter()[:howmany]])))
	match = wrap(match, ["int","int"])
	
	def game(self, irc, msg, args, gameid, withLink):
		"""gameid, [withLink]
		
		Info about a game."""
		
		try:
			irc.reply(gameinfo.gameinfo(gameid,withLink))
		except URLError:
			irc.error("There was a problem accessing the interface to ai-contest.com")
	game = wrap(game, ["int",optional("boolean",True)])
	
	def titleSnarfer(self, irc, msg, match):
		r"test"
		channel = msg.args[0]
		#if not irc.isChannel(channel):
		#	return
		#if callbacks.addressed(irc.nick, msg):
		#	return
		#url = match.group(0)
		#print "test"
		irc.reply("ok")
	titleSnarfer = urlSnarfer(titleSnarfer)

Class = AIChallenge


# vim:set shiftwidth=4 softtabstop=4 expandtab textwidth=79:
