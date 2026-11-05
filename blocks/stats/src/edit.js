import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, background, stats } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stats" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
				help={ __( 'Optional. Leave empty to show the numbers alone.', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<SelectControl
				label={ __( 'Background', 'cb-andwisentrances2026' ) }
				value={ background }
				options={ [
					{ label: 'Sage', value: 'sage' },
					{ label: 'Forest', value: 'forest' },
					{ label: 'Sage tint', value: 'sage-tint' },
					{ label: 'Paper (white)', value: 'paper' },
				] }
				onChange={ ( value ) => setAttributes( { background: value } ) }
			/>
			<RepeaterField
				label={ __( 'Stats', 'cb-andwisentrances2026' ) }
				value={ stats }
				onChange={ ( rows ) => setAttributes( { stats: rows } ) }
				fields={ [
					{ name: 'value', label: __( 'Value', 'cb-andwisentrances2026' ), type: 'text', help: __( 'e.g. 300+, 24/7, Top 5', 'cb-andwisentrances2026' ) },
					{ name: 'label', label: __( 'Label', 'cb-andwisentrances2026' ), type: 'text' },
				] }
				emptyRow={ { value: '', label: '' } }
			/>
		</EditorBlockShell>
	);
}
