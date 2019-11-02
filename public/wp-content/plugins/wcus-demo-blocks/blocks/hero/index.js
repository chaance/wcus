( function() {
	const { createElement } = wp.element;
	const { MediaUpload, MediaUploadCheck, RichText, PlainText } = wp.editor;
	const { __, _x } = wp.i18n;
	const { registerBlockType } = wp.blocks;

	const buttonClassName = 'wcus-button';
	const className = 'wcus-block-hero';
	const containerClass = `${ className }__container`;
	const contentClass = `${ className }__content`;
	const titleClass = `${ className }__title wcus-title`;
	const descClass = `${ className }__desc`;
	const buttonWrapperClass = `${ className }__buttons`;
	const imageWrapperClass = `${ className }__images`;
	const imageClass = `${ className }__image`;

	registerBlockType( 'wcus-demo/hero', {
		title: _x( 'Hero', 'block name', 'wcus-demo' ),
		category: 'layout',
		description: __(
			'A hero area with an image and some content',
			'wcus-demo',
		),
		keywords: [
			_x( 'hero section', 'block keyword', 'wcus-demo' ),
			_x( 'hero image', 'block keyword', 'wcus-demo' ),
		],
		attributes: {
			title: {
				source: 'html',
				selector: 'h1',
			},
			desc: {
				type: 'array',
				source: 'children',
				selector: 'p',
			},
			button1Text: {
				type: 'string',
			},
			button2Text: {
				type: 'string',
			},
			imgAlt: {
				type: 'string',
			},
			imgUrl: {
				type: 'string',
			},
		},
		edit: function( { attributes, setAttributes } ) {
			const {
				imgUrl,
				imgAlt,
				title,
				desc,
				button1Text,
				button2Text,
			} = attributes;
			return createElement( 'div', { className }, [
				createElement( 'div', { className: containerClass }, [
					createElement( 'div', { className: contentClass }, [
						createElement( RichText, {
							tagName: 'h1',
							className: titleClass,
							value: title,
							allowedFormats: [ 'core/bold' ],
							placeholder: 'Title goes here',
							onChange: function( title ) {
								setAttributes( { title } );
							},
						} ),
						createElement( RichText, {
							tagName: 'p',
							className: descClass,
							value: desc,
							placeholder:
								'Odio nisi sapien orci maecenas iaculis nascetur taciti...',
							onChange: function( desc ) {
								setAttributes( { desc } );
							},
						} ),
						createElement( 'div', { className: buttonWrapperClass }, [
							createElement( 'span', { className: `${ buttonClassName } ${ buttonClassName }--primary` }, [
								createElement( PlainText, {
									placeholder: 'Button 1',
									value: button1Text,
									onChange: function( button1Text ) {
										setAttributes( { button1Text } );
									},
								} ),
							] ),
							createElement( 'span', { className: `${ buttonClassName } ${ buttonClassName }--neutral` }, [
								createElement( PlainText, {
									placeholder: 'Button 2',
									value: button2Text,
									onChange: function( button2Text ) {
										setAttributes( { button2Text } );
									},
								} ),
							] ),
						] ),
					] ),
					createElement( 'div', { className: imageWrapperClass }, [
						createElement( MediaUploadCheck, {}, [
							createElement( MediaUpload, {
								allowedTypes: [ 'jpg', 'png', 'svg' ],
								onSelect: function( value ) {
									setAttributes( {
										imgUrl: value.sizes.full.url,
										imgAlt: value.alt || '',
									} );
								},
								render: function( { open } ) {
									return imgUrl
										? createElement( 'img', {
												onClick: open,
												className: imageClass,
												src: imgUrl,
												alt: imgAlt || '',
										  } )
										: createElement(
												'button',
												{ onClick: open, className: buttonClassName, type: 'button' },
												[ __( 'Upload Image', 'wcus-demo' ) ],
										  );
								},
							} ),
						] ),
					] ),
				] ),
			] );
		},
		save: function( { attributes } ) {
			const {
				imgUrl,
				imgAlt,
				title,
				desc,
				button1Text,
				button2Text,
			} = attributes;
			return createElement( 'div', { className }, [
				createElement( 'div', { className: containerClass }, [
					createElement( 'div', { className: contentClass }, [
						createElement( RichText.Content, {
							tagName: 'h1',
							className: titleClass,
							value: title,
						} ),
						createElement( RichText.Content, {
							tagName: 'p',
							className: descClass,
							value: desc,
						} ),
						createElement( 'div', { className: buttonWrapperClass }, [
							button1Text
								? createElement(
										'a',
										{
											href: '#',
											className: `${ buttonClassName } ${ buttonClassName }--primary`,
										},
										[ button1Text ],
								  )
								: null,
							button2Text
								? createElement(
										'a',
										{
											href: '#',
											className: `${ buttonClassName } ${ buttonClassName }--neutral`,
										},
										[ button2Text ],
								  )
								: null,
						] ),
					] ),
					createElement( 'div', { className: imageWrapperClass }, [
						imgUrl
							? createElement( 'img', {
									className: imageClass,
									src: imgUrl,
									alt: imgAlt || '',
							  } )
							: '',
					] ),
				] ),
			] );
		},
	} );
} )();
