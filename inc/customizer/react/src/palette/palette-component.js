import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';
/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from '@wordpress/element';
import map from 'lodash/map';
import ColorControl from '../common/color.js';
const { ButtonGroup, Dashicon, Tooltip, Button, Popover, TabPanel, TextareaControl } = wp.components;
class ColorComponent extends Component {
	constructor(props) {
		super( props );
		this.handleChangeComplete = this.handleChangeComplete.bind( this );
		this.handleChangePalette = this.handleChangePalette.bind( this );
		this.handleTextImport = this.handleTextImport.bind( this );
		this.handlePresetImport = this.handlePresetImport.bind( this );
		this.updateValues = this.updateValues.bind( this );
		let value = JSON.parse( this.props.control.setting.get() );
		let baseDefault =  ( baseCustomizerControlsData.palette ? JSON.parse( baseCustomizerControlsData.palette ) : { palette: [] } );
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...this.defaultValue,
			...value
		} : this.defaultValue;
		let defaultParams = {
			reset: '{"palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"}],"second-palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"}],"third-palette":[{"color":"#3182CE","slug":"palette1","name":"Palette Color 1"},{"color":"#2B6CB0","slug":"palette2","name":"Palette Color 2"},{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},{"color":"#718096","slug":"palette6","name":"Palette Color 6"},{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},{"color":"#FFFFFF","slug":"palette9","name":"Palette Color 9"}],"active":"palette"}',
			palettes: ( baseCustomizerControlsData.palettePresets ? baseCustomizerControlsData.palettePresets : [] ),
			colors: {
				palette1: {
					tooltip: __( '1 - Accent', 'avanam' ),
					palette: false,
				},
				palette2: {
					tooltip: __( '2 - Accent - alt', 'avanam' ),
					palette: false,
				},
				palette3: {
					tooltip: __( '3 - Strongest text', 'avanam' ),
					palette: false,
				},
				palette4: {
					tooltip: __( '4 - Strong Text', 'avanam' ),
					palette: false,
				},
				palette5: {
					tooltip: __( '5 - Medium text', 'avanam' ),
					palette: false,
				},
				palette6: {
					tooltip: __( '6 - Subtle Text', 'avanam' ),
					palette: false,
				},
				palette7: {
					tooltip: __( '7 - Subtle Background', 'avanam' ),
					palette: false,
				},
				palette8: {
					tooltip: __( '8 - Lighter Background', 'avanam' ),
					palette: false,
				},
				palette9: {
					tooltip: __( '9 - White or offwhite', 'avanam' ),
					palette: false,
				},
			},
			paletteMap: {
				'palette': {
					'tooltip': __( 'Palette 1', 'avanam' ),
				},
				'second-palette': {
					'tooltip': __( 'Palette 2', 'avanam' ),
				},
				'third-palette': {
					'tooltip': __( 'Palette 3', 'avanam' ),
				}
			}
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		this.state = {
			value: value,
			colorPalette: [],
			fresh: 'start',
			isVisible: false,
			textImport: '',
			importError: false,
		};
		this.anchorNodeRef = createRef();
	}
	handleChangePalette( active ) {
		let value = this.state.value;
		const newItems = this.state.value[ active ].map( ( item, index ) => {
			document.documentElement.style.setProperty('--global-' + item.slug, item.color );
			return item;
		} );
		value.active  = active;
		value[active] = newItems;
		this.setState( { fresh: ( this.state.fresh !== 'start' ? 'start' : 'second' ) } );
		this.updateValues( value );
	}
	handlePresetImport( preset ) {
		const presetPalettes = JSON.parse( this.controlParams.palettes );
		// Verify data.
		if ( presetPalettes && presetPalettes[preset] && 9 === presetPalettes[preset].length ) {
			const newItems = presetPalettes[preset].map( ( item, index ) => {
				if ( item.color ) {
					this.handleChangeComplete( { hex: item.color }, false, '', index );
				}
			} );
			this.setState( { fresh: ( this.state.fresh !== 'start' ? 'start' : 'second' ), importError:false } );
		} else {
			this.setState( { importPresetError:true } );
		}
	}
	handleTextImport() {
		const importText = this.state.textImport;
		if ( ! importText ) {
			this.setState( { importError:true } );
			return;
		}
		const textImportData = JSON.parse( importText );
		// Verify data.
		if ( textImportData && textImportData instanceof Array && textImportData.length === 9 && textImportData[0] && textImportData[0].color ) {
			const newItems = textImportData.map( ( item, index ) => {
				if ( item.color ) {
					this.handleChangeComplete( { hex: item.color }, false, '', index );
				}
			} );
			this.setState( { fresh: ( this.state.fresh !== 'start' ? 'start' : 'second' ), textImport: '', isVisible:false, importError:false } );
		} else {
			this.setState( { importError:true } );
		}
	}
	handleChangeComplete( color, isPalette, item, index ) {
		let newColor = {};
		if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			newColor.color = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			newColor.color = color.hex;
		}
		let value = this.state.value;
		const newItems = this.state.value[this.state.value.active].map( ( item, thisIndex ) => {
			if ( parseInt( index ) === parseInt( thisIndex ) ) {
				item = { ...item, ...newColor };
				document.documentElement.style.setProperty('--global-' + this.state.value[this.state.value.active][index].slug, newColor.color );
			}

			return item;
		} );
		value[this.state.value.active] = newItems;
		this.updateValues( value );
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
		const presetPalettes = JSON.parse( this.controlParams.palettes );
		let currentPaletteData = [];
		Object.keys( this.controlParams.colors ).map( ( item, index ) => {
			return(
				currentPaletteData.push( {color: ( undefined !== this.state.value[this.state.value.active][ index ] &&  this.state.value[this.state.value.active][ index ].color ?  this.state.value[this.state.value.active][ index ].color : '' ),
				} )
			)
		} );
		const currentPaletteJson = JSON.stringify(currentPaletteData);
		return (
				<div className="base-control-field base-palette-control base-color-control">
					<div className="base-palette-header">
						<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
							<Button
								className="reset base-reset"
								onClick={ () => {
									let value = this.state.value;
									const reset = JSON.parse( this.controlParams.reset );
									const newItems = this.state.value[this.state.value.active].map( ( item, thisIndex ) => {
										item = { ...item, ...reset[this.state.value.active][ thisIndex ] };
										document.documentElement.style.setProperty('--global-' + reset[this.state.value.active][ thisIndex ].slug, reset[this.state.value.active][ thisIndex ].color );						
										return item;
									} );
									value[this.state.value.active] = newItems;
									this.updateValues( value );
								} }
							>
								<Dashicon icon='image-rotate' />
							</Button>
						</Tooltip>
						{
							this.props.control.params.label &&
							<span className="customize-control-title">
								{ this.props.control.params.label }
							</span>
						}
						{
							!this.props.hideResponsive &&
							<div className="floating-controls">
								<ButtonGroup>
									{Object.keys( this.controlParams.paletteMap ).map( (palette) => {
										return (
											<Tooltip text={this.controlParams.paletteMap[palette].tooltip}>
												<Button
														isTertiary
														className={ ( palette === this.state.value.active ?
																'active-palette ' :
																'' ) + palette}
														onClick={ () => {
															this.handleChangePalette( palette );
														} }
												>
													{ this.controlParams.paletteMap[palette].tooltip }
												</Button>
											</Tooltip>
										);
									} )}
								</ButtonGroup>
							</div>
						}
					</div>
					<div ref={ this.anchorNodeRef } className="base-palette-colors">
						{ 'start' === this.state.fresh && (
							<Fragment>
								{ Object.keys( this.controlParams.colors ).map( ( item, index ) => {
										return (
											<ColorControl
												key={ index }
												presetColors={ this.state.colorPalette }
												color={ ( undefined !== this.state.value[this.state.value.active][ index ] &&  this.state.value[this.state.value.active][ index ].color ?  this.state.value[this.state.value.active][ index ].color : '' ) }
												isPalette={ '' }
												usePalette={ false }
												tooltip={ ( undefined !== this.controlParams.colors[ item ].tooltip ? this.controlParams.colors[ item ].tooltip : '' ) }
												onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette, item, index ) }
												controlRef={ this.anchorNodeRef }
											/>
										)
								} ) }
							</Fragment>
						) }
						{ 'start' !== this.state.fresh && (
							<Fragment>
								{ Object.keys( this.controlParams.colors ).map( ( item, index ) => {
										return (
											<ColorControl
												key={ index }
												presetColors={ this.state.colorPalette }
												color={ ( undefined !== this.state.value[this.state.value.active][ index ] &&  this.state.value[this.state.value.active][ index ].color ?  this.state.value[this.state.value.active][ index ].color : '' ) }
												isPalette={ '' }
												usePalette={ false }
												tooltip={ ( undefined !== this.controlParams.colors[ item ].tooltip ? this.controlParams.colors[ item ].tooltip : '' ) }
												onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette, item, index ) }
												controlRef={ this.anchorNodeRef }
											/>
										)
								} ) }
							</Fragment>
						) }
					</div>
					<div className={'base-palette-import-wrap'}>
						<Button className={ 'base-palette-import' } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible() } }>
							<Dashicon icon="portfolio" />
						</Button>
						{ this.state.isVisible && (
							<Popover position="bottom right" inline={true} className="base-palette-popover-copy-paste base-customizer-popover" onClose={ toggleClose }>
								<TabPanel className="base-palette-popover-tabs"
									activeClass="active-tab"
									initialTabName={ 'import'}
									tabs={ [
										{
											name: 'import',
											title: __( 'Select Palette', 'avanam' ),
											className: 'base-color-presets',
										},
										{
											name: 'custom',
											title: __( 'Export/Import', 'avanam' ),
											className: 'base-export-import',
										}
									] }>
									{
										( tab ) => {
											let tabout;
											if ( tab.name ) {
												if ( 'import' === tab.name ) {
													tabout = (
														<Fragment>
															{ Object.keys( presetPalettes ).map( ( item, index ) => {
																return (
																	<Button
																		className={ 'base-palette-item' }
																		style={ {
																			height: '100%',
																			width: '100%',
																		} }
																		onClick={ () => this.handlePresetImport( item ) }
																		tabIndex={ 0 }
																	>
																		{ Object.keys( presetPalettes[item] ).map( ( color, subIndex ) => {
																			return (
																				<div key={ subIndex } style={ {
																					width: 26,
																					height: 26,
																					marginBottom: 0,
																					transform: 'scale(1)',
																					transition: '100ms transform ease',
																				} } className="base-swatche-item-wrap">
																					<span
																						className={ 'base-swatch-item' }
																						style={ {
																							height: '100%',
																							display: 'block',
																							width: '100%',
																							border: '1px solid rgb(218, 218, 218)',
																							borderRadius: '50%',
																							color: `${ presetPalettes[item][color].color }`,
																							boxShadow: `inset 0 0 0 ${ 30 / 2 }px`,
																							transition: '100ms box-shadow ease',
																						} }
																						>
																					</span>
																				</div>
																			)
																		} ) }
																	</Button>
																)
															} ) }
														</Fragment>
													);
												} else {
													tabout = (
														<Fragment>
															<h2>{ __('Export', 'avanam' ) }</h2>
															<TextareaControl
																label=""
																help="Copy export data to use in another site."
																value={ currentPaletteJson }
																onChange={ false }
															/>
															<h2>{ __('Import', 'avanam' ) }</h2>
															<TextareaControl
																label="Import color set from text data."
																help="Follow format from export above."
																value={ this.state.textImport }
																onChange={ ( text ) => this.setState( { textImport: text } ) }
															/>
															{ this.state.importError && (
																<p style={{color:'red'}}>{ __( 'Error with Import data', 'avanam') }</p>
															) }
															<Button
																className={ 'base-import-button' }
																isPrimary
																disabled={ this.state.textImport ? false : true }
																onClick={ () => this.handleTextImport() }
															>
																{ __('Import', 'avanam' ) }
															</Button>
														</Fragment>
													);
												}
											}
											return <div>{ tabout }</div>;
										}
									}
								</TabPanel>
							</Popover>
						) }
					</div>
					{ this.props.control.params.description && (
						<span className="customize-control-description"><a href="https://avanam.org/wordpress#color-palette" target="_blank">{ this.props.control.params.description }</a></span>
					) }
				</div>
		);
	}

	updateValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( JSON.stringify( value ) );
		baseCustomizerControlsData.palette = JSON.stringify( value );
	}
}

ColorComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default ColorComponent;
