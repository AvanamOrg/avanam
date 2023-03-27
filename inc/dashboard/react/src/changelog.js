/**
 * WordPress dependencies
 */
 import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters } = wp.components;
const { TabPanel, Panel, PanelBody } = wp.components;
import ChangelogItem from './changelog-item';

export const ChangelogTab = () => {
	const tabs = [
		{
			name: 'avanam',
			title: __( 'Changelog', 'avanam' ),
			className: 'base-changelog-tab',
		},
		{
			name: 'pro',
			title: __( 'Pro Changelog', 'avanam' ),
			className: 'base-pro-changelog-tab',
		},
	];
	return (
		<Fragment>
			{ baseDashboardParams.changelog && (
				<Fragment>
					{ baseDashboardParams.proChangelog && baseDashboardParams.proChangelog.length && (
						<TabPanel className="base-dashboard-changelog-tab-panel"
							activeClass="active-tab"
							tabs={ tabs }>
							{
								( tab ) => {
									switch ( tab.name ) {
										case 'avanam':
											return (
												<Panel className="base-changelog-section tab-section">
													<PanelBody
														opened={ true }
													>
														{ baseDashboardParams.changelog.map( ( item, index ) => {
															return <ChangelogItem
																item={ item }
																index={ item }
															/>;
														} ) }
													</PanelBody>
												</Panel>
											);

										case 'pro':
											return (
												<Panel className="pro-changelog-section tab-section">
													<PanelBody
														opened={ true }
													>
														{ baseDashboardParams.proChangelog.map( ( item, index ) => {
															return <ChangelogItem
																item={ item }
																index={ item }
															/>;
														} ) }
													</PanelBody>
												</Panel>
											);
									}
								}
							}
						</TabPanel>
					) }
					{ ( '' == baseDashboardParams.proChangelog || ( Array.isArray( baseDashboardParams.proChangelog ) && ! baseDashboardParams.proChangelog.length ) ) && (
						<Fragment>
							{ baseDashboardParams.changelog.map( ( item, index ) => {
								return <ChangelogItem
									item={ item }
									index={ item }
								/>;
							} ) }
						</Fragment>
					) }
				</Fragment>
			) }
		</Fragment>
	);
};

export default withFilters( 'base_theme_changelog' )( ChangelogTab );