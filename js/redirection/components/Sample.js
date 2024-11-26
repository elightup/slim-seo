import { useReducer } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { exportCSV, fetcher } from "../helper/misc";

const Sample = () => {
	const [ generating, toggleGenerating ] = useReducer( generating => !generating, false );
	const text = generating ? __( 'Downloading...', 'slim-seo' ) : __( 'Download sample CSV file', 'slim-seo' );

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
			<p>{ __( 'Your CSV must have a header row and exactly 7 columns in this order:', 'slim-seo' ) }</p>
			<table>
				<thead>
					<tr>
						<th>{ __( 'Type', 'slim-seo' ) }</th>
						<th>{ __( 'Condition', 'slim-seo' ) }</th>
						<th>{ __( 'From', 'slim-seo' ) }</th>
						<th>{ __( 'To', 'slim-seo' ) }</th>
						<th>{ __( 'Note', 'slim-seo' ) }</th>
						<th>{ __( 'Enable', 'slim-seo' ) }</th>
						<th>{ __( 'Ignore Parameters', 'slim-seo' ) }</th>
					</tr>
				</thead>
				<tbody>
					{
						SSRedirection.csvSampleData.map( row => (
							<tr key={ row[2] }>
								<td>{ row[0] }</td>
								<td>{ row[1] }</td>
								<td>{ row[2] }</td>
								<td>{ row[3] }</td>
								<td>{ row[4] }</td>
								<td>{ row[5] }</td>
								<td>{ row[6] }</td>
							</tr>
						) )
					}
				</tbody>
			</table>
			<p>
				<a href='#' onClick={ submit }>{ text }</a>
				&nbsp; | &nbsp;
				<a href='https://docs.wpslimseo.com/slim-seo/redirection/#exportimport-redirects' target="_blank">
					{ __( 'View documentation', 'slim-seo' ) }
				</a>
			</p>
		</div>
	);
};

export default Sample;
