import { createRoot } from '@wordpress/element';
import RowLayoutComponent from './row-layout-component';

export const RowControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <RowLayoutComponent control={control} customizer={ wp.customize }/> );
		// ReactDOM.render(
		// 		<RowLayoutComponent control={control} customizer={ wp.customize }/>,
		// 		control.container[0]
		// );
	}
} );
