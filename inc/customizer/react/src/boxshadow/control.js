import { createRoot } from '@wordpress/element';
import BoxShadowComponent from './boxshadow-component.js';

export const BoxShadowControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <BoxShadowComponent control={control} customizer={ wp.customize }/> );
		// ReactDOM.render(
		// 		<BoxShadowComponent control={control} customizer={ wp.customize }/>,
		// 		control.container[0]
		// );
	}
} );
