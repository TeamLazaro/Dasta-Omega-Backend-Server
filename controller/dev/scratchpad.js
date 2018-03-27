
let util = require( "util" );
let child_process = require( "child_process" );
let qs = require( "qs" );

let enquiries = require( "../../db-log/enquiries.json" );

let exec = util.promisify( child_process.exec );

async function main () {

	try {
		let { stdout, stderr } = await exec( `php index.php '${ qs.stringify( { enquiry: enquiries[ 1 ] } ) }'`, { cwd: "../../generate-document" } );
		console.log( stdout )
		console.log( JSON.parse( stdout ) )
		console.log( stderr )
	} catch ( e ) {
		console.log( JSON.parse( e.stderr ) );
	}

}

main();
