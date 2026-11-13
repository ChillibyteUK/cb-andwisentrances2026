import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, head1, head2, head3, rows, firstColHeader, note, background } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Table" textDomain="cb-andwisentrances2026">
			<TextControl
				label={ __( 'Heading', 'cb-andwisentrances2026' ) }
				value={ heading }
				onChange={ ( value ) => setAttributes( { heading: value } ) }
			/>
			<TextControl
				label={ __( 'Intro', 'cb-andwisentrances2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Column 1 heading', 'cb-andwisentrances2026' ) }
						value={ head1 }
						onChange={ ( value ) => setAttributes( { head1: value } ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Column 2 heading', 'cb-andwisentrances2026' ) }
						value={ head2 }
						onChange={ ( value ) => setAttributes( { head2: value } ) }
					/>
				</div>
				<div style={ { flex: '33 1 0%' } }>
					<TextControl
						label={ __( 'Column 3 heading', 'cb-andwisentrances2026' ) }
						value={ head3 }
						onChange={ ( value ) => setAttributes( { head3: value } ) }
						help={ __( 'Optional. The third column only shows when this or a row uses it.', 'cb-andwisentrances2026' ) }
					/>
				</div>
			</div>
			<RepeaterField
				label={ __( 'Rows', 'cb-andwisentrances2026' ) }
				value={ rows }
				onChange={ ( next ) => setAttributes( { rows: next } ) }
				fields={ [
					{ name: 'c1', label: head1 || __( 'Column 1', 'cb-andwisentrances2026' ), type: 'text' },
					{ name: 'c2', label: head2 || __( 'Column 2', 'cb-andwisentrances2026' ), type: 'textarea' },
					{ name: 'c3', label: head3 || __( 'Column 3', 'cb-andwisentrances2026' ), type: 'text' },
				] }
				emptyRow={ { c1: '', c2: '', c3: '' } }
			/>
			<ToggleControl
				label={ __( 'First column is a row heading', 'cb-andwisentrances2026' ) }
				checked={ firstColHeader }
				onChange={ ( value ) => setAttributes( { firstColHeader: value } ) }
				help={ __( 'Shows the first cell of each row bolder, as the label for that row.', 'cb-andwisentrances2026' ) }
			/>
			<TextControl
				label={ __( 'Note', 'cb-andwisentrances2026' ) }
				value={ note }
				onChange={ ( value ) => setAttributes( { note: value } ) }
				help={ __( 'Small print under the table (optional)', 'cb-andwisentrances2026' ) }
			/>
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
		</EditorBlockShell>
	);
}
