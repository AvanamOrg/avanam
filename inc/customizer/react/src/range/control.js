import { createRoot } from '@wordpress/element';
import RangeComponent from './range-component.js';

export const RangeControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <RangeComponent control={control}/> );
		// ReactDOM.render(
		// 		<RangeComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
