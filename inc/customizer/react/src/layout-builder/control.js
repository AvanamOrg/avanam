import { createRoot } from '@wordpress/element';
import BuilderComponent from './builder-component.js';

export const BuilderControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <BuilderComponent control={ control } customizer={ wp.customize } /> );
		// ReactDOM.render( <BuilderComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
