
// Standard libraries
let fs = require( "fs" );

// Third-party packages
let express = require( "express" );
let cookieParser = require( "cookie-parser" );
let bodyParser = require( "body-parser" );
let base64 = require( "base-64" );

// Our custom imports
let datetime = require( "./lib/datetime.js" );
let scheduler = require( "./lib/scheduler.js" );
let processEnquiry = require( "./lib/enquiry-processor.js" );
let enquiries = require( "../data/enquiries.json" );
let enquiryFields = require( "./lib/enquiry-fields.js" );



/*
 * Constants declarations
 */
let httpPort = 9999;
let credentialsFileName = __dirname + "/../data/users.json";
let logFileName = __dirname + "/../data/enquiries.json";

// Initiate the background task
var backgroundTask = scheduler.schedule( processEnquiry, 5 );
backgroundTask.start();

/*
 * Set up the HTTP server and the routes
 */
let router = express();
// Create an HTTP body parser for the type "application/json"
let jsonParser = bodyParser.json()
// Create an HTTP body parser for the type "application/x-www-form-urlencoded"
let urlencodedParser = bodyParser.urlencoded( { extended: false } )

// Plugging in the middleware
router.use( cookieParser() );
router.use( urlencodedParser );
router.use( jsonParser );

router.post( "/enquiries", function ( req, res ) {

	// res.header( "Access-Control-Allow-Origin", "*" );
	res.header( "Access-Control-Allow-Origin", req.headers.origin );
	res.header( "Access-Control-Allow-Credentials", "true" );

	/*
	 * Validate credentials if they are passed
	 */
	var credentials = { };
	if ( req.cookies.auth ) {
		try {
			credentials = JSON.parse( base64.decode( req.cookies.auth ) );

			// Check if the user is an executive
			var users = JSON.parse( fs.readFileSync( credentialsFileName ) );
			var user = users.find( function ( user ) {
				return user.identifier == credentials.identifier
			} );
			var userIsLegit = !! user;

			// Check if the credentials are still valid
			var tokenValidity = credentials.timestamp && ( (datetime.getUnixTimestamp() / 1000 - credentials.timestamp ) < 3600 );

			credentials.areValid = userIsLegit && tokenValidity;
		} catch ( e ) {
			res.status( 401 );
			res.end();
			return;
		}
	}

	/*
	 * Validate the request body
	 */
	// var requiredFieldsPresent = enquiryFields.regular.every( function ( key ) {
	// 	return key in req.query;
	// } );
	// if ( ! requiredFieldsPresent ) {
	// 	res.writeHead( 400, 'Invalid arguments' );
	// 	res.end();
	// 	return;
	// }

	/*
	 * Log the enquiry
	 */
	// var enquiryFieldsOfConcern = enquiryFields.regular.reduce( function ( acc, currentField ) {
	// 	acc[ currentField ] = req.query[ currentField ];
	// 	return acc;
	// }, { } );
	// if ( credentials.areValid ) {
	// 	enquiryFieldsOfConcern = enquiryFields.executive.reduce( function ( acc, currentField ) {
	// 		acc[ currentField ] = req.query[ currentField ];
	// 		return acc;
	// 	}, enquiryFieldsOfConcern );
	// }
	var enquiry = {
		_id: datetime.getUnixTimestamp(),
		_when: datetime.getDatetimeStamp(),
		_state: "processing",
		_hostname: `${req.protocol}://${req.headers[ "x-forwarded-host" ]}`,
		_user: credentials.areValid ? "executive" : "regular",
		...req.body
		// ...enquiryFieldsOfConcern
	};
	enquiries.push( enquiry );
	fs.writeFileSync( logFileName, JSON.stringify( enquiries ) );

	// Respond back
	res.json( { message: "We're processing the enquiry." } );
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
