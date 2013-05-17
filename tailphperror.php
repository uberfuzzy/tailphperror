<?php
ob_start();
# init -------------------------------------------
$lines = array();
$n = 25;
$debug = false;
$colors = true;
$auto = 0;

# check params -------------------------------------------
if( !empty($_GET['n']) ) {
	if( is_numeric($_GET['n']) ) {
		$n = (int)$_GET['n'];
	}
}

if( !empty($_GET['debug']) ) {
	$debug = true;
}

if( !empty($_GET['auto']) ) {
	$auto = (int)$_GET['auto'];
}

if( !empty($_GET['nocolor']) ) {
	$colors = false;
} elseif ( isset($_GET['color']) && $_GET['color'] == '0' ) {
	$colors = false;
} elseif ( isset($_GET['colors']) && $_GET['colors'] == '0' ) {
	$colors = false;
}


# build stuff --------------------------------------

$logpath = ini_get('error_log');
if( $debug ) {
	print "logpath=";
	var_dump($logpath);
}
if( empty($logpath) ) {
	print "cannot find path to logpath\n";
	exit();
}


/*
 * This array sets up some text replacing+color wrapping
 */
$cleanUpIO = array('i'=>null, 'o'=>null);

$cleanUpIO['i'] = array();
$cleanUpIO['o'] = array();

$cleanUpIO['i'][] = '] PHP Warning: ';
$cleanUpIO['o'][] = '] <span class="w">Warning</span>:';

$cleanUpIO['i'][] = '] PHP Parse error: ';
$cleanUpIO['o'][] = ']   <span class="p">Parse</span>:';

$cleanUpIO['i'][] = '] PHP Fatal error: ';
$cleanUpIO['o'][] = ']   <span class="f">Fatal</span>:';

$cleanUpIO['i'][] = '] PHP Notice: ';
$cleanUpIO['o'][] = ']  <span class="n">Notice</span>:';

if( empty($colors) ) {
	# if "color" mode is disabled,
	# strip out the span tags from the 'o' replacement set
	# (but keeps the text replacement)
	$cleanUpIO['o'] = array_map('strip_tags', $cleanUpIO['o']);
}

if( !empty($localReplace) ) {
	foreach($localReplace as $lR) {
		$cleanUpIO['i'][] = $lR[0];
		$cleanUpIO['o'][] = $lR[1];
	}
}


if( $debug ) {
	print "html_errors=";
	$html_errors = ini_get('html_errors');
	var_dump($html_errors);
}

# assume we're on *nix, and have proper tail access
$tool = "tail -n " . $n;

if( strtolower(substr(PHP_OS,0,3)) == "win" ) {
	/*
	windows generally doesnt have "tail" command.
	we can sort of fake it using type.exe (which gives the whole file),
		then slice off the number we need from the end.
	this is a horrible hack, but it does work.
	*/

	# by some chance you DO have tail on your windows box,
	# 	set the $winTail flag, and we'll use tail instead.

	if( empty($winTail) ) {
		$tool = "type";
		# oh yeah, since we're shelling to type.exe (in windows), we need to make the path win friendly.
		$logpath = str_replace("/", "\\", $logpath);
	}
}

# do stuff -------------------------------------------------

# by our powers combined!
$cmd = "{$tool} {$logpath}";
if( $debug ) {
	print "cmd=";
	var_dump($cmd);
}

exec($cmd, $lines);

if( count($lines) > $n ) {
	#this should only happen on windows, because `type` is stupid
	$lines = array_slice($lines, -$n);
}

function cleanUp($line) {
	global $cleanUpIO;
	$line = str_ireplace($cleanUpIO['i'], $cleanUpIO['o'], $line);

	return $line;
}

$lines = array_map('cleanUp', $lines);

$early = ob_get_clean();
?><!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="data:image/x-icon;base64,AAABAAEAEBAAAAEACABoBQAAFgAAACgAAAAQAAAAIAAAAAEACAAAAAAAAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAD//////wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAA/wAAAP8AAAD/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEBAAAAAAAAAQABAAAAAAEAAAEAAQAAAQAAAAEAAAABAAAAAAAAAAEAAAAAAQAAAQAAAQABAAEAAAAAAQAAAAABAQAAAAABAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" />
<title>php_error.log</title>
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="PRIVATE">
<meta NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW,NOARCHIVE">
<META HTTP-EQUIV="Content-Language" Content="EN">
<?php if( !empty($auto) ) {
	print "<meta http-equiv=\"refresh\" content=\"{$auto}\">\n";
}
?>
<style>
body {
	font-size: 14px;
	font-family: monospace;
	white-space: pre;
}
<?php if( !empty($colors) ) { ?>

.f { background-color: black; color: white; }
.p { background-color: red; color: white; }
.w { background-color: yellow; color: black; }
.n { background-color: orange; color: black; }

<?php } ?>
</style>
</head>

<body><?php

if( !empty($early) ) {
	print $early;
	print "\n";
}

if( !empty($auto) ) {
	print "Auto-refresh @ {$auto}s. Last: ". date('r') ."\n";
}

foreach( $lines as $lid=>$line )
{
	print "<span class='line' data-id='{$lid}'>" . $line . "</span>\n";
}

?>
</body>
</html>
