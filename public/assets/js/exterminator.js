$("a.toggle-debug").click(function(e){
	e.preventDefault();

	if ($(this).text() == "Hide") {
		$("div.debug div.var-dump").fadeOut("fast");
		$(this).addClass("show").text("Show");
	} else {
		$("div.debug div.var-dump").fadeIn("fast");
		$(this).removeClass("show").text("Hide");
	}
});

$(".var-string, .var-numeric, .var-bool-true, .var-bool-false").click(function(){
	console.log("whoa!");
	selectText($(this).get(0));
});

function selectText(text) {
	var doc = document, range, selection;

	if (doc.body.createTextRange) { //ms

		range = doc.body.createTextRange();
		range.moveToElementText(text);
		range.select();

	} else if (window.getSelection) { //all others
		selection = window.getSelection();        
		range     = doc.createRange();

		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}