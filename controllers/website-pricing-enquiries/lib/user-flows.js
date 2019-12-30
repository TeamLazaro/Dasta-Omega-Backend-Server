
module.exports = {
	regular,
	executive
};





// Standard libraries
let util = require( "util" );
let child_process = require( "child_process" );

// Third-party packages
let qs = require( "qs" );

/*
 * Constants declarations
 */
let rootDir = __dirname + "/../../../";


// Promisify-ing the exec function so that it plays well with the async/await syntax
let exec = util.promisify( child_process.exec );



/*
 *
 * Regular user flow
 *
 */
async function regular ( enquiry ) {

	// Generate the pricing sheet
	enquiry.description = "Rendering the pricing sheet";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php generate-document/index.php '" + apiInput + "'";
		let { stdout } = await exec( command, { cwd: rootDir } );
		let response = JSON.parse( stdout );
		enquiry.pricingSheet = encodeURI( response.pricingSheet );
	} catch ( e ) {
		enquiry.errors += "[Pricing Sheet]\n" + e.stdout + "\n" + e.stderr + "\n\n";
	}

	// Send an e-mail to the customer
	enquiry.description = "Sending an e-mail to the customer.";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php end-points/php-mailer/send-mail-to-customer.php '" + apiInput + "'";
		await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[Mailing]\n" + e.stderr + "\n\n";
	}

	// Ingest the enquiry into the CRM
	enquiry.description = "Ingesting the enquiry into the CRM.";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php routes/upsert-customer.php '" + apiInput + "'";
		let response = await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[CRM]\n" + e.stderr + "\n\n";
	}

	return enquiry;

}


/*
 *
 * Executive user flow
 *
 */
async function executive ( enquiry ) {

	// Generate the pricing sheet
	enquiry.description = "Rendering the Quote";
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
		let command = "php end-points/php-mailer/send-mail-to-executive.php '" + apiInput + "'";
		await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[Mailing]\n" + e.stderr + "\n\n";
	}

	// Ingest the quote into the CRM
	enquiry.description = "Ingesting the enquiry into the CRM.";
	try {
		let apiInput = qs.stringify( { enquiry: enquiry } );
		let command = "php routes/quote-create.php '" + apiInput + "'";
		let response = await exec( command, { cwd: rootDir } );
	} catch ( e ) {
		enquiry.errors += "[CRM]\n" + e.stderr + "\n\n";
	}

	return enquiry;

}
