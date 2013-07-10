pyrocms-twitter-widget
======================

Twitter Widget for PyroCMS with support for Twitter API 1.1. This widget has been tested under PyroCMS community / professional 2.1 and 2.2.

Licensed under the DBAD license (http://www.dbad-license.org).

### Installation

To install in PyroCMS, clone the repository:

	cd addons/shared_addons/widgets
	git clone git@github.com:webcomm/pyrocms-twitter-widget.git twitter_oauth

Or add it as a submodule:

	git add submodule git@github.com:webcomm/pyrocms-twitter-widget.git addons/shared_addons/widgets/twitter_oauth

### OAuth Authentication & Applications

C'mon, it's not as scary as it sounds. Recently, Twitter [changed their API](https://dev.twitter.com/blog/api-v1-is-retired) by requiring all requests to be authenticated through OAuth, rather than an unauthenticated HTTP request.

You will need to do the following:

1. Visit http://dev.twitter.com and login with your Twitter credentials.
2. Hover over your icon (top-right), click on **My Applications**.
3. Click **Create a new application** and fill out the appropriate credentials. A callback URL is not necessary.
4. Once your application is created, you will be able to scroll down and click **Create my access token**. Follow through the procedure.
5. Great, make note of the **consumer key**, **consumer secret**, **access token** and **access token secret**.

### Usage

Once you have created an application, you can proceed to use the widget as normal. You'll need to use the 4 hashes we made note of in the above section when creating a widget, so that it can talk to Twitter accordingly.

If something goes wrong, your widget will output the error code & message from Twitter, to make debugging easier.

### Roadmap

I plan on adding a site-wide setting for keys, tokens & secrets with the ability to specify per-widget. PRs welcome!

### Credit

Credit for the majority of this widget goes @jHoldroyd and [his awesome PR](pyrocms/pyrocms#2822).
