import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, URLInput } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		eyebrow,
		heading,
		body,
		list,
		listStyle,
		listLabel,
		primaryText,
		primaryUrl,
		secondaryText,
		secondaryUrl,
		layout,
		align,
		background,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Content section" textDomain="cb-andwisentrances2026">
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
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<TextControl
						label={ __( 'List label', 'cb-andwisentrances2026' ) }
						value={ listLabel }
						onChange={ ( value ) => setAttributes( { listLabel: value } ) }
						help={ __( 'Small uppercase label above the list (optional)', 'cb-andwisentrances2026' ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'List style', 'cb-andwisentrances2026' ) }
						value={ listStyle }
						options={ [
							{ label: 'Tick list', value: 'check' },
							{ label: 'Numbered steps', value: 'number' },
							{ label: 'Plain list with dividers', value: 'plain' },
						] }
						onChange={ ( value ) => setAttributes( { listStyle: value } ) }
					/>
				</div>
			</div>
			<TextareaControl
				label={ __( 'List', 'cb-andwisentrances2026' ) }
				value={ list }
				onChange={ ( value ) => setAttributes( { list: value } ) }
				help={ __( 'One item per line', 'cb-andwisentrances2026' ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '33 1 0%' } }>
					<SelectControl
						label={ __( 'Layout', 'cb-andwisentrances2026' ) }
						value={ layout }
						options={ [
							{ label: 'Split (heading left, copy right)', value: 'split' },
							{ label: 'Stack (single column)', value: 'stack' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<SelectControl
						label={ __( 'Alignment', 'cb-andwisentrances2026' ) }
						value={ align }
						options={ [
							{ label: 'Left', value: 'start' },
							{ label: 'Centred', value: 'center' },
						] }
						onChange={ ( value ) => setAttributes( { align: value } ) }
						help={ __( 'Centred only applies to the stack layout', 'cb-andwisentrances2026' ) }
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
							{ label: 'Mint', value: 'mint' },
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
