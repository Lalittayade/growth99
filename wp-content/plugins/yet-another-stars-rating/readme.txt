=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AXE284FYMNWDC
Tags: rating, rate post, rate page, star rating, google rating, votes
Requires at least: 4.9.0
Contributors: Dudo
Tested up to: 5.6.2
Requires PHP: 5.4
Stable tag: 2.6.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost the way people interact with your site with an easy WordPress stars rating system! With schema.org rich snippets YASR will improve your SEO

== Description ==
Improving the user experience with your website is a top priority for everyone who cares about their online activity,
as it promotes familiarity and loyalty with your brand, and enhances visibility of your activity.

Yasr - Yet Another Stars Rating is a powerful way to add SEO-friendly user-generated reviews and testimonials to your
website posts, pages and CPT, without affecting its speed.

= How To use =

= Reviewer Vote =
With the classic editor, when you create or update a page or a post, a box (metabox) will be available in the upper right corner where you'll
be able to insert the overall rating.
With the new Guteneberg editor, just click on the "+" icon to add a block and search for Yasr Overall Rating.
You can either place the overall rating automatically at the beginning or the end of a post (look in "Settings"
-> "Yet Another Stars Rating: Settings"), or wherever you want in the page using the shortcode [yasr_overall_rating] (easily added through the visual editor).

= Visitor Votes =
You can give your users the ability to vote, pasting the shortcode [yasr_visitor_votes] where you want the stars to appear.
If you're using the new Gutenberg editor, just click on the "+" icon to add a block and search for Yasr Visitor Votes
Again, this can be placed automatically at the beginning or the end of each post; the option is in "Settings" -> "Yet Another Stars Rating: Settings".

= Multi Set =
Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics", "Gameplay", "Story", etc.

= Migration tools =
You can easily migrate from *WP-PostRatings*, *kk Star Ratings*, *Rate My Post* and *Multi Rating*
A tab will appear in the settings if one of these plugin is detected.

= Supported itemtypes =
YASR supports the following schema.org itemtypes:

BlogPosting ✝,
Book ¶,
Course,
CreativeWorkSeason,
CreativeWorkSeries,
Episode,
Event,
Game,
LocalBusiness ‡,
MediaObject,
Movie Δ,
MusicPlaylist,
MusicRecording,
Organization,
Product §,
Recipe ||,
SoftwareApplication

✝ BlogPosting itemtype will not show stars in search result.
More info [here](https://wordpress.org/plugins/yet-another-stars-rating/faq/)

¶ Book supports the following properties
* author
* bookEdition
* BookFormat
* ISBN
* numberOfPages

‡ LocalBusiness supports the following properties
* Address
* PriceRange
* Telephone

Δ Movie supports the following properties
* actor
* director
* Duration
* dateCreated

§ Products supports the following properties
* Brand
* Sku
* Global identifiers
* Price
* Currency
* Price Valid Until
* Availability
* Url

|| Recipe supports the following properties
* cookTime
* prepTime
* description
* keywords
* nutrition
* recipeCategory
* recipeCuisine
* recipeIngredient

= Video Tutorial =
In this video I'll show you the "Auto Insert" feature and manual placement of yasr_visitor_votes and yasr_overall_rating shortcodes
[youtube https://www.youtube.com/watch?v=M47xsJMQJ1E]

= Related Link =
* Documentation at [Yasr Official Site](https://yetanotherstarsrating.com/docs/)
* [Demo page for Overall Rating and Vistor Rating](https://yetanotherstarsrating.com/yasr-basics-shortcode/)
* [Demo page for Multi Sets](https://yetanotherstarsrating.com/yasr-multi-sets/)
* [Demo page for Rankings](https://yetanotherstarsrating.com/yasr-rankings/)

Do you want more feature? [Check out Yasr Pro!](https://yetanotherstarsrating.com/#yasr-pro)

== Installation ==
1. Navigate to Dashboard -> Plugins -> Add New and search for YASR
2. Click on "Installa Now" and than "Activate"

== Frequently Asked Questions ==

= What is "Overall Rating"? =
It is the vote given by who writes the review: readers are able to see this vote in read-only mode. Reviewer can vote using the box on the top right in the editor screen. Remember to insert this shortcode **[yasr_overall_rating]** to make it appear where you like.

= What is "Visitor Rating"? =
It is the vote that allows your visitors to vote: just paste this shortcode **[yasr_visitor_votes]** where you want the stars to appear.

[Demo page for Overall Rating and Vistor Rating](https://yetanotherstarsrating.com/yasr-basics-shortcode/)

= What is "Multi Set"? =
It is the feature that makes YASR awesome. Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics", "Gameplay", "Story", etc. and give a vote for each one. To create a set, just go in "Settings" -> "Yet Another Stars Rating: Settings" and click on the "Multi Sets" tab. To insert it into a post, just paste the shortcode that YASR will create for you.

[Demo page for Multi Sets](https://yetanotherstarsrating.com/yasr-multi-sets/)

= What is "Ranking reviews" ? =
It is the 10 highest rated item chart by reviewer. In order to insert it into a post or page, just paste this shortcode **[yasr_top_ten_highest_rated]**

= Wht is "Users' ranking" ? =
This is 2 charts in 1. Infact, this chart shows both the most rated posts/pages or the highest rated posts/pages.
For an item to appear in this chart, it has to be rated twice at least.
Paste this shortcode to make it appear where you want **[yasr_most_or_highest_rated_posts]**

= What is "Most active reviewers" ? =
If in your site there are more than 1 person writing reviews, this chart will show the 5 most active reviewers. Shortcode is **[yasr_top_5_reviewers]**

= What is "Most active users" ? =
When a visitor (logged in or not) rates a post/page, his rating is stored in the database. This chart will show the 10 most active users, displaying the login name if logged in or "Anonymous" otherwise. The shortcode : **[yasr_top_ten_active_users]**

[Demo page for Rankings](https://yetanotherstarsrating.com/yasr-rankings/)

= Wait, wait! Do I need to keep in mind all this shortcode? =
If you're using the new Gutenberg editor, you don't need at all: just use the blocks.
If, instead, you're using the classic editor, in the visual tab just click the "Yasr Shortcode" button above the editor

= Does it work with caching plugins? =
Since version 2.3.0 YASR works with *every caching plugin available out there*.
In the settings, just select "yes" to "Load results with AJAX".
YASR has been tested with:
* Wp Super Cache
* LiteSpeed Cache
* Wp Fastest Cache
* WP-Optimize
* Cache Enabler
* Hyper Cache
* Wp Rocket

= Why I don't see stars in google? =
[Read here](https://yetanotherstarsrating.com/docs/rich-snippet/reviewrating-and-aggregaterating/) and find out how to set up rich snippets.
You can use the [Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool/u/0/) to validate your pages.
Also [read this](https://webmasters.googleblog.com/2019/09/making-review-rich-results-more-helpful.html) google announcement.
If you set up everythings fine, in 99% of cases your stars will appear in a week.
If doesn't, you should work on your seo reputation.

== Screenshots ==
1. Example of Yasr Overall Rating and Yast Visitor Votes shortcodes
2. Yasr Multi Set
3. User's ranking showing most rated posts
4. User's ranking showing highest rated posts
5. Ranking reviews

== Changelog ==

The full changelog can be found in the plugin's directory. Recent entries:

= 2.6.0 =
* FIX:  if load results with ajax is enabled, and for some reason an api call fails, span below the stars didn't shows up
* TWEAK: containerAfterStars method is now public

= 2.5.9 =
* TWEAK: when a rating is saved, ajax answer returns an array instead of the html. This will no overwrite the actual text but just update it.
* FIX: "0" Showing up at the end of the titles in some circumstances.
* Minor changes

= 2.5.8 =
* FIX: wrong css style for rankings yasr_top_5_reviewers and yasr_top_ten_active_users

= 2.5.7 =
* TWEAK: Rankings have been rewritten: now are build in REACT, this will allow much more flexibility

= 2.5.6 =
* FIX: register_rest_route called incorrectly in Gutenberg
* FIX: random js error into dashboard (thanks to @lwangaman)
* TWEAK: On stars hover, cursor is now displayed an an hand
* FIX: minor changes to support older versions

= 2.5.5 =
* FIX: rich snippet's attribute name returns the post_id instead of the title in some circumstances

= 2.5.4 =
* FIX: yasr_visitor_multiset data didn't save correctly if more than one were used in the same page.


= 2.5.3 =
* FIX: in Yet Another Stars Rating: Stats -> Overall Rating only posts with rating > 0 are shown

= 2.5.2 =
* NEW FEATURE: is now possible delete overall rating data in Yet Another Stars Rating -> Stats -> Overall Rating
* FIXED: Schema title come with rating string if "Enable stars next to the title?" is enabled
* TWEAKED: all rankings functions has been rewritten to work with REST API. Documentation will come soon.
* TWEAKED: a lot of minor changes.

= 2.5.1 =
* TWEAKED: added new hooks: yasr_vv_shortcode, yasr_vv_ro_shortcode and yasr_overall_shortcode. These hooks can
be used to customize the shortcodes.


= 2.5.0 =
* TWEAKED: minor changes. Nothing to be excited about

= 2.4.9 =
* FIXED: Removed unnecessary closing div for yasr_visitor_votes shortcode

= 2.4.8 =
* Removed YasrSettings file from svn repo

= 2.4.7 =
* NEW FEATURE: Added new fields for these itemTypes:
#### Book
* author
* bookEdition
* BookFormat
* ISBN
* numberOfPages
#### Movie
* actor
* director
* Duration
* dateCreated
* TWEAKED: added hooks yasr_vv_saved_text and yasr_mv_saved_text to filter text showed after a rating is saved
* TWEAKED: added hooks yasr_vv_txt_before and yasr_vv_text_after to filter text showed before or after the star ratings
* TWEAKED: code cleanup in setting page
* FIX: check cookie name to use hooks added in version 2.4.6
* FIX: on fresh install, with php 7.4, a notice is returned

= Additional Info =
See credits.txt file