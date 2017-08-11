=== FlashPoll++ ===
Contributors: atteromedia
Tags: flashpoll++, flash poll, poll, polling, poll builder, polls, vote, survey
Requires at least: 2.0
Stable tag: 1.2
Tested up to: 2.9

FlashPoll++ is a Flash based poll builder with a lot of customization options.

== Description ==

FlashPoll++ is a Flash based poll builder with a lot of customization options. <br />
It allows you to display multiple polls, one after another, also view a full voting log from admin panel.<br />

You can customize the size, fonts, colors and more!

Check out the demo here: <br />
<a href="http://www.flash-poll.com/">http://www.flash-poll.com</a>



== Installation ==

Download the archive, then unzip to your plugins directory and activate. <br />
Then from the new FlashPoll++ menu in admin, add some polls and adjust settings as you like.

To display the polls on your site, you could use the widget or another way is to place the following code somewhere in your template files:

`<?php if (function_exists('fppp_display_poll')) fppp_display_poll(); ?>`

It's also possible to use the shortcode [fppp_display_poll] within your posts.


That's it, enjoy!


== Not Working? ==

If you see a blank screen and not the actual poll, you can try the following: <br />
* open the file /wp-content/plugins/flashpoll/amfphp/gateway.php <br />
* on line 128 you should see this:  <br />
`$gateway->setCharsetHandler("iconv", "utf8", "utf8");` <br />
* delete that line, or comment it like that:  <br />
`//$gateway->setCharsetHandler("iconv", "utf8", "utf8");` <br />

It should be working now. <br />
Big thanks to Jean Lyon for helping with that!


If you encounter any other problems, please post a comment in <a href="http://blog.atteromedia.com/flash-based-voting-system">our blog</a> and I'll see what I can do.


== Changelog ==

= 1.2 =
* Added shortcode support

= 1.1 =
* Added a widget version
* Editing a poll question now works properly
* Fixed the 'SWFObject is not defined' error