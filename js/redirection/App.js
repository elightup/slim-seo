import { render } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from 'react-tabs';
import Redirects from './redirects/List';
import Log404 from './404/List';
import Settings from './settings/Settings';

const App = () => {
	return (
		<Tabs forceRenderTabPanel={ true }>
			<TabList>
				<Tab>{ __( 'Redirects', 'slim-seo' ) }</Tab>
				{ 1 == SSRedirection.settings['enable_404_logs'] && <Tab>{ __( '404 Logs', 'slim-seo' ) }</Tab> }
				<Tab>{ __( 'Settings', 'slim-seo' ) }</Tab>
			</TabList>

			<TabPanel>
				<Redirects />
			</TabPanel>

			{
				1 == SSRedirection.settings['enable_404_logs']
				&& (
					<TabPanel>
						<Log404 />
					</TabPanel>
				)
			}

			<TabPanel>
				<Settings />
			</TabPanel>
		</Tabs>
	);
};

render( <App />, document.getElementById( 'ss-redirection' ) );