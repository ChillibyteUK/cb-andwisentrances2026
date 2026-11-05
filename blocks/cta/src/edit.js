import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, URLInput } from '@wordpress/block-editor';
import { TextControl, SelectControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, body, primaryText, primaryUrl, secondaryText, secondaryUrl, useSiteCtas, background, layout } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="CTA Band" textDomain="cb-andwisentrances2026">
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
					placeholder={ __( 'One short line under the heading (optional)', 'cb-andwisentrances2026' ) }
					value={ body }
					onChange={ ( value ) => setAttributes( { body: value } ) }
				/>
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
						label={ __( 'Background', 'cb-andwisentrances2026' ) }
						value={ background }
						options={ [
							{ label: 'Forest', value: 'forest' },
							{ label: 'Sage', value: 'sage' },
							{ label: 'Sage tint', value: 'sage-tint' },
							{ label: 'Paper (white)', value: 'paper' },
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Layout', 'cb-andwisentrances2026' ) }
						value={ layout }
						options={ [
							{ label: 'Split (copy left, buttons right)', value: 'split' },
							{ label: 'Centred', value: 'centred' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
				</div>
			</div>
		</EditorBlockShell>
	);
}
