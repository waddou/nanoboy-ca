<?php
/**
 * NanoBoy — Champ de titre H1 personnalisé des catégories et étiquettes.
 *
 * @package NanoBoy
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistre la métadonnée native utilisée par les archives.
 *
 * @return void
 */
function nanoboy_register_term_meta(): void {
	$args = array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => static fn(): bool => current_user_can( 'manage_categories' ),
		'show_in_rest'      => true,
	);

	register_term_meta( 'category', 'titre-h1', $args );
	register_term_meta( 'post_tag', 'titre-h1', $args );
}
add_action( 'init', 'nanoboy_register_term_meta' );

/**
 * Affiche le champ lors de la création d'un terme.
 *
 * @param string $taxonomy Taxonomie courante.
 * @return void
 */
function nanoboy_add_term_h1_field( string $taxonomy ): void {
	?>
	<div class="form-field term-titre-h1-wrap">
		<label for="nanoboy-titre-h1"><?php esc_html_e( 'Custom H1 title', 'nanoboy' ); ?></label>
		<input id="nanoboy-titre-h1" name="nanoboy_titre_h1" type="text" value="">
		<p><?php esc_html_e( 'Optional title displayed as the main heading on this archive.', 'nanoboy' ); ?></p>
		<?php wp_nonce_field( 'nanoboy_save_term_h1', 'nanoboy_term_h1_nonce' ); ?>
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'nanoboy_add_term_h1_field' );
add_action( 'post_tag_add_form_fields', 'nanoboy_add_term_h1_field' );

/**
 * Affiche le champ lors de la modification d'un terme.
 *
 * @param WP_Term $term Terme modifié.
 * @return void
 */
function nanoboy_edit_term_h1_field( WP_Term $term ): void {
	$value = (string) get_term_meta( $term->term_id, 'titre-h1', true );
	?>
	<tr class="form-field term-titre-h1-wrap">
		<th scope="row">
			<label for="nanoboy-titre-h1"><?php esc_html_e( 'Custom H1 title', 'nanoboy' ); ?></label>
		</th>
		<td>
			<input
				id="nanoboy-titre-h1"
				name="nanoboy_titre_h1"
				type="text"
				value="<?php echo esc_attr( $value ); ?>">
			<p class="description"><?php esc_html_e( 'Optional title displayed as the main heading on this archive.', 'nanoboy' ); ?></p>
			<?php wp_nonce_field( 'nanoboy_save_term_h1', 'nanoboy_term_h1_nonce' ); ?>
		</td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'nanoboy_edit_term_h1_field' );
add_action( 'post_tag_edit_form_fields', 'nanoboy_edit_term_h1_field' );

/**
 * Enregistre ou supprime le titre H1 personnalisé.
 *
 * @param int $term_id Identifiant du terme.
 * @return void
 */
function nanoboy_save_term_h1( int $term_id ): void {
	$nonce = isset( $_POST['nanoboy_term_h1_nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['nanoboy_term_h1_nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'nanoboy_save_term_h1' ) || ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$value = isset( $_POST['nanoboy_titre_h1'] )
		? sanitize_text_field( wp_unslash( $_POST['nanoboy_titre_h1'] ) )
		: '';

	if ( '' === $value ) {
		delete_term_meta( $term_id, 'titre-h1' );
		return;
	}

	update_term_meta( $term_id, 'titre-h1', $value );
}
add_action( 'created_category', 'nanoboy_save_term_h1' );
add_action( 'edited_category', 'nanoboy_save_term_h1' );
add_action( 'created_post_tag', 'nanoboy_save_term_h1' );
add_action( 'edited_post_tag', 'nanoboy_save_term_h1' );

/**
 * Retourne le H1 personnalisé d'un terme, avec son nom comme repli.
 *
 * @param WP_Term $term Terme ciblé.
 * @return string
 */
function nanoboy_term_h1( WP_Term $term ): string {
	$title = (string) get_term_meta( $term->term_id, 'titre-h1', true );

	return '' !== $title ? $title : $term->name;
}
