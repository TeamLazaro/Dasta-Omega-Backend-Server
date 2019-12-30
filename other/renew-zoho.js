
// Exports
module.exports = main;





// Constants
let rootDir = __dirname + "/../../..";

/*
 *
 * Packages
 *
 */
// Our custom imports
let log = require( `${ rootDir }/lib/logger.js` );
let crm = require( `${ rootDir }/lib/crm.js` );





/*
 *
 * Renew the API keys
 *
 */
async function renewAPIKey () {

	let response;

	let queryParameters = qs.stringify( {
		client_id: apiCredentials.client_id,
		client_secret: apiCredentials.client_secret,
		refresh_token: apiCredentials.refresh_token,
		grant_type: "refresh_token"
	} );
	response = await axios.post( `https://accounts.zoho.com/oauth/v2/token?${ queryParameters }` );

	let freshAPICredentials = response.data;
	let accessToken = freshAPICredentials.access_token;
	let expiresOn = Math.round(
		( Date.now() / 1000 )
		+ ( freshAPICredentials.expires_in / 1000 )
		- ( 5 * 60 )
	);

	apiCredentials = Object.assign( apiCredentials, {
		"access_token": accessToken,
		"expires_at": expiresOn,
		"lastRefreshedOn": ( new Date ).toString()
	} );

	await fs.writeFile( apiCredentialsFilename, JSON.stringify( apiCredentials, null, "\t" ) );

}




/*
 * -/-/-/-/-/
 * Add a person
 * -/-/-/-/-/
 */
function main ( router, middleware ) {

	// First, allow pre-flight requests
	router.options( "/v2/provider/zoho/renew", middleware.allowPreFlightRequest );


	router.post( "/v2/provider/zoho/renew", async function ( req, res ) {

		// res.header( "Access-Control-Allow-Origin", "*" );
		res.header( "Access-Control-Allow-Origin", req.headers.origin );
		res.header( "Access-Control-Allow-Credentials", "true" );

		// Respond back
		res.json( {
			code: 200,
			message: "Will do the necessary.",
			timestamp: ( new Date ).toISOString()
		} );
		res.end();

		/* ------------------------------------------- \
		 * Renew Zoho's API key
		 \-------------------------------------------- */
		try {
			await crm.renewAPIKey();
		}
		catch ( e ) {
			await log.toUs( {
				message: e.message,
				context: "Renewing the Zoho API key",
				data: e
			} );
		}

	} );

	return router;

}
