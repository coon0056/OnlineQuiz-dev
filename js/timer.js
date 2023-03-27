jQuery(document).ready(function(){
	
			var timer2 = jQuery('.countdown').data('num');
			//timer2 = "00"; //use for testing purposes
			timer2 = timer2 + ":00";

			var interval = setInterval(function() {
			var timer = timer2.split(':');

			//Parsing integer
			var minutes = parseInt(timer[0], 10);
			var seconds = parseInt(timer[1], 10);
			--seconds;
			minutes = (seconds < 0) ? --minutes : minutes;
			if (minutes < 0) clearInterval(interval);
			seconds = (seconds < 0) ? 59 : seconds;
			seconds = (seconds < 10) ? '0' + seconds : seconds;

			jQuery('.countdown').html("Time Remaining: " + minutes + ':' + seconds);
			timer2 = minutes + ':' + seconds;

			if(minutes == 0 && seconds ==0){

				//make sure name field is filled
				if(jQuery('#testTaker').val() === ""){
					personName = window.prompt("Enter your name: ");
					while(personName === null || personName === ""){
						personName = window.prompt("Enter your name: ");
					}				
						jQuery('#testTaker').val(personName);
				}
				//temporarily removes the requied fields
				jQuery('#quizForm :input').removeAttr('required');

				//submits the quiz
				jQuery('.submit-button').trigger("click");
			}
			}, 1000);
});

