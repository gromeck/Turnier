#!/bin/bash
#
#	start the playlist MusicBox
#
#	Parameters
#		$1		Hostname or IP address of MP-Deamon
#		$2		Volume Level for Effect
#		$3		Volume Level for Music
#		$4		effect name (start or stop)
#

#
#	configure parameters
#
MPBOX=$1
VOLEFFECT=$2
VOLMUSIC=$3
EFFECT=$4

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
volume $VOLEFFECT
insert file:///music/MusicBox/Effects/$EFFECT.mp3
next
play
sleep 25
volume $VOLMUSIC
EOM
