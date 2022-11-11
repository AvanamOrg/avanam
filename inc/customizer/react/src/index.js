/* global wp */
import { Base } from './customizer.js';
import { BaseControl } from './base/control.js';
import { ColorControl } from './color/control.js';
import { PaletteControl } from './palette/control.js';
import { RangeControl } from './range/control.js';
import { SwitchControl } from './switch/control.js';
import { RadioIconControl } from './radio-icon/control.js';
import { MultiRadioIconControl } from './multi-radio-icon/control.js';
import { BuilderControl } from './layout-builder/control.js';
import { AvailableControl } from './available/control.js';
import { BackgroundControl } from './background/control.js';
import { BorderControl } from './border/control.js';
import { BordersControl } from './borders/control.js';
import { TypographyControl } from './typography/control.js';
import { TitleControl } from './title/control.js';
import { FocusButtonControl } from './focus-button/control.js';
import { ColorLinkControl } from './color-link/control.js';
import { TextControl } from './text/control.js';
import { MeasureControl } from './measure/control.js';
import { EditorControl } from './editor/control.js';
import { SocialControl } from './social/control.js';
import { ContactControl } from './contact/control.js';
import { CheckIconControl } from './check-icon/control.js';
import { SelectControl } from './select/control.js';
import { SorterControl } from './sorter/control.js';
import { RowControl } from './row-layout/control.js';
import { TabsControl } from './tabs/control.js';
import { BoxShadowControl } from './boxshadow/control.js';

wp.customize.controlConstructor.base_shadow_control = BoxShadowControl;
wp.customize.controlConstructor.base_tab_control = TabsControl;
wp.customize.controlConstructor.base_borders_control = BordersControl;
wp.customize.controlConstructor.base_row_control = RowControl;
wp.customize.controlConstructor.base_sorter_control = SorterControl;
wp.customize.controlConstructor.base_select_control = SelectControl;
wp.customize.controlConstructor.base_check_icon_control = CheckIconControl;
wp.customize.controlConstructor.base_contact_control = ContactControl;
wp.customize.controlConstructor.base_social_control = SocialControl;
wp.customize.controlConstructor.base_editor_control = EditorControl;
wp.customize.controlConstructor.base_measure_control = MeasureControl;
wp.customize.controlConstructor.base_text_control = TextControl;
wp.customize.controlConstructor.base_color_link_control = ColorLinkControl;
wp.customize.controlConstructor.base_focus_button_control = FocusButtonControl;
wp.customize.controlConstructor.base_title_control = TitleControl;
wp.customize.controlConstructor.base_typography_control = TypographyControl;
wp.customize.controlConstructor.base_border_control = BorderControl;
wp.customize.controlConstructor.base_background_control = BackgroundControl;
wp.customize.controlConstructor.base_color_palette_control = PaletteControl;
wp.customize.controlConstructor.base_available_control = AvailableControl;
wp.customize.controlConstructor.base_builder_control = BuilderControl;
wp.customize.controlConstructor.base_color_control = ColorControl;
wp.customize.controlConstructor.base_range_control = RangeControl;
wp.customize.controlConstructor.base_switch_control = SwitchControl;
wp.customize.controlConstructor.base_radio_icon_control = RadioIconControl;
wp.customize.controlConstructor.base_multi_radio_icon_control = MultiRadioIconControl;

window.addEventListener( 'load', () => {
	let deviceButtons = document.querySelector('#customize-footer-actions .devices' );
	deviceButtons.addEventListener( 'click', function(e) {
		let event = new CustomEvent( 'baseChangedRepsonsivePreview', {
			'detail': e.target.dataset.device
		} );
		document.dispatchEvent( event );
	} );
} );