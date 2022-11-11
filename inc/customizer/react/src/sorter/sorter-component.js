/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';
import { ReactSortable } from "react-sortablejs";
import isEqual from 'lodash/isEqual';

import ItemComponent from './item-component';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Tooltip, Button } = wp.components;

const { Component, Fragment } = wp.element;
class SorterComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.onDragEnd = this.onDragEnd.bind( this );
		this.onDragStart = this.onDragStart.bind( this );
		this.onDragStop = this.onDragStop.bind( this );
		this.saveObjectUpdate = this.saveObjectUpdate.bind( this );
		this.onItemChange = this.onItemChange.bind( this );;
		let value = this.props.control.setting.get();
		let baseDefault = {
			'items': {
				'title': {
					'id': 'title',
					'enabled': true,
				},
				'breadcrumb': {
					'id': 'breadcrumb',
					'enabled': true,
					'divider': 'dot',
				},
				'meta': {
					'id': 'meta',
					'enabled': true,
					'metaLabel': true,
					'divider': 'dot',
					'author': true,
					'authorImage': true,
					'authorLabel': '',
					'date': true,
					'dateTime': false,
					'dateLabel': '',
					'dateUpdated': true,
					'dateUpdatedTime': false,
					'dateUpdatedDifferent': false,
					'dateUpdatedLabel': '',
					'categories': true,
					'categoriesLabel': '',
					'comments': false,
					'commentsLabel': '',
					'commentsCondition': false,
				},
				'categories': {
					'id': 'categories',
					'enabled': true,
					'divider': 'dot',
				}
			}
		};
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...JSON.parse( JSON.stringify( this.defaultValue ) ),
			...value
		} : JSON.parse( JSON.stringify( this.defaultValue ) );
		let defaultParams = {
			'group': 'title_item_group',
			dividers: {
				dot: {
					icon: 'dot',
				},
				slash: {
					name: '/',
					icon: '',
				},
				dash: {
					name: '-',
					icon: '',
				},
				arrow: {
					name: '>',
					icon: '',
				},
				doubleArrow: {
					name: '>>',
					icon: '',
				},
			},
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		this.state = {
			value: value,
		};
	}
	onDragStart() {
		var dropzones = document.querySelectorAll( '.base-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.add( 'base-dragging-dropzones' );
		}
	}
	onDragStop() {
		var dropzones = document.querySelectorAll( '.base-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.remove( 'base-dragging-dropzones' );
		}
	}
	saveObjectUpdate( value, index ) {
		let updateState = this.state.value;
		let items = updateState.items;
		let newItems = updateState.items;
		Object.keys( items ).map( ( item, thisIndex ) => {
			if ( index === thisIndex ) {
				newItems[item] = { ...items[item], ...value };
			}
		} );
		updateState.items = newItems;
		this.setState( { value: updateState } );
		this.updateValues( updateState );
	}
	onItemChange( value, itemIndex ) {
		this.saveObjectUpdate( value, itemIndex );
	}
	onDragEnd( items ) {
		let updateState = this.state.value;
		let update = updateState.items;
		let updateItems = {};
		{ items.length > 0 && (
			items.map( ( item ) => {
				if ( update[item.id].id === item.id ) {
					updateItems[item.id] = update[item.id];
				}
			} )
		) };
		if ( JSON.stringify( update ) !== JSON.stringify( updateItems ) ) {
			update.items = updateItems;
			updateState.items = updateItems;
			this.setState( { value: updateState } );
			this.updateValues( updateState );
		}
	}
	arraysEqual( a, b ) {
		if (a === b) return true;
		if (a == null || b == null) return false;
		if (a.length != b.length) return false;		
		for (var i = 0; i < a.length; ++i) {
			if (a[i] !== b[i]) return false;
		}
		return true;
	}
	render() {
		const controlLabel = (
			<Fragment>
				<Tooltip text={ __( 'Reset Value', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ ( this.state.value === this.defaultValue ) }
						onClick={ () => {
							let value = this.defaultValue;
							this.setState( { value: value } );
							this.updateValues( value );
						} }
					>
						<Dashicon icon='image-rotate' />
					</Button>
				</Tooltip>
				{ this.props.control.params.label &&
					this.props.control.params.label
				}
			</Fragment>
		);
		const currentList = ( typeof this.state.value != "undefined" && undefined !== this.state.value.items ? this.state.value.items : {} );
		let theItems = [];
		{ Object.keys( currentList ).map( ( item ) => {
			theItems.push(
				{
					id: item,
				}
			)
		} ) }
		return (
			<div className="base-control-field base-sorter-items base-post-title-sorter">
				<div className="base-responsive-control-bar">
					<span className="customize-control-title">{ controlLabel }</span>
				</div>
				<div className="base-sorter-row">
					<ReactSortable animation={100} onStart={ () => this.onDragStop() } onEnd={ () => this.onDragStop() } group={ this.controlParams.group } className={ `base-sorter-drop base-sorter-sortable-panel base-meta-sorter base-sorter-drop-${ this.controlParams.group }` } handle={ '.base-sorter-item-panel-header' } list={ theItems } setList={ ( newState ) => this.onDragEnd( newState ) } >
						{ Object.keys( currentList ).map( ( item, index ) => {
							return <ItemComponent
							onItemChange={ ( value, itemIndex ) => this.onItemChange( value, itemIndex ) }
							key={ item }
							index={ index }
							item={ currentList[item] }
							controlParams={ this.controlParams }
							/>;
						} ) }
					</ReactSortable>
				</div>
			</div>
		);
	}
	updateValues( value ) {
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: !this.props.control.setting.get().flag
		} );
	}
}

SorterComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default SorterComponent;
