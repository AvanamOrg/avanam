import ColorComponent from './color-component.js';

export const ColorControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
			<ColorComponent control={control} customizer={ wp.customize }/>,
			control.container[0]
		);
	}
} );
