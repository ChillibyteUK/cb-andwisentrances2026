import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, items, schema, layout, background } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="FAQs / accordion" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextareaControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'Optional short line under the heading.', 'cb-andwisentrances2026' ) }
			/>
			<RepeaterField
				label={ __( 'Questions', 'cb-andwisentrances2026' ) }
				layout="column"
				value={ items }
				onChange={ ( rows ) => setAttributes( { items: rows } ) }
				fields={ [
					{ name: 'question', label: __( 'Question', 'cb-andwisentrances2026' ), type: 'text' },
					{
						name: 'answer',
						label: __( 'Answer', 'cb-andwisentrances2026' ),
						type: 'textarea',
						help: __( 'Blank lines become paragraphs. Basic HTML is allowed: links, lists, bold.', 'cb-andwisentrances2026' ),
					},
				] }
				emptyRow={ { question: '', answer: '' } }
			/>
			<ToggleControl
				label={ __( 'Output FAQ structured data (FAQPage schema)', 'cb-andwisentrances2026' ) }
				checked={ schema }
				onChange={ ( value ) => setAttributes( { schema: value } ) }
				help={ __( 'Turn off when this is a generic click-to-expand list rather than real questions and answers.', 'cb-andwisentrances2026' ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Layout', 'cb-andwisentrances2026' ) }
						value={ layout }
						options={ [
							{ label: 'Split (heading left, accordion right)', value: 'split' },
							{ label: 'Stacked (heading centred above)', value: 'stack' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
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
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
			</div>
		</EditorBlockShell>
	);
}
