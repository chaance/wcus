( function() {
	const { createElement } = wp.element;
	const { RichText } = wp.editor;
	const { __, _x } = wp.i18n;
	const { registerBlockType } = wp.blocks;

	const buttonClassName = 'wcus-button';
	const className = 'wcus-block-cta-form';
	const containerClass = `${ className }__container`;
	const contentClass = `${ className }__content`;
	const titleClass = `${ className }__title wcus-h1`;
	const descClass = `${ className }__desc`;
	const formClass = `${ className }__form`;
	const labelClass = `${ className }__label-wrap`;
	const fieldClass = `${ className }__fleid`;
	const textareaClass = `${ className }__fleid ${ className }__fleid--textarea`;
	const buttonClass = `${ buttonClassName } ${ buttonClassName }--primary ${ className }__submit`;

	console.log('sdfkljsdjflksdjfkldjsklfjsdlkfjdslk');

	registerBlockType( 'wcus-demo/cta-form', {
		title: _x( 'CTA Form', 'block name', 'wcus-demo' ),
		category: 'layout',
		description: __( 'A CTA region with a form.', 'wcus-demo' ),
		keywords: [
			_x( 'cta', 'block keyword', 'wcus-demo' ),
			_x( 'form', 'block keyword', 'wcus-demo' ),
		],
		attributes: {
			title: {
				source: 'html',
				selector: 'h2',
			},
			desc: {
				type: 'array',
				source: 'children',
				selector: 'p',
			},
		},
		edit: function( { attributes, setAttributes } ) {
			const { title, desc } = attributes;
			return createElement( 'div', { className }, [
				createElement( 'div', { className: containerClass }, [
					createElement( 'div', { className: contentClass }, [
						createElement( RichText, {
							tagName: 'h2',
							className: titleClass,
							value: title,
							allowedFormats: [],
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
					] ),
				] ),
			] );
		},
		save: function( { attributes } ) {
			const { title, desc } = attributes;
			return createElement( 'div', { className }, [
				createElement( 'div', { className: containerClass }, [
					createElement( 'div', { className: contentClass }, [
						createElement( RichText.Content, {
							tagName: 'h2',
							className: titleClass,
							value: title,
						} ),
						createElement( RichText.Content, {
							tagName: 'p',
							className: descClass,
							value: desc,
						} ),
						createElement( 'form', { className: formClass }, [
							createElement( 'label', { className: labelClass }, [
								createElement( 'span', {}, [ 'Name' ] ),
								createElement( 'input', {
									className: fieldClass,
									type: 'text',
									name: 'name',
								} ),
							] ),
							createElement( 'label', { className: labelClass }, [
								createElement( 'span', {}, [ 'Email' ] ),
								createElement( 'input', {
									className: fieldClass,
									type: 'email',
									name: 'email',
								} ),
							] ),
							createElement( 'label', { className: labelClass }, [
								createElement( 'span', {}, [ 'Comments' ] ),
								createElement( 'textarea', {
									className: textareaClass,
									name: 'comments',
								} ),
							] ),
							createElement(
								'button',
								{ className: buttonClass, type: 'submit' },
								[ __( 'Submit', 'wcus-demo' ) ],
							),
						] ),
					] ),
				] ),
			] );
		},
	} );
} )();
