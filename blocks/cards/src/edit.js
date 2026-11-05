import { __ } from '@wordpress/i18n';
import { useBlockProps, URLInput } from '@wordpress/block-editor';
import { TextControl, SelectControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';
import MultiPostPicker from '../../_shared/MultiPostPicker';

const SELECTABLE_TYPES = [ 'product', 'service', 'sector', 'case_study', 'page' ];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		heading,
		intro,
		source,
		postType,
		topLevelOnly,
		limit,
		selected,
		relatedKey,
		cards,
		style,
		media,
		columns,
		linkText,
		viewAllText,
		viewAllUrl,
		background,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-andwisentrances2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Cards" textDomain="cb-andwisentrances2026">
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
			<SelectControl
				label={ __( 'Source', 'cb-andwisentrances2026' ) }
				value={ source }
				options={ [
					{ label: 'A post type', value: 'post_type' },
					{ label: 'Child pages of this page', value: 'children' },
					{ label: 'Chosen posts', value: 'selected' },
					{ label: 'Related to this page', value: 'related' },
					{ label: 'Manual cards', value: 'manual' },
				] }
				onChange={ ( value ) => setAttributes( { source: value } ) }
			/>
			{ 'post_type' === source && (
				<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
					<div style={ { flex: '50 1 0%' } }>
						<SelectControl
							label={ __( 'Post type', 'cb-andwisentrances2026' ) }
							value={ postType }
							options={ [
								{ label: 'Products', value: 'product' },
								{ label: 'Services', value: 'service' },
								{ label: 'Sectors', value: 'sector' },
								{ label: 'Case studies', value: 'case_study' },
								{ label: 'Pages', value: 'page' },
								{ label: 'News posts', value: 'post' },
							] }
							onChange={ ( value ) => setAttributes( { postType: value } ) }
						/>
						<ToggleControl
							label={ __( 'Top-level only', 'cb-andwisentrances2026' ) }
							checked={ topLevelOnly }
							onChange={ ( value ) => setAttributes( { topLevelOnly: value } ) }
							help={ __( 'Only items with no parent. For products this lists the hubs rather than every product.', 'cb-andwisentrances2026' ) }
						/>
					</div>
					<div style={ { flex: '50 1 0%' } }>
						<TextControl
							type="number"
							label={ __( 'Limit', 'cb-andwisentrances2026' ) }
							value={ limit }
							onChange={ ( value ) => setAttributes( { limit: Number( value ) } ) }
							help={ __( '0 shows all.', 'cb-andwisentrances2026' ) }
						/>
					</div>
				</div>
			) }
			{ 'children' === source && (
				<p className="cb-andwisentrances2026-editor-field__help">
					{ __( 'Lists the published child pages of the page this block sits on, in menu order.', 'cb-andwisentrances2026' ) }
				</p>
			) }
			{ 'selected' === source && (
				<MultiPostPicker
					label={ __( 'Posts', 'cb-andwisentrances2026' ) }
					postTypes={ SELECTABLE_TYPES }
					value={ selected }
					onChange={ ( value ) => setAttributes( { selected: value } ) }
					help={ __( 'Search to add. Cards render in this order.', 'cb-andwisentrances2026' ) }
				/>
			) }
			{ 'related' === source && (
				<SelectControl
					label={ __( 'Relationship', 'cb-andwisentrances2026' ) }
					value={ relatedKey }
					options={ [
						{ label: 'Related products & services', value: 'related_products' },
						{ label: 'Related sectors', value: 'related_sectors' },
					] }
					onChange={ ( value ) => setAttributes( { relatedKey: value } ) }
					help={ __( "Reads the relationship field from this page's Listing & details panel.", 'cb-andwisentrances2026' ) }
				/>
			) }
			{ 'manual' === source && (
				<RepeaterField
					label={ __( 'Cards', 'cb-andwisentrances2026' ) }
					layout="column"
					value={ cards }
					onChange={ ( value ) => setAttributes( { cards: value } ) }
					fields={ [
						{ name: 'title', label: __( 'Title', 'cb-andwisentrances2026' ), type: 'text' },
						{ name: 'text', label: __( 'Text', 'cb-andwisentrances2026' ), type: 'textarea' },
						{ name: 'link', label: __( 'Link', 'cb-andwisentrances2026' ), type: 'link' },
						{ name: 'image', label: __( 'Image', 'cb-andwisentrances2026' ), type: 'image' },
					] }
					emptyRow={ { title: '', text: '', link: '', linkText: '', image: 0, imageUrl: '' } }
				/>
			) }
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '25 1 0%' } }>
					<SelectControl
						label={ __( 'Tile style', 'cb-andwisentrances2026' ) }
						value={ style }
						options={ [
							{ label: 'Forest', value: 'forest' },
							{ label: 'Light', value: 'light' },
							{ label: 'Sage', value: 'sage' },
						] }
						onChange={ ( value ) => setAttributes( { style: value } ) }
					/>
				</div>
				<div style={ { flex: '25 1 0%' } }>
					<SelectControl
						label={ __( 'Media', 'cb-andwisentrances2026' ) }
						value={ media }
						options={ [
							{ label: 'Image', value: 'image' },
							{ label: 'Icon', value: 'icon' },
							{ label: 'None', value: 'none' },
						] }
						onChange={ ( value ) => setAttributes( { media: value } ) }
					/>
				</div>
				<div style={ { flex: '25 1 0%' } }>
					<SelectControl
						label={ __( 'Columns', 'cb-andwisentrances2026' ) }
						value={ columns }
						options={ [
							{ label: '3', value: '3' },
							{ label: '2', value: '2' },
							{ label: '4', value: '4' },
						] }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
					/>
				</div>
				<div style={ { flex: '25 1 0%' } }>
					<SelectControl
						label={ __( 'Background', 'cb-andwisentrances2026' ) }
						value={ background }
						options={ [
							{ label: 'Paper (white)', value: 'paper' },
							{ label: 'Sage tint', value: 'sage-tint' },
							{ label: 'Sage', value: 'sage' },
							{ label: 'Forest', value: 'forest' },
						] }
						onChange={ ( value ) => setAttributes( { background: value } ) }
					/>
				</div>
			</div>
			<TextControl
				label={ __( 'Link text', 'cb-andwisentrances2026' ) }
				value={ linkText }
				onChange={ ( value ) => setAttributes( { linkText: value } ) }
				help={ __( 'Overrides the link label on every card (optional).', 'cb-andwisentrances2026' ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
					<TextControl
						label={ __( 'View all text', 'cb-andwisentrances2026' ) }
						value={ viewAllText }
						onChange={ ( value ) => setAttributes( { viewAllText: value } ) }
					/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
					<div className="cb-andwisentrances2026-editor-field">
						<label className="cb-andwisentrances2026-editor-field__label">{ __( 'View all URL', 'cb-andwisentrances2026' ) }</label>
						<URLInput value={ viewAllUrl || '' } onChange={ ( value ) => setAttributes( { viewAllUrl: value } ) } />
					</div>
				</div>
			</div>
		</EditorBlockShell>
	);
}
