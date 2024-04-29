import { createRoot } from '@wordpress/element';
import EditorComponent from './editor-component.js';

export const EditorControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <EditorComponent control={control} customizer={ wp.customize } /> );
		// ReactDOM.render(
		// 		<EditorComponent control={control} customizer={ wp.customize } />,
		// 		control.container[0]
		// );
	}
} );
