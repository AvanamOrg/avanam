import { createRoot } from '@wordpress/element';
import CheckIconComponent from './check-icon-component.js';

export const CheckIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <CheckIconComponent control={control}/> );
		// ReactDOM.render(
		// 		<CheckIconComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
