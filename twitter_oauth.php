<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * DON'T BE A DICK PUBLIC LICENSE
 *
 * Version 1, December 2009
 *
 * Copyright (C) 2013 Webcomm Pty Ltd <contact@webcomm.com.au>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 * DON'T BE A DICK PUBLIC LICENSE
 * TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 * 1. Do whatever you like with the original work, just don't be a dick.
 *
 *    Being a dick includes - but is not limited to - the following instances:
 *    1a. Outright copyright infringement - Don't just copy this and change the name.
 *    1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
 *    1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.
 *
 * 2. If you become rich through modifications, related works/services, or supporting the original work,
 *    share the love. Only a dick would make loads off this work and not buy the original works
 *    creator(s) a pint.
 *
 * 3. Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes
 *    you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
 *
 * @package    Twitter Widget
 * @version    1.0.0
 * @author     Webcomm Pty Ltd
 * @license    DBAD
 * @copyright  (c) 2013, Webcomm Pty Ltd
 * @link       http://www.webcomm.com.au
 */

class Widget_Twitter_oauth extends Widgets
{
	public $title = array(
		'en' => 'Twitter Feed (1.1 API)',
	);

	public $description = array(
		'en' => 'Display Twitter feeds on your website, with support for Twitter\'s 1.1 API',
	);

	public $author      = 'Webcomm';

	public $website     = 'http://www.webcomm.com.au/';

	public $version     = '1.0';

	public $fields = array(
		array(
			'field' => 'screen_name',
			'label' => 'Username',
			'rules' => 'required'
		),
		array(
			'field' => 'consumer_key',
			'label' => 'Consumer Key',
			'rules' => 'required',
		),
		array(
			'field' => 'consumer_secret',
			'label' => 'Consumer Secret',
			'rules' => 'required',
		),
		array(
			'field' => 'access_token',
			'label' => 'Access Token',
			'rules' => 'required',
		),
		array(
			'field' => 'access_token_secret',
			'label' => 'Access Token',
			'rules' => 'required',
		),
		array(
			'field' => 'count',
			'label' => 'Number of tweets',
			'rules' => 'numeric'
		),
	);

	public function run($options)
	{
		$cache_key = 'twitter-'.'-'.md5(serialize($options));

		if ( ! $tweets = $this->pyrocache->get($cache_key))
		{
			try
			{
				$tweets = $this->fetch_tweets($options);
			}
			catch (\Exception $e)
			{
				return array(
					'error' => $e->getMessage(),
					'code'  => $e->getCode(),
				);
			}

			$this->pyrocache->write($tweets, $cache_key, 300);
		}

		$patterns = array(
			// Detect URL's
			'((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)' => '<a href="$0" target="_blank">$0</a>',
			// Detect Email
			'|[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,6}|i' => '<a href="mailto:$0">$0</a>',
			// Detect Twitter @screen_names
			'|@([a-z0-9-_]+)|i' => '<a href="http://twitter.com/$1" target="_blank">$0</a>',
			// Detect Twitter #tags
			'|#([a-z0-9-_]+)|i' => '<a href="http://twitter.com/search?q=%23$1" target="_blank">$0</a>'
		);

		foreach ($tweets as &$tweet)
		{
			$tweet->text    = str_replace($options['screen_name'] . ': ', '', $tweet->text);
			$tweet->text    = preg_replace(array_keys($patterns), $patterns, $tweet->text);
		}

		// Store the feed items
		return array(
			'screen_name'  => $options['screen_name'],
			'tweets'    => $tweets
		);
	}

	protected function fetch_tweets($options)
	{
		// Variables
		$host   = 'api.twitter.com';
		$method = 'GET';
		$path   = '/1.1/statuses/user_timeline.json';
		$oauth  = array(
			'oauth_consumer_key'     => $options['consumer_key'],
			'oauth_token'            => $options['access_token'],
			'oauth_nonce'            => (string)mt_rand(),
			'oauth_timestamp'        => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_version'          => '1.0'
		);

		unset($options['consumer_key']);
		unset($options['access_token']);
		$consumer_secret = $options['consumer_secret'];
		unset($options['consumer_secret']);
		$access_token_secret = $options['access_token_secret'];
		unset($options['access_token_secret']);

		// Encode and merge perams
		$options  = array_map('rawurlencode', $options);
		$params = array_merge($oauth, $options);

		// Sort the params
		asort($params);
		ksort($params);

		// Build request headers
		$query = urldecode(http_build_query($params, '', '&'));
		$url   = 'https://'.$host.$path;
		$base  = $method.'&'.rawurlencode($url).'&'.rawurlencode($query);
		$key   = rawurlencode($consumer_secret).'&'.rawurlencode($access_token_secret);
		$sign  = rawurlencode(base64_encode(hash_hmac('sha1', $base, $key, true)));

		// Build and format URL
		$url .= '?'.http_build_query($options);
		$url  = str_replace('&amp;', '&', $url);

		// Assign the signature
		$oauth['oauth_signature'] = $sign;
		ksort($oauth);

		// Twitter demo does this, so just incase
		function add_quotes($str) { return '"'.$str.'"'; }
		$oauth = array_map("add_quotes", $oauth);

		// Setup CURL
		$feed = curl_init();
		curl_setopt_array($feed, array(
			CURLOPT_HTTPHEADER     => array('Authorization: OAuth '.urldecode(http_build_query($oauth, '', ', '))),
			CURLOPT_HEADER         => false,
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		));

		// Make request
		$json = curl_exec($feed);

		$http_status = curl_getinfo($feed, CURLINFO_HTTP_CODE);
		curl_close($feed);

		// Decode and return
		$data = json_decode($json);

		if ($http_status != 200)
		{
			$error = reset($data->errors);
			throw new \Exception($error->message, $error->code);
		}

		return $data;
	}

}
