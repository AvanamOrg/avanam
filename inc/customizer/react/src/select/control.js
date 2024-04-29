import { createRoot } from '@wordpress/element';
import SelectComponent from './select-component.js';

export const SelectControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <SelectComponent control={control}/> );
		// ReactDOM.render(
		// 		<SelectComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
