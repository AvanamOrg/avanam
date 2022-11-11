/**
 * @output wp-admin/js/widgets/text-widgets.js
 */

/* global tinymce, switchEditors */
/* eslint consistent-this: [ "error", "control" ] */

/**
 * @namespace wp.textWidgets
 */
( function( $ ) {
	'use strict';

	wp.textWidgets.TextWidgetControl = Backbone.View.extend( {
		/**
		 * View events.
		 *
		 * @type {Object}
		 */
		events: {},

		/**
		 * Text widget control.
		 *
		 * @constructs wp.textWidgets.TextWidgetControl
		 * @augments   Backbone.View
		 * @abstract
		 *
		 * @param {Object} options - Options.
		 * @param {jQuery} options.el - Control field container element.
		 * @param {jQuery} options.syncContainer - Container element where fields are synced for the server.
		 *
		 * @return {void}
		 */
		initialize: function initialize( options ) {
			var control = this;

			if ( ! options.el ) {
				throw new Error( 'Missing options.el' );
			}
			if ( ! options.syncContainer ) {
				throw new Error( 'Missing options.syncContainer' );
			}

			Backbone.View.prototype.initialize.call( control, options );
			control.syncContainer = options.syncContainer;

			control.$el.addClass( 'text-widget-fields' );
			control.$el.html( wp.template( 'widget-text-control-fields' ) );

			control.customHtmlWidgetPointer = control.$el.find( '.wp-pointer.custom-html-widget-pointer' );
			if ( control.customHtmlWidgetPointer.length ) {
				control.customHtmlWidgetPointer.find( '.close' ).on( 'click', function( event ) {
					event.preventDefault();
					control.customHtmlWidgetPointer.hide();
					$( '#' + control.fields.text.attr( 'id' ) + '-html' ).focus();
					control.dismissPointers( [ 'text_widget_custom_html' ] );
				});
				control.customHtmlWidgetPointer.find( '.add-widget' ).on( 'click', function( event ) {
					event.preventDefault();
					control.customHtmlWidgetPointer.hide();
					control.openAvailableWidgetsPanel();
				});
			}

			control.pasteHtmlPointer = control.$el.find( '.wp-pointer.paste-html-pointer' );
			if ( control.pasteHtmlPointer.length ) {
				control.pasteHtmlPointer.find( '.close' ).on( 'click', function( event ) {
					event.preventDefault();
					control.pasteHtmlPointer.hide();
					control.editor.focus();
					control.dismissPointers( [ 'text_widget_custom_html', 'text_widget_paste_html' ] );
				});
			}

			control.fields = {
				title: control.$el.find( '.title' ),
				text: control.$el.find( '.text' )
			};

			// Sync input fields to hidden sync fields which actually get sent to the server.
			_.each( control.fields, function( fieldInput, fieldName ) {
				fieldInput.on( 'input change', function updateSyncField() {
					var syncInput = control.syncContainer.find( '.sync-input.' + fieldName );
					if ( syncInput.val() !== fieldInput.val() ) {
						syncInput.val( fieldInput.val() );
						syncInput.trigger( 'change' );
					}
				});

				// Note that syncInput cannot be re-used because it will be destroyed with each widget-updated event.
				fieldInput.val( control.syncContainer.find( '.sync-input.' + fieldName ).val() );
			});
		},

		/**
		 * Dismiss pointers for Custom HTML widget.
		 *
		 * @since 4.8.1
		 *
		 * @param {Array} pointers Pointer IDs to dismiss.
		 * @return {void}
		 */
		dismissPointers: function dismissPointers( pointers ) {
			_.each( pointers, function( pointer ) {
				wp.ajax.post( 'dismiss-wp-pointer', {
					pointer: pointer
				});
				wp.textWidgets.dismissedPointers.push( pointer );
			});
		},

		/**
		 * Open available widgets panel.
		 *
		 * @since 4.8.1
		 * @return {void}
		 */
		openAvailableWidgetsPanel: function openAvailableWidgetsPanel() {
			var sidebarControl;
			wp.customize.section.each( function( section ) {
				if ( section.extended( wp.customize.Widgets.SidebarSection ) && section.expanded() ) {
					sidebarControl = wp.customize.control( 'sidebars_widgets[' + section.params.sidebarId + ']' );
				}
			});
			if ( ! sidebarControl ) {
				return;
			}
			setTimeout( function() { // Timeout to prevent click event from causing panel to immediately collapse.
				wp.customize.Widgets.availableWidgetsPanel.open( sidebarControl );
				wp.customize.Widgets.availableWidgetsPanel.$search.val( 'HTML' ).trigger( 'keyup' );
			});
		},

		/**
		 * Update input fields from the sync fields.
		 *
		 * This function is called at the widget-updated and widget-synced events.
		 * A field will only be updated if it is not currently focused, to avoid
		 * overwriting content that the user is entering.
		 *
		 * @return {void}
		 */
		updateFields: function updateFields() {
			var control = this, syncInput;

			if ( ! control.fields.title.is( document.activeElement ) ) {
				syncInput = control.syncContainer.find( '.sync-input.title' );
				control.fields.title.val( syncInput.val() );
			}

			syncInput = control.syncContainer.find( '.sync-input.text' );
			if ( control.fields.text.is( ':visible' ) ) {
				if ( ! control.fields.text.is( document.activeElement ) ) {
					control.fields.text.val( syncInput.val() );
				}
			} else if ( control.editor && ! control.editorFocused && syncInput.val() !== control.fields.text.val() ) {
				control.editor.setContent( wp.oldEditor.autop( syncInput.val() ) );
			}
		},

		/**
		 * Initialize editor.
		 *
		 * @return {void}
		 */
		initializeEditor: function initializeEditor() {
			var control = this, changeDebounceDelay = 1000, id, textarea, triggerChangeIfDirty, restoreTextMode = false, needsTextareaChangeTrigger = false, previousValue;
			textarea = control.fields.text;
			id = textarea.attr( 'id' );
			previousValue = textarea.val();

			/**
			 * Trigger change if dirty.
			 *
			 * @return {void}
			 */
			triggerChangeIfDirty = function() {
				var updateWidgetBuffer = 300; // See wp.customize.Widgets.WidgetControl._setupUpdateUI() which uses 250ms for updateWidgetDebounced.
				if ( control.editor.isDirty() ) {

					/*
					 * Account for race condition in customizer where user clicks Save & Publish while
					 * focus was just previously given to the editor. Since updates to the editor
					 * are debounced at 1 second and since widget input changes are only synced to
					 * settings after 250ms, the customizer needs to be put into the processing
					 * state during the time between the change event is triggered and updateWidget
					 * logic starts. Note that the debounced update-widget request should be able
					 * to be removed with the removal of the update-widget request entirely once
					 * widgets are able to mutate their own instance props directly in JS without
					 * having to make server round-trips to call the respective WP_Widget::update()
					 * callbacks. See <https://core.trac.wordpress.org/ticket/33507>.
					 */
					if ( wp.customize && wp.customize.state ) {
						wp.customize.state( 'processing' ).set( wp.customize.state( 'processing' ).get() + 1 );
						_.delay( function() {
							wp.customize.state( 'processing' ).set( wp.customize.state( 'processing' ).get() - 1 );
						}, updateWidgetBuffer );
					}

					if ( ! control.editor.isHidden() ) {
						control.editor.save();
					}
				}

				// Trigger change on textarea when it has changed so the widget can enter a dirty state.
				if ( needsTextareaChangeTrigger && previousValue !== textarea.val() ) {
					textarea.trigger( 'change' );
					needsTextareaChangeTrigger = false;
					previousValue = textarea.val();
				}
			};

			// Just-in-time force-update the hidden input fields.
			control.syncContainer.closest( '.widget' ).find( '[name=savewidget]:first' ).on( 'click', function onClickSaveButton() {
				triggerChangeIfDirty();
			});

			/**
			 * Build (or re-build) the visual editor.
			 *
			 * @return {void}
			 */
			function buildEditor() {
				var editor, onInit, showPointerElement;

				// Abort building if the textarea is gone, likely due to the widget having been deleted entirely.
				if ( ! document.getElementById( id ) ) {
					return;
				}

				// The user has disabled TinyMCE.
				if ( typeof window.tinymce === 'undefined' ) {
					wp.oldEditor.initialize( id, {
						quicktags: true,
						mediaButtons: true
					});

					return;
				}

				// Destroy any existing editor so that it can be re-initialized after a widget-updated event.
				if ( tinymce.get( id ) ) {
					restoreTextMode = tinymce.get( id ).isHidden();
					wp.oldEditor.remove( id );
				}

				// Add or enable the `wpview` plugin.
				$( document ).one( 'wp-before-tinymce-init.text-widget-init', function( event, init ) {
					// If somebody has removed all plugins, they must have a good reason.
					// Keep it that way.
					if ( ! init.plugins ) {
						return;
					} else if ( ! /\bwpview\b/.test( init.plugins ) ) {
						init.plugins += ',wpview';
					}
				} );

				wp.oldEditor.initialize( id, {
					tinymce: {
						wpautop: true
					},
					quicktags: true,
					mediaButtons: true
				});

				/**
				 * Show a pointer, focus on dismiss, and speak the contents for a11y.
				 *
				 * @param {jQuery} pointerElement Pointer element.
				 * @return {void}
				 */
				showPointerElement = function( pointerElement ) {
					pointerElement.show();
					pointerElement.find( '.close' ).focus();
					wp.a11y.speak( pointerElement.find( 'h3, p' ).map( function() {
						return $( this ).text();
					} ).get().join( '\n\n' ) );
				};

				editor = window.tinymce.get( id );
				if ( ! editor ) {
					throw new Error( 'Failed to initialize editor' );
				}
				onInit = function() {

					// When a widget is moved in the DOM the dynamically-created TinyMCE iframe will be destroyed and has to be re-built.
					$( editor.getWin() ).on( 'unload', function() {
						_.defer( buildEditor );
					});

					// If a prior mce instance was replaced, and it was in text mode, toggle to text mode.
					if ( restoreTextMode ) {
						switchEditors.go( id, 'html' );
					}

					// Show the pointer.
					$( '#' + id + '-html' ).on( 'click', function() {
						control.pasteHtmlPointer.hide(); // Hide the HTML pasting pointer.

						if ( -1 !== wp.textWidgets.dismissedPointers.indexOf( 'text_widget_custom_html' ) ) {
							return;
						}
						showPointerElement( control.customHtmlWidgetPointer );
					});

					// Hide the pointer when switching tabs.
					$( '#' + id + '-tmce' ).on( 'click', function() {
						control.customHtmlWidgetPointer.hide();
					});

					// Show pointer when pasting HTML.
					editor.on( 'pastepreprocess', function( event ) {
						var content = event.content;
						if ( -1 !== wp.textWidgets.dismissedPointers.indexOf( 'text_widget_paste_html' ) || ! content || ! /&lt;\w+.*?&gt;/.test( content ) ) {
							return;
						}

						// Show the pointer after a slight delay so the user sees what they pasted.
						_.delay( function() {
							showPointerElement( control.pasteHtmlPointer );
						}, 250 );
					});
				};

				if ( editor.initialized ) {
					onInit();
				} else {
					editor.on( 'init', onInit );
				}

				control.editorFocused = false;

				editor.on( 'focus', function onEditorFocus() {
					control.editorFocused = true;
				});
				editor.on( 'paste', function onEditorPaste() {
					editor.setDirty( true ); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				});
				editor.on( 'NodeChange', function onNodeChange() {
					needsTextareaChangeTrigger = true;
				});
				editor.on( 'NodeChange', _.debounce( triggerChangeIfDirty, changeDebounceDelay ) );
				editor.on( 'blur hide', function onEditorBlur() {
					control.editorFocused = false;
					triggerChangeIfDirty();
				});

				control.editor = editor;
			}

			buildEditor();
		}
	});
})( jQuery );