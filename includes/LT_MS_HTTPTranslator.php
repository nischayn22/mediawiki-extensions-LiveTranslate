<?php

/**
 * Handler class for Access Token based Authentication for Microsoft Translation Service.
 * Borrowed from http://blogs.msdn.com/b/translation/p/phptranslator.aspx
 *
 * @since 1.3
 *
 * @file LT_MS_HTTPTranslator.php
 * @ingroup LiveTranslate
 *
 * @licence GNU GPL v3
 * @author Nischay Nahata < nischayn22@gmail.com >
 */
Class LTMSHTTPTranslator {

	public static function getAppId() {

		if( $GLOBALS['egLiveTranslateMSAppId'] != '' ) {
			return $GLOBALS['egLiveTranslateMSAppId'];
		}

		//Client ID of the application.
		$clientID       = $GLOBALS['egLiveTranslateMSClientId'];
		//Client Secret key of the application.
		$clientSecret = $GLOBALS['egLiveTranslateMSClientSecret'];
		//OAuth Url.
		$authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
		//Application Scope Url
		$scopeUrl     = "http://api.microsofttranslator.com";
		//Application grant type
		$grantType    = "client_credentials";

		//Get the Access token.
		$accessToken  = self::getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);

		return "Bearer $accessToken";
	}

	/*
	 * Get the access token.
	 *
	 * @param string $grantType    Grant type.
	 * @param string $scopeUrl     Application Scope URL.
	 * @param string $clientID     Application client ID.
	 * @param string $clientSecret Application client ID.
	 * @param string $authUrl      Oauth Url.
	 *
	 * @return string.
	 */
	public static function getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl){
		try {
			//Initialize the Curl Session.
			$ch = curl_init();
			//Create the request Array.
			$paramArr = array (
				'grant_type'    => $grantType,
				'scope'         => $scopeUrl,
				'client_id'     => $clientID,
				'client_secret' => $clientSecret
			);
			//Create an Http Query.//
			$paramArr = http_build_query($paramArr);
			//Set the Curl URL.
			curl_setopt($ch, CURLOPT_URL, $authUrl);
			//Set HTTP POST Request.
			curl_setopt($ch, CURLOPT_POST, TRUE);
			//Set data to POST in HTTP "POST" Operation.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
			//CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
			//CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			//Execute the  cURL session.
			$strResponse = curl_exec($ch);
			//Get the Error Code returned by Curl.
			$curlErrno = curl_errno($ch);
			if($curlErrno){
				$curlError = curl_error($ch);
				throw new Exception($curlError);
			}
			//Close the Curl Session.
			curl_close($ch);
			//Decode the returned JSON string.
			$objResponse = json_decode($strResponse);
			if( property_exists( $objResponse, 'error' ) ) {
				throw new Exception($objResponse->error_description);
			}
			return $objResponse->access_token;
		} catch (Exception $e) {
			echo "Exception-".$e->getMessage();
		}
	}
}