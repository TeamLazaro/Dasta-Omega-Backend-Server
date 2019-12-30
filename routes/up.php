<?php


$endpoint = 'http://localhost:9996/';
$method = 'POST';
$data = __DIR__ . '/../routes/beru.pdf';

$httpRequest = curl_init();
curl_setopt( $httpRequest, CURLOPT_URL, $endpoint );
curl_setopt( $httpRequest, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $httpRequest, CURLOPT_USERAGENT, 'Zo Ho Ho' );
$headers = [
	'Authorization: Zoho-oauthtoken ' . 'haha',
	'Cache-Control: no-cache, no-store, must-revalidate'
];
if ( ! empty( $data ) ) {
	if ( is_array( $data ) ) {
		$headers[ ] = 'Content-Type: application/json';
		$requestBody = json_encode( $data );
	}
	else if ( is_string( $data ) )
		$requestBody = [
			'file' => new CurlFile( $data, mime_content_type( $data ) )
		];

	curl_setopt( $httpRequest, CURLOPT_POSTFIELDS, $requestBody );
}
curl_setopt( $httpRequest, CURLOPT_HTTPHEADER, $headers );
curl_setopt( $httpRequest, CURLOPT_CUSTOMREQUEST, $method );
$response = curl_exec( $httpRequest );
curl_close( $httpRequest );

$body = json_decode( $response, true );



var_dump( $response );
var_dump( $body );
