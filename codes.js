var codes = {
	input: "", // button sequence that was pressed, will be cleared automatically after no input for 2 seconds
	code_list: [], // list of codes to check, elements are "code" for the code that gets entered and "fn" for the function to be executes
	timer: setTimeout('codes.clear_input()', 2000), // timer ID for the current timer

	/*
	function onkeydown
	run when a key is pressed, checks if any of the stored codes were entered and runs the appropriate function if it was
	Paramaters:
		e:	Event object passed from the onkeydown system event
	*/
	onkeydown: function(e) {
		//window.status = e ? e.keyCode : event.keyCode; // for debugging

		clearTimeout(codes.timer); // cancel timer to clear current input as a button was pressed

		codes.input += e ? e.keyCode : event.keyCode;
		for (var i=0; i<codes.code_list.length; i++) {
			var test_str = codes.input;
			if (test_str.length>codes.code_list[i].code.length) {
				test_str = test_str.substr(test_str.length-codes.code_list[i].code.length);
			}
			if (test_str == codes.code_list[i].code) {
				codes.code_list[i].fn();
				//clearTimeout(codes.timer);
				return;
			}
		}

		codes.timer = setTimeout("codes.clear_input()", 2000); // set to clear current input if nothing entered within 2 seconds
	},

	/*
	function add_code
	adds a push button code to the list
	Parameters:
		code:	the ascii numberic codes for the push button code (e.g. "38" is up, "97" is "a" and "97989799979898 is "abacabb"
		fn:		the function to be run when the code is entered, no paramaters are passed to the function
	*/
	add_code: function(code, fn) {
		//window.status = code; // for debugging

		codes.code_list.push( {code:code, fn:fn} );
	},

	/*
	function clear_input
	clears history of what buttons were pressed
	Parameters: none
	*/
	clear_input: function() {
		//window.status = "a"; // for debugging

		codes.input="";
		clearTimeout(codes.timer);
	}
}

if (document.addEventListener) { 
	document.addEventListener('keydown', codes.onkeydown, false);  
}  
else if (document.attachEvent) { 
	document.attachEvent('onkeydown', codes.onkeydown); 
}

/*
// sample code for adding the Konami Code
codes.add_code("38384040373937396665", functionToRun);
*/
