=== PDF & Print Button Joliprint ===
Contributors: joliprint
Tags:  pdf, print, button, printable, joliprint
Requires at least: 2.7.0
Tested up to: 3.3.1
Stable tag: 1.3.2

The Joliprint button prints your posts into sharp looking PDF.

== Description ==

= IMPORTANT NOTICE :: Joliprint will shut down on January 4, 2013 =
We're sorry to announce that Joliprint will be stopped on January 4, 2013. After that date, you will no longer have access to the service by any channel (Joliprint webpage, browser bookmarklets, wordpress plugins, ...).
More infos on http://joliprint.com/2012/12/11/joliprint-is-shutting-down/

Once installed, the plugin generates a button on each post and page. So your readers can save, share or even print a nice formatted PDF from each post or page.

The Joliprint Settings Page offers a number of options: 

*	Choose the button and its locations
*	Customize your PDF Header
*	Set up a Google Analytics campaign, track PDF downloads and clicks inside PDF.  

Learn more, and take Joliprint for a test drive. Check out [joliprint.com](http://joliprint.com "Joliprint button").

IMPORTANT NOTE : Our complete privacy policy is being drafted and will be published very soon. By then, remember that Joliprint does not store any personnal data on server side. The Joliprint servers log calls to our buttons in order to have some statistics on the use of the plugin.

== Installation ==
1. Download the `joliprint.zip` file and unzip it.
2. Move the resulting folder into `wp-content/plugins` folder your wordpress install
3. Activate the plugin in your WordPress Admin area.
4. Choose your button style, your layout settings in the plugin settings area.
5. Optionnaly you may specify the joliprint server you wish to work with depending of the location of your Wordpress installation.

== Support ==
If you have any issues with the plugin or the Joliprint widget, please write to support@joliprint.com

== Screenshots ==
1. The Joliprint widget layout your text inside a professional multi-column design, and render a crisp PDF for your readers.
2. Choose your button, text link, or use your own text or graphic.

== Frequently Asked Questions ==
= How Joliprint handles my PDF files ? =
Joliprint is keeping your PDF files on the Joliprint servers for about 12 hours. The Joliprint plugin will update the PDF cache when change your options in the `PDF Layout` section of the Joliprint settings or when you click on the `Click here to reset the cache now` link in the `Technical Options` section. When this link is clicked your cache files are erased from the server at the moment you see the message `PDF cache erased`.

= I want to place the Joliprint button myself, where do I need to paste the Joliprint PHP code in my Wordpress templates ? =

1. Check the Custom position radio button in the Button section of the Joliprint Options panel.
2. Copy the following PHP code `<?php if ( function_exists( 'joliprint_show_the_button' ) ) echo joliprint_show_the_button(); ?>`
3. This PHP code must be pasted in the [Wordpress Loop](http://codex.wordpress.org/The_Loop) of your templates files.
4. Open and modify your templates files :

* **index.php** if you want to place a button on each entry on your homepage,
* **single.php** if you want to place a button on your posts details,
* **page.php** if you want to display a button on your Wordpress pages.

**Example**

If you want to place the Joliprint button on your posts details in the `entry-meta` block right after the `posted on` infos (default Twenty Ten WP template)

1. Open the `single.php` page of your Twenty Ten theme
2. Locate the the [Wordpress Loop](http://codex.wordpress.org/The_Loop) (the loop starts here `<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>`)
3. Locate the `entry-meta` block `<div class="entry-meta"><?php twentyten_posted_on(); ?></div><!-- .entry-meta -->`
4. Modify the `entry-meta` block as following `<div class="entry-meta"><?php twentyten_posted_on(); ?><?php if ( function_exists( 'joliprint_show_the_button' ) ) echo joliprint_show_the_button(); ?></div><!-- .entry-meta -->`
5. Save your template file

** Important **
In automatic mode, the Joliprint plugin will only show the Joliprint button on post and pages.
In manual mode, the plugin send to Joliprint servers the permalink of the page your user wants to print. This works pretty well for posts and pages objects but cannot works correctly with customs objects (you may find some of these in some 3rd party plugins). In such cases, when the plugin detects that it is not on a post or a page it will try to send the current URL of the server. Sometimes this method will not work either and you will need to specify yourself the URL the plugin need to send to Joliprint by passing the real URL to the plugin thanks to the `joliprint_show_the_button($THE_REAL_URL)` where $THE_REAL_URL is the URL of the page you want to print out.

== Upgrade Notice ==
= 1.2.2 to 1.2.3 =
Nothing speacial.
= 1.2.1 to 1.2.2 =
Nothing special.
= 1.2.0 to 1.2.1 =
Nothing special.
= 1.1.5 to 1.2.0 =
Nothing special.
= 1.1.4 to 1.1.5 =
Nothing special.
= 1.1.3 to 1.1.4 =
Nothing special.
= 1.1.2 to 1.1.3 =
Nothing special.
= 1.1.1 to 1.1.2 =
Nothing special.
= 1.1.0 to 1.1.1 =
Nothing special.
= 1.0.1 to 1.1.0 =
Nothing special.
= 1.0.0 to 1.0.1 =
Nothing special.


== Changelog ==

= 1.3.2 = 

* NOTICE : Joliprint is shutting down. More infos on http://joliprint.com/2012/12/11/joliprint-is-shutting-down/

= 1.3.1 = 
* FIXED		: Minor security fixes in joliprint_admin_options.php and joliprint_options_upload.php

= 1.3.0 = 
* FIXED		: fixed HTTPS/SSL issues
* CHANGED	: wp-joliprint.js, alert has been removed when jQuery is missing in the Administration Panel
* CHANGED	: Zendesk is not used anymore, all references to zendesk have been removed
* CHANGED	: European servers have been removed from available server options.

= 1.2.2 =
* CHANGED : wp-joliprint.js is now minified

= 1.2.1 =
* BUGFIX : In some rare cases, when jQuery was loaded twice (or more) in some Wordpress themes the Joliprint jQuery's plugin was not loaded correctly and Joliprint buttons were not shown. This behaviour should not happen anymore and Joliprint buttons should be shown correctly in such themes.

= 1.2.0 =
* NEW : New PDF sharing options are now available for your users in the Joliprint popin option page.
* NEW : If you are using Google Analytics on your blog Joliprint will feed GA with Printing Tracking Events (category Joliprint > Print PDF in you events tracker in your GA dashboard). You will need to have a Google Analytics plugin running on your blog. Joliprint doesn't load GA itself.
* NEW : Download of the Joliprint button CSS stylesheets can now be disabled in the administration panel (click Skip default Joliprint stylesheet if you want to disable automatic Joliprint CSS styling). You may find a CSS example here `http://api.joliprint.com/joliprint/css/joliprint.css` if you want to make your own.
* NEW : Joliprint button can now be disabled in posts/page. Simple add `<!--nojoliprint-->` in your content to disable the Joliprint button on the post/page.
* NEW : You can now place your button thanks to a short HTML code. This can be useful to add a Joliprint button inside a widget. Add `<span class='joliprint_button' data-url='[url]' data-title='[title]'></span>` where you want your button to appear. Replace [url] with the permalink of the page and [title] with the title of the post/page. You can also try to let data-url and data-title blanks, the Joliprint plugin will try to find these parameters by itself if possible (not recommanded).
* CHANGED : The plugin is now compatible with new Wordpress 3.2 (beta2)
* CHANGED : ThickBox is not used anywore in the plugin.
* CHANGED : Optimizations in the javascript files and calls to the Joliprint servers. Joliprint buttons are now displayed on page load and should'nt block navigation if Joliprint servers are offline or under maintenance.
* BUGFIX : The plugin was calling wrong WP functions when deployed on a 2.7/2.8 Wordpress blogs. This is now fixed.
* BUGFIX : The "show/hide" Joliprint credits button in the WP Administration Panel was'nt working correctly the very first time it was clicked. This is now fixed.

= 1.1.5 =
* CHANGED : Optimizations in the javascript calls to the Joliprint server.
* BUGFIX : In some cases, on some WP templates with the use of some plugins, when jQuery is loaded in noConflict mode the ThickBox WP component used to load the Joliprint waiting page was crashing.

= 1.1.4 =
* NEW : Title, author and post date are now passed to Joliprint as metadata and should be now be used in PDF correctly.
* CHANGED : wp_joliprint.php has been changed to wp_joliprint.js.

= 1.1.3 =
* BUGFIX : Changes in `joliprint_show_the_button` in order to prevent to send bad url to joliprint in manual positionning.

= 1.1.2 =
* BUGFIX : Logos and buttons were not correctly uploaded and selected in the Administration section.
* BUGFIX : joliprint.php : new method `joliprint_getCurrentPageUrl` added and `joliprint_show_the_button` has been modified. In some cases, with custom template files or plugins, the URL of the page cannot be retrieved with the standard get_permalink() Wordpress method. For those pages, only custom position can be used to display the joliprint button. Instead of getting the URL via the get_permalink() the URL is retrieved via the new joliprint_getCurrentPageUrl method in joliprint.php

= 1.1.1 =
* NEW : Calls to 3rd party support plateform removed from the Wordpress Administration Panel (replaced by a direct link to anonymous ticket creation on zendesk)
* NEW : Logo and link to our site can now be removed on the PDF waiting page (new joliprint_credits option added )

= 1.1.0 =
* NEW : Changed plugin name and version number 

= 1.0.1 =
* BUGFIX : Thickbox is now declared in the `wp_head()` WP hook instead of being enqueued in the footer called by the wp_foot() hook which is not always present in custom WP themes.
* NEW : Changed the Joliprint adminstration panel look and feel
* NEW : `Support us` box added in the Joliprint administration panel
* NEW : Changes in the instruction and description of the plugin.

= 1.0.0 =
* NEW : First release of the Joliprint button for Wordpress.