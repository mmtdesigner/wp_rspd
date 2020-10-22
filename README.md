# wp_rspd

Use this file to convert post_date and post_date_gmt for contents which had been scraped from other websites but post dates could n't be scraped properly.
Adding this file to your functions.php and setting the category_id will randomize all the posts dates using 'ASC' order.

Change $days_before_rand variable to have your own pattern.

If you had scraped contents from the last page to first then change 'ASC' in WP_Query to 'DESC'.

To run this functions add `osup=true` parameter to your wordpress URL.
