import { render, useEffect ,useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import { request } from "./functions";
import PostType from "./components/PostType";

const Single = () => {
	return <>
		hiiiiii single
	</>;
};

render( <Single />, document.getElementById( 'ss-post-type' ) );