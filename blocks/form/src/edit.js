import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, formId, need, product, building, note, layout, background } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	// Gravity Forms id => title, printed by cbae_editor_data() (inc/helpers.php).
	// Empty when the plugin is inactive or has no forms yet.
	const forms = ( typeof window !== 'undefined' && window.cbaeEditorData?.forms ) || {};
	const formEntries = Object.entries( forms );
	const hasForms = formEntries.length > 0;
	const currentId = Number( formId ) || 0;
	const currentTitle = forms[ currentId ] || '';

	const formOptions = [
		{ label: __( 'Select a form', 'cb-andwisentrances2026' ), value: '0' },
		...formEntries.map( ( [ id, title ] ) => ( { label: `${ title } (#${ id })`, value: String( id ) } ) ),
	];

	// Keep a saved ID visible even if that form has since been trashed or
	// deactivated, rather than silently showing "Select a form".
	if ( hasForms && currentId && ! currentTitle ) {
		formOptions.push( { label: `#${ currentId } (${ __( 'form not found', 'cb-andwisentrances2026' ) })`, value: String( currentId ) } );
	}

	let placeholder = __( 'No form selected', 'cb-andwisentrances2026' );

	if ( currentId ) {
		placeholder = currentTitle ? `Gravity Form #${ currentId }: ${ currentTitle }` : `Gravity Form #${ currentId }`;
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Form" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextareaControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'Plain text. Line breaks are kept.', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Note', 'cb-andwisentrances2026' ) }
				value={ note }
				onChange={ ( value ) => setAttributes( { note: value } ) }
				help={ __( 'Short reassurance line under the intro, e.g. "No obligation. A local specialist will call you back." (optional)', 'cb-andwisentrances2026' ) }
			/>
			{ hasForms ? (
				<SelectControl
					label={ __( 'Form', 'cb-andwisentrances2026' ) }
					value={ String( currentId ) }
					options={ formOptions }
					onChange={ ( value ) => setAttributes( { formId: parseInt( value, 10 ) || 0 } ) }
				/>
			) : (
				<TextControl
					type="number"
					min={ 0 }
					label={ __( 'Form ID', 'cb-andwisentrances2026' ) }
					value={ currentId || '' }
					onChange={ ( value ) => setAttributes( { formId: parseInt( value, 10 ) || 0 } ) }
					help={ __( 'No Gravity Forms found, so enter the form ID by hand.', 'cb-andwisentrances2026' ) }
				/>
			) }
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Preset: need', 'cb-andwisentrances2026' ) }
						value={ need }
						onChange={ ( value ) => setAttributes( { need: value } ) }
						help={ __( 'Pre-selects the matching option in the form\'s "need" dropdown. Must match the choice value exactly. Leave empty for no preset.', 'cb-andwisentrances2026' ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Preset: product', 'cb-andwisentrances2026' ) }
						value={ product }
						onChange={ ( value ) => setAttributes( { product: value } ) }
						help={ __( 'Pre-selects the matching option in the form\'s "product" dropdown.', 'cb-andwisentrances2026' ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Preset: building', 'cb-andwisentrances2026' ) }
						value={ building }
						onChange={ ( value ) => setAttributes( { building: value } ) }
						help={ __( 'Pre-selects the matching option in the form\'s "building" dropdown.', 'cb-andwisentrances2026' ) }
					/>
				</div>
			</div>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Layout', 'cb-andwisentrances2026' ) }
						value={ layout }
						options={ [
							{ label: 'Split (copy left, form right)', value: 'split' },
							{ label: 'Stacked (copy centred above the form)', value: 'stack' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Background', 'cb-andwisentrances2026' ) }
						value={ background }
						options={ [
							{ label: 'Sage tint', value: 'sage-tint' },
							{ label: 'Sage', value: 'sage' },
							{ label: 'Paper (white)', value: 'paper' },
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
			</div>
			<div className="cb-andwisentrances2026-editor-field">
				<label className="cb-andwisentrances2026-editor-field__label">{ __( 'Form preview', 'cb-andwisentrances2026' ) }</label>
				<div
					style={ {
						padding: '24px 16px',
						border: '1px dashed #9aa89e',
						borderRadius: '8px',
						background: '#f6faf6',
						color: currentId ? '#1c4220' : '#757575',
						fontWeight: 600,
						textAlign: 'center',
					} }
				>
					{ placeholder }
				</div>
				<p className="cb-andwisentrances2026-editor-field__help">
					{ __( 'The real form only renders on the front end.', 'cb-andwisentrances2026' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}
