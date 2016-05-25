<?php
include_once('database.php');

ini_set("log_errors", 1);
ini_set("error_log", explode( "wp-content", __FILE__ )[0] . 'wp-content/debug.log');

//error_log('full request URI: '. $_SERVER['REQUEST_URI']);


// ** filter out the requested link ** //
$cache_table = 'jk_instagram_cache';
$local_store = explode( "wp-content", __FILE__ )[0] . 'wp-content/uploads/instagram-cache/';
$site_url = preg_replace('/wp-content.+/', '', $_SERVER['REQUEST_URI']);

//error_log('local_store: ' . $local_store);
//error_log('site_url: ' . $site_url);


preg_match('/(?<=\?request=).+/', $_SERVER['REQUEST_URI'], $matches);
$request = preg_replace('/%site%/', 'https://api.instagram.com', $matches[0]);

// getting callback function
$callback = false;
preg_match('/(?<=&callback=)[^&]+/', $request, $matches);
if ($matches){
    $callback = $matches[0];
}

// ** END OF getting requested link ** //


// get request type: media or else
$is_media_request = preg_match('/\/media\//', $request);

// ** strip callback function wrap so that we can decode it into PHP object ** //
if ($callback){
    $request = preg_replace('/&callback=.+$/', '', $request);
}
error_log('request: ' . $request);

$response = file_get_contents($request);
error_log('response: ' . $response);

/*
$response_obj = json_decode($response);
//error_log('response_obj: ' . print_r($response_obj, true));



// replace media links with cache
if ( $is_media_request ) {

    error_log('fetching media ...');

    //$response_obj->data // array of images

    foreach ($response_obj->data as &$ig_object){

        // retrieve cache, download if new, media files
        $media_urls = get_media($ig_object);

//        error_log('fetched urls: ' . print_r($media_urls, true));

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

//        error_log('object after changing: ' . print_r($ig_object, true));
    }

} else {

}



// ** restore the callback function to the next_url field ** //
if ( isset($response_obj->pagination->next_url) && $callback )
    $response_obj->pagination->next_url .= '&callback=' . $callback;

$response = json_encode($response_obj);


*/

if ($callback) {
    header('Content-Type: text/javascript; charset=utf-8');
//    header('Access-Control-Allow-Origin: http://www.example.com/');
//    header('Access-Control-Max-Age: 3628800');
//    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo $callback . '(' . $response . ')';
    error_log('final return: ' . $callback . '(' . $response . ')');
} else {
    // normal JSON string
    header('Content-Type: application/json; charset=utf-8');
    echo $response;
    error_log('final return: ' . $response);
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
    $image_low_resolution_fpath = $image_thumbnail_fpath = $image_standard_resolution_fpath = '';
    $video_low_resolution_fpath = $video_standard_resolution_fpath = $video_low_bandwidth_fpath = '';

    // create parent path if not exist
    if (!file_exists($local_store)){
        mkdir($local_store, 0777, true);
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
    $image_low_resolution_url = $image_low_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_low_resolution_fpath) : '';
    $image_thumbnail_url = $image_thumbnail_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_thumbnail_fpath) : '';
    $image_standard_resolution_url = $image_standard_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $image_standard_resolution_fpath) : '';
    $video_low_resolution_url = $video_low_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_low_resolution_fpath) : '';
    $video_standard_resolution_url = $video_standard_resolution_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_standard_resolution_fpath) : '';
    $video_low_bandwidth_url = $video_low_bandwidth_fpath ? preg_replace('/.+(?=wp-content)/', $site_url, $video_low_bandwidth_fpath) : '';

    // record in database
    $query = "INSERT INTO {$cache_table} (link, image_low_resolution, image_thumbnail, image_standard_resolution, video_low_resolution, video_standard_resolution, video_low_bandwidth) VALUES ('{$ig_object->link}', '$image_low_resolution_url', '$image_thumbnail_url', '$image_standard_resolution_url', '$video_low_resolution_url', '$video_standard_resolution_url', '$video_low_bandwidth_url')";
    $query .= " ON DUPLICATE KEY UPDATE image_low_resolution = '$image_low_resolution_url', image_thumbnail = '$image_thumbnail_url', image_standard_resolution = '$image_standard_resolution_url', video_low_resolution = '$video_low_resolution_url', video_standard_resolution = '$video_standard_resolution_url', video_low_bandwidth = '$video_low_bandwidth_url';";

    if ( $database->query($query) && $database->conn->affected_rows == 1 ){
        return array(
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