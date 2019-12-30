<?php





class CRM {

	public static $apiUrl = 'https://www.zohoapis.com/crm/v2/';
	// public static $apiUrl = 'https://sandbox.zohoapis.com/crm/v2/';
	public static $authCredentials;
	public static $operatorRelationMap = [
		'=' => 'equals',
		'^=' => 'starts_with'
	];



	public static function getAccessToken ( $credentialsFilename ) {
		if ( empty( realpath( $credentialsFilename ) ) )
			sleep( 1 );
		self::$authCredentials = json_decode( file_get_contents( $credentialsFilename ), true );
		return self::$authCredentials;
	}



	/*
	 * -----
	 * Criterion stringifier
	 * -----
	 */
	public static function getStringifiedCriterion ( $name, $relation__value ) {

		if ( empty( $relation__value ) ) {
			$criteriaString = '';
		}
		else if ( is_array( $relation__value ) ) {
			$operator = $relation__value[ 0 ];
			$value = $relation__value[ 1 ];
			$criteriaString = '(' . $name . ':' . self::$operatorRelationMap[ $operator ] . ':';
			// If the value has spaces, then urlencode it, else don't
			if ( preg_match( '/\s/', $value ) === 1 )
				$criteriaString .= urlencode( $value ) . ')';
			else
				$criteriaString .= $value . ')';
		}
		else {
			$value = $relation__value;
			$criteriaString = '(' . $name . ':equals:';
			// If the value has spaces, then urlencode it, else don't
			if ( preg_match( '/\s/', $value ) === 1 )
				$criteriaString .= urlencode( $value ) . ')';
			else
				$criteriaString .= $value . ')';
		}

		return $criteriaString;

	}
	/*
	 * -----
	 * Criteria resolver
	 * -----
	 */
	public static function getResolvedCriteria ( $criteria ) {

		$name = array_keys( $criteria )[ 0 ];

		if ( in_array( $name, [ 'or', 'and' ] ) ) {
			$operator = $name;
			$subCriteria = $criteria[ $operator ];
			$subCriteriaStrings = array_map( function ( $name, $value ) {
				return self::getResolvedCriteria( [ $name => $value ] );
			}, array_keys( $subCriteria ), array_values( $subCriteria ) );
			return '(' . implode( $operator, $subCriteriaStrings ) . ')';
		}
		else {
			return self::getStringifiedCriterion(
				array_keys( $criteria )[ 0 ],
				array_values( $criteria )[ 0 ]
			);
		}

	}



	/*
	 * -----
	 * Get a record with the given id
	 * -----
	 */
	public static function getRecordById ( $type, $id ) {

		$endpoint = self::$apiUrl . $type . '/' . $id;

		$responseBody = self::httpRequest( $endpoint, 'GET' );

		if ( ! isset( $responseBody[ 'data' ] ) )
			return null;

		$record = $responseBody[ 'data' ][ 0 ];
		$record[ 'recordType' ] = $type;

		return $record;

	}



	public static function getRecordWhere ( $recordType, $criteria = [ ] ) {

		$baseURL = self::$apiUrl . $recordType . '/search';
		$criteriaString = '?criteria=(' . self::getResolvedCriteria( $criteria ) . ')';
		$endpoint = $baseURL . $criteriaString;

		$responseBody = self::httpRequest( $endpoint );

		// If no record was found
		if ( empty( $responseBody ) || empty( $responseBody[ 'data' ] ) )
			return null;

		// If more than one record was found
		// if ( $responseBody[ 'info' ][ 'count' ] > 1 ) {
		// 	$errorMessage = 'More than one ' . $recordType . ' found with the given criteria; ' . json_encode( $criteria ) . '.';
		// 	throw new \Exception( $errorMessage, 4002 );
		// }

		$record = array_filter( $responseBody[ 'data' ][ 0 ] );
		$record[ 'recordType' ] = $recordType;

		return $record;

	}



	/*
	 * -----
	 * Update a record with a given id
	 * -----
	 */
	public static function updateRecord ( $type, $id, $data ) {

		$endpoint = self::$apiUrl . $type . '/' . $id;

		$responseRaw = self::httpRequest( $endpoint, 'PUT', [
			'data' => [ $data ],
			'trigger' => [ 'approval', 'workflow', 'blueprint' ]
		] );

		if ( ! isset( $responseRaw[ 'data' ] ) )
			throw new \Exception( 'Response from update operation was empty.', 4002 );

		$response = $responseRaw[ 'data' ][ 0 ];

		if ( strtolower( $response[ 'code' ] ) != 'success' ) {
			$errorMessage = 'The update operation was not successful.'
							. PHP_EOL . $response[ 'message' ];
			throw new \Exception( $errorMessage, 4003 );
		}

		return $response[ 'details' ];

	}



	public static function httpRequest ( $endpoint, $method = 'GET', $data = [ ] ) {

		$request = curl_init();
		curl_setopt( $request, CURLOPT_URL, $endpoint );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $request, CURLOPT_USERAGENT, 'Zo Ho Ho' );
		$headers = [
			'Authorization: Zoho-oauthtoken ' . self::$authCredentials[ 'access_token' ],
			'Cache-Control: no-cache, no-store, must-revalidate'
		];
		if ( ! empty( $data ) ) {
			if ( is_array( $data ) ) {
				$headers[ ] = 'Content-Type: application/json';
				$requestBody = json_encode( $data );
			}
			else if ( is_string( $data ) ) {
				$isURL = preg_match( '/^https?:\/\//', $data );
				if ( ! $isURL ) {
					if ( $data[ 0 ] == '/' )
						$resourcePath = $data;
					else {
						$basePath = $_SERVER[ 'DOCUMENT_ROOT' ] ?: __DIR__;
						$resourcePath = $basePath . '/' . $data;
					}
				}
				if ( ! $isURL ) {
					$mimeType = mime_content_type( $resourcePath ) ?? 'application/octet-stream';
					$requestBody = [
						'file' => new CurlFile( $resourcePath, $mimeType )
					];
				}
				else
					$requestBody = [
						'attachmentUrl' => $resourcePath
					];

			}

			curl_setopt( $request, CURLOPT_POSTFIELDS, $requestBody );
		}
		curl_setopt( $request, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $request, CURLOPT_CUSTOMREQUEST, $method );
		$response = curl_exec( $request );
		curl_close( $request );

		$body = json_decode( $response, true );

		if ( empty( $body ) )
			return [ ];
			// throw new \Exception( 'Response is empty.', 10 );

		// If an error occurred
		if ( ! empty( $body[ 'code' ] ) ) {
			if ( $body[ 'code' ] == 'INVALID_TOKEN' )
				throw new \Exception( 'Access token is invalid.', 5001 );
			if ( $body[ 'code' ] == 'AUTHENTICATION_FAILURE' )
				throw new \Exception( 'Failure in authenticating.', 5002 );
		}

		return $body;

	}



	/*
	 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
	 *  CUSTOMERS
	 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
	 */
	/*
	 * -----
	 * Creates a customer record with the given data
	 * -----
	 */
	function createLead ( $data ) {

		$endpoint = self::$apiUrl . 'Leads';

		$requestBody = [
			'data' => [
				[
					'Project' => [ $data[ 'project' ] ],	// because it's a list
					'Phone' => $data[ 'phoneNumber' ],
					'Email' => $data[ 'email' ] ?? '',
					'First_Name' => $data[ 'firstName' ],
					'Last_Name' => $data[ 'lastName' ],
					'Lead_Source' => $data[ 'context' ],
					'Lead_Status' => 'Fresh'
				]
			],
			'trigger' => [
				'approval',
				'workflow',
				'blueprint'
			]
		];
		if ( $data[ 'ownerId' ] )
			$requestBody[ 'data' ][ 0 ][ 'Owner' ] = $data[ 'ownerId' ];

		$responseBody = self::httpRequest( $endpoint, 'POST', $requestBody );

		if ( empty( $responseBody ) )
			return [ ];

		// Pull out the (internal) id of the newly created customer
		$responseBody = array_filter( $responseBody[ 'data' ][ 0 ] );
		$recordId = trim( $responseBody[ 'details' ][ 'id' ] );

		// // Now, get the `Hidden UID` value
		// $customer = self::getCustomerById( $recordId );
		// $uid = trim( $customer[ 'Hidden_UID' ] );

		// Return the record Id and the UID
		return [
			'_id' => $recordId,	// This now has to be kept for ThinkMobi
			// 'uid' => $uid
		];

	}



	public static function getCustomerById ( $id ) {

		$customer = self::getRecordById( 'Leads', $id );
		if ( empty( $customer ) ) {
			$customer = self::getRecordById( 'Contacts', $id );
			if ( ! empty( $customer ) )
				$customer[ 'isProspect' ] = true;
		}

		return $customer;

	}



	public static function getCustomerByPhoneNumber ( $phoneNumber ) {

		$customer = self::getRecordWhere( 'Leads', [
			// 'and' => [
			// 	'Project' => [ '^=', $client ],
				'or' => [
					'Phone' => $phoneNumber,
					'Secondary_Phone' => $phoneNumber
				]
			// ]
		] );
		if ( empty( $customer ) ) {
			$customer = self::getRecordWhere( 'Contacts', [
				// 'and' => [
				// 	'Project' => [ '^=', $client ],
					'or' => [
						'Phone' => $phoneNumber,
						'Secondary_Phone' => $phoneNumber
					]
				// ]
			] );
			if ( ! empty( $customer ) )
				if ( $customer[ 'recordType' ] == 'Contacts' )
					$customer[ 'isProspect' ] = true;
		}

		return $customer;

	}



	function updateCustomer ( $recordType, $id, $data ) {
		return self::updateRecord( $recordType, $id, $data );
	}



	/*
	 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
	 *  QUOTES
	 * -/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-
	 */
	public static function createQuote ( $customer, $quote ) {

		$validFor = date( 'Y-m-d', strtotime( '+ ' . $quote[ 'validFor' ] ) );

		$endpoint = self::$apiUrl . 'Deals';

		$requestBody = [
			'data' => [
				[
					// 'SMOWNERID' => $customer[ 'SMOWNERID' ],
					'Owner' => $customer[ 'owner' ],
					'CONTACTID' => $customer[ '_id' ],
					'Amount' => $quote[ 'amount' ],
					'Deal_Name' => $quote[ 'name' ],
					'Closing_Date' => $validFor,
					'Stage' => 'Quote Generated',
					'Email' => $customer[ 'email' ]
				]
			],
			'trigger' => [
				'approval',
				'workflow',
				'blueprint'
			]
		];

		$responseBody = self::httpRequest( $endpoint, 'POST', $requestBody );

		if ( empty( $responseBody ) )
			return [ ];

		// Pull out the (internal) id of the newly created customer
		$responseBody = array_filter( $responseBody[ 'data' ][ 0 ] );
		$recordId = trim( $responseBody[ 'details' ][ 'id' ] );

		// Return the record Id
		return $recordId;

	}



	public static function uploadAttachment ( $recordType, $recordId, $resourcePath ) {

		$endpoint = self::$apiUrl . $recordType . '/' . $recordId . '/Attachments';

		$responseBody = self::httpRequest( $endpoint, 'POST', $resourcePath );

		return $responseBody;

	}

}




// Get the access token
$authCredentialsFilename = __DIR__ . '/../environment/configuration/zoho.json';
CRM::getAccessToken( $authCredentialsFilename );
