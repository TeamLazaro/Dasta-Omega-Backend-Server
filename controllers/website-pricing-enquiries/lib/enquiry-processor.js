
module.exports = processEnquiry;





// Standard libraries
let fs = require( "fs" );

// Third-party packages
// let axios = require( "axios" );

// Our custom imports
let jsonFs = require( "./json-fs.js" );
let enquiries = require( "../db/enquiries.js" );
let userFlows = require( "./user-flows.js" );
// let lib = require( "./other.js" );

/*
 * Constants declarations
 */
// let rootDir = __dirname + "/../../";
let liveEnquiriesLogFileName = __dirname + "/../../../data/enquiries.live.json";
let processedEnquiriesLogFileName = __dirname + "/../../../data/enquiries.processed.json";
let errorEnquiriesLogFileName = __dirname + "/../../../data/enquiries.errors.json";

async function processEnquiry ( cb ) {

	// Find the first enquiry whose state is "processing"
	let enquiry = enquiries.db.find( function ( enquiry ) {
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

	// Remove the enquiry from the live enquiries database
	enquiries.db = enquiries.db.filter( function ( currentEnquiry ) {
		return currentEnquiry._id != enquiry._id;
	} );
	fs.writeFileSync( liveEnquiriesLogFileName, JSON.stringify( enquiries.db ) );

	// Notify us if there were any errors
	if ( enquiry.errors ) {
		// Log them separately
		await jsonFs.add( errorEnquiriesLogFileName, enquiry );
		cb();
		// Send mail to us
		// axios.get( "http://ser.om/notify-error", { params: enquiry } );
		return;
	}

	// Finally, write the results back to the log file
	enquiry._state = "processed";
	enquiry.description = "Finished end-to-end processing of the enquiry.";
	await jsonFs.add( processedEnquiriesLogFileName, enquiry );
	cb();

	return;

}
