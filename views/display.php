<?php if (isset($error)): ?>
	<pre>Twitter API error #<?=$code?>:

"<?=$error?>"</pre>
<?php else: ?>
	<ul class="rss">
		<?php foreach($tweets as $tweet): ?>
		<li>
			<?php echo $tweet->text; ?>
			<p class="date"><?php echo anchor('https://twitter.com/' . $screen_name . '/status/' . $tweet->id_str, format_date($tweet->created_at, Settings::get('date_format') . ' h:i'), 'target="_blank"'); ?></p>
		</li>
		<?php endforeach; ?>
	</ul>
<?php endif ?>
