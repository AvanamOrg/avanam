/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import capitalizeFirstLetter from '../common/capitalize-first.js';
import { __ } from '@wordpress/i18n';
import Icons from '../common/icons.js';
const { MediaUpload } = wp.blockEditor;
const { ButtonGroup, Dashicon, Tooltip, TextControl, Button, SelectControl, TabPanel, ToggleControl, RangeControl, Placeholder } = wp.components;

const { Component, Fragment } = wp.element;
class ItemComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		let value = this.props.control.settings[this.props.item] ? this.props.control.settings[this.props.item].get() : '';
		let baseDefault = { 'enabled': false };
		if ( this.props.setting === 'product_content_element' && this.props.item === 'payments' ) {
			baseDefault = {
				'enabled': false,
				'title': 'GUARANTEED SAFE CHECKOUT',
				'visa': true,
				'mastercard': true,
				'amex': true,
				'discover': true,
				'paypal': true,
				'applepay': false,
				'stripe': false,
				'card_color': 'inherit',
				'custom_enable_01': false,
				'custom_img_01': '',
				'custom_id_01': '',
				'custom_enable_02': false,
				'custom_img_02': '',
				'custom_id_02': '',
				'custom_enable_03': false,
				'custom_img_03': '',
				'custom_id_03': '',
				'custom_enable_04': false,
				'custom_img_04': '',
				'custom_id_04': '',
				'custom_enable_05': false,
				'custom_img_05': '',
				'custom_id_05': '',
			};
		}
		baseDefault = this.props.controlParams.defaults && this.props.controlParams.defaults[this.props.item] ? {
			...baseDefault,
			...this.props.controlParams.defaults[this.props.item]
		} : baseDefault;
		value = value ? {
			...JSON.parse( JSON.stringify( baseDefault ) ),
			...value
		} : JSON.parse( JSON.stringify( baseDefault ) );
		this.state = {
			open: false,
			item: value,
		};
	}
	render() {
		let sizeOptions;
		{ undefined !== this.state.item.size && (
			sizeOptions = Object.keys( this.props.controlParams.imageSizes ).map( ( item ) => { 
				return ( { label: this.props.controlParams.imageSizes[ item ].name, value: item } );
			} )
		) }
		let taxOptions;
		{ undefined !== this.state.item.taxonomy && (
			taxOptions = Object.keys( this.props.controlParams.taxonomies ).map( ( item ) => { 
				return ( { label: this.props.controlParams.taxonomies[ item ], value: item } );
			} )
		) }
		if ( taxOptions ) {
			taxOptions = [{ label: __( 'Select', 'avanam' ), value: '' }].concat( taxOptions );
		}

		// An item has panel content if it's item object has more than just 'enabled' and/or 'flag'.
		const hasPanelContent = ! ( ( Object.keys(this.state.item).length === 1 && this.state.item.hasOwnProperty('enabled') ) || ( Object.keys(this.state.item).length === 2 && this.state.item.hasOwnProperty('enabled') && this.state.item.hasOwnProperty('flag') ) );

		return (
			<div className="base-sorter-item" data-id={ this.props.item } key={ this.props.item }>
				<div className={ `base-sorter-item-panel-header ${ ( this.state.item.enabled ? 'panel-item-is-visible' : 'panel-item-is-hidden' ) }` }>
					{ this.props.moveable && ( 
						<span
							className="base-builder-item-icon base-move-icon"
						>
							{ Icons['drag'] }
						</span>
					) }
					<Tooltip text={ __( 'Toggle Item Visibility', 'avanam' ) }>
						<Button
							className={ `base-sorter-visiblity ${ ( this.state.item.enabled ? 'item-is-visible' : 'item-is-hidden' ) }`}
							onClick={ () => {
								this.updateValues( { enabled: ( this.state.item.enabled ? false : true ) } );
							} }
						>
							<Dashicon icon={ ( this.state.item.enabled ? 'visibility' : 'hidden' ) } />
						</Button>
					</Tooltip>
					<span className="base-sorter-title">
						{ capitalizeFirstLetter( this.props.item.replace( /_/g, ' ' ) ) }
					</span>
					{ hasPanelContent && (
						<Tooltip text={ __( 'Expand Item Controls', 'avanam' ) }>
							<Button
								className="base-sorter-item-expand"
								onClick={ () => {
									this.setState( { open: ( this.state.open ? false : true ) } )
								} }
							>
								<Dashicon icon={ ( this.state.open ? 'arrow-up-alt2' : 'arrow-down-alt2' ) }/>
							</Button>
						</Tooltip>
					) }
				</div>
				{ this.state.open && (
					<div className="base-sorter-item-panel-content">
						{ undefined !== this.state.item.ratio && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Set Image Ratio', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control base-featured-image-ratio">
									{ Object.keys( this.props.controlParams.ratios ).map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.ratio ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { ratio: item } );
													} }
												>
													{ this.props.controlParams.ratios[ item ].icon && (
														<span className="base-radio-icon">
															{ Icons[ this.props.controlParams.ratios[ item ].icon ] }
														</span>
													) }
													{ this.props.controlParams.ratios[ item ].name && (
														this.props.controlParams.ratios[ item ].name
													) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.imageLink && (
							<div className="components-base-control">
								<ToggleControl
									label={ __( 'Image is link to post', 'avanam' ) }
									checked={ this.state.item.imageLink }
									onChange={ ( value ) => {
										this.updateValues( { imageLink: value } );
									} }
								/>
							</div>
						) }
						{ undefined !== this.state.item.show_title && (
							<div className="components-base-control">
								<ToggleControl
									label={ __( 'Show Post Title in Breadcrumb?', 'avanam' ) }
									checked={ this.state.item.show_title }
									onChange={ ( value ) => {
										this.updateValues( { show_title: value } );
									} }
								/>
							</div>
						) }
						{ undefined !== this.state.item.fullContent && (
							<div className="components-base-control">
								<ToggleControl
									label={ __( 'Show Full Content?', 'avanam' ) }
									checked={ this.state.item.fullContent }
									onChange={ ( value ) => {
										this.updateValues( { fullContent: value } );
									} }
								/>
								{ undefined !== this.state.item.words && false === this.state.item.fullContent && (
									<RangeControl
										initialPosition={ this.state.item.words ? this.state.item.words : 55 }
										value={ ( undefined !== this.state.item.words ? this.state.item.words : 55 ) }
										onChange={ ( value ) => {
											this.updateValues( { words: value } );
										} }
										min={ 10 }
										max={ 125 }
										step={ 1 }
										label={ __( 'Excerpt Word Count', 'avanam' ) }
									/>
								) }
							</div>
						) }
						{ undefined !== this.state.item.size && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Set Image Size', 'avanam' ) }</span>
								<SelectControl
									value={ this.state.item.size }
									options={ sizeOptions }
									onChange={ value => {
										this.updateValues( { size: value } );
									} }
								/>
							</div>
						) }
						{ undefined !== this.state.item.divider && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose a Divider', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ Object.keys( this.props.controlParams.dividers ).map( ( item ) => {
										if ( undefined !== this.state.item.style && item === 'customicon' ) {
											return false;
										}
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.divider ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { divider: item } );
													} }
												>
													{ this.props.controlParams.dividers[ item ].icon && (
														<span className="base-radio-icon">
															{ Icons[ this.props.controlParams.dividers[ item ].icon ] }
														</span>
													) }
													{ this.props.controlParams.dividers[ item ].name && (
														this.props.controlParams.dividers[ item ].name
													) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.button_size && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose Button Size', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'normal', 'medium-large', 'large' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.button_size ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { button_size: item } );
													} }
												>
														{ capitalizeFirstLetter( item ) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.style && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose a Style', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'normal', 'pill', 'underline' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.style ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { style: item } );
													} }
												>
														{ capitalizeFirstLetter( item ) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.taxonomy && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose a Taxonomy', 'avanam' ) }</span>
								<SelectControl
									value={ this.state.item.taxonomy }
									options={ taxOptions }
									onChange={ value => {
										this.updateValues( { taxonomy: value } );
									} }
								/>
							</div>
						) }
						{ undefined !== this.state.item.show_shipping && (
							<div className="components-base-control">
								<ToggleControl
									label={ __( 'Show Shipping Statement?', 'avanam' ) }
									checked={ this.state.item.show_shipping }
									onChange={ ( value ) => {
										this.updateValues( { show_shipping: value } );
									} }
								/>
								{ this.state.item.show_shipping && (
									<Fragment>
										{ undefined !== this.state.item.shipping_statement && (
											<div className="meta-label-control">
												<span className="sorter-control-title">{ __( 'Shipping Excerpt', 'avanam' ) }</span>
												<div className={ 'meta-label-input-control' }>
													<TextControl
														label=""
														value={ this.state.item.shipping_statement ? this.state.item.shipping_statement : this.state.item.shipping_statement }
														onChange={ ( value ) => {
															this.updateValues( { shipping_statement: value } );
														} }
													/>
												</div>
											</div>
										) }
									</Fragment>
								) }
							</div>
						) }
						{ undefined !== this.state.item.title && (
							<TextControl
								label={ __( 'Title', 'avanam' ) }
								value={ this.state.item.title }
								onChange={ ( value ) => {
									this.updateValues( { title: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.label && (
							<TextControl
								label={ __( 'Label', 'avanam' ) }
								value={ this.state.item.label }
								onChange={ ( value ) => {
									this.updateValues( { label: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.feature_1 && (
							<div className="sorter-sub-option">
								<TextControl
									label={ __( 'First Feature', 'avanam' ) }
									value={ this.state.item.feature_1 }
									onChange={ ( value ) => {
										this.updateValues( { feature_1: value } );
									} }
								/>
								<span className="sorter-control-title">{ __( 'Choose a Icon', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'shield_check', 'check', 'checkbox', 'checkbox_alt', 'disc' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.feature_1_icon ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { feature_1_icon: item } );
													} }
												>
														<span className="base-radio-icon">
															{ Icons[ item ] }
														</span>
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.feature_2 && (
							<div className="sorter-sub-option">
								<TextControl
									label={ __( 'Second Feature', 'avanam' ) }
									value={ this.state.item.feature_2 }
									onChange={ ( value ) => {
										this.updateValues( { feature_2: value } );
									} }
								/>
								<span className="sorter-control-title">{ __( 'Choose a Icon', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'shield_check', 'check', 'checkbox', 'checkbox_alt', 'disc' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.feature_2_icon ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { feature_2_icon: item } );
													} }
												>
														<span className="base-radio-icon">
															{ Icons[ item ] }
														</span>
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.feature_3 && (
							<div className="sorter-sub-option">
								<TextControl
									label={ __( 'Third Feature', 'avanam' ) }
									value={ this.state.item.feature_3 }
									onChange={ ( value ) => {
										this.updateValues( { feature_3: value } );
									} }
								/>
								<span className="sorter-control-title">{ __( 'Choose a Icon', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'shield_check', 'check', 'checkbox', 'checkbox_alt', 'disc' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.feature_3_icon ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { feature_3_icon: item } );
													} }
												>
														<span className="base-radio-icon">
															{ Icons[ item ] }
														</span>
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.feature_4 && (
							<div className="sorter-sub-option">
								<TextControl
									label={ __( 'Fourth Feature', 'avanam' ) }
									value={ this.state.item.feature_4 }
									onChange={ ( value ) => {
										this.updateValues( { feature_4: value } );
									} }
								/>
								<span className="sorter-control-title">{ __( 'Choose a Icon', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'shield_check', 'check', 'checkbox', 'checkbox_alt', 'disc' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.feature_4_icon ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { feature_4_icon: item } );
													} }
												>
														<span className="base-radio-icon">
															{ Icons[ item ] }
														</span>
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.feature_5 && (
							<div className="sorter-sub-option">
								<TextControl
									label={ __( 'Fifth Feature', 'avanam' ) }
									value={ this.state.item.feature_5 }
									onChange={ ( value ) => {
										this.updateValues( { feature_5: value } );
									} }
								/>
								<span className="sorter-control-title">{ __( 'Choose a Icon', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'shield_check', 'check', 'checkbox', 'checkbox_alt', 'disc' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.feature_5_icon ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { feature_5_icon: item } );
													} }
												>
														<span className="base-radio-icon">
															{ Icons[ item ] }
														</span>
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.author && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Author?', 'avanam' ) }
									checked={ this.state.item.author ? this.state.item.author : this.state.item.author }
									onChange={ ( value ) => {
										this.updateValues( { author: value } );
									} }
								/>
								{ this.state.item.author && (
									<Fragment>
										{ undefined !== this.state.item.authorLink && (
											<Fragment>
												<ToggleControl
													label={ __( 'Enable Author Link?', 'avanam' ) }
													checked={ this.state.item.authorLink }
													onChange={ ( value ) => {
														this.updateValues( { authorLink: value } );
													} }
												/>
											</Fragment>
										) }
										{ undefined !== this.state.item.authorImage && (
											<Fragment>
												<ToggleControl
													label={ __( 'Show Author Image?', 'avanam' ) }
													checked={ this.state.item.authorImage ? this.state.item.authorImage : this.state.item.authorImage }
													onChange={ ( value ) => {
														this.updateValues( { authorImage: value } );
													} }
												/>
												{ undefined !== this.state.item.authorImageSize && true === this.state.item.authorImage && (
													<RangeControl
														initialPosition={ this.state.item.authorImageSize ? this.state.item.authorImageSize : 25 }
														value={ ( undefined !== this.state.item.authorImageSize ? this.state.item.authorImageSize : 25 ) }
														onChange={ ( value ) => {
															this.updateValues( { authorImageSize: value } );
														} }
														min={ 10 }
														max={ 125 }
														step={ 1 }
														label={ __( 'Author Image Size (px)', 'avanam' ) }
													/>
												) }
											</Fragment>
										) }
										{ undefined !== this.state.item.authorLabel && (
											<div className="meta-label-control">
												<span className="sorter-control-title">{ __( 'Author Label', 'avanam' ) }</span>
												<div className={ `meta-label-input-control ${ ( this.state.item.authorEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
													<Tooltip text={ __( 'Toggle Label Visibility', 'avanam' ) }>
														<Button
															className={ `base-label-visiblity ${ ( this.state.item.authorEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
															onClick={ () => {
																this.updateValues( { authorEnableLabel: ( this.state.item.authorEnableLabel ? false : true ) } );
															} }
														>
															<Dashicon icon={ ( this.state.item.authorEnableLabel ? 'visibility' : 'hidden' ) } />
														</Button>
													</Tooltip>
													<TextControl
														label=""
														placeholder={ __( 'By', 'avanam' ) }
														value={ this.state.item.authorLabel ? this.state.item.authorLabel : this.state.item.authorLabel }
														onChange={ ( value ) => {
															this.updateValues( { authorLabel: value } );
														} }
													/>
												</div>
											</div>
										) }
									</Fragment>
								) }
							</div>
						) }
						{ undefined !== this.state.item.date && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Date?', 'avanam' ) }
									checked={ this.state.item.date ? this.state.item.date : this.state.item.date }
									onChange={ ( value ) => {
										this.updateValues( { date: value } );
									} }
								/>
								{ this.state.item.date && (
									<ToggleControl
										label={ __( 'Show Time?', 'avanam' ) }
										checked={ this.state.item.dateTime ? this.state.item.dateTime : this.props.item.dateTime }
										onChange={ ( value ) => {
											this.updateValues( { dateTime: value }, this.props.index );
										} }
									/>
								) }
								{ undefined !== this.state.item.dateLabel && this.state.item.date && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Date Label', 'avanam' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.state.item.dateEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'avanam' ) }>
												<Button
													className={ `base-label-visiblity ${ ( this.state.item.dateEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.updateValues( { dateEnableLabel: ( this.state.item.dateEnableLabel ? false : true ) } );
													} }
												>
													<Dashicon icon={ ( this.state.item.dateEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Posted on', 'avanam' ) }
												value={ this.state.item.dateLabel ? this.state.item.dateLabel : this.state.item.dateLabel }
												onChange={ ( value ) => {
													this.updateValues( { dateLabel: value } );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.state.item.dateUpdated && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __(  'Show Last Updated Date?', 'avanam' ) }
									checked={ this.state.item.dateUpdated ? this.state.item.dateUpdated : this.state.item.dateUpdated }
									onChange={ ( value ) => {
										this.updateValues( { dateUpdated: value } );
									} }
								/>
								{ this.state.item.dateUpdated && (
									<ToggleControl
										label={ __( 'Show only if different from publish date?', 'avanam' ) }
										checked={ this.state.item.dateUpdatedDifferent ? this.state.item.dateUpdatedDifferent : this.props.item.dateUpdatedDifferent }
										onChange={ ( value ) => {
											this.updateValues( { dateUpdatedDifferent: value }, this.props.index );
										} }
									/>
								) }
								{ this.state.item.dateUpdated && (
									<ToggleControl
										label={ __( 'Show Time?', 'avanam' ) }
										checked={ this.state.item.dateUpdatedTime ? this.state.item.dateUpdatedTime : this.props.item.dateUpdatedTime }
										onChange={ ( value ) => {
											this.updateValues( { dateUpdatedTime: value }, this.props.index );
										} }
									/>
								) }
								{ undefined !== this.state.item.dateUpdatedLabel && this.state.item.dateUpdated && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Updated Date Label', 'avanam' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.state.item.dateUpdatedEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'avanam' ) }>
												<Button
													className={ `base-label-visiblity ${ ( this.state.item.dateUpdatedEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.updateValues( { dateUpdatedEnableLabel: ( this.state.item.dateUpdatedEnableLabel ? false : true ) } );
													} }
												>
													<Dashicon icon={ ( this.state.item.dateUpdatedEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Updated on', 'avanam' ) }
												value={ this.state.item.dateUpdatedLabel ? this.state.item.dateUpdatedLabel : this.state.item.dateUpdatedLabel }
												onChange={ ( value ) => {
													this.updateValues( { dateUpdatedLabel: value } );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.state.item.categories && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Categories?', 'avanam' ) }
									checked={ this.state.item.categories ? this.state.item.categories : this.state.item.categories }
									onChange={ ( value ) => {
										this.updateValues( { categories: value } );
									} }
								/>
								{ undefined !== this.state.item.categoriesLabel && this.state.item.categories && this.state.item.metaLabel && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Categories Label', 'avanam' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.state.item.categoriesEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'avanam' ) }>
												<Button
													className={ `base-label-visiblity ${ ( this.state.item.categoriesEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.updateValues( { categoriesEnableLabel: ( this.state.item.categoriesEnableLabel ? false : true ) } );
													} }
												>
													<Dashicon icon={ ( this.state.item.categoriesEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Posted in', 'avanam' ) }
												value={ this.state.item.categoriesLabel ? this.state.item.categoriesLabel : this.state.item.categoriesLabel }
												onChange={ ( value ) => {
													this.updateValues( { categoriesLabel: value } );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.state.item.comments && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __(  'Show Comments?', 'avanam' ) }
									checked={ this.state.item.comments ? this.state.item.comments : this.state.item.comments }
									onChange={ ( value ) => {
										this.updateValues( { comments: value } );
									} }
								/>
								{ this.state.item.comments && (
									<ToggleControl
										label={ __( 'Show only if post has at least one comment', 'avanam' ) }
										checked={ this.state.item.commentsCondition ? this.state.item.commentsCondition : this.props.item.commentsCondition }
										onChange={ ( value ) => {
											this.updateValues( { commentsCondition: value }, this.props.index );
										} }
									/>
								) }
							</div>
						) }
						{ undefined !== this.state.item.card_color && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose Icon Colors', 'avanam' ) }</span>
								<ButtonGroup className="base-radio-container-control">
									{ [ 'inherit', 'gray' ].map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.state.item.card_color ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.updateValues( { card_color: item } );
													} }
												>
														{ capitalizeFirstLetter( item ) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.state.item.stripe && (
							<ToggleControl
								label={ __(  'Show Stripe?', 'avanam' ) }
								checked={ this.state.item.stripe }
								onChange={ ( value ) => {
									this.updateValues( { stripe: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.visa && (
							<ToggleControl
								label={ __(  'Show Visa?', 'avanam' ) }
								checked={ this.state.item.visa }
								onChange={ ( value ) => {
									this.updateValues( { visa: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.mastercard && (
							<ToggleControl
								label={ __(  'Show Mastercard?', 'avanam' ) }
								checked={ this.state.item.mastercard }
								onChange={ ( value ) => {
									this.updateValues( { mastercard: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.amex && (
							<ToggleControl
								label={ __(  'Show Amex?', 'avanam' ) }
								checked={ this.state.item.amex }
								onChange={ ( value ) => {
									this.updateValues( { amex: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.discover && (
							<ToggleControl
								label={ __(  'Show Discover?', 'avanam' ) }
								checked={ this.state.item.discover }
								onChange={ ( value ) => {
									this.updateValues( { discover: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.paypal && (
							<ToggleControl
								label={ __(  'Show Paypal?', 'avanam' ) }
								checked={ this.state.item.paypal }
								onChange={ ( value ) => {
									this.updateValues( { paypal: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.applepay && (
							<ToggleControl
								label={ __(  'Show Apple Pay?', 'avanam' ) }
								checked={ this.state.item.applepay }
								onChange={ ( value ) => {
									this.updateValues( { applepay: value } );
								} }
							/>
						) }
						{ undefined !== this.state.item.custom_enable_01 && (
							<Fragment>
								<ToggleControl
									label={ __(  'Show Custom 01?', 'avanam' ) }
									checked={ this.state.item.custom_enable_01 }
									onChange={ ( value ) => {
										this.updateValues( { custom_enable_01: value } );
									} }
								/>
								{ this.state.item.custom_enable_01 && (
									<Fragment>
										{ ! this.state.item.custom_img_01 && (
											<div className="attachment-media-view">
												<MediaUpload
													onSelect={ ( imageData ) => {
														this.updateValues( { custom_img_01: imageData.url, custom_id_01: imageData.id } );
													} }
													allowedTypes={ ['image'] }
													render={ ( { open } ) => (
														<Button className="button-add-media" isSecondary onClick={ open }>
															{ __( 'Add Image', 'avanam' )}
														</Button>
													) }
												/>
											</div>
										) }
										{ this.state.item.custom_img_01 && (
											<div className="social-custom-image">
												<div className="base-social-image">
													<img className="base-social-image-preview" src={ this.state.item.custom_img_01 } />
												</div>
												<Button
													className='remove-image'
													isDestructive
													onClick={ () => {
														this.updateValues( { custom_img_01: '', custom_id_01: '' } );
													} }
												>
													{ __( 'Remove Image', 'avanam' ) }
													<Dashicon icon='no'/>
												</Button>
											</div>
										) }
									</Fragment>
								) }
							</Fragment>
						) }
						{ undefined !== this.state.item.custom_enable_02 && (
							<Fragment>
								<ToggleControl
									label={ __(  'Show Custom 02?', 'avanam' ) }
									checked={ this.state.item.custom_enable_02 }
									onChange={ ( value ) => {
										this.updateValues( { custom_enable_02: value } );
									} }
								/>
								{ this.state.item.custom_enable_02 && (
									<Fragment>
										{ ! this.state.item.custom_img_02 && (
											<div className="attachment-media-view">
												<MediaUpload
													onSelect={ ( imageData ) => {
														this.updateValues( { custom_img_02: imageData.url, custom_id_02: imageData.id } );
													} }
													allowedTypes={ ['image'] }
													render={ ( { open } ) => (
														<Button className="button-add-media" isSecondary onClick={ open }>
															{ __( 'Add Image', 'avanam' )}
														</Button>
													) }
												/>
											</div>
										) }
										{ this.state.item.custom_img_02 && (
											<div className="social-custom-image">
												<div className="base-social-image">
													<img className="base-social-image-preview" src={ this.state.item.custom_img_02 } />
												</div>
												<Button
													className='remove-image'
													isDestructive
													onClick={ () => {
														this.updateValues( { custom_img_02: '', custom_id_02: '' } );
													} }
												>
													{ __( 'Remove Image', 'avanam' ) }
													<Dashicon icon='no'/>
												</Button>
											</div>
										) }
									</Fragment>
								) }
							</Fragment>
						) }
						{ undefined !== this.state.item.custom_enable_03 && (
							<Fragment>
								<ToggleControl
									label={ __(  'Show Custom 03?', 'avanam' ) }
									checked={ this.state.item.custom_enable_03 }
									onChange={ ( value ) => {
										this.updateValues( { custom_enable_03: value } );
									} }
								/>
								{ this.state.item.custom_enable_03 && (
									<Fragment>
										{ ! this.state.item.custom_img_03 && (
											<div className="attachment-media-view">
												<MediaUpload
													onSelect={ ( imageData ) => {
														this.updateValues( { custom_img_03: imageData.url, custom_id_03: imageData.id } );
													} }
													allowedTypes={ ['image'] }
													render={ ( { open } ) => (
														<Button className="button-add-media" isSecondary onClick={ open }>
															{ __( 'Add Image', 'avanam' )}
														</Button>
													) }
												/>
											</div>
										) }
										{ this.state.item.custom_img_03 && (
											<div className="social-custom-image">
												<div className="base-social-image">
													<img className="base-social-image-preview" src={ this.state.item.custom_img_03 } />
												</div>
												<Button
													className='remove-image'
													isDestructive
													onClick={ () => {
														this.updateValues( { custom_img_03: '', custom_id_03: '' } );
													} }
												>
													{ __( 'Remove Image', 'avanam' ) }
													<Dashicon icon='no'/>
												</Button>
											</div>
										) }
									</Fragment>
								) }
							</Fragment>
						) }
						{ undefined !== this.state.item.custom_enable_04 && (
							<Fragment>
								<ToggleControl
									label={ __(  'Show Custom 04?', 'avanam' ) }
									checked={ this.state.item.custom_enable_04 }
									onChange={ ( value ) => {
										this.updateValues( { custom_enable_04: value } );
									} }
								/>
								{ this.state.item.custom_enable_04 && (
									<Fragment>
										{ ! this.state.item.custom_img_04 && (
											<div className="attachment-media-view">
												<MediaUpload
													onSelect={ ( imageData ) => {
														this.updateValues( { custom_img_04: imageData.url, custom_id_04: imageData.id } );
													} }
													allowedTypes={ ['image'] }
													render={ ( { open } ) => (
														<Button className="button-add-media" isSecondary onClick={ open }>
															{ __( 'Add Image', 'avanam' )}
														</Button>
													) }
												/>
											</div>
										) }
										{ this.state.item.custom_img_04 && (
											<div className="social-custom-image">
												<div className="base-social-image">
													<img className="base-social-image-preview" src={ this.state.item.custom_img_04 } />
												</div>
												<Button
													className='remove-image'
													isDestructive
													onClick={ () => {
														this.updateValues( { custom_img_04: '', custom_id_04: '' } );
													} }
												>
													{ __( 'Remove Image', 'avanam' ) }
													<Dashicon icon='no'/>
												</Button>
											</div>
										) }
									</Fragment>
								) }
							</Fragment>
						) }
						{ undefined !== this.state.item.custom_enable_05 && (
							<Fragment>
								<ToggleControl
									label={ __(  'Show Custom 05?', 'avanam' ) }
									checked={ this.state.item.custom_enable_05 }
									onChange={ ( value ) => {
										this.updateValues( { custom_enable_05: value } );
									} }
								/>
								{ this.state.item.custom_enable_05 && (
									<Fragment>
										{ ! this.state.item.custom_img_05 && (
											<div className="attachment-media-view">
												<MediaUpload
													onSelect={ ( imageData ) => {
														this.updateValues( { custom_img_05: imageData.url, custom_id_05: imageData.id } );
													} }
													allowedTypes={ ['image'] }
													render={ ( { open } ) => (
														<Button className="button-add-media" isSecondary onClick={ open }>
															{ __( 'Add Image', 'avanam' ) }
														</Button>
													) }
												/>
											</div>
										) }
										{ this.state.item.custom_img_05 && (
											<div className="social-custom-image">
												<div className="base-social-image">
													<img className="base-social-image-preview" src={ this.state.item.custom_img_05 } />
												</div>
												<Button
													className='remove-image'
													isDestructive
													onClick={ () => {
														this.updateValues( { custom_img_05: '', custom_id_05: '' } );
													} }
												>
													{ __( 'Remove Image', 'avanam' ) }
													<Dashicon icon='no'/>
												</Button>
											</div>
										) }
									</Fragment>
								) }
							</Fragment>
						) }
					</div>
				) }
			</div>
		);
	}
	updateValues( value ) {
		const stateUpdate = {
			...this.state.item,
			...value
		};
		this.setState( { item: stateUpdate } );
		this.props.control.settings[ this.props.item ].set( {
			...this.props.control.settings[ this.props.item ].get(),
			...value,
			flag: !this.props.control.settings[ this.props.item ].get().flag
		} );
	}
}
ItemComponent.propTypes = {
	item: PropTypes.string.isRequired,
	customizer: PropTypes.object.isRequired
};

export default ItemComponent;
