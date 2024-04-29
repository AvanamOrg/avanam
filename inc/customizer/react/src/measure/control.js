import { createRoot } from '@wordpress/element';
import MeasureComponent from './measure-component';

export const MeasureControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <MeasureComponent control={control}/> );
		// ReactDOM.render(
		// 		<MeasureComponent control={control}/>,
		// 		control.container[0]
		// );
	}
} );
