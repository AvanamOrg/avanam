import BordersComponent from './borders-component.js';

export const BordersControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<BordersComponent control={control} customizer={ wp.customize }/>,
				control.container[0]
		);
	}
} );
