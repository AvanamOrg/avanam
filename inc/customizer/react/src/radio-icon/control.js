import { createRoot } from '@wordpress/element';
import RadioIconComponent from './radio-icon-component.js';

export const RadioIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <RadioIconComponent control={control}/> );
		// ReactDOM.render(
		// 		<RadioIconComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
