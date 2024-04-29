import { createRoot } from '@wordpress/element';
import SocialComponent from './social-component.js';

export const SocialControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <SocialComponent control={ control } /> );
		// ReactDOM.render( <SocialComponent control={ control } />, control.container[0] );
	}
} );
