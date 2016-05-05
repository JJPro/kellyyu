<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<!--
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) );?>">
	<span class="jk-font icon-search"></span>
    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder' );?>" value="<?php echo get_search_query(); ?>" name="s" />
    <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" />
</form>
-->

<form role="search" class='search-form' action="http://www.google.com" id="cse-search-box" >
    <div>
        <span class="jk-font icon-search"></span>
        <input type="hidden" name="cx" value="partner-pub-0919081176944377:4326239676" />
        <input type="hidden" name="ie" value="UTF-8" />
        <input type="text" name="q" size="15"  class="search-field" />
        <input type="submit" name="sa" value="Search" class="search-submit"/>
    </div>
</form>

<script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=cse-search-box&amp;lang=en"></script>

<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js"></script>
<div id="queries"></div>
<script src="http://www.google.com/cse/api/partner-pub-0919081176944377/cse/4326239676/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render"></script>
