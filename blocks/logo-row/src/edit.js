import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, logos, imageId, imageUrl, imageAlt, size, background, showLabels } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Logo Row" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
				help={ __( 'Optional', 'cb-andwisentrances2026' ) }
			/>
			<TextareaControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'A short paragraph under the heading (optional)', 'cb-andwisentrances2026' ) }
				rows={ 3 }
			/>
			<RepeaterField
				label={ __( 'Logos', 'cb-andwisentrances2026' ) }
				value={ logos }
				onChange={ ( value ) => setAttributes( { logos: value } ) }
				fields={ [
					{ name: 'image', label: __( 'Logo', 'cb-andwisentrances2026' ), type: 'image' },
					{
						name: 'label',
						label: __( 'Name', 'cb-andwisentrances2026' ),
						type: 'text',
						help: __( 'Used as alt text, and shown under the logo when labels are on', 'cb-andwisentrances2026' ),
					},
					{ name: 'link', label: __( 'Link', 'cb-andwisentrances2026' ), type: 'link' },
				] }
				emptyRow={ { image: 0, imageUrl: '', label: '', link: '', linkText: '' } }
			/>
			<div className="cb-andwisentrances2026-editor-field">
				<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Side image', 'cb-andwisentrances2026' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							setAttributes( {
								imageId: media.id,
								imageUrl: media.sizes?.medium?.url || media.url,
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
				<p className="components-base-control__help" style={ { marginTop: '8px' } }>
					{ __( 'Optional. Puts a photo on the right of the band with the heading, intro and logos on the left (the andwis lifts compliance layout). Works best with the Forest background.', 'cb-andwisentrances2026' ) }
				</p>
			</div>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Logo size', 'cb-andwisentrances2026' ) }
						value={ size }
						options={ [
							{ label: 'Small', value: 'sm' },
							{ label: 'Medium', value: 'md' },
							{ label: 'Large', value: 'lg' },
						] }
						onChange={ ( value ) => setAttributes( { size: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Background', 'cb-andwisentrances2026' ) }
						value={ background }
						options={ [
							{ label: 'Paper (white)', value: 'paper' },
							{ label: 'Sage tint', value: 'sage-tint' },
							{ label: 'Sage', value: 'sage' },
							{ label: 'Forest (logos in white tiles)', value: 'forest' },
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
			</div>
			<ToggleControl
				label={ __( 'Show names under the logos', 'cb-andwisentrances2026' ) }
				checked={ showLabels }
				onChange={ ( value ) => setAttributes( { showLabels: value } ) }
			/>
		</EditorBlockShell>
	);
}
