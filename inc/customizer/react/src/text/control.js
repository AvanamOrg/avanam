import { createRoot } from '@wordpress/element';
import TextComponent from './text-component.js';

export const TextControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <TextComponent control={ control } /> );
		// ReactDOM.render( <TextComponent control={ control } />, control.container[0] );
	}
} );
