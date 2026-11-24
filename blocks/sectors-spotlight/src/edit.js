import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, body, sectorsSource, sectors } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Sectors Spotlight" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextareaControl
				label={ __( 'Body', 'cb-andwisentrances2026' ) }
				value={ body }
				rows={ 6 }
				onChange={ ( value ) => setAttributes( { body: value } ) }
				help={ __( 'Leave a blank line between paragraphs.', 'cb-andwisentrances2026' ) }
			/>
			<SelectControl
				label={ __( 'Sectors source', 'cb-andwisentrances2026' ) }
				value={ sectorsSource }
				options={ [
					{ label: 'Automatic (every published sector)', value: 'auto' },
					{ label: 'Manual', value: 'manual' },
				] }
				onChange={ ( value ) => setAttributes( { sectorsSource: value } ) }
				help={ __( 'Each name links to its sector page. Short names work best: a long one wraps onto two lines.', 'cb-andwisentrances2026' ) }
			/>
			{ 'manual' === sectorsSource && (
				<RepeaterField
					label={ __( 'Sectors', 'cb-andwisentrances2026' ) }
					value={ sectors }
					onChange={ ( value ) => setAttributes( { sectors: value } ) }
					fields={ [
						{ name: 'label', label: __( 'Label', 'cb-andwisentrances2026' ), type: 'text' },
						{ name: 'link', label: __( 'Link (optional)', 'cb-andwisentrances2026' ), type: 'link' },
					] }
					emptyRow={ { label: '', link: '', linkText: '' } }
				/>
			) }
		</EditorBlockShell>
	);
}
