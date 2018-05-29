
// Standard libraries
let fs = require( "fs" );

// Third-party packages
let express = require( "express" );
let bodyParser = require( "body-parser" );

// Our custom imports
let datetime = require( "./lib/datetime.js" );
let scheduler = require( "./lib/scheduler.js" );
let processCall = require( "./lib/call-processor.js" );
let calls = require( "./db/calls.js" );



/*
 * Constants declarations
 */
let httpPort = 9991;
let logFileName = __dirname + "/../../data/calls.live.json";

// Initiate the background task
var backgroundTask = scheduler.schedule( processCall, 15 );
backgroundTask.start();

/*
 * Set up the HTTP server and the routes
 */
let router = express();
// Create an HTTP body parser for the type "application/json"
let jsonParser = bodyParser.json();
// Create an HTTP body parser for the type "application/x-www-form-urlencoded"
let urlencodedParser = bodyParser.urlencoded( { extended: false } );

// Plugging in the middleware
router.use( urlencodedParser );
router.use( jsonParser );


router.post( "/calls", function ( req, res ) {

	// Are these two lines required?
	res.header( "Access-Control-Allow-Origin", req.headers.origin );
	res.header( "Access-Control-Allow-Credentials", "true" );

	// Construct the call record and store it in the database
	var call = {
		_id: datetime.getUnixTimestamp(),
		_state: "processing",
		...req.body
	};
	calls.db.push( call );
	fs.writeFileSync( logFileName, JSON.stringify( calls.db ) );

	// Respond back to client
	res.json( { message: "We're processing the call." } );
	res.end();

} );





let httpServer = router.listen( httpPort, function (  ) {
	if ( process.env.NODE_ENV != "production" )
		console.log( "Server listening at " + httpPort + "." );
	if ( process.send )
		process.send( "ready" );
} );


/*
 * Handle process shutdown
 *
 * 1. Stop the background task.
 * 2. Once that is done, then close the HTTP server.
 * 3. Finally, quit the process.
 *
 */
process.on( "SIGINT", function () {
	backgroundTask.stop();
	scheduler.onStopped( backgroundTask, function () {
		httpServer.close();
		process.exit( 0 );
	} );
} );
