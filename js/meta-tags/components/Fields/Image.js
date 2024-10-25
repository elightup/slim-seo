import { Control } from "@elightup/form";
import { Button } from "@wordpress/components";
import { useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Image = ( { id, std, ...rest } ) => {
	const inputRef = useRef();

	const openMediaPopup = e => {
		e.preventDefault();

		let frame = wp.media( {
			multiple: false,
			title: ss.mediaPopupTitle
		} );

		frame.open();
		frame.off( 'select' );

		frame.on( 'select', () => {
			const url = frame.state().get( 'selection' ).first().toJSON().url;
			inputRef.current.value = url;
		} );
	};

	return (
		<Control id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } />
				<Button icon="format-image" onClick={ openMediaPopup } className="ss-select-image" />
				<PropInserter data="meta-tags/image_variables" inputRef={ inputRef } replace={ true } />
			</div>
		</Control>
	);
};

export default Image;
