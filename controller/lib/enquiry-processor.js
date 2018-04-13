
module.exports = processEnquiry;





// Standard libraries
let fs = require( "fs" );

// Third-party packages
// let axios = require( "axios" );

// Our custom imports
let enquiries = require( "../../data/enquiries.json" );
let userFlows = require( "./user-flows.js" );
// let lib = require( "./other.js" );

/*
 * Constants declarations
 */
// let rootDir = __dirname + "/../../";
let logFileName = __dirname + "/../../data/enquiries.json";

async function processEnquiry ( cb ) {

	// Find the first enquiry whose state is "processing"
	var enquiry = enquiries.find( function ( enquiry ) {
		return enquiry._state == "processing";
	} );
	if ( ! enquiry ) {
		cb();
		return;
	}
	enquiry.errors = "";

	if ( enquiry._user == "executive" ) {
		enquiry = await userFlows.executive( enquiry );
	}
	else {
		enquiry = await userFlows.regular( enquiry );
	}

	// Notify us if there were any errors
	if ( enquiry.errors ) {
		// send mail to us
		// axios.get( "http://ser.om/notify-error", { params: enquiry } );
	}

	// Finally, write the results back to the log file
	enquiry._state = "processed";
	enquiry.description = "Finished end-to-end processing of the enquiry.";
	fs.writeFile( logFileName, JSON.stringify( enquiries ), function () {
		cb();
	} );

	return;

}
