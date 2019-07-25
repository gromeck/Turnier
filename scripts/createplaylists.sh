#!/bin/bash
#
#	this script has to be placed on the RaspberryPI
#	in root's home
#
#	rund this script to generate a playlist per each
#	directory on the top-level USB media
#
#	the USB media containing the songs is expected
#	in the $USBDIR
#

USBDIR="/music/USB/"
PLAYLISTDIR="/var/lib/mopidy/playlists"

for MUSICDIR in $USBDIR/*; do
	[ ! -d "$MUSICDIR" ] && continue
	NAME="$( basename "$MUSICDIR" )"
	PLAYLIST="$PLAYLISTDIR/$NAME.m3u"
	echo -n "Scanning music directory $MUSICDIR and generating playlist $PLAYLIST ... "
	( echo "#EXTM3U" ; find $MUSICDIR -iname "*.mp3" ) >$PLAYLIST
	SONGS=$( grep -v "^#" $PLAYLIST | wc -l )
	echo -n "$SONGS songs found ... "
	echo "done."
done
