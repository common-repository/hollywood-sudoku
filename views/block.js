( function( blocks, i18n, element ) {
	var el = element.createElement;
	var __ = i18n.__;

	var blockStyle = {
		backgroundColor: '#999',
		color: '#fff',
		padding: '20px',
	};

	blocks.registerBlockType( 'hollywood/sudoku', {
		title: __( 'Hollywood Sudoku', 'hollywood-sudoku' ),
		icon: 'grid-view',
		category: 'layout',
		example: {},
		edit: function() {
			return el(
				'p',
				{ style: blockStyle },
				'Hollywood Sudoku'
			);
		},
		save: function() {
			return el(
				'p',
				{ style: blockStyle },
				'Hollywood Sudoku'
			);
		},
	} );
} )( window.wp.blocks, window.wp.i18n, window.wp.element );
