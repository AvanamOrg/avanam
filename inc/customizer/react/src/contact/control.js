import ContactComponent from './contact-component.js';

export const ContactControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render( <ContactComponent control={ control } />, control.container[0] );
	}
} );
