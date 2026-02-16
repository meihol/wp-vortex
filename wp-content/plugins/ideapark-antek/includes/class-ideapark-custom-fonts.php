<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ideapark_Custom_Fonts {
	public function __construct() {

		add_filter( 'upload_mimes', function ( $mimes ) {
			if ( current_user_can( 'administrator' ) ) {
				$mimes['ttf']   = 'application/x-font-ttf';
				$mimes['eot']   = 'application/vnd.ms-fontobject';
				$mimes['woff']  = 'application/font-woff';
				$mimes['woff2'] = 'application/font-woff2';
				$mimes['otf']   = 'application/vnd.oasis.opendocument.formula-template';
			}

			return $mimes;
		} );

		add_filter( 'rwmb_meta_boxes', function ( $meta_boxes ) {
			$meta_boxes[] = [
				'id'     => 'ideapark_section_fonts',
				'title'  => __( 'Custom Fonts', 'ideapark-antek' ),
				'panel'  => '',
				'fields' => [
					[
						'id'         => 'custom_fonts',
						'type'       => 'group',
						'clone'      => true,
						'sort_clone' => false,
						'fields'     => [
							[
								'name' => __( 'Name', 'ideapark-antek' ),
								'id'   => 'name',
								'type' => 'text',
							],
							[
								'name' => __( 'Font .woff2', 'ideapark-antek' ),
								'id'   => 'woff2',
								'type' => 'file_input',
							],
							[
								'name' => __( 'Font .woff', 'ideapark-antek' ),
								'id'   => 'woff',
								'type' => 'file_input',
							],
							[
								'name' => __( 'Font .ttf', 'ideapark-antek' ),
								'id'   => 'ttf',
								'type' => 'file_input',
							],
							[
								'name' => __( 'Font .svg', 'ideapark-antek' ),
								'id'   => 'svg',
								'type' => 'file_input',
							],
							[
								'name' => __( 'Font .otf', 'ideapark-antek' ),
								'id'   => 'otf',
								'type' => 'file_input',
							],
							[
								'name'    => __( 'Font Display', 'ideapark-antek' ),
								'id'      => 'font_display',
								'type'    => 'select',
								'std'     => 'auto',
								'options' => [
									'auto'     => __( 'Auto', 'ideapark-antek' ),
									'block'    => __( 'Block', 'ideapark-antek' ),
									'swap'     => __( 'Swap', 'ideapark-antek' ),
									'fallback' => __( 'Fallback', 'ideapark-antek' ),
									'optional' => __( 'Optional', 'ideapark-antek' ),
								],
							],
						],
					],
				],
			];

			return $meta_boxes;
		} );
	}
}

new Ideapark_Custom_Fonts();