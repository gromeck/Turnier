#!/bin/bash
#
#	start the playlist Mixed on the MusicBox
#
#	Parameters
#		$1		Hostname or IP address of MP-Deamon
#		$2		Volume Level for Music
#		$3		playlist name
#

#
#	configure parameters
#
MPBOX=$1
VOLMUSIC=$2
PLAYLIST=$3

#
#	send the commands
#
while read LINE ; do
	if [ "${LINE:0:5}" == "sleep" ]; then
		echo "Sleeping ${LINE:5} seconds ..."
		sleep ${LINE:5}
	else
		echo "Sending: $LINE"
		mpc --quiet --host=$MPBOX $LINE
	fi
done <<EOM
volume $VOLMUSIC
clear
load $PLAYLIST
shuffle
play
EOM
