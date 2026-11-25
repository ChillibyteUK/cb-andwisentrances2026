import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, people, columns, background } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Team" textDomain="cb-andwisentrances2026">
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
				label={ __( 'People', 'cb-andwisentrances2026' ) }
				value={ people }
				onChange={ ( value ) => setAttributes( { people: value } ) }
				fields={ [
					{
						name: 'image',
						label: __( 'Photo', 'cb-andwisentrances2026' ),
						type: 'image',
						help: __( 'Square head-and-shoulders works best; it is shown as a circle. A placeholder shows until one is chosen.', 'cb-andwisentrances2026' ),
					},
					{ name: 'name', label: __( 'Name', 'cb-andwisentrances2026' ), type: 'text' },
					{ name: 'role', label: __( 'Role', 'cb-andwisentrances2026' ), type: 'text' },
					{
						name: 'bio',
						label: __( 'Short bio', 'cb-andwisentrances2026' ),
						type: 'textarea',
						help: __( 'Optional, two or three lines', 'cb-andwisentrances2026' ),
					},
					{
						name: 'linkedin',
						label: __( 'LinkedIn URL', 'cb-andwisentrances2026' ),
						type: 'text',
						help: __( 'Optional. The full address of their LinkedIn profile.', 'cb-andwisentrances2026' ),
					},
				] }
				emptyRow={ { image: 0, imageUrl: '', name: '', role: '', bio: '', linkedin: '' } }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<SelectControl
						label={ __( 'Columns', 'cb-andwisentrances2026' ) }
						value={ columns }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
							{ label: '4', value: '4' },
						] }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
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
