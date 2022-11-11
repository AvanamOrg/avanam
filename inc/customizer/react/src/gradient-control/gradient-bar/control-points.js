/**
 * External dependencies
 */
import classnames from 'classnames';
import { colord } from 'colord';
import { map } from 'lodash';

/**
 * WordPress dependencies
 */
 import { useSetting } from '@wordpress/block-editor';
import { useInstanceId, useMergeRefs } from '@wordpress/compose';
import { useEffect, useRef, useState, useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { plus } from '@wordpress/icons';
const globeIcon = <svg
xmlns="http://www.w3.org/2000/svg"
fillRule="evenodd"
strokeLinejoin="round"
strokeMiterlimit="2"
clipRule="evenodd"
viewBox="0 0 20 20"
>
<path fill="none" d="M0 0H20V20H0z"></path>
<path
  fillRule="nonzero"
  d="M10 1a9 9 0 10.001 18.001A9 9 0 0010 1zm3.46 11.95c0 1.47-.8 3.3-4.06 4.7.3-4.17-2.52-3.69-3.2-5A3.25 3.25 0 018 10.1c-1.552-.266-3-.96-4.18-2 .05.47.28.904.64 1.21a4.18 4.18 0 01-1.94-1.5 7.94 7.94 0 017.25-5.63c-.84 1.38-1.5 4.13 0 5.57C8.23 8 7.26 6 6.41 6.79c-1.13 1.06.33 2.51 3.42 3.08 3.29.59 3.66 1.58 3.63 3.08zm1.34-4c-.32-1.11.62-2.23 1.69-3.14a7.27 7.27 0 01.84 6.68c-.77-1.89-2.17-2.32-2.53-3.57v.03z"
></path>
</svg>;
/**
 * Internal dependencies
 */
// import { HStack } from '../../h-stack';
// import { ColorPicker } from '../../color-picker';
// import { VisuallyHidden } from '../../visually-hidden';
import ColorPicker from '../../common/color-picker';
import { __experimentalHStack as HStack, Button, VisuallyHidden, Popover, Dashicon, Tooltip, Icon } from '@wordpress/components';
import {
	addControlPoint,
	clampPercent,
	removeControlPoint,
	updateControlPointColor,
	updateControlPointColorByPosition,
	updateControlPointPosition,
	getHorizontalRelativeGradientPosition,
} from './utils';
import {
	MINIMUM_SIGNIFICANT_MOVE,
	KEYBOARD_CONTROL_POINT_VARIATION,
} from './constants';

function useObservableState( initialState, onStateChange ) {
	const [ state, setState ] = useState( initialState );
	return [
		state,
		( value ) => {
			setState( value );
			if ( onStateChange ) {
				onStateChange( value );
			}
		},
	];
}

function CustomDropdown( props ) {
	const {
		renderContent,
		renderToggle,
		className,
		contentClassName,
		expandOnMobile,
		headerTitle,
		focusOnMount,
		position,
		popoverProps,
		onClose,
		onToggle,
		style,
		popoverRef,
	} = props;
	// Use internal state instead of a ref to make sure that the component
	// re-renders when the popover's anchor updates.
	const [ fallbackPopoverAnchor, setFallbackPopoverAnchor ] =
		useState( null );
	const containerRef = useRef();
	const [ isOpen, setIsOpen ] = useObservableState( false, onToggle );

	useEffect(
		() => () => {
			if ( onToggle && isOpen ) {
				onToggle( false );
			}
		},
		[ onToggle, isOpen ]
	);

	function toggle() {
		setIsOpen( ! isOpen );
	}

	/**
	 * Closes the popover when focus leaves it unless the toggle was pressed or
	 * focus has moved to a separate dialog. The former is to let the toggle
	 * handle closing the popover and the latter is to preserve presence in
	 * case a dialog has opened, allowing focus to return when it's dismissed.
	 */
	function closeIfFocusOutside() {
		const { ownerDocument } = containerRef.current;
		const dialog = ownerDocument.activeElement.closest( '[role="dialog"]' );
		if (
			! containerRef.current.contains( ownerDocument.activeElement ) &&
			( ! dialog || dialog.contains( containerRef.current ) )
		) {
			close();
		}
	}

	function close() {
		if ( onClose ) {
			onClose();
		}
		setIsOpen( false );
	}

	const args = { isOpen, onToggle: toggle, onClose: close };
	const popoverPropsHaveAnchor =
		!! popoverProps?.anchor ||
		// Note: `anchorRef`, `getAnchorRect` and `anchorRect` are deprecated and
		// be removed from `Popover` from WordPress 6.3
		!! popoverProps?.anchorRef ||
		!! popoverProps?.getAnchorRect ||
		!! popoverProps?.anchorRect;

	return (
		<div
			className={ classnames( 'components-dropdown', className ) }
			ref={ useMergeRefs( [ setFallbackPopoverAnchor, containerRef ] ) }
			// Some UAs focus the closest focusable parent when the toggle is
			// clicked. Making this div focusable ensures such UAs will focus
			// it and `closeIfFocusOutside` can tell if the toggle was clicked.
			tabIndex="-1"
			style={ style }
		>
			{ renderToggle( args ) }
			{ isOpen && (
				<Popover
					position={ position }
					onClose={ close }
					onFocusOutside={ closeIfFocusOutside }
					expandOnMobile={ expandOnMobile }
					headerTitle={ headerTitle }
					focusOnMount={ focusOnMount }
					// This value is used to ensure that the dropdowns
					// align with the editor header by default.
					offset={ 13 }
					anchorRef={ ! popoverPropsHaveAnchor ? popoverRef.current : undefined }
					anchor={
						! popoverPropsHaveAnchor
							? fallbackPopoverAnchor
							: undefined
					}
					{ ...popoverProps }
					className={ classnames(
						'components-dropdown__content',
						popoverProps ? popoverProps.className : undefined,
						contentClassName
					) }
				>
					{ renderContent( args ) }
				</Popover>
			) }
		</div>
	);
}

function CustomColorPickerDropdown( {
	isRenderedInSidebar,
	popoverProps: receivedPopoverProps,
	...props
} ) {
	const popoverProps = useMemo(
		() => ( {
			shift: true,
			...( isRenderedInSidebar
				? {
						// When in the sidebar: open to the left (stacking),
						// leaving the same gap as the parent popover.
						placement: 'left-start',
						offset: 34,
				  }
				: {
						// Default behavior: open below the anchor
						placement: 'bottom',
						offset: 8,
				  } ),
			...receivedPopoverProps,
		} ),
		[ isRenderedInSidebar, receivedPopoverProps ]
	);

	return (
		<CustomDropdown
			contentClassName="components-color-palette__custom-color-dropdown-content base-pop-color-popover"
			popoverProps={ popoverProps }
			{ ...props }
		/>
	);
}

function ControlPointButton( { isOpen, position, color, ...additionalProps } ) {
	const instanceId = useInstanceId( ControlPointButton );
	const descriptionId = `components-custom-gradient-picker__control-point-button-description-${ instanceId }`;
	return (
		<>
			<Button
				aria-label={ sprintf(
					// translators: %1$s: gradient position e.g: 70, %2$s: gradient color code e.g: rgb(52,121,151).
					__(
						'Gradient control point at position %1$s%% with color code %2$s.'
					),
					position,
					color
				) }
				aria-describedby={ descriptionId }
				aria-haspopup="true"
				aria-expanded={ isOpen }
				className={ classnames(
					'components-custom-gradient-picker__control-point-button',
					{
						'is-active': isOpen,
					}
				) }
				{ ...additionalProps }
			/>
			<VisuallyHidden id={ descriptionId }>
				{ __(
					'Use your left or right arrow keys or drag and drop with the mouse to change the gradient position. Press the button to change the color or remove the control point.'
				) }
			</VisuallyHidden>
		</>
	);
}

function GradientColorPickerDropdown( {
	popoverRef,
	isRenderedInSidebar,
	className,
	...props
} ) {
	// Open the popover below the gradient control/insertion point
	const popoverProps = useMemo(
		() => ( {
			placement: 'bottom',
			offset: 8,
			flip: false,
		} ),
		[]
	);

	const mergedClassName = classnames(
		'components-custom-gradient-picker__control-point-dropdown',
		className
	);

	return (
		<CustomColorPickerDropdown
			isRenderedInSidebar={ isRenderedInSidebar }
			popoverRef={ popoverRef }
			popoverProps={ popoverProps }
			className={ mergedClassName }
			{ ...props }
		/>
	);
}
function getReadableColor( value, colors ) {
	if ( ! value ) {
		return '';
	}
	if ( ! colors ) {
		return value;
	}
	if ( value.startsWith( 'var(--global-' ) ) {
		let slug = value.replace( 'var(--global-', '' );
		slug = slug.substring(0,8);
		const found = colors.find( ( option ) => option.slug === slug );
		if ( found ) {
			return found.color;
		}
	}
	return value;
}

function ControlPoints( {
	disableRemove,
	gradientPickerDomRef,
	ignoreMarkerPosition,
	value: controlPoints,
	onChange,
	onStartControlPointChange,
	onStopControlPointChange,
	isRenderedInSidebar,
	popoverRef,
	activePalette,
} ) {
	const controlPointMoveState = useRef();

	const onMouseMove = ( event ) => {
		const relativePosition = getHorizontalRelativeGradientPosition(
			event.clientX,
			gradientPickerDomRef.current
		);
		const { initialPosition, index, significantMoveHappened } =
			controlPointMoveState.current;
		if (
			! significantMoveHappened &&
			Math.abs( initialPosition - relativePosition ) >=
				MINIMUM_SIGNIFICANT_MOVE
		) {
			controlPointMoveState.current.significantMoveHappened = true;
		}

		onChange(
			updateControlPointPosition( controlPoints, index, relativePosition )
		);
	};

	const cleanEventListeners = () => {
		if (
			window &&
			window.removeEventListener &&
			controlPointMoveState.current &&
			controlPointMoveState.current.listenersActivated
		) {
			window.removeEventListener( 'mousemove', onMouseMove );
			window.removeEventListener( 'mouseup', cleanEventListeners );
			onStopControlPointChange();
			controlPointMoveState.current.listenersActivated = false;
		}
	};

	// Adding `cleanEventListeners` to the dependency array below requires the function itself to be wrapped in a `useCallback`
	// This memoization would prevent the event listeners from being properly cleaned.
	// Instead, we'll pass a ref to the function in our `useEffect` so `cleanEventListeners` itself is no longer a dependency.
	const cleanEventListenersRef = useRef();
	cleanEventListenersRef.current = cleanEventListeners;

	useEffect( () => {
		return () => {
			cleanEventListenersRef.current();
		};
	}, [] );
	const disableCustomColors = false;
	const colors = activePalette ? activePalette : useSetting( 'color.palette' );
	return controlPoints.map( ( point, index ) => {
		const initialPosition = point?.position;
		const pointColor = getReadableColor( point.color, colors ); 
		return (
			ignoreMarkerPosition !== initialPosition && (
				<GradientColorPickerDropdown
					isRenderedInSidebar={ isRenderedInSidebar }
					key={ index }
					popoverRef={ popoverRef }
					onClose={ onStopControlPointChange }
					renderToggle={ ( { isOpen, onToggle } ) => (
						<ControlPointButton
							key={ index }
							onClick={ () => {
								if (
									controlPointMoveState.current &&
									controlPointMoveState.current
										.significantMoveHappened
								) {
									return;
								}
								if ( isOpen ) {
									onStopControlPointChange();
								} else {
									onStartControlPointChange();
								}
								onToggle();
							} }
							onMouseDown={ () => {
								if ( window && window.addEventListener ) {
									controlPointMoveState.current = {
										initialPosition,
										index,
										significantMoveHappened: false,
										listenersActivated: true,
									};
									onStartControlPointChange();
									window.addEventListener(
										'mousemove',
										onMouseMove
									);
									window.addEventListener(
										'mouseup',
										cleanEventListeners
									);
								}
							} }
							onKeyDown={ ( event ) => {
								if ( event.code === 'ArrowLeft' ) {
									// Stop propagation of the key press event to avoid focus moving
									// to another editor area.
									event.stopPropagation();
									onChange(
										updateControlPointPosition(
											controlPoints,
											index,
											clampPercent(
												point.position -
													KEYBOARD_CONTROL_POINT_VARIATION
											)
										)
									);
								} else if ( event.code === 'ArrowRight' ) {
									// Stop propagation of the key press event to avoid focus moving
									// to another editor area.
									event.stopPropagation();
									onChange(
										updateControlPointPosition(
											controlPoints,
											index,
											clampPercent(
												point.position +
													KEYBOARD_CONTROL_POINT_VARIATION
											)
										)
									);
								}
							} }
							isOpen={ isOpen }
							position={ point.position }
							color={ point.color }
						/>
					) }
					renderContent={ ( { onClose } ) => (
						<div className="base-pop-gradient-color-picker">
							<HStack
									className="components-custom-gradient-picker__remove-control-point-wrapper"
									alignment="center"
								>
								<Button
									onClick={ () => {
										onClose();
									} }
									variant="link"
								>
									{ __( 'Close Color Picker', 'avanam' ) }
								</Button>
							</HStack>
							{ ! disableCustomColors && (
								<ColorPicker
									color={ pointColor }
									onChange={ ( color ) => {
										onChange(
											updateControlPointColor(
												controlPoints,
												index,
												colord( color.rgb ).toRgbString(),
											)
										);
									} }
									onChangeComplete={ ( color ) => {
										onChange(
											updateControlPointColor(
												controlPoints,
												index,
												colord( color.rgb ).toRgbString(),
											)
										);
									} }
								/>
							) }
							{ colors && (
								<>
								<div style={ {
									display: 'flex',
									flexWrap: 'wrap',
									justifyContent: 'space-between',
									paddingTop: '15px',
									paddingBottom: '15px',
									borderTop: '1px solid rgb(238, 238, 238)',
								} } className="base-swatches-wrap">
								{ map( colors, ( { color, slug, name } ) => {
									const key = `${color}${slug || ''}`
									const palette = slug.replace( 'theme-', '' );
									const isActive = ( ( slug.startsWith( 'palette' ) && pointColor === color ) );
									return (
										<div key={ key } style={ {
											width: 26,
											height: 26,
											marginBottom: 0,
											transform: 'scale(1)',
											transition: '100ms transform ease',
										} } className="base-swatche-item-wrap">
											<Button
												className={ `base-swatch-item ${ isActive ? 'swatch-active' : 'swatch-inactive' }` }
												style={ {
													height: '100%',
													width: '100%',
													border: '1px solid rgb(218, 218, 218)',
													borderRadius: '50%',
													color: `${ color }`,
													boxShadow: `inset 0 0 0 ${ 26 / 2 }px`,
													transition: '100ms box-shadow ease',
												} }
												onClick={ () => {
													if ( slug.startsWith( 'palette' ) ) {
														onChange(
															updateControlPointColor(
																controlPoints,
																index,
																'var(--global-' + palette + ',' + color + ')'
															)
														);
													} else {
														onChange(
															updateControlPointColor(
																controlPoints,
																index,
																colord( color ).toRgbString()
															)
														);
													}
												} }
												tabIndex={ 0 }
												>
													<Icon className="dashicon" icon={ globeIcon } />
											</Button>
										</div>
									  )
								} ) }
								</div>
								</>
							) }
							{ ! disableRemove && controlPoints.length > 2 && (
								<HStack
									className="components-custom-gradient-picker__remove-control-point-wrapper"
									alignment="center"
								>
									<Button
										onClick={ () => {
											onChange(
												removeControlPoint(
													controlPoints,
													index
												)
											);
											onClose();
										} }
										variant="link"
									>
										{ __( 'Remove Control Point', 'avanam' ) }
									</Button>
								</HStack>
							) }
						</div>
					) }
					style={ {
						left: `${ point.position }%`,
						transform: 'translateX( -50% )',
					} }
				/>
			)
		);
	} );
}

function InsertPoint( {
	value: controlPoints,
	onChange,
	onOpenInserter,
	onCloseInserter,
	insertPosition,
	isRenderedInSidebar,
	activePalette,
	popoverRef,
} ) {
	const [ alreadyInsertedPoint, setAlreadyInsertedPoint ] = useState( false );
	const disableCustomColors = false;
	const colors = activePalette ? activePalette : useSetting( 'color.palette' );
	const [ tempColor, setTempColor ] = useState( '' );
	const pointColor = getReadableColor( tempColor, colors ); 
	return (
		<GradientColorPickerDropdown
			isRenderedInSidebar={ isRenderedInSidebar }
			popoverRef={ popoverRef }
			className="components-custom-gradient-picker__inserter"
			onClose={ () => {
				onCloseInserter();
			} }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Button
					aria-expanded={ isOpen }
					aria-haspopup="true"
					onClick={ () => {
						if ( isOpen ) {
							onCloseInserter();
						} else {
							setAlreadyInsertedPoint( false );
							onOpenInserter();
						}
						onToggle();
					} }
					className="components-custom-gradient-picker__insert-point-dropdown"
					icon={ plus }
				/>
			) }
			renderContent={ () => (
				<div className="base-pop-gradient-color-picker">
					<HStack
							className="components-custom-gradient-picker__remove-control-point-wrapper"
							alignment="center"
						>
						<Button
							onClick={ () => {
								onCloseInserter();
							} }
							variant="link"
						>
							{ __( 'Close Color Picker', 'avanam' ) }
						</Button>
					</HStack>
					{ ! disableCustomColors && (
						<ColorPicker
							color={ pointColor }
							onChange={ ( color ) => {
								setTempColor( colord( color.rgb ).toRgbString() );
								if ( ! alreadyInsertedPoint ) {
									onChange(
										addControlPoint(
											controlPoints,
											insertPosition,
											colord( color.rgb ).toRgbString()
										)
									);
									setAlreadyInsertedPoint( true );
								} else {
									onChange(
										updateControlPointColorByPosition(
											controlPoints,
											insertPosition,
											colord( color.rgb ).toRgbString()
										)
									);
								}
							} }
							onChangeComplete={ ( color ) => {
								setTempColor( colord( color.rgb ).toRgbString() );
								if ( ! alreadyInsertedPoint ) {
									onChange(
										addControlPoint(
											controlPoints,
											insertPosition,
											colord( color.rgb ).toRgbString()
										)
									);
									setAlreadyInsertedPoint( true );
								} else {
									onChange(
										updateControlPointColorByPosition(
											controlPoints,
											insertPosition,
											colord( color.rgb ).toRgbString()
										)
									);
								}
							} }
						/>
					) }
					{ colors && (
						<div style={ {
							display: 'flex',
							flexWrap: 'wrap',
							justifyContent: 'space-between',
							paddingTop: '15px',
							paddingBottom: '15px',
							borderTop: '1px solid rgb(238, 238, 238)',
						} } className="base-swatches-wrap">
						{ map( colors, ( { color, slug, name } ) => {
							const key = `${color}${slug || ''}`
							const palette = slug.replace( 'theme-', '' );
							const isActive = ( ( slug.startsWith( 'palette' ) && pointColor === color ) );
							return (
								<div key={ key } style={ {
									width: 26,
									height: 26,
									marginBottom: 0,
									transform: 'scale(1)',
									transition: '100ms transform ease',
								} } className="base-swatche-item-wrap">
									<Button
										className={ `base-swatch-item ${ isActive ? 'swatch-active' : 'swatch-inactive' }` }
										style={ {
											height: '100%',
											width: '100%',
											border: '1px solid rgb(218, 218, 218)',
											borderRadius: '50%',
											color: `${ color }`,
											boxShadow: `inset 0 0 0 ${ 26 / 2 }px`,
											transition: '100ms box-shadow ease',
										} }
										onClick={ () => {
											setTempColor( colord( color ).toRgbString() );
											if ( ! alreadyInsertedPoint ) {
												if ( slug.startsWith( 'palette' ) ) {
													onChange(
														addControlPoint(
															controlPoints,
															insertPosition,
															'var(--global-' + palette + ',' + color + ')'
														)
													);
												} else {
													onChange(
														addControlPoint(
															controlPoints,
															insertPosition,
															colord( color ).toRgbString()
														)
													);
												}
												setAlreadyInsertedPoint( true );
											} else {
												if ( slug.startsWith( 'palette' ) ) {
													onChange(
														updateControlPointColorByPosition(
															controlPoints,
															insertPosition,
															'var(--global-' + palette + ',' + color + ')'
														)
													);
												} else {
													onChange(
														updateControlPointColorByPosition(
															controlPoints,
															insertPosition,
															colord( color ).toRgbString()
														)
													);
												}
											}
										} }
										tabIndex={ 0 }
										>
											<Icon className="dashicon" icon={ globeIcon } />
									</Button>
								</div>
							  )
						} ) }
						</div>
					) }
				</div>
			) }
			style={
				insertPosition !== null
					? {
							left: `${ insertPosition }%`,
							transform: 'translateX( -50% )',
					  }
					: undefined
			}
		/>
	);
}
ControlPoints.InsertPoint = InsertPoint;

export default ControlPoints;
