/*
**
*/

/*
**	end of configuration area
*/

var _start;
var _secondstostop;
var _trigger;
var _interval;
var _pullup;
var _pulldown;
var _pos = 0;

function stopwatch_init(playtime)
{
    var now = new Date;

	var timeused = now.getTime() / 1000 - playtime;
	if (0)
	alert('now.getTime()=' + now.getTime() + '\n' +
		'playtime=' + playtime + '\n' +
		'timeused=' + timeused);

	if (timeused < stopwatch_pre_seconds) {
		/*
		**	we are in the pre play phase
		*/
		stopwatch_visibilty(1);
		stopwatch_color('orange','white');
		stopwatch_start(stopwatch_pre_seconds - timeused,'trigger_pre');
	}
	else if (timeused < stopwatch_pre_seconds + stopwatch_play_seconds) {
		/*
		**	we are in the play phase
		*/
		stopwatch_visibilty(1);
		stopwatch_color('green','white');
		stopwatch_start(stopwatch_pre_seconds + stopwatch_play_seconds - timeused,'trigger_stop');
	}
	else if (timeused < stopwatch_pre_seconds + stopwatch_play_seconds + stopwatch_stop_seconds) {
		/*
		**	we are in the post/stop phase
		*/
		stopwatch_visibilty(1);
		stopwatch_display('STOP');
		stopwatch_color('red','white');
		setTimeout('stopwatch_visibilty(0)',(stopwatch_pre_seconds + stopwatch_play_seconds + stopwatch_stop_seconds - timeused) * 1000);
	}
	else {
		document.location.reload();
	}
}

function stopwatch_exit()
{
	stopwatch_visibilty(0);
	setTimeout("document.location.reload();",2000);
}

function stopwatch_pulldown()
{
	var obj = document.getElementById('stopwatch');

	if (pos < 150)
		obj.style.top = ++pos - 150;
	else
		clearInterval(_pulldown);
}

function stopwatch_pullup()
{
	var obj = document.getElementById('stopwatch');

	if (pos > 0)
		obj.style.top = --pos - 150;
	else
		clearInterval(_pulldown);
}

function stopwatch_visibilty(visible)
{
	var obj = document.getElementById('stopwatch');

	if (visible) {
		pos = 0;
		stopwatch_pulldown();
		_pulldown = setInterval('stopwatch_pulldown()',10);
		obj.style.visibility = 'visible';
	}
	else {
		pos = 150;
		stopwatch_pullup();
		_pulldown = setInterval('stopwatch_pullup()',10);
		//obj.style.visibility = 'hidden';
	}
}

function stopwatch_display(text,bgcolor,fgcolor)
{
	var obj = document.getElementById('stopwatch');

	obj.innerHTML = text;
}

function stopwatch_color(bgcolor,fgcolor)
{
	var obj = document.getElementById('stopwatch');

	obj.style.background = bgcolor;
	obj.style.color = fgcolor;
}

function stopwatch_start(secondstostop,trigger)
{
    var now = new Date;

	_start = now.getTime();
	_secondstostop = secondstostop;
	_trigger = trigger;
	stopwatch_update();
	_interval = setInterval('stopwatch_update()',1000);
}

function stopwatch_update()
{
    var now = new Date;
	var countdown = Math.round((now.getTime() - _start) / 1000);
	var left = _secondstostop - countdown;

	if (left > 0) {
		var minutes = Math.floor(left / 60);
		var seconds = Math.floor(left % 60);

		display = minutes + ':' + ((seconds < 10) ? '0' + seconds : seconds);
	}
	else {
		display = '0:00';
		clearInterval(_interval);
		setTimeout(_trigger + '()',500);
	}
	stopwatch_display(display);
}

function audio_play(effect)
{
	if (frames['audio'])
		frames['audio'].location = 'frames/audio.php?effect=' + effect;
}

function audio_playlist(playlist)
{
	if (frames['audio'])
		frames['audio'].location = 'frames/audio.php?playlist=' + playlist;
}

function audio_openplayer(hostname)
{
	window.open('http://' + hostname + '/');
}

/*
**	the pre play time is complete
*/
function trigger_pre()
{
	stopwatch_start(stopwatch_play_seconds,'trigger_stop')
	stopwatch_color('green','white');
	audio_play('start');
}

/*
**	the time is complete
*/
function trigger_stop()
{
	stopwatch_display('STOP');
	stopwatch_color('red','white');
	setTimeout('stopwatch_exit()',stopwatch_stop_seconds * 1000);
	audio_play('stop');
}
