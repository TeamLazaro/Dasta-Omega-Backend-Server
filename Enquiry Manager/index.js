
// Standard libraries
let fs = require( "fs" );

// Third-party packages
let express = require( "express" );

// Our custom imports
let datetime = require( "./datetime.js" );
let scheduler = require( "./scheduler.js" );
let processEnquiry = require( "./lib/enquiry-processor.js" );
let enquiries = require( "./logs/enquiries.json" );



/*
 * Constants declarations
 */
let httpPort = 9999;
let logFileName = "logs/enquiries.json";

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
		when: datetime.getDatetimeStamp(),
		state: "processing",
		// description: "Rendering the pricing sheet",
		...req.query
	};
	enquiries.push( enquiry );
	fs.writeFileSync( logFileName, JSON.stringify( enquiries ) );

	// respond back
	res.header( "Access-Control-Allow-Origin", "*" );
	res.json( { status: "alright", ...req.query } );

} );

httpServer.listen( httpPort, function (  ) {
	console.log( "Server listening at " + httpPort + "." )
} );

















// httpServer.get( "/start", function ( req, res ) {
// 	backgroundTask.start();
// 	res.end( "Started the task." );
// } );

// httpServer.get( "/stop", function ( req, res ) {
// 	backgroundTask.stop();
// 	res.end( "Stopped the task." );
// } );
