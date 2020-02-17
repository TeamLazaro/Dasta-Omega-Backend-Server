
let express = require( "express" );
let bodyParser = require( "body-parser" );
let formidable = require( "formidable" );


let router = express();

/*
 * -/-/-/-/-/-/
 * Middleware
 * -/-/-/-/-/-/
 */
// An HTTP body parser for the type "application/json"
let jsonParser = bodyParser.json()
// An HTTP body parser for the type "application/x-www-form-urlencoded"
let urlencodedParser = bodyParser.urlencoded( { extended: true } )
// Finally, plug them in
router.use( urlencodedParser );
router.use( jsonParser );

router.all( "*", function ( req, res ) {

	new formidable.IncomingForm().parse( req, ( err, fields, files ) => {
		debugger;
		res.json( { code: "red", message: "ok." } )
	} )

} );

router.listen( 9996, function () {

	//

} );
