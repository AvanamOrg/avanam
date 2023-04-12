/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters, TabPanel, Panel, PanelBody, PanelRow, Button } = wp.components;
export const Sidebar = () => {
	return (
		<Fragment>
			<Panel className="support-section sidebar-section">
				<PanelBody
					opened={ true }
				>
					<h2>{ __( 'Support', 'avanam' ) }</h2>
					<p>{ __( 'Have a question, we are happy to help! Get in touch with our support team.', 'avanam' ) }</p>
					<a href="https://avanam.org/wordpress#support" target="_blank" class="sidebar-link">{ __( 'Submit a Ticket', 'avanam' ) }</a>
				</PanelBody>
			</Panel>
		</Fragment>
	);
};

export default withFilters( 'base_theme_sidebar' )( Sidebar );