( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, RangeControl, Placeholder } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	registerBlockType( 'tiktok-profile-embed/profile', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { username, height } = attributes;
			const blockProps = useBlockProps( {
				className: 'tpe-block-editor',
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
						{ title: __( 'TikTok-asetukset', 'tiktok-profile-embed' ), initialOpen: true },
						el( TextControl, {
							label: __( 'TikTok-käyttäjänimi', 'tiktok-profile-embed' ),
							help: __( 'Ilman @-merkkiä, esim. sara_rai', 'tiktok-profile-embed' ),
							value: username,
							onChange: function ( value ) {
								setAttributes( { username: value } );
							},
						} ),
						el( RangeControl, {
							label: __( 'Korkeus (px)', 'tiktok-profile-embed' ),
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
								block: 'tiktok-profile-embed/profile',
								attributes: attributes,
						  } )
						: el(
								Placeholder,
								{
									icon: 'video-alt3',
									label: __( 'TikTok Profile Embed', 'tiktok-profile-embed' ),
									instructions: __(
										'Anna TikTok-käyttäjänimi sivupaneelista.',
										'tiktok-profile-embed'
									),
								},
								el( TextControl, {
									label: __( 'TikTok-käyttäjänimi', 'tiktok-profile-embed' ),
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
