import RadioIconComponent from './radio-icon-component.js';

export const RadioIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<RadioIconComponent control={control}/>,
				control.container[0]
		);
	}
} );
