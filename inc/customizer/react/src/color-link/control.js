import { createRoot } from '@wordpress/element';
import ColorLinkComponent from './color-link-component.js';

export const ColorLinkControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <ColorLinkComponent control={control} customizer={ wp.customize }/> );
		// ReactDOM.render(
		// 	<ColorLinkComponent control={control} customizer={ wp.customize }/>,
		// 	control.container[0]
		// );
	}
} );
