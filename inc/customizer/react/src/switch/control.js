import { createRoot } from '@wordpress/element';
import SwitchComponent from './switch-component.js';

export const SwitchControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <SwitchComponent control={control}/> );
		// ReactDOM.render(
		// 		<SwitchComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
