
module.exports = processEnquiry;





// Standard libraries
let util = require( "util" );
let child_process = require( "child_process" );
let fs = require( "fs" );

// Third-party packages
let qs = require( "qs" );
let axios = require( "axios" );

// Our custom imports
let enquiries = require( "../../db-log/enquiries.json" );
let lib = require( "./other.js" );

/*
 * Constants declarations
 */
let rootDir = __dirname + "/../../";
let logFileName = __dirname + "/../../db-log/enquiries.json";

// Promisify-ing the exec function so that it plays well with the async/await syntax
let exec = util.promisify( child_process.exec );

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

	// Generate the pricing sheet
	enquiry.description = "Rendering the pricing sheet";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php generate-document/index.php '" + apiInput + "'";
		let { stdout } = await exec( command, { cwd: rootDir } );
		let response = JSON.parse( stdout );
		enquiry.pricingSheet = encodeURI( response.pricingSheet );
	} catch ( e ) {
		enquiry.errors += "[Quote]\n" + e.stderr + "\n\n";
	}

	// Send an e-mail to the customer
	enquiry.description = "Sending an e-mail to the customer.";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php end-points/php-mailer/index.php '" + apiInput + "'";
		await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[Mailer]\n" + e.stderr + "\n\n";
	}

	// Ingest the enquiry into the CRM
	enquiry.description = "Ingesting the enquiry into the CRM.";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php end-points/zoho-crm/index.php '" + apiInput + "'";
		await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[CRM]\n" + e.stderr + "\n\n";
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
