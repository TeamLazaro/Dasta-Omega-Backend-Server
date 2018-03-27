
// Standard libraries
let fs = require( "fs" );

// Third-party packages
let express = require( "express" );

// Our custom imports
let datetime = require( "./lib/datetime.js" );
let scheduler = require( "./lib/scheduler.js" );
let processEnquiry = require( "./lib/enquiry-processor.js" );
let enquiries = require( "../db-log/enquiries.json" );



/*
 * Constants declarations
 */
let httpPort = 9999;
let logFileName = __dirname + "/../db-log/enquiries.json";

// Initiate the background task
var backgroundTask = scheduler.schedule( processEnquiry, 5 );
backgroundTask.start();

/*
 * Set up the HTTP server
 */
let httpServer = express();

httpServer.get( "/enquire", function ( req, res ) {

	// log the enquiry
	var enquiry = {
		_id: datetime.getUnixTimestamp(),
		_when: datetime.getDatetimeStamp(),
		_state: "processing",
		...req.query
	};
	enquiries.push( enquiry );
	fs.writeFileSync( logFileName, JSON.stringify( enquiries ) );

	// respond back
	res.header( "Access-Control-Allow-Origin", "*" );
	res.json( { status: "alright", ...req.query } );

} );

// httpServer.get( "/executive-enquire", function ( req, res ) {

// 	processEnquiry( function () {
// 		// respond back
// 		res.header( "Access-Control-Allow-Origin", "*" );
// 		res.json( { status: "alright", ...req.query } );
// 	} )

// } );

httpServer.listen( httpPort, function (  ) {
	console.log( "Server listening at " + httpPort + "." )
} );
