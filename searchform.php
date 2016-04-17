<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) );?>">
	<span class="jk-font icon-search"></span>
    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder' );?>" value="<?php echo get_search_query(); ?>" name="s" />
    <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" />
</form>
