<?php
use SlimSEO\Helpers\Data;
use SlimSEO\Settings\MetaTags;

$post_types = array_values( Data::get_post_types() );
$taxonomies = array_values( Data::get_taxonomies() );
?>

<div class="ss-tabs ss-tabs--no-hash">
	<nav class="ss-tab-list">
		<?php if ( ! $this->is_static_homepage() ) : ?>
			<a class="ss-tab" href="#content-homepage"><?php esc_html_e( 'Homepage', 'slim-seo' ) ?></a>
		<?php endif ?>
		<a class="ss-tab" href="#content-post-types"><?php esc_html_e( 'Post types', 'slim-seo' ) ?></a>
		<a class="ss-tab" href="#content-taxonomies"><?php esc_html_e( 'Taxonomies', 'slim-seo' ) ?></a>
		<a class="ss-tab" href="#content-archives"><?php esc_html_e( 'Archives', 'slim-seo' ) ?></a>
	</nav>

	<div class="ss-tab-pane" id="content-homepage">
		<?php
		( new MetaTags( 'home' ) )->render();

		submit_button( __( 'Save Changes', 'slim-seo' ) );
		?>
	</div>

	<div class="ss-tab-pane" id="content-post-types">
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
		<?php
		foreach ( $post_types as $index => $post_type_object ) {
			printf(
				'<div class="ss-post-type-settings ss-post-type-settings--%s%s">',
				esc_attr( $post_type_object->name ),
				$index === 0 ? ' ss-is-active' : ''
			);
			?>

			<div class="ss-field">
				<div class="ss-label">
					<label for="ss-noindex-<?= esc_attr( $post_type_object->name ) ?>"><?php esc_html_e( 'Hide from search results', 'slim-seo' ) ?></label>
				</div>
				<div class="ss-input">
					<input type="checkbox" id="ss-noindex-<?= esc_attr( $post_type_object->name ) ?>" name="slim_seo[<?= esc_attr( $post_type_object->name ) ?>][noindex]" value="1">
				</div>
			</div>

			<?php
			if ( $post_type_object->has_archive ) {
				// Translators: %s - post type singular name.
				printf( '<h3>' . esc_html__( '%s archive page', 'slim-seo' ) . '</h3>', $post_type_object->labels->singular_name );
				( new MetaTags( $post_type_object->name ) )->render();
			}

			submit_button( __( 'Save Changes', 'slim-seo' ) );

			echo '</div>';
		}
		?>
	</div>

	<div class="ss-tab-pane" id="content-taxonomies">
		<div class="ss-field">
			<div class="ss-label">
				<label for="ss-taxonomy-select"><?php esc_html_e( 'Taxonomy', 'slim-seo' ) ?></label>
			</div>
			<div class="ss-input">
				<select id="ss-taxonomy-select">
					<?php
					foreach ( $taxonomies as $taxonomy_object ) {
						printf(
							'<option value="%s">%s (%s)</option>',
							esc_attr( $taxonomy_object->name ),
							esc_html( $taxonomy_object->labels->singular_name ),
							esc_html( $taxonomy_object->name )
						);
					}
					?>
				</select>
			</div>
		</div>
		<?php
		foreach ( $taxonomies as $index => $taxonomy_object ) {
			printf(
				'<div class="ss-taxonomy-settings ss-taxonomy-settings--%s%s">',
				esc_attr( $taxonomy_object->name ),
				$index === 0 ? ' ss-is-active' : ''
			);

			// Translators: %s - taxonomy singular name.
			printf( '<h3>' . esc_html__( '%s settings', 'slim-seo' ) . '</h3>', $taxonomy_object->labels->singular_name );
			( new MetaTags( $taxonomy_object->name ) )->render();

			submit_button( __( 'Save Changes', 'slim-seo' ) );

			echo '</div>';
		}
		?>
	</div>

</div>


