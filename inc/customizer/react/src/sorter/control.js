import { createRoot } from '@wordpress/element';
import SorterComponent from './setting-sorter-component.js';

export const SorterControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <SorterComponent control={ control } customizer={ wp.customize } /> );
		// ReactDOM.render( <SorterComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
