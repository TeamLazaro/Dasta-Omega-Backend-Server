
let fs = require( "fs" )

let autocannon = require( "autocannon" )

const concurrent = 10000
let benchmarker = autocannon( {
	url: 'http://localhost:9999/enquire?name=adi&email=ad@ya&unit=15',
	connections: concurrent,
	amount: concurrent,
	overallRate: concurrent,
	// duration: 10,
	maxConnectionRequests: 1
} )

// benchmarker.on( "response", handleResponse )

// benchmarker.on( "done", publishResults )

let attempts = [ ]
function handleResponse ( client, statusCode, responseBytes, responseTime ) {
	attempts.push( {
		responseBody: client.parser.chunk,
		responseTime
	} )
}

function publishResults ( results ) {
	// console.log( results )
	// let logFileName = "log_responses.csv"
	// let columnHeaders = [ "Index", "Unit", "\"Basic Cost\"", "\"Response time (ms)\""/*, "\"Calculation Time\""*/ ]
	let index = 1
	for ( let { responseBody, responseTime } of attempts ) {
		let parsedResponse
		try {
			parsedResponse = JSON.parse( responseBody.toString().split( /\n\s*\n/ )[ 1 ] )
		} catch ( e ) {
			parsedResponse = { }
		}
		// let time = ( responseTime / 1000 ).toFixed( 3 )
		// fs.appendFileSync(
		// 	logFileName,
		// 	[ index, unit, basicCost, responseTime/*, calculationTime*/ ].join( "," ) + "\n"
		// )
		console.log( index )
		console.log( parsedResponse );
		index += 1
	}
}
