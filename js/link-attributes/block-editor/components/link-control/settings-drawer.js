import { __ } from '@wordpress/i18n';
import { ToggleControl, TextControl, VisuallyHidden } from '@wordpress/components';
import { Component } from '@wordpress/element';

const defaultSettings = [
	{
		id    : 'title',
		title : __( 'Title', 'slim-seo' ),
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
			textValue : props.value.title
		}
	}

	componentDidUpdate (prevProps, prevState) {
		if (prevState.textValue !== this.state.textValue) {
			return
		}

		if (prevProps.value.title !== this.props.value.title) {
			return
		}

		if (prevProps.value.title !== prevState.textValue) {
			this.setState({ textValue: prevProps.value.title })
		}
	}

	componentWillUnmount () {
		// If we don't have any title or if the title hasn't changed, let's return early since this prop change is unnecessary.
		if (!this.state.textValue || (this.state.textValue === this.props.value.title)) {
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