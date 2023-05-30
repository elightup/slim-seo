<?php
use SlimSEO\Helpers\Data;

$post_types = array_values( Data::get_post_types() );
?>
<div class="ss-field">
	<div class="ss-label">
		<label for="ss-post-type-select"><?php esc_html_e( 'Post type', 'slim-seo' ) ?></label>
	</div>
	<div class="ss-input">
		<select id="ss-post-type-select">
			<?php
			foreach ( $post_types as $post_type_object ) {
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
<?php foreach ( $post_types as $post_type_object ) : ?>
	<div class="ss-post-type-settings ss-post-type-settings--<?= esc_attr( $post_type_object->name ) ?>">
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-noindex-<?= esc_attr( $post_type_object->name ) ?>">
					<?php esc_html_e( 'Hide from search results', 'slim-seo' ) ?>
					<?php $this->tooltip( __( 'This setting will hide all posts of this post types from search results and also will exclude the post type from the sitemap.', 'slim-seo' ) ) ?>
				</label>
			</div>
			<div class="ss-input">
				<input type="checkbox" id="ss-noindex-<?= esc_attr( $post_type_object->name ) ?>" name="slim_seo[<?= esc_attr( $post_type_object->name ) ?>][noindex]" value="1">
			</div>
		</div>

		<?php
		if ( $post_type_object->has_archive ) {
			// Translators: %s - post type singular name.
			printf( '<h3>' . esc_html__( '%s archive page', 'slim-seo' ) . '</h3>', $post_type_object->labels->singular_name );
			$this->meta_tags_manager->get( "{$post_type_object->name}_archive" )->render();
		}

		submit_button( __( 'Save Changes', 'slim-seo' ) );
		?>
	</div>
<?php endforeach ?>
