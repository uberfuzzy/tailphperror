    a simple web interface to simulate doing `tail php_error.log`

I wrote this to easily access the php error log without having to ssh into the server and tail the log when something "broke".
This also removed my need to turn on public error messages while live coding/testing.

This isnt really a "production" grade script. Its more a hacky mess of code I made, and have been personally using for a while,
and I'm throwing it on github for the hell of it (and to have a backup).

# Query Params
(proper docs coming)
It has a few crude query params to mimic some of the features from `tail`.
Like "?n=50" to show 50 lines then the default 25,
 and "auto=15" to auto reload the page every 15 seconds (sort like like -f)

# Platform
It "works" on both windows and linux, making some best attempts to find what it needs.
On linux, it uses `tail`, as you would expect.
On windows, it does some hacky fallback stuff with `type` and array_slice().
(If you do have a `tail.exe` on your windows environment, set $winTail to true to use)

# Installation

## quick
If you dont need any filters or flags (see config below), you can download it right from here and drop the script right in your web facing dir (feel free to rename as needed).

## customized
If you DO need to set things (most likely), but you cloned this repo, and dont want to edit the original, you can use what I call a "loader script".
A simple php page in your public www dir, that sets vars, then require()s the original file from the repo.
This loader file can be named what ever you want, so people dont "guess" it.
The loader also gives you a place to set flags and custom filters, and possibly do authentication.

# Configs

## Flags
The only flag you can set right now is `$winTail`, to not fall back to type.exe on windows.
(more will likely come)

## Additional Filters
There is a string filtering system in place.
By default, there 4 pairs, for masking and coloring the log row type (warnings in yellow, parse in red, etc).
(Note: you can set to disable these colors, but keep the type shortening, either `?colors=0` or `?nocolor=1`)

You can add more to this by creating and adding to an array called `$localReplace`,
these are fed into `str_replace()`, and mapped over each line displayed.

Example:
    $localReplace = array();
    $localReplace[] = array('G:\wamp\www\', '%WEBROOT%\');
    $localReplace[] = array('G:/wamp/www/', '%WEBROOT%/');
    $localReplace[] = array('/home/uberfuzzy/www/', '~/');

This is useful to shorten (also hide, see security disclaimer below) long disk paths that may be in your logs often.
Unless you are doing a "quick" install, I would suggest you set a local filter to mask your webroot path (see examples above)
(honestly, even then, take a minute and copy/paste the default coloring filters inside, to do one for your home dir)

Note: When doing ones for windows paths, you kind of need to them twice.
Using both \ and /, since some error functions arent entirely consistant. (see example above)

 
# Security
This whole script was never meant to be secure. It was run on personal servers, and until now, no one knew the filename to hit.

Generally speaking, unless you are logging private details with error_log(),
exposing your php error log is no more "insecure" then having error_messages enabled.

You might leak some disk paths/folder names, and that is usually more a privacy concern more then a security issue.
(but could give someone extra details to find a crack in your armor)

By using a loader script with a unique name, and including the core of a known filename, you at least benefit from "security through obscurity".

If you are stupid enough to use this on your server, where real things live, I would suggest you take extra steps to secure your interface.
Something like BASIC auth checking (even with a shared password), or a simple IP check.

I might add built in support for these later, if I feel they would be of widespread value (or to me).
