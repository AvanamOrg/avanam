import { createRoot } from '@wordpress/element';
import FocusButtonComponent from './focus-button-component';

export const FocusButtonControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <FocusButtonComponent control={ control } customizer={ wp.customize } /> );
		// ReactDOM.render( <FocusButtonComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
