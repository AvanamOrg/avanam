/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import capitalizeFirstLetter from '../common/capitalize-first.js';
import map from 'lodash/map';
import { __ } from '@wordpress/i18n';
import Icons from '../common/icons.js';
const { ButtonGroup, Dashicon, Tooltip, TextControl, Button, SelectControl, Popover, TabPanel, ToggleControl, RangeControl, Placeholder } = wp.components;

const { Component, Fragment } = wp.element;
class FontPairModal extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.updateSettings = this.updateSettings.bind( this );
		this.state = {
			isVisible: false,
			confirm: '',
			fonts: ( baseCustomizerControlsData.fontPairs ? baseCustomizerControlsData.fontPairs : [] ),
		};
	}
	render() {
		const toggleVisible = () => {
			this.setState( { isVisible: true } );
		};
		const toggleClose = () => {
			if ( this.state.isVisible === true ) {
				this.setState( { isVisible: false } );
			}
		};
		return (
			<div className={'base-font-pair-wrap'}>
				<Button className={ 'base-font-pair-btn' } label={ __( 'Choose a font', 'avanam' ) } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible() } }>
					<Dashicon icon="portfolio" />
				</Button>
				{ this.state.isVisible && (
					<Popover position="bottom left" inline={true} className="base-font-pair-popover base-customizer-popover" onClose={ toggleClose }>
						<h2 style={{ textAlign:'center' }}>{ __( 'Select a Font Pairing', 'avanam' ) }</h2>
						<ButtonGroup className="bt-font-pair-group" aria-label={ __( 'Select a Font Pair', 'avanam' ) }>
							{ map( this.state.fonts, ( { hfont, bfont, hv, img, name } ) => {
								return (
									this.state.confirm === name ? (
										<Button
											className={ 'bt-font-pair-btn state-confirm' }
											onClick={ () => {
												this.updateSettings( hfont, bfont, hv );
											} }
										>
											{ __( 'Confirm Change Settings?', 'avanam' ) }
										</Button>
									) : (
										<Button
											className={ 'bt-font-pair-btn' }
											onClick={ () => {
												this.setState( { confirm: name } );
											} }
										>
											<img src={ img } className="font-pairing" />
											<span>{ name }</span>
										</Button>
									)
								);
							} ) }
						</ButtonGroup>
					</Popover>
				) }
			</div>
		);
	}
	updateSettings( hfont, bfont, hv ) {
		const bodyFont = this.props.customizer( 'base_font' ).get();
		bodyFont['family'] = bfont;
		bodyFont['weight'] = 'normal';
		bodyFont['google'] = true;
		const headingFont = this.props.customizer( 'heading_font' ).get();
		headingFont['family'] = hfont;
		headingFont['variant'] = hv;
		headingFont['google'] = true;
		this.updateValues( bodyFont, headingFont );
	}
	updateValues( bodyFont, headingFont ) {
		this.props.customizer( 'base_font' ).set( {
			...this.props.customizer( 'base_font' ).get(),
			...bodyFont,
			flag: ! this.props.customizer( 'base_font' ).get().flag
		} );
		this.props.customizer( 'heading_font' ).set( {
			...this.props.customizer( 'heading_font' ).get(),
			...headingFont,
			flag: ! this.props.customizer( 'heading_font' ).get().flag
		} );
		var event = new CustomEvent('baseRemoteUpdateFonts', {
			'detail': 'typography'
		});
		document.dispatchEvent(event);
		this.setState( { isVisible: false, confirm: '' } );
	}
}

FontPairModal.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default FontPairModal;
