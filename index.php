<html>
<head><title>Google AI Challenge contestbot from the irc channel</title></head>

<body>

<h2>Welcome to contestbot.</h2>

<p>The following factoid databases exist:</p>
<ul>
<?php

$dir = "factoids/";

// Open a known directory, and proceed to read its contents
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file !== "." && $file !== "..") {
                //echo "<li>filename: $file : filetype: " . filetype($dir . $file) . "</li>" . "\n";
                echo "<li><a href=\"viewfactoids.php?db=" . urlencode($file) . "\">$file</a></li>\n";
            }
        }
        closedir($dh);
    }
}
?>

</ul>

<p>The following channel logs are available:</p>
<ul>
<?php

$dir = "channellogs/";

// Open a known directory, and proceed to read its contents
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file !== "." && $file !== ".." && $file !== ".htaccess" && is_link($dir . $file)) {
                echo "<li><a href=\"" . $dir . urlencode($file) . "\">$file</a></li>\n";
            }
        }
        closedir($dh);
    }
?>
</ul>

<!--<p>Aggregate channel statistics for are available for:</p>
<ul>
<li><a href="pisg.output/">#sourceforge</a></li>
</ul>-->

<p><b>contestbot</b> is a <a href="http://supybot.com">supybot</a>. 
For documentation about supybot, see the docs subdirectory of the supybot project's <a href="http://supybot.git.sourceforge.net/git/gitweb-index.cgi">git repository</a>. 
Also see this nice <a href="http://supybook.fealdia.org/">supybot guidebook</a>.</p>

<p><a href="http://csclub.uwaterloo.ca/~amstan/">amstan</a> is the owner of this bot, you can contact him about the bot on the <a href="http://ai-contest.com/forum/viewtopic.php?f=19&t=436">forums</a>.</p>

</body>
</html>
