
// Standard libraries
let util = require( "util" );
let child_process = require( "child_process" );
let fs = require( "fs" );





// Promisify-ing the exec function so that it plays well with the async/await syntax
let exec = util.promisify( child_process.exec );

async function main () {
	let response
	try {
		response = await exec( "php test.php" )
		// response = await exec( "mkdir" )
		console.log( response )
	} catch ( { code, stdout, stderr } ) {
		console.log( code )
		if ( code == 69 ) {
			console.log( stdout )
		}
		else {
			console.log( stderr )
		}
	}
}
main()
