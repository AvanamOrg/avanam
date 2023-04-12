/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters } = wp.components;

export const HelpTab = () => {
	return (
		<div className="base-desk-help-inner">
			<h2>{ __( 'Welcome to Avanam!', 'avanam' ) }</h2>
			<p>{ __( 'You are going to love working with this theme! View the video below to get started with our video tutorials or click the view knowledge base button below to see all the documentation.', 'avanam' ) }</p>
			<div className="video-container">
				<a href="https://www.youtube.com/watch?v=avanam"></a>
			</div>
			<a href="https://avanam.org/wordpress#learn" className="base-desk-button" target="_blank">{ __( 'Video Tutorials', 'avanam' ) }</a><a href="https://avanam.org/wordpress#kb" className="base-desk-button base-desk-button-second" target="_blank">{ __( 'View Knowledge Base', 'avanam' ) }</a>
		</div>
	);
};

export default withFilters( 'base_theme_help' )( HelpTab );