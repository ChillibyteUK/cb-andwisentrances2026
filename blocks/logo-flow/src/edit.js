import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, logos, showResult, resultId, resultUrl, resultAlt, background } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Logo Flow" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'One short line under the heading (optional)', 'cb-andwisentrances2026' ) }
			/>
			<RepeaterField
				label={ __( 'Legacy logos', 'cb-andwisentrances2026' ) }
				value={ logos }
				onChange={ ( value ) => setAttributes( { logos: value } ) }
				fields={ [
					{ name: 'image', label: __( 'Logo', 'cb-andwisentrances2026' ), type: 'image' },
					{
						name: 'label',
						label: __( 'Name', 'cb-andwisentrances2026' ),
						type: 'text',
						help: __( 'Used as alt text, and shown in the box when there is no logo yet', 'cb-andwisentrances2026' ),
					},
				] }
				emptyRow={ { image: 0, imageUrl: '', label: '' } }
			/>
			<ToggleControl
				label={ __( 'Show the arrow and the brand they became', 'cb-andwisentrances2026' ) }
				checked={ showResult }
				onChange={ ( value ) => setAttributes( { showResult: value } ) }
			/>
			{ showResult && (
				<div className="cb-andwisentrances2026-editor-field">
					<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Result logo', 'cb-andwisentrances2026' ) }</label>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									resultId: media.id,
									resultUrl: media.url,
									resultAlt: media.alt || '',
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ resultId }
							render={ ( { open } ) => (
								<div className="cb-andwisentrances2026-editor-field__control">
									{ resultUrl && (
										<img
											src={ resultUrl }
											alt={ resultAlt }
											style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } }
										/>
									) }
									<Button variant="secondary" onClick={ open }>
										{ resultUrl ? __( 'Replace Image', 'cb-andwisentrances2026' ) : __( 'Select Image', 'cb-andwisentrances2026' ) }
									</Button>
									{ resultUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={ () => setAttributes( { resultId: 0, resultUrl: '', resultAlt: '' } ) }
											style={ { marginLeft: '8px' } }
										>
											{ __( 'Remove', 'cb-andwisentrances2026' ) }
										</Button>
									) }
								</div>
							) }
						/>
					</MediaUploadCheck>
					<p className="cb-andwisentrances2026-editor-field__help">
						{ __( 'Leave empty to use the site logo.', 'cb-andwisentrances2026' ) }
					</p>
				</div>
			) }
			<SelectControl
				label={ __( 'Background', 'cb-andwisentrances2026' ) }
				value={ background }
				options={ [
					{ label: 'Paper (white)', value: 'paper' },
					{ label: 'Sage tint', value: 'sage-tint' },
				] }
				onChange={ ( value ) => setAttributes( { background: value } ) }
			/>
		</EditorBlockShell>
	);
}
