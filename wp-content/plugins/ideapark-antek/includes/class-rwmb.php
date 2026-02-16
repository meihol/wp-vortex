<?php

add_action( 'init', function(){
	if ( class_exists( 'RWMB_Fieldset_Text_Field' ) ) {
		class RWMB_Fieldset_Condition_Field extends RWMB_Fieldset_Text_Field {
			public static function html( $meta, $field ) {
				$html = [];
				$tpl  = '<p><label>%s</label> %s</p>';

				foreach ( $field['options'] as $key => $label ) {
					$value                       = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
					$field['attributes']['name'] = $field['field_name'] . "[{$key}]";
					if ( $key === 'condition' ) {
						$field_select                = $field;
						$field_select['size']        = '';
						$field_select['placeholder'] = __( 'Select a Condition', 'ideapark-antek' );
						$field_select['flatten']     = false;
						$field_select['multiple']    = false;
						$field_select['options']     = [];

						$terms = get_terms( [
							'taxonomy'   => 'condition',
							'hide_empty' => false,
						] );

						foreach ( $terms AS $term ) {
							$field_select['options'][ $term->term_id ] = $term->name;
						}

						$html[] = sprintf( $tpl, $label, RWMB_Select_Field::html( $value, $field_select ) );
					} else {
						$html[] = sprintf( $tpl, $label, RWMB_Input_Field::html( $value, $field ) );
					}
				}

				$out = '<div class="row">' . implode( ' ', $html ) . '</div>';

				return $out;
			}
		}

		class RWMB_Fieldset_Delivery_Field extends RWMB_Fieldset_Text_Field {
			public static function html( $meta, $field ) {
				$html = [];
				$tpl  = '<p><label>%s</label> %s</p>';

				foreach ( $field['options'] as $key => $label ) {
					$value                       = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
					$field['attributes']['name'] = $field['field_name'] . "[{$key}]";
					if ( $key === 'location' ) {
						$field_select                = $field;
						$field_select['size']        = '';
						$field_select['placeholder'] = __( 'Select a Location', 'ideapark-antek' );
						$field_select['flatten']     = false;
						$field_select['multiple']    = false;
						$field_select['options']     = [];

						$terms = get_terms( [
							'taxonomy'   => 'location',
							'hide_empty' => false,
						] );

						foreach ( $terms AS $term ) {
							$field_select['options'][ $term->term_id ] = $term->name;
						}

						$html[] = sprintf( $tpl, $label, RWMB_Select_Field::html( $value, $field_select ) );
					} else {
						$html[] = sprintf( $tpl, $label, RWMB_Input_Field::html( $value, $field ) );
					}
				}

				$out = '<div class="row">' . implode( ' ', $html ) . '</div>';

				return $out;
			}
		}
	}
} );
