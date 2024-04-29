import { createRoot } from '@wordpress/element';
import TabsComponent from './tabs-component';

export const TabsControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <TabsComponent control={ control } customizer={ wp.customize } /> );
		// ReactDOM.render( <TabsComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
