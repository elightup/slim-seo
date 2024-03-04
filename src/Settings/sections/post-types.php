<?php
use SlimSEO\Helpers\Data;
use SlimSEO\Helpers\UI;

$post_types = $this->meta_tags_manager->get_post_types(); // phpcs:ignore
$option     = get_option( 'slim_seo' ); // phpcs:ignore
?>

<div class="ss-field">
	<div class="ss-label">
		<label for="ss-post-type-select"><?php esc_html_e( 'Select a post type', 'slim-seo' ) ?></label>
	</div>
	<div class="ss-input">
		<select id="ss-post-type-select">
			<?php
			foreach ( $post_types as $post_type_object ) { // phpcs:ignore
				printf(
					'<option value="%s">%s (%s)</option>',
					esc_attr( $post_type_object->name ),
					esc_html( $post_type_object->labels->singular_name ),
					esc_html( $post_type_object->name )
				);
			}
			?>
		</select>
	</div>
</div>
<?php foreach ( $post_types as $post_type => $post_type_object ) : //phpcs:ignore ?>
	<?php $data = $option[ $post_type ] ?? []; // phpcs:ignore ?>
	<div class="ss-post-type-settings ss-post-type-settings--<?= esc_attr( $post_type ) ?>">
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-noindex-<?= esc_attr( $post_type ) ?>">
					<?php esc_html_e( 'Hide from search results', 'slim-seo' ) ?>
					<?php UI::tooltip( __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) ) ?>
				</label>
			</div>
			<div class="ss-input">
				<?php UI::toggle( "slim_seo[$post_type][noindex]", 1, ! empty( $data['noindex'] ) ) ?>
			</div>
		</div>

		<?php
		if ( $post_type_object->has_archive ) {
			// Translators: %s - post type singular name.
			printf( '<h3>' . esc_html__( '%s archive page', 'slim-seo' ) . '</h3>', $post_type_object->labels->singular_name ); // phpcs:ignore

			$archive_page = Data::get_post_type_archive_page( $post_type ); // phpcs:ignore
			if ( $archive_page ) {
				echo '<p>', wp_kses_post( sprintf(
					// Translators: %1$s - link to the archive page, %2$s - page title, %3$s - post type slug.
					__( 'You have a page <a href="%1$s" target="_blank">%2$s</a> that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the <code>%3$s</code> post type.', 'slim-seo' ),
					get_permalink( $archive_page ),
					$archive_page->post_title,
					$post_type
				) ), '</p>';
				echo '<p>', wp_kses_post( sprintf(
					// Translators: %s - edit link.
					__( 'To set the meta tags for the page, please <a href="%s">set on the edit page</a>.', 'slim-seo' ),
					get_edit_post_link( $archive_page )
				) ), '</p>';
			} else {
				$this->meta_tags_manager->get( "{$post_type}_archive" )->render();
			}
		}

		submit_button( __( 'Save Changes', 'slim-seo' ) );
		?>
	</div>
<?php endforeach ?>
