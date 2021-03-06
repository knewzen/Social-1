<?php 

	/* Copyright (c) 2014 Karl-Edward F P Jean-Mehu
		
	* 	Permission is hereby granted, free of charge, to any person obtaining a copy
	* 	of this software and associated documentation files (the "Software"), to deal
	* 	in the Software without restriction, including without limitation the rights
	* 	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	* 	copies of the Software, and to permit persons to whom the Software is
	* 	furnished to do so, subject to the following conditions:
		
	* 	The above copyright notice and this permission notice shall be included in all
	* 	copies or substantial portions of the Software.
		
	* 	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	* 	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	* 	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	* 	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	* 	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	* 	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	* 	SOFTWARE.
	*/

	namespace Social\Networks;

	// required facebook namespaces
	use Facebook\GraphObject;
	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookRedirectLoginHelper;

	class SocFacebook implements \Social\iSocialNetwork{
	private static $_config;
	private static $_session;
	private static $_loginHelper;
	public static $graph_api_ver = 2.0;

	/**
	 * constructor
	 *
	 * throws SocialNetworkException in case of an error
	 * 
	 * @author Karl-Edward F P Jean-Mehu <karlej99@yahoo.com> 
	 * @throws SocialNetworkException
	 */
	public function __construct(array $config)
	{
		self::$_config = $config;

		// throw exception if session not exist
		if (session_status() == PHP_SESSION_NONE) throw new \Social\SocialNetworkException('Facebook requires sessions! Please be more... "Social"!');

		// check for valid app id
		if(!isset(self::$_config['app_id']) or !is_string(self::$_config['app_id']) or empty(self::$_config['app_id']))
			throw new \Social\SocialNetworkException('Missing or invalid app id for Facebook config');

		// check for valid app secret
		if(!isset(self::$_config['app_secret']) or !is_string(self::$_config['app_secret']) or empty(self::$_config['app_secret']))
			throw new \Social\SocialNetworkException('Missing or invalid app secret for Facebook config');		

		// set appid and app secret of Facebook app
		FacebookSession::setDefaultApplication(self::$_config['app_id'], self::$_config['app_secret']);
	}

	/**
	 * checks whether user has signed in
	 * throws FacebookRequestException or SocialNetworkException exception
	 * 
	 * @param  string  $redirectURL URL user must be redirected to
	 * @return boolean			 	returns true if signed in else
	 * @throws SocialNetworkException
	 */
	public function isSignedIn($redirectURL = null)
	{
		$redirectURL = (isset($redirectURL))? $redirectURL : self::$_config['redirectURL'];
		self::$_loginHelper = new FacebookRedirectLoginHelper($redirectURL);
		
		$return = false;

		try {
		  self::$_session = self::$_loginHelper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
			throw new \Social\SocialNetworkException($ex->getMessage());
		} catch(FacebookSDKException $ex){
			throw new \Social\SocialNetworkException($ex->getMessage());
		}

		if (self::$_session) {
		  $return = true;
		}

		return $return;
	}

	/**
	 * returns Facebook fully qualified LoginURL
	 * @return string login FQURL
	 */
	public function getLoginURL()
	{
		return self::$_loginHelper->getLoginURL();
	}

	/**
	 * executes HTTP GET / POST Facebook GraphAPI command
	 * @param  string $request Facebook Graph node request
	 * @param  array $post_ar (optional) node/endpoint 
	 * @return GraphObject          
	 * @throws SocialNetworkException
	 */
	public function request($request, array $post_ar = null)
	{		
		try {

			$http_req_type = (empty($post_ar))? 'GET' : 'POST';

			$request  = new FacebookRequest(self::$_session, $http_req_type, $request);

			$graphObj = $request->execute()->getGraphObject();

			return (is_array($graphObj))? $graphObj[0] : $graphObj;
		} catch (FacebookRequestException  $e) {
			throw new \Social\SocialNetworkException("Facebook Exception code: ". $e->getCode() ." message: ". $e->getMessage());
		}
	}

}
	
?>