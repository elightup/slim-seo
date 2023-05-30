<?php
if ( $this->meta_tags_manager->is_static_homepage() ) {
	include __DIR__ . '/post-types-settings.php';
	return;
}
?>

<div class="ss-tabs ss-tabs--no-hash">
	<nav class="ss-tab-list">
		<a class="ss-tab" href="#meta-tags-homepage"><?php esc_html_e( 'Homepage', 'slim-seo' ) ?></a>
		<a class="ss-tab" href="#meta-tags-post-types"><?php esc_html_e( 'Post types', 'slim-seo' ) ?></a>
	</nav>

	<div class="ss-tab-pane" id="meta-tags-homepage">
		<?php
		$this->meta_tags_manager->get( 'home' )->render();

		submit_button( __( 'Save Changes', 'slim-seo' ) );
		?>
	</div>

	<div class="ss-tab-pane" id="meta-tags-post-types">
		<?php include __DIR__ . '/post-types-settings.php'; ?>
	</div>
</div>
