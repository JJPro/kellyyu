(function($){

	/**
	 * XIMALAYA AUDIO
	 *
     */
	var external_audios = [];
	$('.external-audio').each(function(index, element){
		initPlayer($(element));
	});



	// ** audio - audio element ** //
	function initPlayer($external_audio_container){

		//debugger;
		var audio = $external_audio_container.find('audio')[0];
		var $audio = $(audio);
		external_audios.push(audio);

		var $player = $external_audio_container.find('.player');

		var $button = $external_audio_container.find('.meta-container .play-pause');
		var $currentTime = $player.find('.current-time');
		var $duration = $player.find('.duration');
		var $scrubber = $player.find('.scrubber');
		var $progress = $player.find('.progress');
		var $cursor_move = $player.find('.cursor-move');
		var $loaded = $player.find('.loaded');
		var $error_message = $player.find('.error-message');
		var trails = 0;


		$button.on('click', function(){
			togglePlay(audio, $button);
		});
			
		$audio.on('loadedmetadata', function(){
			showDuration();
		});

		$audio.on('timeupdate', function(){
			var width = (audio.currentTime / audio.duration).toFixed(4) * 100;
			$progress.css('width', width + '%');
			showCurrentTime();
		});

		/*$audio.on('progress', function(){
		});*/

		$audio.on('ended pause', function(){
			$button.removeClass('icon-pause').addClass('icon-play');
		});

		$audio.on('playing', function(){
			$button.removeClass('icon-play').addClass('icon-pause');
		});

		$audio.on('error', function(){
			if (trails > 5){
				$button.removeClass('icon-play icon-pause').addClass('icon-error');
				$error_message.text('Error loading this track.').toggle();
			} else {
				trails++;
				audio.load();
			}
		});

		$scrubber.on('click', function(event){
			var delta = event.pageX - $scrubber.offset().left;
			var percent = (delta / parseFloat($scrubber.css('width'))).toFixed(4) * 100;
			setCurrentTime(audio, percent);
		});

		// ** dragging the scrubber ** //
		var percent;
		$scrubber.mousemove(function(event){
			var delta = event.pageX - $scrubber.offset().left;
			percent = (delta / parseFloat($scrubber.css('width'))).toFixed(4) * 100;
			$cursor_move.css('width', percent + '%');
			showTimeOnElement( parseInt(audio.duration * percent / 100), $duration );
			
		}).mouseout(function(){
			$cursor_move.css('width', 0);
			showDuration();
		});

		function showDuration(){
			showTimeOnElement(audio.duration, $duration);
		}

		function showCurrentTime() {
			showTimeOnElement(audio.currentTime, $currentTime);
		}

	}

	function showTimeOnElement(seconds, $element) {
		var s = parseInt(seconds % 60),
			m = parseInt(seconds / 60 % 60),
			h = parseInt(seconds / 3600);
		if (s < 10) s = '0' + s;
		if (m < 10) m = '0' + m;

		var time = m + ':' + s;
		if (h != 0){
			time = h + ':' + time;
		}

		$element.text(time);
	}

	function togglePlay(audio, $button) {
		if ($button.hasClass('icon-play')){
			external_audios.map(function(obj){
				obj.pause();
			});
			audio.play();
		} else {
			audio.pause();
		}
	}

	function setCurrentTime(audio, percent){
		audio.currentTime = parseInt(audio.duration * (percent/100) );
	}

	/**
	 * Nav Toggle
	 */
	$(document).on('click', '.nav-toggle', function(){
		$('.nav-container').toggleClass('is-active');
		//$('.main-navigation').slideToggle();
	});


	// ** All ** //
	$('[data-toggle="tooltip"]').tooltip({html:true});

})(jQuery);
