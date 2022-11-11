import SorterComponent from './setting-sorter-component.js';

export const SorterControl = wp.customize.BaseControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		ReactDOM.render( <SorterComponent control={ control } customizer={ wp.customize } />, control.container[0] );
	}
} );
