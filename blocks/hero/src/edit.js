import { __ } from '@wordpress/i18n';
import { useBlockProps, URLInput, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		eyebrow,
		heading,
		highlight,
		subline,
		intro,
		bullets,
		imageId,
		imageUrl,
		imageAlt,
		useSiteCtas,
		primaryText,
		primaryUrl,
		secondaryText,
		secondaryUrl,
		size,
		showCurve,
		showBreadcrumbs,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Hero" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Eyebrow', 'cb-andwisentrances2026' ) }
				value={ eyebrow }
				onChange={ ( value ) => setAttributes( { eyebrow: value } ) }
				help={ __( 'Short uppercase line above the heading (optional).', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
				help={ __( 'The page H1. Falls back to the page title when empty.', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Highlight', 'cb-andwisentrances2026' ) }
				value={ highlight }
				onChange={ ( value ) => setAttributes( { highlight: value } ) }
				help={ __( 'A phrase from the heading to pick out in mint. Must match the heading text exactly.', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Subline', 'cb-andwisentrances2026' ) }
				value={ subline }
				onChange={ ( value ) => setAttributes( { subline: value } ) }
			/>
			<TextareaControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<TextareaControl
				label={ __( 'Bullets', 'cb-andwisentrances2026' ) }
				value={ bullets }
				onChange={ ( value ) => setAttributes( { bullets: value } ) }
				help={ __( 'One per line', 'cb-andwisentrances2026' ) }
			/>
			<div className="cb-andwisentrances2026-editor-field">
				<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Background image', 'cb-andwisentrances2026' ) }</label>
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
									{ imageUrl ? __( 'Replace Background image', 'cb-andwisentrances2026' ) : __( 'Select Background image', 'cb-andwisentrances2026' ) }
								</Button>
								{ imageUrl && (
									<Button
										variant="link"
										isDestructive
										style={ { marginLeft: '8px' } }
										onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) }
									>
										{ __( 'Remove', 'cb-andwisentrances2026' ) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
				<p className="cb-andwisentrances2026-editor-field__help">{ __( 'Optional. Without an image the hero is solid forest.', 'cb-andwisentrances2026' ) }</p>
			</div>
			<ToggleControl
				label={ __( 'Use the site-wide Get a Quote / Book a Survey buttons', 'cb-andwisentrances2026' ) }
				checked={ useSiteCtas }
				onChange={ ( value ) => setAttributes( { useSiteCtas: value } ) }
				help={ __( 'Turn off to set your own buttons below.', 'cb-andwisentrances2026' ) }
			/>
			{ ! useSiteCtas && (
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
			) }
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Size', 'cb-andwisentrances2026' ) }
						value={ size }
						options={ [
							{ label: 'Full (home and hub pages)', value: 'full' },
							{ label: 'Compact (inner pages)', value: 'compact' },
						] }
						onChange={ ( value ) => setAttributes( { size: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<ToggleControl
						label={ __( 'Curved bottom edge', 'cb-andwisentrances2026' ) }
						checked={ showCurve }
						onChange={ ( value ) => setAttributes( { showCurve: value } ) }
					/>
					<ToggleControl
						label={ __( 'Show breadcrumbs', 'cb-andwisentrances2026' ) }
						checked={ showBreadcrumbs }
						onChange={ ( value ) => setAttributes( { showBreadcrumbs: value } ) }
						help={ __( 'Inner pages only; the front page never shows them.', 'cb-andwisentrances2026' ) }
					/>
				</div>
			</div>
		</EditorBlockShell>
	);
}
