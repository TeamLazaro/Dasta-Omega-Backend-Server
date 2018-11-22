
module.exports = processCall;





// Standard libraries
let util = require( "util" );
let fs = require( "fs" );
let child_process = require( "child_process" );

// Third-party packages
// let axios = require( "axios" );
let qs = require( "qs" );

// Our custom imports
let jsonFs = require( "./json-fs.js" );
let calls = require( "../db/calls.js" );
let datetime = require( "./datetime.js" );

/*
 * Constants declarations
 */
let rootDir = __dirname + "/../../../end-points";
let liveCallsLogFileName = __dirname + "/../../../data/calls.live.json";
let processedCallsLogFileName = __dirname + "/../../../data/calls.processed.json";
let errorCallsLogFileName = __dirname + "/../../../data/calls.errors.json";

// Promisify-ing the exec function so that it plays well with the async/await syntax
let exec = util.promisify( child_process.exec );


async function processCall ( cb ) {

	// Find the first call whose state is "processing"
	let call = calls.db.find( function ( call ) {
		return call._state == "processing";
	} );
	if ( ! call ) {
		cb();
		return;
	}
	call.errors = "";

	// Process the call
	call = await callProcessingPipeline( call );

	// Remove the call from the live calls database
	calls.db = calls.db.filter( function ( currentCall ) {
		return currentCall._id != call._id;
	} );
	fs.writeFileSync( liveCallsLogFileName, JSON.stringify( calls.db ) );

	// Notify us if there were any errors
	if ( call.errors ) {
		// Log them separately
		await jsonFs.add( errorCallsLogFileName, call );
		cb();
		// Send mail to us
		// axios.get( "http://ser.om/notify-error", { params: call } );
		return;
	}

	// Finally, write the results back to the log file
	call._state = "processed";
	call.description = "Finished end-to-end processing of the call.";
	await jsonFs.add( processedCallsLogFileName, call );
	cb();

	return;

}



async function callProcessingPipeline ( call ) {

	/*
	 *
	 * 1. Fetch the caller's number
	 * If the caller's number is in the CRM, do not proceed
	 *
	 */
	let phoneNumber = call.caller_id;
	let user = { };
	try {
		let apiInput = qs.stringify( { phoneNumber } );
		let command = "php get-user-by-phone/index.php '" + apiInput + "'";
		let { stdout } = await exec( command, { cwd: rootDir } );
		let response = JSON.parse( stdout );
		// If a lead or prospect already exists
		if ( response.code == 1 ) {
			return call;
		}
	} catch ( { code, stdout, stderr } ) {
		call.errors += "[Get-User-By-Phone]\n" + stdout + "\n" + stderr + "\n\n";
		return call;
	}



	/*
	 * 2. Initialize the lead with some default data
	 */
	let isMissedCall = /call missed/i.test( call.destination );
	user.firstName = isMissedCall ? "AG Missed Call" : "AG Incoming Call";
	user.lastName = datetime.getDatetimeStamp();
	user.phoneNumber = phoneNumber;
	user.leadStatus = "Fresh";
	user.leadSource = "Phone";



	/*
	 * 3. Ingest the lead into the CRM, and assign it to the manager
	 */
	let leadId;
	try {
		let apiInput = qs.stringify( user );
		let command = "php create-lead/index.php '" + apiInput + "'";
		let { stdout } = await exec( command, { cwd: rootDir } );
		let response = JSON.parse( stdout );
		if ( response.code == 1 ) {
			leadId = response.data.id;
		}
	} catch ( { code, stdout, stderr } ) {
		call.errors += "[Create-Lead]\n" + stdout + "\n" + stderr + "\n\n";
	}



	/*
	 * 4. Attach the recording to the lead record on the CRM
	 */
	try {
		let apiInput = qs.stringify( {
			leadId,
			resourceURL: call.resource_url
		} );
		let command = "php attach-file-to-lead/index.php '" + apiInput + "'";
		let { stdout } = await exec( command, { cwd: rootDir } );
		let response = JSON.parse( stdout );
	} catch ( { code, stdout, stderr } ) {
		call.errors += "[Attach-recording]\n" + stdout + "\n" + stderr + "\n\n";
	}

	return call;

}
