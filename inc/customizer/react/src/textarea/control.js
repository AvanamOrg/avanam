import { createRoot } from '@wordpress/element';
import TextareaComponent from './textarea-component.js';

export const TextareaControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <TextareaComponent control={ control } /> );
		// ReactDOM.render( <TextareaComponent control={ control } />, control.container[0] );
	}
} );
