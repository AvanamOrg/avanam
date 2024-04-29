import { createRoot } from '@wordpress/element';
import TitleComponent from './title-component.js';

export const TitleControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <TitleComponent control={control} /> );
		// ReactDOM.render(
		// 		<TitleComponent control={control} />,
		// 		control.container[0]
		// );
	}
} );
