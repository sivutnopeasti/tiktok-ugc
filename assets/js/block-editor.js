( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, RangeControl, Placeholder } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	registerBlockType( 'instagram-profile-embed/profile', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { username, height } = attributes;
			const blockProps = useBlockProps( {
				className: 'ipe-block-editor',
			} );

			const heightValue = height !== '' ? parseInt( height, 10 ) : 600;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Instagram-asetukset', 'instagram-profile-embed' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Instagram-käyttäjänimi', 'instagram-profile-embed' ),
							help: __( 'Ilman @-merkkiä, esim. sara_rai', 'instagram-profile-embed' ),
							value: username,
							onChange: function ( value ) {
								setAttributes( { username: value } );
							},
						} ),
						el( RangeControl, {
							label: __( 'Korkeus (px)', 'instagram-profile-embed' ),
							value: heightValue,
							onChange: function ( value ) {
								setAttributes( { height: String( value ) } );
							},
							min: 200,
							max: 1200,
							step: 50,
						} )
					)
				),
				el(
					'div',
					blockProps,
					username
						? el( ServerSideRender, {
								block: 'instagram-profile-embed/profile',
								attributes: attributes,
						  } )
						: el(
								Placeholder,
								{
									icon: 'instagram',
									label: __( 'Instagram Profile Embed', 'instagram-profile-embed' ),
									instructions: __(
										'Anna Instagram-käyttäjänimi sivupaneelista.',
										'instagram-profile-embed'
									),
								},
								el( TextControl, {
									label: __( 'Instagram-käyttäjänimi', 'instagram-profile-embed' ),
									value: username,
									onChange: function ( value ) {
										setAttributes( { username: value } );
									},
								} )
						  )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
