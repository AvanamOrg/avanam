import PropTypes from 'prop-types';
import Icons from './icons';
import { __ } from '@wordpress/i18n';
const {
	Component,
	Fragment
} = wp.element;
const {
	Button,
	Popover,
	Dashicon,
	ColorIndicator,
	Tooltip,
	Icon,
} = wp.components;
// /**
//  * WordPress dependencies
//  */
// import { site, Icon } from '@wordpress/icons';

export const SwatchesControl = ( { colors, isPalette, onClick = () => {}, circleSize, circleSpacing } ) => {  
	const handleClick = ( color, swatch ) => {
		onClick( {
		  hex: color,
		}, swatch )
	}
	return (
	  <div style={ {
			display: 'flex',
			flexWrap: 'wrap',
			justifyContent: 'space-between',
			paddingTop: circleSpacing,
			marginBottom: '15px',
    		borderTop: '1px solid rgb(238, 238, 238)',
		} } className="base-swatches-wrap">
		{ colors.map( ( colorObjOrString ) => {
			const c = typeof colorObjOrString === 'string'
				? { color: colorObjOrString }
				: colorObjOrString;
			const key = `${c.color}${c.slug || ''}`
			return (
				<div key={ key } style={ {
					width: circleSize,
					height: circleSize,
					marginBottom: 0,
					transform: 'scale(1)',
					transition: '100ms transform ease',
				} } className="base-swatche-item-wrap">
					<Button
						className={ `base-swatch-item ${ isPalette === c.slug ? 'swatch-active' : 'swatch-inactive' }` }
						style={ {
							height: '100%',
							width: '100%',
							border: '1px solid rgb(218, 218, 218)',
							borderRadius: '50%',
							color: `${ c.color }`,
							boxShadow: `inset 0 0 0 ${ circleSize / 2 }px`,
							transition: '100ms box-shadow ease',
						} }
						onClick={ () => handleClick( c.color, c.slug ) }
						tabIndex={ 0 }
						>
							{/* <Icon
								className="dashicon"
								icon={ site }
							/> */}
							<Icon className="dashicon" icon={ Icons.globe } />
					</Button>
				</div>
		  	)
		} ) }
		</div>
	)
}
SwatchesControl.defaultProps = {
	circleSize: 26,
	circleSpacing: 15,
}
  
SwatchesControl.propTypes = {
	colors: PropTypes.arrayOf(PropTypes.oneOfType([
		PropTypes.string,
		PropTypes.shape({
		color: PropTypes.string,
		slug: PropTypes.string,
		name: PropTypes.string,
		})],
	)).isRequired,
	isPalette: PropTypes.string,
}

export default SwatchesControl