/* jshint esversion: 6 */

const { Component, Fragment } = wp.element;
import { isString } from 'lodash';
import BaseColorPicker from '../../common/color-picker';
import SwatchesControl from '../../common/swatches';

import {
	getGradientWithColorAtPositionChanged,
	getGradientWithColorStopAdded,
} from './utils';

class BaseGradientColorPicker extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			color: this.props.color ? this.props.color : '',
			alreadyInsertedPoint: false,
		};
	}
	render() {
		const getColorValue = () => {
			let color;
			if ( undefined !== this.state.color && '' !== this.state.color && null !== this.state.color && isString( this.state.color ) && this.state.color.includes( 'palette' ) ) {
				color = this.props.activePalette[ parseInt( this.state.color.slice(-1), 10 ) - 1 ].color;
			} else {
				color = this.state.color;
			}
			return color;
		}
		return (
			<div className="base-background-color-wrap">
				<BaseColorPicker
					color={ getColorValue() }
					onChangeComplete={ ( color ) => {
						let rgb = {
							type: 'rgba',
							value: '',
						}
						if ( undefined !== color.rgb ) {
							this.setState( { color: color.rgb } )
							rgb.value = color.rgb;
						} else {
							this.setState( { color: color.hex } )
							rgb.type = 'literal';
							rgb.value = color.hex;
						}
						//console.log( rgb );
						let newGradient;
						if ( this.state.alreadyInsertedPoint ) {
							newGradient = getGradientWithColorAtPositionChanged(
								this.props.gradientAST,
								this.props.insertPosition,
								rgb
							);
						} else {
							newGradient = getGradientWithColorStopAdded(
								this.props.gradientAST,
								this.props.insertPosition,
								rgb
							);
							this.setState( { alreadyInsertedPoint: true } );
						}
						this.props.onChange( newGradient );
					} }
				/>
				<SwatchesControl
					colors={ this.props.activePalette }
					isPalette={ ( undefined !== this.state.color && '' !== this.state.color && isString( this.state.color ) && this.state.color.includes( 'palette' ) ? this.state.color : '' ) }
					onClick={ ( color, palette ) => {
						this.setState( { color: palette } );
						let rgb = {
							type: 'literal',
							value: 'var(--global-' + palette + ')',
						}
						let newGradient;
						if ( this.state.alreadyInsertedPoint ) {
							newGradient = getGradientWithColorAtPositionChanged(
								this.props.gradientAST,
								this.props.insertPosition,
								rgb
							);
						} else {
							newGradient = getGradientWithColorStopAdded(
								this.props.gradientAST,
								this.props.insertPosition,
								rgb
							);
							this.setState( { alreadyInsertedPoint: true } );
						}
						this.props.onChange( newGradient );
					} }
				/>
			</div>
		);
	}
}
export default BaseGradientColorPicker;
