<?php
include_once('database.php');

ini_set("log_errors", 1);
ini_set("error_log", explode( "wp-content", __FILE__ )[0] . 'wp-content/debug.log');

// ** filter out the requested link ** //
$cache_table = 'jk_instagram_cache';
$local_store = explode( "wp-content", __FILE__ )[0] . 'wp-content/uploads/instagram-cache/';
$site_url = preg_replace('/wp-content.+/', '', $_SERVER['REQUEST_URI']);

//error_log('full request uri: ' . $_SERVER['REQUEST_URI']);

preg_match('/(?<=\?request=).+/', $_SERVER['REQUEST_URI'], $matches);
$request = preg_replace('/%site%/', 'https://api.instagram.com', $matches[0]);

// getting callback function
$callback = false;

if (isset($_GET['callback'])){
    $callback = $_GET['callback'];
}

// ** END OF getting requested link ** //


// get request type: media or user request
$is_media_request = preg_match('/\/media\//', $request);

// ** strip callback function wrap so that we can decode it into PHP object ** //
if ($callback){
    $request = preg_replace('/&callback=.+$/', '', $request);
}

$response = file_get_contents($request);

error_log('request: ' . $request);
//error_log('response: ' . $response);

$response_obj = json_decode($response);
//error_log('response_obj: ' . print_r($response_obj, true));



// replace media links with cache
if ( $is_media_request ) {

//    error_log('fetching media ...');

    //$response_obj->data is array of images

    foreach ($response_obj->data as &$ig_object){

        // retrieve cache, download if new, media files
        $media_urls = get_media($ig_object);

//        error_log('fetched urls: ' . print_r($media_urls, true));

        // Alter user profile_picture with cached
        $ig_object->user->profile_picture = $media_urls['profile_picture'];

        // Alter images with cached
        $ig_object->images->low_resolution->url = $media_urls['image_low_resolution'];
        $ig_object->images->thumbnail->url = $media_urls['image_thumbnail'];
        $ig_object->images->standard_resolution->url = $media_urls['image_standard_resolution'];

        // Alter videos with cached
        if ($ig_object->type == 'video') {
            $ig_object->videos->low_resolution->url = $media_urls['video_low_resolution'];
            $ig_object->videos->standard_resolution->url = $media_urls['video_standard_resolution'];
            $ig_object->videos->low_bandwidth->url = $media_urls['video_low_bandwidth'];
        }

    }

} else { // this is a user info request,

//    error_log('Requesting for user profile picture');
    // ** cache user profile picture (user's "id" as key in link field, "profile_picture" as value in thumbnail field) ** //
    $profile_picture = get_user_profile_picture($response_obj->data);
    if ($profile_picture){
//        error_log('profile picture is found in cache');
        $response_obj->data->profile_picture = $profile_picture;
    }

}



// ** restore the callback function to the next_url field ** //
if ( isset($response_obj->pagination->next_url) && $callback )
    $response_obj->pagination->next_url .= '&callback=' . $callback;

$response = json_encode($response_obj);


if ($callback) {
    header('Content-Type: text/javascript; charset=utf-8');
//    header('Access-Control-Allow-Origin: http://www.example.com/');
//    header('Access-Control-Max-Age: 3628800');
//    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo $callback . '(' . $response . ')';
//    error_log('final response: ' . $callback . '(' . $response . ')');
} else {
    // normal JSON string
    header('Content-Type: application/json; charset=utf-8');
    echo $response;
//    error_log('final response: ' . $response);
}

// ** cache and record to db if link not found in db ** //
/**
 * @param $image object
 * @param $resolution
 * @return array Array('low_resolution' => url,
 *                     'thumbnail' => url,
 *                     'standard_resolution' => url
 *               ).
 */
function get_media($ig_object) {

    // get media cache
    $result = _get_cached_media($ig_object->link);

    // cache not found
    if (!$result) {
        $result = _cache_media($ig_object);
    }

    return $result;
}

/**
 * Search database for the cached image, using $link as key and $resolution as column name
 * @param $link
 * @return false|array Array('low_resolution' => url,
 *                           'thumbnail' => url,
 *                           'standard_resolution' => url
 *                     ).
 */
function _get_cached_media($link) {
    global $database, $cache_table;
    $query = "SELECT * FROM {$cache_table} WHERE link = '{$link}' LIMIT 1";
    $rs = $database->query($query);
    while( $record = $rs->fetch_assoc() ){
        return array(
            'profile_picture' => $record['profile_picture'],

            'image_low_resolution' => $record['image_low_resolution'],
            'image_thumbnail' => $record['image_thumbnail'],
            'image_standard_resolution' => $record['image_standard_resolution'],

            'video_low_resolution' => $record['video_low_resolution'],
            'video_standard_resolution' => $record['video_standard_resolution'],
            'video_low_bandwidth' => $record['video_low_bandwidth'],
        );
    }
    return false;
}

/**
 * Download the image and save record to database, using $link as key and $resolution as column name
 * @param $link The key
 * @param $resolution
 * @return false|array Array('low_resolution' => url,
 *                           'thumbnail' => url,
 *                           'standard_resolution' => url
 *                     ).
 */
function _cache_media($ig_object) {
    global $database, $cache_table, $local_store, $site_url;
    $profile_picture_fpath = '';
    $image_low_resolution_fpath = $image_thumbnail_fpath = $image_standard_resolution_fpath = '';
    $video_low_resolution_fpath = $video_standard_resolution_fpath = $video_low_bandwidth_fpath = '';

    // create parent path if not exist
    if (!file_exists($local_store)){
        mkdir($local_store, 0777, true);
    }

    // download profile pictures to local store. name picture after: md5 of link + 'profile_picture' + profile_picture url
    $fbase = md5($ig_object->link . 'profile_picture' . $ig_object->user->profile_picture);
    preg_match('/(?<=[^.])\.[a-z]+$/', $ig_object->user->profile_picture, $matches);
    $fext = $matches[0];
    $fname = $fbase . $fext;
    $fpath = $local_store . $fname;

    if ( ! file_exists($fpath) ) {
        if ( file_put_contents($fpath, file_get_contents($ig_object->user->profile_picture)) ){
            // save succeeded
            $profile_picture_fpath = $fpath;
        } else {
            // save failed
        }
    } else {
        // skip when file already exists
    }

    // download images to local store, name images after: md5 of link + 'image' + resolution
    foreach( array('low_resolution', 'thumbnail', 'standard_resolution') as $resolution ){
        $fbase = md5($ig_object->link . 'image' . $resolution);
        preg_match('/\.[^.]+(?=\?ig_cache_key)/', $ig_object->images->$resolution->url, $matches);
        $fext = $matches[0];
        $fname = $fbase . $fext;
        $fpath = $local_store . $fname;

        if ( ! file_exists($fpath) ) {
            if ( file_put_contents($fpath, file_get_contents($ig_object->images->$resolution->url)) ){
                // save succeeded
                ${'image_' . $resolution . '_fpath'} = $fpath;
            } else { // save failed

            }
        } else {
            // skip when file already exists
        }
    }

    // download videos to local store, name videos after: md5 of link + 'video' + resolution
    if ($ig_object->type == 'video'){
        foreach( array('low_resolution', 'standard_resolution', 'low_bandwidth') as $resolution ){
            $fbase = md5($ig_object->link . 'video' . $resolution);
            preg_match('/\.[^.\/]+$/', $ig_object->videos->$resolution->url, $matches);
            $fext = $matches[0];
            $fname = $fbase . $fext;
            $fpath = $local_store . $fname;

//            error_log('url: ' . $ig_object->videos->$resolution->url);
//            error_log('file_path: ' . $fpath);

            if ( ! file_exists($fpath) ){
                if ( file_put_contents($fpath, file_get_contents($ig_object->videos->$resolution->url)) ){
                    // save succeeded
                    ${'video_' . $resolution . '_fpath'} = $fpath;
                } else { // save failed

                }
            } else {
                // skip when file already exists
            }
        }
    }

    // get url from fpath(s)
    $profile_picture_url = $profile_picture_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $profile_picture_fpath) : '';
    $image_low_resolution_url = $image_low_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_low_resolution_fpath) : '';
    $image_thumbnail_url = $image_thumbnail_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_thumbnail_fpath) : '';
    $image_standard_resolution_url = $image_standard_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_standard_resolution_fpath) : '';
    $video_low_resolution_url = $video_low_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_low_resolution_fpath) : '';
    $video_standard_resolution_url = $video_standard_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_standard_resolution_fpath) : '';
    $video_low_bandwidth_url = $video_low_bandwidth_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_low_bandwidth_fpath) : '';

    // record in database
    $query = "INSERT INTO {$cache_table} (link, profile_picture, image_low_resolution, image_thumbnail, image_standard_resolution, video_low_resolution, video_standard_resolution, video_low_bandwidth) VALUES ('{$ig_object->link}', '$profile_picture_url', '$image_low_resolution_url', '$image_thumbnail_url', '$image_standard_resolution_url', '$video_low_resolution_url', '$video_standard_resolution_url', '$video_low_bandwidth_url')";
    $query .= " ON DUPLICATE KEY UPDATE profile_picture = '$profile_picture_url', image_low_resolution = '$image_low_resolution_url', image_thumbnail = '$image_thumbnail_url', image_standard_resolution = '$image_standard_resolution_url', video_low_resolution = '$video_low_resolution_url', video_standard_resolution = '$video_standard_resolution_url', video_low_bandwidth = '$video_low_bandwidth_url';";

    if ( $database->query($query) && $database->conn->affected_rows == 1 ){
        return array(
            'profile_picture' => $profile_picture_url,
            'image_low_resolution' => $image_low_resolution_url,
            'image_thumbnail' => $image_thumbnail_url,
            'image_standard_resolution' => $image_standard_resolution_url,
            'video_low_resolution' => $video_low_resolution_url,
            'video_standard_resolution' => $video_standard_resolution_url, 
            'video_low_bandwidth' => $video_low_bandwidth_url,
        );
    } else {
        return false;
    }
}

function get_user_profile_picture($ig_object){

    // try to get from cache
    $result = _get_cached_profile_picture($ig_object->id);

    // cache not found
    if (!$result){
        $result = _cache_profile_picture($ig_object);
    }

    return $result;
}

/**
 * using uid as key, profile pictured is stored in col image_thumbnail
 * @param $uid
 * @return false|string
 */
function _get_cached_profile_picture($uid){
    global $database, $cache_table;
    $query = "SELECT image_thumbnail FROM {$cache_table} WHERE link = '{$uid}' LIMIT 1";
    $rs = $database->query($query);

    while( $record = $rs->fetch_assoc() ){
        return $record['image_thumbnail'];
    }

    return false; // No cache found, return false.
}

/**
 * Download profile picture and save to database
 * @param $ig_object
 * @return false|string
 */
function _cache_profile_picture($ig_object){
    global $database, $cache_table, $local_store, $site_url;

    $profile_picture_fpath = '';
    // create parent path if not exist
    if (!file_exists($local_store)){
        mkdir($local_store, 0777, true);
    }

    // ** download profile picture to local store, using md5 of uid + profile_picture url as filename ** //
    $fbase = md5($ig_object->id . $ig_object->profile_picture);
    preg_match('/(?<=[^.])\.[a-z]+$/', $ig_object->profile_picture, $matches);
    $fext = $matches[0];
    $fname = $fbase . $fext;
    $fpath = $local_store . $fname;

    if ( ! file_exists($fpath) ) {
        if ( file_put_contents($fpath, file_get_contents($ig_object->profile_picture)) ){
            // save succeeded
            $profile_picture_fpath = $fpath;
        } else {
            // save failed
        }
    } else {
        // skip when file already exists
    }
    // ** END OF downloading file ** //

    // ** Save record to database ** //

        // get url from fpath
    $profile_picture_url = $profile_picture_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $profile_picture_fpath) : '';
//    error_log('profile path: ' . $profile_picture_fpath);
//    error_log('profile url: ' . $profile_picture_url);

        // save url to database
    $query = "INSERT INTO {$cache_table} (link, image_thumbnail) VALUES ('{$ig_object->id}', '{$profile_picture_url}')";
    $query .= " ON DUPLICATE KEY UPDATE image_thumbnail = '{$profile_picture_url}';";

    if ( $database->query($query) && $database->conn->affected_rows == 1 ){
        return $profile_picture_url;
    } else {
        return false;
    }
}