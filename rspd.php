<?php 

add_action('init', 'mmt_update_posts_datetime');

function mmt_update_posts_datetime() {

    if(isset($_GET['osup']) && $_GET['osup'] == true) {

        $dateTime = '';
        $category_id = 2;
        $wp_timezone = get_option('timezone_string');
        $time_zones = [
            'UTC',
            $wp_timezone
        ];

        $q = new WP_Query([
            'posts_per_page'    => -1,
            'post_type'         => 'post',
            'orderby'           => 'date',
            'order'             => 'ASC',
            'cat'               => $category_id
        ]);

        if ($q->have_posts()) {
            while($q->have_posts()) {
                $q->the_post();

                $post_id = get_the_ID();

                $dateTime = mmt_randomize_date_time($dateTime);

                $new_datetimes_timestamp = mmt_get_datetime_timezones_timestamp( $dateTime, $time_zones);
                
                $post_data = [
                    'ID'                => $post_id, 
                    'post_date'         => $new_datetimes_timestamp['UTC']['mysql'], 
                    'post_date_gmt'     => $new_datetimes_timestamp[$wp_timezone]['mysql'],
                ];

                // Update the post
                wp_update_post( $post_data );

            }
            
            wp_reset_postdata();
        }

    }
}

function mmt_randomize_date_time($dateTime) {

    // get some random numbers for days ago and a random time HH:MM
    $days_before_rand = rand(0,8);
    $hour_rand = rand(7,23);
    $min_rand  = rand(0,59);

    // If we are in initial state then user the current time to create a random date time
    if ( ! $dateTime ) {
        // current date time in timestamp and Y-m-d H:i:s format
        //$dateTime_timestamp = current_time('timestamp');
        $dateTime_format = current_time('mysql');

    } else {
        $dateTime_format = $dateTime->format('Y-m-d H:i:s');
    }

    $dateTime = new DateTime($dateTime_format);

    // use $days_before_rand variable to create a new random date
    $dateTime->modify('-' . $days_before_rand . ' day');

    // set the new date HH:MM by random
    $dateTime->setTime($hour_rand, $min_rand);

    return $dateTime;
}

function mmt_get_datetime_timezones_timestamp($dateTime, $timezones) {

    if ( ! $dateTime ) return;
    if ( ! $timezones || (count($timezones) == 1) && ($timezones[0] == 'UTC')) return $dateTime->getTimestamp();

    $datetime_timestamps = [];

    foreach($timezones as $tz)  {
        if ($tz !== 'UTC') {
            $dateTime->setTimeZone(new DateTimeZone($tz));
        }
        
        $datetime_timestamps[$tz]['timestamp'] = strtotime($dateTime->format('Y-m-d H:i:s'));
        $datetime_timestamps[$tz]['mysql']     = $dateTime->format('Y-m-d H:i:s');
    }

    return $datetime_timestamps;

}
