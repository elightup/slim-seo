import { find } from 'lodash-es';
import { Clipboard } from 'react-native';
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { withSpokenMessages } from '@wordpress/components';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { applyFormat, getActiveFormat, getTextContent, isCollapsed, removeFormat, slice } from '@wordpress/rich-text';
import { isURL } from '@wordpress/url';
import { link as linkIcon } from '@wordpress/icons';
import ModalLinkUI from './modal';

const name = 'core/link';

export const link = {
	name,
	title      : __( 'Link', 'slim-seo' ),
	tagName    : 'a',
	className  : null,
	attributes : {
		url    : 'href',
		target : 'target',
		rel    : 'rel',
		title  : 'title'
	},
	edit : withSpokenMessages(
		class LinkEdit extends Component {
			constructor () {
				super(...arguments)

				this.addLink = this.addLink.bind(this)
				this.stopAddingLink = this.stopAddingLink.bind(this)
				this.onRemoveFormat = this.onRemoveFormat.bind(this)
				this.getURLFromClipboard = this.getURLFromClipboard.bind(
					this
				)
				this.state = {
					addingLink : false
				}
			}

			addLink () {
				const { value, onChange } = this.props
				const text = getTextContent(slice(value))

				if (text && isURL(text)) {
					onChange(
						applyFormat(value, {
							type       : name,
							attributes : { url: text }
						})
					)
				} else {
					this.setState({ addingLink: true })
					this.getURLFromClipboard()
				}
			}

			stopAddingLink () {
				this.setState({ addingLink: false })
			}

			getLinkSelection () {
				const { value, isActive } = this.props
				const startFormat = getActiveFormat(value, 'core/link')

				// if the link isn't selected, get the link manually by looking around the cursor
				if (startFormat && isCollapsed(value) && isActive) {
					let startIndex = value.start,
					 endIndex = value.end

					while (find(value.formats[startIndex], startFormat)) {
						startIndex--
					}

					endIndex++

					while (find(value.formats[endIndex], startFormat)) {
						endIndex++
					}

					return {
						...value,
						start : startIndex + 1,
						end   : endIndex
					}
				}

				return value
			}

			onRemoveFormat () {
				const { onChange, speak, value } = this.props
				const startFormat = getActiveFormat(value, 'core/link')

				// Before we try to remove anything we check if there is something at the caret position to remove.
				if (isCollapsed(value) && startFormat === undefined) {
					return
				}

				const linkSelection = this.getLinkSelection()

				onChange(removeFormat(linkSelection, name))
				speak( __( 'Link removed.', 'slim-seo' ), 'assertive' )
			}

			async getURLFromClipboard () {
				const clipboardText = await Clipboard.getString()
				if (!clipboardText) {
					return
				}
				// Check if pasted text is URL
				if (!isURL(clipboardText)) {
					return
				}
				this.setState({ clipboardURL: clipboardText })
			}

			render () {
				const { isActive, activeAttributes, onChange } = this.props
				const linkSelection = this.getLinkSelection()
				// If no URL is set and we have a clipboard URL let's use it
				if (!activeAttributes.url && this.state.clipboardURL) {
					activeAttributes.url = this.state.clipboardURL
				}
				return (
					<>
						<ModalLinkUI
							isVisible={ this.state.addingLink }
							isActive={ isActive }
							activeAttributes={ activeAttributes }
							onClose={ this.stopAddingLink }
							onChange={ onChange }
							onRemove={ this.onRemoveFormat }
							value={ linkSelection }
						/>
						<RichTextToolbarButton
							name="link"
							icon={ linkIcon }
							title={ __('Link', 'slim-seo') }
							onClick={ this.addLink }
							isActive={ isActive }
							shortcutType="primary"
							shortcutCharacter="k"
						/>
					</>
				)
			}
		}
	)
}