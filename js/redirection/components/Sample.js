import { useReducer } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { fetcher, exportCSV } from "../helper/misc";

const Sample = () => {
	const [ generating, toggleGenerating ] = useReducer( generating => !generating, false );
	const text = generating ? __( 'Downloading sample file', 'slim-seo' ) : __( 'Download sample CSV file', 'slim-seo' );

	const submit = e => {
		e.preventDefault();

		toggleGenerating();
	
		fetcher( 'sample' ).then( result => {
			if ( result ) {
				exportCSV( result.filename, result.data );
			}

			toggleGenerating();
		} );
	};

	return (
		<div className='ss-redirect-import-instructions'>
			<p><strong>{ __( 'Your CSV must have exactly 7 columns in this order:', 'slim-seo' ) }</strong></p>
			<table>
				<thead>
					<tr>
						<th>{ __( 'Column name', 'slim-seo' ) }</th>
						<th>{ __( 'Data type', 'slim-seo' ) }</th>
						<th>{ __( 'Description', 'slim-seo' ) }</th>
						<th>{ __( 'Posible values', 'slim-seo' ) }</th>
						<th>{ __( 'Sample', 'slim-seo' ) }</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{ __( 'Type', 'slim-seo' ) }</td>
						<td>{ __( 'Number', 'slim-seo' ) }</td>
						<td>{ __( 'Redirect type is used to forward one URL to another', 'slim-seo' ) }</td>
						<td>{ Object.keys( SSRedirection.redirectTypes ).join( ', ' ) }</td>
						<td>{ SSRedirection.csvSampleData.type }</td>
					</tr>
					<tr>
						<td>{ __( 'Condition', 'slim-seo' ) }</td>
						<td>{ __( 'String', 'slim-seo' ) }</td>
						<td>{ __( 'Condition is used for matching URL to redirect', 'slim-seo' ) }</td>
						<td>{ Object.keys( SSRedirection.conditionOptions ).join( ', ' ) }</td>
						<td>{ SSRedirection.csvSampleData.condition }</td>
					</tr>
					<tr>
						<td>{ __( 'From', 'slim-seo' ) }</td>
						<td>{ __( 'String', 'slim-seo' ) }</td>
						<td>{ __( 'URL to redirect', 'slim-seo' ) }</td>
						<td></td>
						<td>{ SSRedirection.csvSampleData.from }</td>
					</tr>
					<tr>
						<td>{ __( 'To', 'slim-seo' ) }</td>
						<td>{ __( 'String', 'slim-seo' ) }</td>
						<td>{ __( 'Destination URL', 'slim-seo' ) }</td>
						<td></td>
						<td>{ SSRedirection.csvSampleData.to }</td>
					</tr>
					<tr>
						<td>{ __( 'Note', 'slim-seo' ) }</td>
						<td>{ __( 'String', 'slim-seo' ) }</td>
						<td>{ __( 'Something to reminds you about the redirects', 'slim-seo' ) }</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>{ __( 'Enable', 'slim-seo' ) }</td>
						<td>{ __( 'Number', 'slim-seo' ) }</td>
						<td>{ __( 'Is the redirect enabled?', 'slim-seo' ) }</td>
						<td>0, 1</td>
						<td>{ SSRedirection.csvSampleData.enable }</td>
					</tr>
					<tr>
						<td>{ __( 'Ignore Parameters', 'slim-seo' ) }</td>
						<td>{ __( 'Number', 'slim-seo' ) }</td>
						<td>{ __( "Ignore URL's parameters if URL has parameters", 'slim-seo' ) }</td>
						<td>0, 1</td>
						<td>{ SSRedirection.csvSampleData.ignoreParameters }</td>
					</tr>
				</tbody>
			</table>
			<p className='description'>{ __( 'Notice: Header row with 7 columns is required.', 'slim-seo' ) }</p>
			<p className='description'>
				<a href='#' title={ text } onClick={ submit } disabled={ generating }>
					{ text }
				</a>
			</p>
		</div>
	);
};

export default Sample;
