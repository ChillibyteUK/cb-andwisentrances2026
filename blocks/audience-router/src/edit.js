import { __ } from '@wordpress/i18n';
import { useBlockProps, URLInput } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

const ICON_OPTIONS = [
	{ label: 'Door', value: 'door' },
	{ label: 'Wrench', value: 'wrench' },
	{ label: 'Shield', value: 'shield' },
	{ label: 'Bolt', value: 'bolt' },
	{ label: 'Building', value: 'building' },
	{ label: 'Phone', value: 'phone' },
	{ label: 'Clock', value: 'clock' },
	{ label: 'Download', value: 'download' },
	{ label: 'Pin', value: 'pin' },
	{ label: 'Mail', value: 'mail' },
	{ label: 'Quote', value: 'quote' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		heading,
		intro,
		needsHeading,
		needs,
		sectorsHeading,
		sectorsSource,
		sectors,
		sectorsAllText,
		sectorsAllUrl,
		background,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Audience Router" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'One short line under the heading (optional).', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Needs heading', 'cb-andwisentrances2026' ) }
				value={ needsHeading }
				onChange={ ( value ) => setAttributes( { needsHeading: value } ) }
			/>
			<RepeaterField
				label={ __( 'Needs', 'cb-andwisentrances2026' ) }
				layout="column"
				value={ needs }
				onChange={ ( value ) => setAttributes( { needs: value } ) }
				fields={ [
					{ name: 'label', label: __( 'Label', 'cb-andwisentrances2026' ), type: 'text' },
					{ name: 'text', label: __( 'Text', 'cb-andwisentrances2026' ), type: 'textarea' },
					{ name: 'link', label: __( 'Link', 'cb-andwisentrances2026' ), type: 'link' },
					{ name: 'icon', label: __( 'Icon', 'cb-andwisentrances2026' ), type: 'radio', options: ICON_OPTIONS },
				] }
				emptyRow={ { label: '', text: '', link: '', linkText: '', icon: 'door' } }
			/>
			<TextControl
				label={ __( 'Sectors heading', 'cb-andwisentrances2026' ) }
				value={ sectorsHeading }
				onChange={ ( value ) => setAttributes( { sectorsHeading: value } ) }
			/>
			<SelectControl
				label={ __( 'Sectors source', 'cb-andwisentrances2026' ) }
				value={ sectorsSource }
				options={ [
					{ label: 'Automatic (every published sector)', value: 'auto' },
					{ label: 'Manual', value: 'manual' },
				] }
				onChange={ ( value ) => setAttributes( { sectorsSource: value } ) }
			/>
			{ 'manual' === sectorsSource && (
				<RepeaterField
					label={ __( 'Sectors', 'cb-andwisentrances2026' ) }
					value={ sectors }
					onChange={ ( value ) => setAttributes( { sectors: value } ) }
					fields={ [
						{ name: 'label', label: __( 'Label', 'cb-andwisentrances2026' ), type: 'text' },
						{ name: 'link', label: __( 'Link', 'cb-andwisentrances2026' ), type: 'link' },
					] }
					emptyRow={ { label: '', link: '', linkText: '' } }
				/>
			) }
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<TextControl
						label={ __( 'All sectors text', 'cb-andwisentrances2026' ) }
						value={ sectorsAllText }
						onChange={ ( value ) => setAttributes( { sectorsAllText: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<div className="cb-andwisentrances2026-editor-field">
						<label className="cb-andwisentrances2026-editor-field__label">{ __( 'All sectors URL', 'cb-andwisentrances2026' ) }</label>
						<URLInput value={ sectorsAllUrl || '' } onChange={ ( value ) => setAttributes( { sectorsAllUrl: value } ) } />
						<p className="cb-andwisentrances2026-editor-field__help">
							{ __( 'Leave empty to link to the Sectors page automatically.', 'cb-andwisentrances2026' ) }
						</p>
					</div>
				</div>
			</div>
			<SelectControl
				label={ __( 'Background', 'cb-andwisentrances2026' ) }
				value={ background }
				options={ [
					{ label: 'Sage tint', value: 'sage-tint' },
					{ label: 'Paper (white)', value: 'paper' },
					{ label: 'Sage', value: 'sage' },
				] }
				onChange={ ( value ) => setAttributes( { background: value } ) }
			/>
		</EditorBlockShell>
	);
}
