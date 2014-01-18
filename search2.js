$(document).ready(function()
{
	$('.searchbar').autocomplete({
		paramName: 'summonerName',
		serviceUrl: 'searchData.php',
	    width: 278,
		triggerSelectOnValidInput: false,
		onSelect: function (suggestion) {
			window.location = "index.php?name="+suggestion.value;
		}
	});	
});
