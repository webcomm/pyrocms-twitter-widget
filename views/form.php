<ol>
	<li class="even">
		<label>Username</label>
		<?php echo form_input('screen_name', $options['screen_name']); ?>
	</li>
	<li class="even">
		<label>Consumer Key</label>
		<?php echo form_input('consumer_key', $options['consumer_key']); ?>
	</li>
	<li class="even">
		<label>Consumer Secret</label>
		<?php echo form_input('consumer_secret', $options['consumer_secret']); ?>
	</li>
	<li class="even">
		<label>Access Token</label>
		<?php echo form_input('access_token', $options['access_token']); ?>
	</li>
	<li class="even">
		<label>Access Token Secret</label>
		<?php echo form_input('access_token_secret', $options['access_token_secret']); ?>
	</li>
	<li class="even">
		<label>Number of tweets</label>
		<?php echo form_input('count', $options['count']); ?>
	</li>
</ol>
