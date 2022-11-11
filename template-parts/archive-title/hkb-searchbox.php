<?php
/**
 * The template for displaying Heroic Knowledgebase Search
 *
 * @package Base
 */

namespace Base;

use function hkb_show_knowledgebase_search;
use function hkb_get_knowledgebase_searchbox_placeholder_text;
use function ht_knowledge_base_activate_live_search;
use function get_search_query;

/* important - load live search scripts */ ht_knowledge_base_activate_live_search(); ?>
<form class="hkb-site-search search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="hkb-screen-reader-text screen-reader-text" for="s"><?php esc_html_e( 'Search For', 'avanam' ); ?></label>
	<input class="hkb-site-search__field search-field" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr( hkb_get_knowledgebase_searchbox_placeholder_text() ); ?>" name="s" autocomplete="off">
	<input type="hidden" name="ht-kb-search" value="1" />
	<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) { ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>"/>
	<?php } ?>
	<button class="hkb-site-search__button search-submit" type="submit"><span><?php esc_html_e( 'Search', 'avanam' ); ?></span></button>
	<div class="base-search-icon-wrap"><?php webapp()->print_icon( 'search', '', false ); ?></div>
</form>
