import { Control } from "@elightup/form";
import { Button } from "@wordpress/components";
import { useRef } from "@wordpress/element";

const Image = ( property ) => {
	const inputRef = useRef();
	const { id, label, std, className = '', mediaPopupTitle, ...rest } = property;

	const openMediaPopup = e => {
		e.preventDefault();

		let frame = wp.media( {
			multiple: false,
			title: mediaPopupTitle
		} );

		frame.open();
		frame.off( 'select' );

		frame.on( 'select', () => {
			const url = frame.state().get( 'selection' ).first().toJSON().url;
			inputRef.current.value = url;
		} );
	};

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } />
				<Button icon="format-image" onClick={ openMediaPopup } className="ss-insert-image" />
			</div>
		</Control>
	);
};

export default Image;
