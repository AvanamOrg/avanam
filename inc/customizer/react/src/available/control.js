import { createRoot } from '@wordpress/element';
import AvailableComponent from './available-component.js';

export const AvailableControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <AvailableComponent control={ control } customizer={ wp.customize } /> );
		// ReactDOM.render( <AvailableComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
