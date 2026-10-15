import { __ } from '@wordpress/i18n';
import { ToggleControl, TextControl, VisuallyHidden } from '@wordpress/components';
import { Component } from '@wordpress/element';

const defaultSettings = [
	{
		id    : 'title',
		title : __( 'Title attribute', 'slim-seo' ),
		type  : 'TextControl'
	},
	{
		id    : 'opensInNewTab',
		title : __( 'Open link in a new tab', 'slim-seo' ),
		type  : 'ToggleControl'
	},
	{
		id    : 'nofollow',
		title : __( 'Add rel="nofollow" to link', 'slim-seo' ),
		type  : 'ToggleControl'
	},
	{
		id    : 'sponsored',
		title : __( 'Add rel="sponsored" to link', 'slim-seo' ),
		type  : 'ToggleControl'
	},
	{
		id    : 'ugc',
		title : __( 'Add rel="ugc" to link', 'slim-seo' ),
		type  : 'ToggleControl'
	}	
]

class LinkControlSettingsDrawer extends Component {
	constructor (props) {
		super(props)

		this.state = {
			textValue : (props.value && props.value.title) || ''
		}
	}

	componentDidUpdate (prevProps) {
		const prevTitle = (prevProps.value && prevProps.value.title) || ''
		const nextTitle = (this.props.value && this.props.value.title) || ''

		// Sync when parent resets/changes title (e.g. selecting a new link).
		if (prevTitle !== nextTitle && this.state.textValue !== nextTitle) {
			this.setState({ textValue: nextTitle })
		}
	}

	componentWillUnmount () {
		const currentTitle = (this.props.value && this.props.value.title) || ''

		// Skip if already committed (including intentional empty after reset).
		if (this.state.textValue === currentTitle) {
			return
		}

		this.props.onChange({
			...this.props.value,
			title : this.state.textValue
		})
	}

	render () {
		const { value, onChange } = this.props
		const settings = defaultSettings

		if (!settings || !settings.length) {
			return null
		}

		const handleSettingChange = (setting) => (newValue) => {
			onChange({
				...value,
				[setting.id] : newValue
			})
		}

		const handleTitleChange = (event, setting) => {
			// This prevents the link drawer from crashing if no URL is set.
			if (!value.url) {
				this.setState({ textValue: event.target.value })
				return
			}

			onChange({
				...value,
				[setting.id] : event.target.value
			})
		}

		const theSettings = settings.map((setting) => {
			if ( 'TextControl' === setting.type ) {
				return <TextControl
					data-ssp="true"
					className="block-editor-link-control__setting ss-link-title"
					key={setting.id}
					label={setting.title}
					onChange={(val) => {
						this.setState({ textValue: val })
					}}
					onBlur={(event) => {
						handleTitleChange(event, setting)
					}}
					onKeyDown={(event) => {
						if (13 !== event.keyCode) {
							return
						}

						handleTitleChange(event, setting)

						event.preventDefault()
						event.stopPropagation()
					}}
					value={this.state.textValue}
				/>
			} else if ( 'ToggleControl' === setting.type ) {
				return <ToggleControl
					className="block-editor-link-control__setting"
					key={setting.id}
					label={setting.title}
					onChange={handleSettingChange(setting)}
					checked={value ? !!value[setting.id] : false}
				/>
			}

			return null
		})

		return (
			<fieldset className="block-editor-link-control__settings">
				<VisuallyHidden as="legend">
					{ __( 'Currently selected link settings', 'slim-seo' ) }
				</VisuallyHidden>
				{theSettings}
			</fieldset>
		)
	}
}

export default LinkControlSettingsDrawer