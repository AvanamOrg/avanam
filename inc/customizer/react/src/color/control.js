import { createRoot } from '@wordpress/element';
import ColorComponent from './color-component.js';

export const ColorControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <ColorComponent control={control} customizer={ wp.customize }/> );
		// ReactDOM.render(
		// 	<ColorComponent control={control} customizer={ wp.customize }/>,
		// 	control.container[0]
		// );
	}
} );
