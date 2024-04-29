import { createRoot } from '@wordpress/element';

import MultiRadioIconComponent from './multi-radio-icon-component.js';

export const MultiRadioIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <MultiRadioIconComponent control={control}/> );
		// ReactDOM.render(
		// 		<MultiRadioIconComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
