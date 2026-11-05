import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, URLInput, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		eyebrow,
		heading,
		body,
		checklist,
		checklistLabel,
		imageId,
		imageUrl,
		imageAlt,
		imageStyle,
		imagePosition,
		primaryText,
		primaryUrl,
		secondaryText,
		secondaryUrl,
		background,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Image & Text" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Eyebrow', 'cb-andwisentrances2026' ) }
				value={ eyebrow }
				onChange={ ( value ) => setAttributes( { eyebrow: value } ) }
				help={ __( 'Small uppercase line above the heading (optional)', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<div className="cb-andwisentrances2026-editor-field">
				<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Body', 'cb-andwisentrances2026' ) }</label>
				<RichText
					tagName="div"
					className="cb-andwisentrances2026-editor-field__control"
					aria-label={ __( 'Body', 'cb-andwisentrances2026' ) }
					placeholder={ __( 'Body copy', 'cb-andwisentrances2026' ) }
					value={ body }
					onChange={ ( value ) => setAttributes( { body: value } ) }
				/>
			</div>
			<TextControl
				label={ __( 'Checklist label', 'cb-andwisentrances2026' ) }
				value={ checklistLabel }
				onChange={ ( value ) => setAttributes( { checklistLabel: value } ) }
				help={ __( 'Small uppercase label above the tick list (optional)', 'cb-andwisentrances2026' ) }
			/>
			<TextareaControl
				label={ __( 'Checklist', 'cb-andwisentrances2026' ) }
				value={ checklist }
				onChange={ ( value ) => setAttributes( { checklist: value } ) }
				help={ __( 'One item per line', 'cb-andwisentrances2026' ) }
			/>
			<div className="cb-andwisentrances2026-editor-field">
				<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Image', 'cb-andwisentrances2026' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							setAttributes( {
								imageId: media.id,
								imageUrl: media.url,
								imageAlt: media.alt || '',
							} )
						}
						allowedTypes={ [ 'image' ] }
						value={ imageId }
						render={ ( { open } ) => (
							<div className="cb-andwisentrances2026-editor-field__control">
								{ imageUrl && (
									<img
										src={ imageUrl }
										alt={ imageAlt }
										style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } }
									/>
								) }
								<Button variant="secondary" onClick={ open }>
									{ imageUrl ? __( 'Replace Image', 'cb-andwisentrances2026' ) : __( 'Select Image', 'cb-andwisentrances2026' ) }
								</Button>
								{ imageUrl && (
									<Button
										variant="link"
										isDestructive
										onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) }
										style={ { marginLeft: '8px' } }
									>
										{ __( 'Remove', 'cb-andwisentrances2026' ) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
			</div>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '33 1 0%' } }>
					<SelectControl
						label={ __( 'Image style', 'cb-andwisentrances2026' ) }
						value={ imageStyle }
						options={ [
							{ label: 'Circle with rotating dots', value: 'circle' },
							{ label: 'Rounded photo', value: 'photo' },
						] }
						onChange={ ( value ) => setAttributes( { imageStyle: value } ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<SelectControl
						label={ __( 'Image position', 'cb-andwisentrances2026' ) }
						value={ imagePosition }
						options={ [
							{ label: 'Right', value: 'right' },
							{ label: 'Left', value: 'left' },
						] }
						onChange={ ( value ) => setAttributes( { imagePosition: value } ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<SelectControl
						label={ __( 'Background', 'cb-andwisentrances2026' ) }
						value={ background }
						options={ [
							{ label: 'Paper (white)', value: 'paper' },
							{ label: 'Sage tint', value: 'sage-tint' },
							{ label: 'Sage', value: 'sage' },
							{ label: 'Forest', value: 'forest' },
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
			</div>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<TextControl
						label={ __( 'Primary button text', 'cb-andwisentrances2026' ) }
						value={ primaryText }
						onChange={ ( value ) => setAttributes( { primaryText: value } ) }
					/>
					<div className="cb-andwisentrances2026-editor-field">
						<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Primary button URL', 'cb-andwisentrances2026' ) }</label>
						<URLInput value={ primaryUrl || '' } onChange={ ( value ) => setAttributes( { primaryUrl: value } ) } />
					</div>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<TextControl
						label={ __( 'Secondary button text', 'cb-andwisentrances2026' ) }
						value={ secondaryText }
						onChange={ ( value ) => setAttributes( { secondaryText: value } ) }
					/>
					<div className="cb-andwisentrances2026-editor-field">
						<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Secondary button URL', 'cb-andwisentrances2026' ) }</label>
						<URLInput value={ secondaryUrl || '' } onChange={ ( value ) => setAttributes( { secondaryUrl: value } ) } />
					</div>
				</div>
			</div>
		</EditorBlockShell>
	);
}
