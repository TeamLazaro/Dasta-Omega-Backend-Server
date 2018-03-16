
module.exports = processEnquiry;





// Standard libraries
let fs = require( "fs" );

// Third-party packages
let axios = require( "axios" );

// Our custom imports
let enquiries = require( "../logs/enquiries.json" );
let lib = require( "./other.js" );

/*
 * Constants declarations
 */
let logFileName = "logs/enquiries.json";

async function processEnquiry ( cb ) {

	// Find the first enquiry whose state is "processing"
	var enquiry = enquiries.find( function ( enquiry ) {
		return enquiry.state == "processing";
	} );
	if ( ! enquiry ) {
		cb();
		return;
	}

	// Delegate the enquiry to PHP for generating the pricing sheet
	enquiry.description = "Rendering the pricing sheet";
	var response;
	try {
		response = await axios.get( "http://ser.om", { params: enquiry } );
	} catch ( e ) {
		lib.handleHTTPErrorResponse( e.response, enquiry, cb );
		fs.writeFile( logFileName, JSON.stringify( enquiries ), function () {
			cb();
		} );
		return;
	}
	enquiry.pricingSheet = response.data.pricingSheet;

	// Delegate the enquiry to the Endpoint Server for other things
	enquiry.description = "Doing other things with the pricing sheet.";
	var response;
	try {
		response = await axios.get( "http://end.om", { params: enquiry } );
	} catch ( e ) {
		lib.handleHTTPErrorResponse( e.response, enquiry, cb );
		fs.writeFile( logFileName, JSON.stringify( enquiries ), function () {
			cb();
		} );
		return;
	}

	// Finally, write the results back to the log file
	enquiry.state = "processed";
	enquiry.description = "Finished end-to-end processing of the enquiry.";
	fs.writeFile( logFileName, JSON.stringify( enquiries ), function () {
		cb();
	} );

	return;

}
