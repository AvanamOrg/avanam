import { createRoot } from '@wordpress/element';
import PaletteComponent from './palette-component.js';

export const PaletteControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <PaletteComponent control={control}/> );
		// ReactDOM.render(
		// 		<PaletteComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
