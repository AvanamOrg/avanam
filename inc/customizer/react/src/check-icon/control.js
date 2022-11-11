import CheckIconComponent from './check-icon-component.js';

export const CheckIconControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render(
				<CheckIconComponent control={control}/>,
				control.container[0]
		);
	}
} );
