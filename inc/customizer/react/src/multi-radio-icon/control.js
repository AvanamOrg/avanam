import MultiRadioIconComponent from './multi-radio-icon-component.js';

export const MultiRadioIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<MultiRadioIconComponent control={control}/>,
				control.container[0]
		);
	}
} );
