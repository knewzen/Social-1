## Social


Consume multiple API's from social networks easily! Essentially,
Social handles most of the confusing and hard work for you so you can begin your API calls sooner.


  Social Networks Suported
----------------------------

> Facebook's SDK v4

For now, only Facebook is being supported. Serveral API's will be supported in the future. Currently uses the Facebook\FacebookRedirectLoginHelper for sessions. 

#Installation:

	 -Make sure PHP 5.4.0 is installed
	 -Download Composer <https://getcomposer.org/> and make a global installation.
	 -In your terminal / command prompt run the "composer install" command inside your local copy of social

#Use:

Beginning with Social is not difficult. A better understanding of the node and endpoint structures of the supported API's in question can be helpful.

1) Enter valid app id's and app secrets in the Networks\config.ini file
2) Make sure a session exists or start one
3) Create a Social object by simply typing

		$social   = Social\Social::init();

4) Specify which social network / API you want. (Only Facebook is available now).

		$facebook = $social->network('facebook');

Note: A SocialException is thron Upon failure or if the network requested is invalid.

5) 	Use the "request" method to send your API calls. A strict minimum of pre-written API calls or requests will ever be created per API. Only isSignedIn, getLoginURL and getLogoutURl methods are available for Facebook and the same as Facebook's.

		// Yay, You got a Graph object so quickly!
		$go = $facebook->request('/me');

#Simple examples:

**Creating an instance of an available network**

	...

	// Facebook require sessions
	session_start();

	use Social\SocialException;

	$social   = Social\Social::init();
	
	try{
		$facebook = $social->network('facebook');
	}catch(SocialException $ex){
		// ...

**Show sign-in link if user is not signed-in**

	// callback url must respect same Facebook app settings
	if (!$facebook->isSignedIn("http://yourcallbackurl.com/"))
	{
		echo "Login Facebook <a href='{$facebook->getLoginURL()}' title='Login Facebook'>here</a>.";
	}else{
		// already signed-in
		$me = $facebook->request("/me");
		
		//... 

**Creating a request (Facebook vs Social)**

Facebook: example as shown at <https://developers.facebook.com/docs/php/howto/postwithgraphapi/4.0.0>

	try {
		$response = (new FacebookRequest(
			$session, 'POST', '/me/feed', array(
		    'link' => 'www.example.com',
		    'message' => 'User provided message'
	)
	))->execute()->getGraphObject();

Social:

	$post = array();
    $post['link'] => 'www.example.com';
    $post['message'] => 'User provided message';
    
	$response = $facebook->request("/me/feed", $post);

	// or 

	$response = $facebook->request("/me/feed", array('link' => 'www.example.com', 'message' => 'User provided message'));

That's a whole lot simpler Wasn't? Not much strain on the eyes too!
