
module.exports = {
	handleHTTPErrorResponse
}

function handleHTTPErrorResponse ( response, log, cb ) {
	console.log( "oh noes!" );
	log.state = "error";
	log.description = response.statusText + ": " + response.data.message;
}
