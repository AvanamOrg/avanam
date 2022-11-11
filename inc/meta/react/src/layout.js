/**
 * Meta Options build.
 */
import RadioIconComponent from './radio-icon.js';
import capitalizeFirstLetter from './capitalize-first.js';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import {
	Component,
	Fragment,
} from '@wordpress/element';
import {
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';

class BaseThemeLayout extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
		};
	}
	render() {
		const selectOptions = Object.keys( baseMetaParams.sidebars ).map( ( item ) => {
			return ( { label: baseMetaParams.sidebars[ item ].label, value: baseMetaParams.sidebars[ item ].value } );
		} );
		const sidebarOptions = {
			'default': {
				name: __( 'Default', 'avanam' ),
				icon: 'inherit',
			},
			'normal': {
				name: __( 'Normal', 'avanam' ),
				icon: 'normal',
			},
			'narrow': {
				name: __( 'Narrow', 'avanam' ),
				icon: 'narrow',
			},
			'fullwidth': {
				name: __( 'Fullwidth', 'avanam' ),
				icon: 'fullwidth',
			},
			'left': {
				name: __( 'Left Sidebar', 'avanam' ),
				icon: 'leftsidebar',
			},
			'right': {
				name: __( 'Right Sidebar', 'avanam' ),
				icon: 'rightsidebar',
			},
		}
		const titleOptions = {
			'default': {
				tooltip: __( 'Inherited Default', 'avanam' ),
				icon: 'inherit',
			},
			'hide': {
				tooltip: __( 'Hide Title', 'avanam' ),
				icon: 'hidetitle',
			},
			'normal': {
				tooltip: __( 'Show In Content', 'avanam' ),
				icon: 'incontent',
			},
			'above': {
				tooltip : __( 'Show Above Content', 'avanam' ),
				icon: 'abovecontent',
			},
		}
		const newTitleOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'show': {
				'name': __( 'Enable', 'avanam' ),
			},
			'hide': {
				'name': __( 'Disable', 'avanam' ),
			},
		}
		const boxedOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'boxed': {
				'name': __( 'Boxed', 'avanam' ),
			},
			'unboxed': {
				'name': __( 'Unboxed', 'avanam' ),
			},
		}
		const paddingOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'show': {
				'name': __( 'Enable', 'avanam' ),
			},
			'hide': {
				'name': __( 'Disable', 'avanam' ),
			},
			'top': {
				'name': __( 'Top Only', 'avanam' ),
			},
			'bottom': {
				'name': __( 'Bottom Only', 'avanam' ),
			},
		}
		const featuredOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'show': {
				'name': __( 'Enable', 'avanam' ),
			},
			'hide': {
				'name': __( 'Disable', 'avanam' ),
			},
		}
		const featuredPositionOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'above': {
				'name': __( 'Above', 'avanam' ),
			},
			'behind': {
				'name': __( 'Behind', 'avanam' ),
			},
			'below': {
				'name': __( 'Below', 'avanam' ),
			},
		}
		const transparentOptions = {
			'default': {
				'name': __( 'Default', 'avanam' ),
			},
			'enable': {
				'name': __( 'Enable', 'avanam' ),
			},
			'disable': {
				'name': __( 'Disable', 'avanam' ),
			},
		}
		//console.log( baseMetaParams );
		const icon = <svg width="20px" height="20px"
		xmlns="http://www.w3.org/2000/svg"
		fillRule="evenodd"
		strokeLinejoin="round"
		strokeMiterlimit="2"
		clipRule="evenodd"
		viewBox="0 0 50 40"
	  >
		<path fill="#CDCDCD" d="M9.857 8.351H29.519V15.874H9.857z"></path>
		<path
		  fill="#CCC"
		  fillRule="nonzero"
		  d="M10.259 17.908h18.847c.225 0 .41.354.41.786 0 .431-.185.785-.41.785H10.259c-.225 0-.41-.354-.41-.785 0-.432.185-.786.41-.786z"
		></path>
		<path
		  fill="#8E8E8E"
		  d="M47.109 38.98H2.891a1.9 1.9 0 01-1.898-1.898V2.918A1.9 1.9 0 012.891 1.02h44.218a1.9 1.9 0 011.898 1.898v34.164a1.9 1.9 0 01-1.898 1.898zm-.102-33.614H2.993V36.98h44.014V5.366zm-8.172-2.94a.9.9 0 110 1.8.9.9 0 010-1.8zm7.153 0a.9.9 0 110 1.8.9.9 0 010-1.8zm-3.538 0a.9.9 0 110 1.8.9.9 0 010-1.8z"
		></path>
		<path
		  fill="#515151"
		  d="M40.119 13.838l4.705 4.844-10.54 9.899a110.5 110.5 0 01-3.115 1.566 64.17 64.17 0 01-2.948 1.35 32.236 32.236 0 01-1.114.445 13 13 0 01-.794.269 4.38 4.38 0 01-.619.145 1.67 1.67 0 01-.189.018h-.061c-.089-.003-.206-.018-.258-.101-.043-.068-.043-.159-.038-.235l.007-.061a2.98 2.98 0 01.179-.646c.09-.245.193-.485.301-.722.186-.408.387-.809.594-1.206.369-.708.759-1.405 1.157-2.097a104.799 104.799 0 012.183-3.624l10.55-9.844zM30.686 24.71l2.542 2.725-3.053 1.621-1.329-1.217 1.84-3.129zm11.137-12.463l2.23-2.081s1.959-1.222 4.028.819c1.729 1.706.765 3.92.765 3.92l-2.323 2.182-4.7-4.84z"
		></path>
		<path
		  fill="#E5E5E5"
		  d="M40.152 26.389v7.571h-8.567v-1.649l.108-.045c.987-.415 1.96-.862 2.92-1.336a44.58 44.58 0 001.751-.906l3.788-3.635zm0-15.912l-8.567 8.041V8.421h8.567v2.056z"
		></path>
		<path
		  fill="#CCC"
		  fillRule="nonzero"
		  d="M28.872 21.063l-.592.557s-.284.383-.716 1.015H10.259a.256.256 0 01-.039-.003.332.332 0 01-.19-.132l-.023-.033c-.01-.014-.018-.029-.027-.043a1.15 1.15 0 01-.124-.436 1.435 1.435 0 01.006-.334c.012-.091.033-.18.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.308.308 0 01.177-.116.515.515 0 01.039-.004h18.613z"
		></path>
		<path
		  fill="#CDCDCD"
		  fillRule="nonzero"
		  d="M26.519 24.219a47.303 47.303 0 00-.953 1.572H10.259a.172.172 0 01-.039-.004.326.326 0 01-.19-.131.405.405 0 01-.023-.034c-.01-.014-.018-.028-.027-.043a1.156 1.156 0 01-.124-.436 1.426 1.426 0 01.006-.333c.012-.091.033-.181.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.308.308 0 01.177-.116.172.172 0 01.039-.004h16.26z"
		></path>
		<path
		  fill="#CCC"
		  fillRule="nonzero"
		  d="M23.417 30.531c-.152.573-.233 1.106-.214 1.571H10.259a.256.256 0 01-.039-.003.328.328 0 01-.19-.132l-.023-.033c-.01-.014-.018-.029-.027-.043a1.162 1.162 0 01-.124-.436 1.436 1.436 0 01.006-.334c.012-.09.033-.18.066-.266.009-.025.02-.049.031-.073l.032-.059.027-.041a.392.392 0 01.025-.032.313.313 0 01.177-.116.256.256 0 01.039-.003h13.158z"
		></path>
		<path
		  fill="#CDCDCD"
		  fillRule="nonzero"
		  d="M24.701 27.375a22.69 22.69 0 00-.732 1.572h-13.71c-.013 0-.026-.002-.039-.004a.326.326 0 01-.19-.131.405.405 0 01-.023-.034c-.01-.014-.018-.028-.027-.043a1.162 1.162 0 01-.124-.436 1.436 1.436 0 01.006-.334 1.177 1.177 0 01.097-.338c.01-.021.021-.04.032-.059l.027-.042a.698.698 0 01.025-.032.312.312 0 01.177-.115.172.172 0 01.039-.004h14.442z"
		></path>
	  </svg>;
		return (
			<Fragment>
				<PluginSidebarMoreMenuItem
					target="theme-meta-panel"
					icon={ icon }
				>
					{ baseMetaParams.post_type_name + ' ' + __( 'Settings', 'avanam' ) }
				</PluginSidebarMoreMenuItem>
				<PluginSidebar
					isPinnable={ true }
					icon={ icon }
					name="theme-meta-panel"
					title={ baseMetaParams.post_type_name + ' ' + __( 'Settings', 'avanam' ) }
				>
					<div className="base-sidebar-container components-panel__body is-opened">
						<RadioIconComponent
							label={ __( 'Transparent Header' ) }
							value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_transparent && '' !== this.props.meta._bst_post_transparent ? this.props.meta._bst_post_transparent : 'default' ) }
							customClass="three-col-short"
							options={ transparentOptions }
							onChange={ value => {
								this.props.setMetaFieldValue( value, '_bst_post_transparent' );
							} }
						/>
						<RadioIconComponent
							label={ baseMetaParams.post_type_name + ' ' + __( 'Title', 'avanam' ) }
							customClass="three-col-short"
							value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_title && '' !== this.props.meta._bst_post_title ? this.props.meta._bst_post_title : 'default' ) }
							options={ newTitleOptions }
							onChange={ value => {
								let title = value;
								if( 'default' === value ) {
									title = baseMetaParams.title;
								} else if ( 'show' === value ) {
									title = baseMetaParams.title_position;
								}
								document.body.classList.remove( 'post-content-title-normal' );
								document.body.classList.remove( 'post-content-title-above' );
								document.body.classList.remove( 'post-content-title-hide' );
								document.body.classList.add( 'post-content-title-' + title );
								this.props.setMetaFieldValue( value, '_bst_post_title' );
							} }
						/>
						<RadioIconComponent
							label={ baseMetaParams.post_type_name + ' ' + __( 'Layout', 'avanam' ) }
							customClass="three-col-square"
							value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_layout && '' !== this.props.meta._bst_post_layout ? this.props.meta._bst_post_layout : 'default' ) }
							options={ sidebarOptions }
							onChange={ value => {
								let layout = value;
								let sidebar = 'none';
								if ( 'left' === value || 'right' === value ) {
									layout = 'narrow';
									sidebar = value;
								} else if( 'default' === value ) {
									layout = baseMetaParams.layout;
									sidebar = baseMetaParams.sidebar;
								}
								document.body.classList.remove( 'post-content-width-narrow' );
								document.body.classList.remove( 'post-content-width-normal' );
								document.body.classList.remove( 'post-content-width-fullwidth' );

								document.body.classList.remove( 'post-content-sidebar-right' );
								document.body.classList.remove( 'post-content-sidebar-left' );
								document.body.classList.remove( 'post-content-sidebar-none' );
								document.body.classList.add( 'post-content-width-' + layout );
								document.body.classList.add( 'post-content-sidebar-' + sidebar );
								this.props.setMetaFieldValue( value, '_bst_post_layout' );
							} }
						/>
						{ ( ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_layout && '' !== this.props.meta._bst_post_layout && ( 'left' === this.props.meta._bst_post_layout || 'right' === this.props.meta._bst_post_layout ) ) || ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_layout && 'default' === this.props.meta._bst_post_layout && ( 'left' === baseMetaParams.sidebar || 'right' === baseMetaParams.sidebar ) ) || ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_layout && '' === this.props.meta._bst_post_layout && ( 'left' === baseMetaParams.sidebar || 'right' === baseMetaParams.sidebar ) ) ) && (
							<div className="base-control-field base-select-control">
								<div className="base-title-control-bar">
									<span className="customize-control-title">{ capitalizeFirstLetter( baseMetaParams.post_type ) + ' ' + __( 'sidebar' )  }</span>
								</div>
								<SelectControl
									value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_sidebar_id && '' !== this.props.meta._bst_post_sidebar_id ? this.props.meta._bst_post_sidebar_id : 'default' ) }
									options={ selectOptions }
									onChange={ ( val ) => {
										this.props.setMetaFieldValue( val, '_bst_post_sidebar_id' );
									} }
								/>
							</div>
						) }
						<RadioIconComponent
							label={ __( 'Content Style', 'avanam' ) }
							value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_content_style && '' !== this.props.meta._bst_post_content_style ? this.props.meta._bst_post_content_style : 'default' ) }
							customClass="three-col-short"
							options={ boxedOptions }
							onChange={ value => {
								let boxed = value;
								if( 'default' === value ) {
									boxed = baseMetaParams.boxed;
								}
								document.body.classList.remove( 'post-content-style-boxed' );
								document.body.classList.remove( 'post-content-style-unboxed' );
								document.body.classList.add( 'post-content-style-' + boxed );
								document.body.classList.remove( 'admin-color-pcs-boxed' );
								document.body.classList.remove( 'admin-color-pcs-unboxed' );
								document.body.classList.add( 'admin-color-pcs-' + boxed );
								this.props.setMetaFieldValue( value, '_bst_post_content_style' );
							} }
						/>
						<RadioIconComponent
							label={ __( 'Content Vertical Padding', 'avanam' ) }
							value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_vertical_padding && '' !== this.props.meta._bst_post_vertical_padding ? this.props.meta._bst_post_vertical_padding : 'default' ) }
							options={ paddingOptions }
							customClass="three-col-short"
							onChange={ value => {
								let padding = value;
								if( 'default' === value ) {
									padding = baseMetaParams.vpadding;
								}
								document.body.classList.remove( 'post-content-vertical-padding-show' );
								document.body.classList.remove( 'post-content-vertical-padding-hide' );
								document.body.classList.remove( 'post-content-vertical-padding-top' );
								document.body.classList.remove( 'post-content-vertical-padding-bottom' );
								document.body.classList.add( 'post-content-vertical-padding-' + padding );
								this.props.setMetaFieldValue( value, '_bst_post_vertical_padding' );
							} }
						/>
						{ baseMetaParams.supports_feature && (
							<Fragment>
								<RadioIconComponent
									label={ __( 'Show Featured Image', 'avanam' ) }
									value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_feature && '' !== this.props.meta._bst_post_feature ? this.props.meta._bst_post_feature : 'default' ) }
									options={ featuredOptions }
									customClass="three-col-short"
									onChange={ value => {
										this.props.setMetaFieldValue( value, '_bst_post_feature' );
									} }
								/>
								{ ( ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_feature && 'show' === this.props.meta._bst_post_feature ) || ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_feature && 'default' === this.props.meta._bst_post_feature && 'show' === baseMetaParams.feature ) || ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_feature && '' === this.props.meta._bst_post_feature && 'show' === baseMetaParams.feature ) ) && (
									<RadioIconComponent
										label={ __( 'Featured Image Position', 'avanam' ) }
										value={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_feature_position && '' !== this.props.meta._bst_post_feature_position ? this.props.meta._bst_post_feature_position : 'default' ) }
										options={ featuredPositionOptions }
										customClass="two-col-short"
										onChange={ value => {
											this.props.setMetaFieldValue( value, '_bst_post_feature_position' );
										} }
									/>
								) }
							</Fragment>
						) }
						<div style={{ paddingTop: 30 + 'px' }}></div>
						<ToggleControl
							label={ __( 'Disable Header', 'avanam' ) }
							checked={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_header && '' !== this.props.meta._bst_post_header ? this.props.meta._bst_post_header : false ) }
							onChange={ ( value ) => {
								this.props.setMetaFieldValue( value, '_bst_post_header' );
							} }
						/>
						<ToggleControl
							label={ __( 'Disable Footer', 'avanam' ) }
							checked={ ( undefined !== this.props.meta && undefined !== this.props.meta._bst_post_footer && '' !== this.props.meta._bst_post_footer ? this.props.meta._bst_post_footer : false ) }
							onChange={ ( value ) => {
								this.props.setMetaFieldValue( value, '_bst_post_footer' );
							} }
						/>
					</div>
				</PluginSidebar>
			</Fragment>
		);
	}
}
export default compose(
	withSelect( ( select ) => {
		const postMeta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		const oldPostMeta = select( 'core/editor' ).getCurrentPostAttribute( 'meta' );
		return {
			meta: { ...oldPostMeta, ...postMeta },
			oldMeta: oldPostMeta,
		};
	} ),
	withDispatch( ( dispatch ) => ( {
		setMetaFieldValue: ( value, field ) => dispatch( 'core/editor' ).editPost(
			{ meta: { [ field ]: value } }
		),
	} ) ),
)( BaseThemeLayout );