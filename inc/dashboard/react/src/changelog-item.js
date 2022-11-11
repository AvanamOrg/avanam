/**
 * WordPress dependencies
 */
 import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters } = wp.components;

export const ChangelogItem = ( version ) => {
	return (
		<div className="changelog-version">
			<h3 className="version-head">{ version.item.head }</h3>
			{ version.item.add && (
				<Fragment>
					{ version.item.add.map( ( adds, index ) => {
						return <div className="version-add">{ adds }</div>;
					} ) }
				</Fragment>
			) }
			{ version.item.update && (
				<Fragment>
					{ version.item.update.map( ( updates, index ) => {
						return <div className="version-update">{ updates }</div>;
					} ) }
				</Fragment>
			) }
			{ version.item.fix && (
				<Fragment>
					{ version.item.fix.map( ( fixes, index ) => {
						return <div className="version-fix">{ fixes }</div>;
					} ) }
				</Fragment>
			) }
		</div>
	);
};

export default withFilters( 'base_theme_changelog' )( ChangelogItem );